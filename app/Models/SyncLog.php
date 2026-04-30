<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    protected $fillable = [
        'sync_type',
        'status',
        'total_records',
        'created_records',
        'updated_records',
        'failed_records',
        'error_message',
        'error_details',
        'duration_ms',
        'metadata',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'error_details' => 'array',
        'metadata' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
    
    // Scopes
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('started_at', '>=', now()->subHours($hours));
    }
    
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
    
    public function scopeType($query, $type)
    {
        return $query->where('sync_type', $type);
    }
}