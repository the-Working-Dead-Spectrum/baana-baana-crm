<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Creator;
use App\Models\SyncLog;
use App\Services\WordPressService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class SyncCreatorsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300;
    
    public function handle(WordPressService $wordPressService): void
    {
        $startTime = microtime(true);
        $log = SyncLog::create([
            'sync_type' => 'creators',
            'status' => 'pending',
            'started_at' => now(),
        ]);
        
        try {
            $stats = [
                'total' => 0,
                'created_users' => 0,
                'created_creators' => 0,
                'updated' => 0,
                'failed' => 0,
            ];
            
            $page = 1;
            $hasMore = true;
            
            while ($hasMore) {
                $creators = $wordPressService->getCreators($page, 100);
                
                if (empty($creators)) {
                    $hasMore = false;
                    continue;
                }
                
                foreach ($creators as $creatorData) {
                    $stats['total']++;
                    
                    try {
                        DB::transaction(function () use ($creatorData, &$stats) {
                            
                            $user = User::where('email', $creatorData['email'] ?? '')->first();
                            
                            if (!$user) {
                                $user = User::create([
                                    'name' => $creatorData['name'] ?? '',
                                    'email' => $creatorData['email'] ?? '',
                                    'password' => Hash::make('password'),
                                    'role' => 'creator',
                                    'is_active' => true,
                                    'wp_creator_id' => $creatorData['id'] ?? null,
                                    'email_verified_at' => now(),
                                ]);
                                
                                $stats['created_users']++;
                                Log::info('✅ User created from sync', [
                                    'user_id' => $user->id, 
                                    'email' => $user->email,
                                    'default_password' => 'password'
                                ]);
                            }
                            
                            $creator = Creator::where('wp_creator_id', $creatorData['id'] ?? 0)
                                ->orWhere('email', $creatorData['email'] ?? '')
                                ->first();
                            
                            if ($creator) {
                                $creator->update([
                                    'user_id' => $user->id,
                                    'name' => $creatorData['name'] ?? $creator->name,
                                    'email' => $creatorData['email'] ?? $creator->email,
                                    'phone' => $creatorData['phone'] ?? $creator->phone,
                                    'address' => $creatorData['address'] ?? $creator->address,
                                    'brand_slug' => $creatorData['brand_slug'] ?? $creator->brand_slug,
                                    'status' => $creatorData['status'] ?? $creator->status,
                                    'last_synced_at' => now(),
                                ]);
                                $stats['updated']++;
                                Log::info('🔄 Creator updated from sync', ['creator_id' => $creator->id]);
                            } else {
                                $creator = Creator::create([
                                    'user_id' => $user->id,
                                    'wp_creator_id' => $creatorData['id'] ?? 0,
                                    'name' => $creatorData['name'] ?? '',
                                    'email' => $creatorData['email'] ?? '',
                                    'phone' => $creatorData['phone'] ?? null,
                                    'address' => $creatorData['address'] ?? null,
                                    'brand_slug' => $creatorData['brand_slug'] ?? '',
                                    'status' => $creatorData['status'] ?? 'active',
                                    'total_orders' => 0,
                                    'total_sales' => 0,
                                    'last_synced_at' => now(),
                                ]);
                                $stats['created_creators']++;
                                Log::info('✅ Creator created from sync', ['creator_id' => $creator->id, 'user_id' => $user->id]);
                            }
                        });
                        
                    } catch (\Exception $e) {
                        $stats['failed']++;
                        Log::error('❌ Failed to sync creator', [
                            'creator_data' => $creatorData,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                }
                
                if (count($creators) < 100) {
                    $hasMore = false;
                } else {
                    $page++;
                }
            }
            
            $duration = round((microtime(true) - $startTime) * 1000);
            
            $log->update([
                'status' => $stats['failed'] > 0 ? 'partial' : 'success',
                'total_records' => $stats['total'],
                'created_records' => $stats['created_creators'],
                'updated_records' => $stats['updated'],
                'failed_records' => $stats['failed'],
                'metadata' => [
                    'users_created' => $stats['created_users'],
                    'default_password' => 'password'
                ],
                'duration_ms' => $duration,
                'completed_at' => now(),
            ]);
            
            Log::info('✅ Creators sync completed', $stats);
            
        } catch (\Exception $e) {
            $duration = round((microtime(true) - $startTime) * 1000);
            
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'duration_ms' => $duration,
                'completed_at' => now(),
            ]);
            
            Log::error('❌ Creators sync failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }
}