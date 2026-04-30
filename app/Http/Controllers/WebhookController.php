<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Creator;
use App\Models\Product;
use App\Jobs\SyncCreatorsJob;
use App\Jobs\SyncProductsJob;
use App\Jobs\SyncOrdersJob;
use App\Jobs\SyncSingleOrderJob; // ✅ BON JOB pour une commande
use App\Jobs\SyncOrderCreatorsJob; // ⚠️ Utilisé seulement APRÈS que la commande existe
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WebhookController extends Controller
{
    /**
     * Vérifie le token du webhook
     */
    private function verifyToken(Request $request): bool
    {
        $token = $request->header('X-MP-Webhook-Token');
        $validToken = config('services.wordpress.webhook_secret');

        if (empty($validToken)) {
            Log::error('Webhook secret not configured in Laravel');
            return false;
        }

        $result = hash_equals($validToken, $token ?? '');

        if (!$result) {
            Log::warning('Invalid webhook token', [
                'provided' => substr($token ?? '', 0, 5) . '...',
                'expected' => substr($validToken, 0, 5) . '...'
            ]);
        }

        return $result;
    }

    /**
     * Endpoint de test WordPress
     */
    public function testWordPressConnection(Request $request)
    {
        if (!$this->verifyToken($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Laravel webhook is working',
            'timestamp' => now()->toIso8601String(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ]);
    }

    /**
     * Création d'un créateur depuis WordPress
     */
    public function handleCreatorCreated(Request $request)
    {
        if (!$this->verifyToken($request)) {
            Log::warning('Webhook: Invalid token received for creator creation');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'event' => 'required|string',
            'creator.wp_creator_id' => 'required|integer',
            'creator.name' => 'required|string|max:255',
            'creator.email' => 'required|email|max:255',
            'creator.phone' => 'nullable|string|max:50',
            'creator.brand_slug' => 'required|string|max:100',
            'creator.address' => 'nullable|string',
            'timestamp' => 'required|string',
            'site_url' => 'required|url'
        ]);

        $creatorData = $data['creator'];

        Log::info('📥 Webhook créateur reçu', [
            'wp_creator_id' => $creatorData['wp_creator_id'],
            'email' => $creatorData['email'],
            'brand_slug' => $creatorData['brand_slug']
        ]);

        try {
            DB::beginTransaction();

            $user = User::where('email', $creatorData['email'])->first();
            $isNewUser = false;

            if (!$user) {
                $password = str_replace('-', '', $creatorData['brand_slug']);

                $user = User::create([
                    'name' => $creatorData['name'],
                    'email' => $creatorData['email'],
                    'password' => Hash::make($password),
                    'role' => 'creator',
                    'is_active' => true,
                    'wp_creator_id' => $creatorData['wp_creator_id'],
                    'email_verified_at' => now(),
                ]);

                $isNewUser = true;
                Log::info('✅ Nouvel utilisateur créé', ['user_id' => $user->id]);
            } else {
                if (empty($user->wp_creator_id)) {
                    $user->update(['wp_creator_id' => $creatorData['wp_creator_id']]);
                }
                if ($user->role !== 'creator') {
                    $user->update(['role' => 'creator']);
                }
            }

            $creator = Creator::where('wp_creator_id', $creatorData['wp_creator_id'])
                ->orWhere('email', $creatorData['email'])
                ->first();

            $isNewCreator = false;

            if ($creator) {
                $creator->update([
                    'user_id' => $user->id,
                    'name' => $creatorData['name'],
                    'email' => $creatorData['email'],
                    'phone' => $creatorData['phone'] ?? $creator->phone,
                    'address' => $creatorData['address'] ?? $creator->address,
                    'brand_slug' => $creatorData['brand_slug'],
                    'status' => 'active',
                    'last_synced_at' => now(),
                ]);

                Log::info('🔄 Créateur mis à jour', ['creator_id' => $creator->id]);
            } else {
                $existingBrand = Creator::where('brand_slug', $creatorData['brand_slug'])->first();
                if ($existingBrand) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Brand slug already exists',
                        'brand_slug' => $creatorData['brand_slug']
                    ], 409);
                }

                $creator = Creator::create([
                    'user_id' => $user->id,
                    'wp_creator_id' => $creatorData['wp_creator_id'],
                    'name' => $creatorData['name'],
                    'email' => $creatorData['email'],
                    'phone' => $creatorData['phone'] ?? null,
                    'address' => $creatorData['address'] ?? null,
                    'brand_slug' => $creatorData['brand_slug'],
                    'status' => 'active',
                    'total_orders' => 0,
                    'total_sales' => 0,
                    'last_synced_at' => now(),
                ]);

                $isNewCreator = true;
                Log::info('✅ Nouveau créateur créé', ['creator_id' => $creator->id]);
            }

            DB::commit();

            if ($creator && $creator->brand_slug) {
                Log::info('🔄 Lancement automatique de la synchronisation des produits', [
                    'brand_slug' => $creator->brand_slug,
                    'creator_id' => $creator->id
                ]);

                SyncProductsJob::dispatch($creator->brand_slug)->onQueue('sync');
            }

            return response()->json([
                'success' => true,
                'message' => $isNewCreator ? 'Creator created successfully' : 'Creator updated successfully',
                'user_id' => $user->id,
                'creator_id' => $creator->id,
                'wp_creator_id' => $creator->wp_creator_id,
                'brand_slug' => $creator->brand_slug,
                'is_new_user' => $isNewUser,
                'is_new_creator' => $isNewCreator,
            ], $isNewCreator ? 201 : 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Unexpected error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // Dans votre WebhookController.php, remplacez syncOrders() par ceci :

    public function syncOrders(Request $request)
    {
        if (!$this->verifyToken($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'event' => 'required|string',
            'order_id' => 'nullable|integer',
            'timestamp' => 'required|string',
            'site_url' => 'required|url',
            'new_status' => 'nullable|string'
        ]);

        Log::info('📦 Webhook commande reçu', [
            'event' => $data['event'],
            'order_id' => $data['order_id'] ?? 'all'
        ]);

        try {
            // ========================================
            // COMMANDE UNIQUE → EXÉCUTION DIRECTE
            // ========================================
            if (isset($data['order_id']) && $data['order_id'] > 0) {
                $orderId = $data['order_id'];

                Log::info("⚡ Exécution directe de la synchronisation de la commande #{$orderId}");

                // ✅ Appel direct sans queue
                $job = new SyncSingleOrderJob($orderId);
                $job->handle(
                    app(\App\Services\WordPressService::class),
                    app(\App\Services\CreatorOrderSyncService::class)
                );

                Log::info("✅ Commande #{$orderId} synchronisée avec succès");

                return response()->json([
                    'success' => true,
                    'message' => "Order #{$orderId} synced immediately",
                    'order_id' => $orderId,
                    'synced_at' => now()->toIso8601String()
                ], 200);
            }

            // ========================================
            // SYNC COMPLÈTE → EXÉCUTION DIRECTE
            // ========================================
            else {
                Log::info("📦 Sync complète des commandes - exécution directe");

                $job = new \App\Jobs\SyncOrdersJob('incremental', false);
                $job->handle(
                    app(\App\Services\WordPressService::class),
                    app(\App\Services\CreatorOrderSyncService::class)
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Full orders sync completed',
                    'synced_at' => now()->toIso8601String()
                ], 200);
            }
        } catch (\Exception $e) {
            Log::error('❌ Sync orders error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Synchronisation des produits
     */
    public function syncProducts(Request $request)
    {
        if (!$this->verifyToken($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'event'      => 'required|string',
            'product_id' => 'nullable|integer',
            'brand_slug' => 'nullable|string',
            'timestamp'  => 'required|string',
            'site_url'   => 'required|url'
        ]);

        Log::info('📦 Webhook produit reçu', ['product_id' => $data['product_id'] ?? 'all']);

        try {
            $job = new SyncProductsJob(
                $data['brand_slug'] ?? null,
                isset($data['product_id']) ? $request->all() : null
            );
            $job->handle(app(\App\Services\WordPressService::class));

            return response()->json([
                'success'   => true,
                'message'   => isset($data['product_id'])
                    ? "Product #{$data['product_id']} synced immediately"
                    : 'Full products sync completed',
                'synced_at' => now()->toIso8601String()
            ], 200);
        } catch (\Exception $e) {
            Log::error('❌ Sync products error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Synchronisation des produits par marque
     */
    public function syncProductsByBrand(Request $request)
    {
        if (!$this->verifyToken($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'event'      => 'required|string',
            'brand_slug' => 'required|string',
            'timestamp'  => 'required|string'
        ]);

        Log::info('🏷️ Sync produits par marque', ['brand_slug' => $data['brand_slug']]);

        try {
            $job = new SyncProductsJob($data['brand_slug'], null);
            $job->handle(app(\App\Services\WordPressService::class));

            return response()->json([
                'success'    => true,
                'message'    => "Brand {$data['brand_slug']} products synced immediately",
                'brand_slug' => $data['brand_slug'],
                'synced_at'  => now()->toIso8601String()
            ], 200);
        } catch (\Exception $e) {
            Log::error('❌ Sync products by brand error', [
                'brand_slug' => $data['brand_slug'],
                'error'      => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Synchronisation des commandes avec créateurs
     */
    public function syncOrdersWithCreators(Request $request)
    {
        if (!$this->verifyToken($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'event'                    => 'required|string',
            'order_id'                 => 'required|integer',
            'creators'                 => 'required|array',
            'creators.*.creator_id'    => 'nullable|integer',
            'creators.*.brand_slug'    => 'required|string',
            'creators.*.total'         => 'required|numeric',
            'timestamp'                => 'required|string'
        ]);

        Log::info('📦 Commande avec créateurs reçue', [
            'order_id'       => $data['order_id'],
            'creators_count' => count($data['creators'])
        ]);

        try {
            $order = \App\Models\Order::where('wp_order_id', $data['order_id'])->first();

            if (!$order) {
                Log::warning("⚠️ Commande #{$data['order_id']} non trouvée, sync complète lancée");

                // Commande inexistante → sync complète directe
                $job = new SyncSingleOrderJob($data['order_id']);
                $job->handle(
                    app(\App\Services\WordPressService::class),
                    app(\App\Services\CreatorOrderSyncService::class)
                );

                return response()->json([
                    'success'  => true,
                    'message'  => "Order #{$data['order_id']} not found, synced immediately",
                    'order_id' => $data['order_id'],
                    'synced_at' => now()->toIso8601String()
                ], 200);
            }

            // Commande existante → sync créateurs directe
            $job = new \App\Jobs\SyncOrderCreatorsJob($order->id);
            $job->handle(app(\App\Services\CreatorOrderSyncService::class));

            return response()->json([
                'success'        => true,
                'message'        => 'Order creators synced immediately',
                'order_id'       => $data['order_id'],
                'creators_count' => count($data['creators']),
                'synced_at'      => now()->toIso8601String()
            ], 200);
        } catch (\Exception $e) {
            Log::error('❌ Sync order creators error', [
                'order_id' => $data['order_id'],
                'error'    => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Synchronisation complète
     */
    public function fullSync(Request $request)
    {
        if (!$this->verifyToken($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'event'     => 'required|string',
            'sync_type' => 'required|string',
            'timestamp' => 'required|string'
        ]);

        Log::info('🔄 Full sync demandée', ['type' => $data['sync_type']]);

        try {
            $wpService           = app(\App\Services\WordPressService::class);
            $creatorSyncService  = app(\App\Services\CreatorOrderSyncService::class);

            switch ($data['sync_type']) {
                case 'creators':
                    (new SyncCreatorsJob())->handle($wpService);
                    break;

                case 'products':
                    (new SyncProductsJob())->handle($wpService);
                    break;

                case 'orders':
                    (new SyncOrdersJob('full', true))->handle($wpService, $creatorSyncService);
                    break;

                case 'all':
                    (new SyncCreatorsJob())->handle($wpService);
                    (new SyncProductsJob())->handle($wpService);
                    (new SyncOrdersJob('full', true))->handle($wpService, $creatorSyncService);
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => "Unknown sync type: {$data['sync_type']}"
                    ], 422);
            }

            return response()->json([
                'success'   => true,
                'message'   => "Full sync '{$data['sync_type']}' completed immediately",
                'sync_type' => $data['sync_type'],
                'synced_at' => now()->toIso8601String()
            ], 200);
        } catch (\Exception $e) {
            Log::error('❌ Full sync error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Synchronisation des marques
     */
    public function syncBrands(Request $request)
    {
        if (!$this->verifyToken($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Log::info('🏷️ Brand sync webhook reçu', $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Brand sync received'
        ]);
    }

    /**
     * Gérer la suppression d'un créateur depuis WordPress
     */
    public function handleCreatorDeleted(Request $request)
    {
        if (!$this->verifyToken($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            // Valider le webhook token
            $this->validateWebhookToken($request);

            $data = $request->all();
            $creatorData = $data['creator'] ?? [];

            Log::info('🗑️ Webhook: Creator deletion received', [
                'wp_creator_id' => $creatorData['wp_creator_id'] ?? null,
                'wp_laravel_id' => $creatorData['wp_laravel_id'] ?? null,
                'name' => $creatorData['name'] ?? null,
                'email' => $creatorData['email'] ?? null,
            ]);

            // Trouver le créateur à supprimer
            $creator = null;

            // Option 1: Chercher par wp_creator_id (ID WordPress)
            if (!empty($creatorData['wp_creator_id'])) {
                $creator = Creator::where('wp_creator_id', $creatorData['wp_creator_id'])->first();
            }

            // Option 2: Chercher par email si pas trouvé
            if (!$creator && !empty($creatorData['email'])) {
                $creator = Creator::where('email', $creatorData['email'])->first();
            }

            // Option 3: Chercher par brand_slug si pas trouvé
            if (!$creator && !empty($creatorData['brand_slug'])) {
                $creator = Creator::where('brand_slug', $creatorData['brand_slug'])->first();
            }

            if (!$creator) {
                Log::warning('⚠️ Creator not found for deletion', [
                    'wp_creator_id' => $creatorData['wp_creator_id'] ?? null,
                    'email' => $creatorData['email'] ?? null,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Creator not found in Laravel CRM'
                ], 404);
            }

            // Sauvegarder les infos avant suppression (pour les logs)
            $creatorInfo = [
                'id' => $creator->id,
                'name' => $creator->name,
                'email' => $creator->email,
                'brand_slug' => $creator->brand_slug,
            ];

            // Option A: Suppression définitive (hard delete)
            $creator->delete();

            // Option B: Suppression douce (soft delete) - si vous avez activé SoftDeletes
            // $creator->delete(); // Avec SoftDeletes, cela fait une suppression douce automatiquement

            // Option C: Désactivation au lieu de suppression
            // $creator->update(['status' => 'deleted', 'deleted_by' => 'wordpress']);

            Log::info('✅ Creator deleted successfully from Laravel', $creatorInfo);

            return response()->json([
                'success' => true,
                'message' => 'Creator deleted successfully from Laravel CRM',
                'creator_id' => $creatorInfo['id'],
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Error deleting creator from Laravel', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting creator: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Valider le token webhook (méthode helper)
     */
    private function validateWebhookToken(Request $request)
    {
        $token = $request->header('X-MP-Webhook-Token');
        $expectedToken = config('services.wordpress.webhook_secret');

        if (!$token || $token !== $expectedToken) {
            throw new \Exception('Invalid webhook token');
        }
    }
}
