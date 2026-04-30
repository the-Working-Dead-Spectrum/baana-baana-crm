<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'wp_product_id',
        'name',
        'sku',
        'brand_slug',
        'price',
        'stock_quantity',
        'status',
        'image_url',
        'total_sales',
        'total_orders',
        'last_synced_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'last_synced_at' => 'datetime',
    ];
    
    // Scopes
    public function scopeByBrand($query, $brandSlug)
    {
        return $query->where('brand_slug', $brandSlug);
    }
    
    public function scopeLowStock($query, $threshold = 10)
    {
        return $query->where('stock_quantity', '<', $threshold)
                    ->where('stock_quantity', '>', 0);
    }
    
    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', '<=', 0);
    }
    
    public function scopeBestSellers($query, $limit = 10)
    {
        return $query->orderBy('total_sales', 'desc')->limit($limit);
    }
}