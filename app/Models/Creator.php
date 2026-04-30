<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Creator extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'wp_creator_id',
        'name',
        'email',
        'phone',
        'address',
        'brand_slug',
        'status',
        'total_orders',
        'total_sales',
        'last_order_date',
        'last_synced_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_sales' => 'decimal:2',
            'last_order_date' => 'datetime',
            'last_synced_at' => 'datetime',
        ];
    }

    /**
     * Relation many-to-many avec les commandes
     * Un créateur peut avoir plusieurs commandes
     * Une commande peut concerner plusieurs créateurs
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'creator_order')
            ->withPivot([
                'creator_total',
                'product_count',
                'total_quantity',
                'metadata',
            ])
            ->withTimestamps();
    }

    /**
     * Relation : Un créateur appartient à un utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les produits via brand_slug
     * (à implémenter si vous avez un modèle Product)
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'brand_slug', 'brand_slug');
    }

    /**
     * Scopes pour faciliter les requêtes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByBrand($query, $brandSlug)
    {
        return $query->where('brand_slug', $brandSlug);
    }

    /**
     * Helper : Vérifier si le créateur est actif
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Helper : Formater le taux de commission
     */
    public function getFormattedCommissionRate(): string
    {
        return $this->commission_rate . '%';
    }

    /**
     * Récupérer les items d'un créateur dans une commande spécifique
     * 
     * @param int $orderId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProductsInOrder(int $orderId)
    {
        return OrderItem::where('order_id', $orderId)
            ->where('brand_slug', $this->brand_slug)
            ->get();
    }

    /**
     * Calculer le total du créateur pour une commande
     * 
     * @param int $orderId
     * @return float
     */
    public function calculateOrderTotal(int $orderId): float
    {
        return OrderItem::where('order_id', $orderId)
            ->where('brand_slug', $this->brand_slug)
            ->sum('total');
    }

    /**
     * Compter les produits du créateur dans une commande
     * 
     * @param int $orderId
     * @return int
     */
    public function countProductsInOrder(int $orderId): int
    {
        return OrderItem::where('order_id', $orderId)
            ->where('brand_slug', $this->brand_slug)
            ->sum('quantity');
    }

    /**
     * Récupérer les commandes complétées du créateur
     */
    public function completedOrders()
    {
        return $this->orders()->where('status', 'completed');
    }

    /**
     * Récupérer les commandes en attente du créateur
     */
    public function pendingOrders()
    {
        return $this->orders()->where('status', 'pending');
    }

    /**
     * Récupérer les commandes d'une période
     */
    public function ordersBetween($startDate, $endDate)
    {
        return $this->orders()
            ->whereBetween('order_date', [$startDate, $endDate]);
    }

    /**
     * Calculer le total des ventes pour une période
     */
    public function getSalesForPeriod($startDate, $endDate): float
    {
        return $this->orders()
            ->where('status', 'completed')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->sum('creator_order.creator_total');
    }
}