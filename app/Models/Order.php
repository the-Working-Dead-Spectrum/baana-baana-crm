<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'wp_order_id',
        'order_number',
        'creator_id',
        'order_date',
        'wp_updated_at',
        'status',
        'logistics_status',
        'payment_status',
        'subtotal',
        'tax',
        'shipping',
        'total',
        'creator_total',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'tracking_number',
        'assigned_to',
        'shipped_at',
        'delivered_at',
        'payment_date',
        'metadata',
        'notes',
        'last_synced_at',
        // Champs PAPS
        'paps_task_id',
        'paps_order_uid',
        'paps_status',
        'paps_delivery_details',
        'paps_pickup_scheduled_at',
        'paps_picked_at',
        'paps_delivered_at',
        'paps_delivery_fee',
        'paps_status_history',
        'paps_metadata',
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'wp_updated_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'payment_date' => 'datetime',
        'last_synced_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping' => 'decimal:2',
        'total' => 'decimal:2',
        'creator_total' => 'decimal:2',
        'metadata' => 'array',
        // Champs PAPS
        'paps_delivery_details' => 'array',
        'paps_pickup_scheduled_at' => 'datetime',
        'paps_picked_at' => 'datetime',
        'paps_delivered_at' => 'datetime',
        'paps_delivery_fee' => 'decimal:2',
        'paps_status_history' => 'array',
        'paps_metadata' => 'array',
    ];

    /**
     * Relation : Une commande appartient à un créateur principal (legacy)
     * À garder pour compatibilité si besoin
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }

    /**
     * Relation : Une commande peut être assignée à un utilisateur
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Relation many-to-many avec les créateurs
     * Une commande peut concerner plusieurs créateurs
     */
    public function creators(): BelongsToMany
    {
        return $this->belongsToMany(Creator::class, 'creator_order')
            ->withPivot([
                'creator_total',
                'product_count',
                'total_quantity',
                'metadata',
            ])
            ->withTimestamps();
    }

    /**
     * Relation : Une commande a plusieurs items
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Récupérer les items d'un créateur spécifique dans cette commande
     * 
     * @param Creator|int $creator
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCreatorItems($creator)
    {
        $brandSlug = $creator instanceof Creator ? $creator->brand_slug : Creator::find($creator)?->brand_slug;
        
        if (!$brandSlug) {
            return collect();
        }

        return $this->items()->where('brand_slug', $brandSlug)->get();
    }

    /**
     * Calculer le total pour un créateur spécifique
     * 
     * @param Creator|int $creator
     * @return float
     */
    public function getCreatorTotal($creator): float
    {
        $brandSlug = $creator instanceof Creator ? $creator->brand_slug : Creator::find($creator)?->brand_slug;
        
        if (!$brandSlug) {
            return 0;
        }

        return $this->items()->where('brand_slug', $brandSlug)->sum('total');
    }

    /**
     * Récupérer tous les créateurs concernés par cette commande
     * Basé sur les brand_slug des produits
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getInvolvedCreators()
    {
        $brandSlugs = $this->items()->pluck('brand_slug')->unique()->filter();
        
        return Creator::whereIn('brand_slug', $brandSlugs)
            ->where('status', 'active')
            ->get();
    }

    /**
     * Vérifier si la commande concerne un créateur spécifique
     * 
     * @param Creator|int $creator
     * @return bool
     */
    public function involvesCreator($creator): bool
    {
        $brandSlug = $creator instanceof Creator ? $creator->brand_slug : Creator::find($creator)?->brand_slug;
        
        if (!$brandSlug) {
            return false;
        }

        return $this->items()->where('brand_slug', $brandSlug)->exists();
    }

    // Scopes existants
    public function scopeForCreator($query, $creatorId)
    {
        return $query->where('creator_id', $creatorId);
    }

    public function scopePendingLogistics($query)
    {
        return $query->where('logistics_status', 'pending');
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('order_date', '>=', now()->subDays($days));
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Nouveau scope : Filtrer par période
     */
    public function scopeInPeriod($query, $startDate, $endDate = null)
    {
        $query->where('order_date', '>=', $startDate);
        
        if ($endDate) {
            $query->where('order_date', '<=', $endDate);
        }
        
        return $query;
    }

    /**
     * Nouveau scope : Commandes avec un créateur spécifique via la table pivot
     */
    public function scopeWithCreator($query, $creatorId)
    {
        return $query->whereHas('creators', function ($q) use ($creatorId) {
            $q->where('creators.id', $creatorId);
        });
    }

    /**
     * Nouveau scope : Commandes avec une marque spécifique
     */
    public function scopeWithBrand($query, $brandSlug)
    {
        return $query->whereHas('items', function ($q) use ($brandSlug) {
            $q->where('brand_slug', $brandSlug);
        });
    }

    /**
     * Scope : Commandes prêtes pour PAPS (statut logistics et non encore envoyées à PAPS)
     */
    public function scopeReadyForPaps($query)
    {
        return $query->where('status', 'logistics')
            ->whereNull('paps_task_id');
    }

    /**
     * Scope : Commandes avec une tâche PAPS active
     */
    public function scopeWithPapsTask($query)
    {
        return $query->whereNotNull('paps_task_id');
    }

    /**
     * Scope : Commandes en transit chez PAPS
     */
    public function scopeInTransit($query)
    {
        return $query->whereIn('paps_status', ['to_pick', 'picked', 'in_transit']);
    }

    /**
     * Vérifier si la commande a été envoyée à PAPS
     */
    public function hasPapsTask(): bool
    {
        return !empty($this->paps_task_id);
    }

    /**
     * Mettre à jour le statut PAPS et l'historique
     */
    public function updatePapsStatus(string $status, ?array $historyEntry = null): void
    {
        $this->paps_status = $status;
        
        if ($historyEntry) {
            $history = $this->paps_status_history ?? [];
            $history[] = array_merge($historyEntry, [
                'date' => now()->toIso8601String(),
            ]);
            $this->paps_status_history = $history;
        }
        
        $this->save();
    }

    // Méthodes utilitaires existantes
    // public function calculateCommission(): float
    // {
    //     if (!$this->creator) {
    //         return 0;
    //     }

    //     return ($this->creator_total * $this->creator->commission_rate) / 100;
    // }

    // public function updateCommission()
    // {
    //     $this->commission_amount = $this->calculateCommission();
    //     $this->save();
    // }
}