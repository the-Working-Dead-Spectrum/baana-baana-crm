<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('wp_product_id')->unique()->index();
            
            // Informations produit
            $table->string('name');
            $table->string('sku')->nullable()->index();
            $table->string('brand_slug')->nullable()->index();
            
            // Prix et stock
            $table->decimal('price', 15, 2)->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->string('status')->default('publish')->index();
            $table->string('image_url')->nullable();
            
            // Stats
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->integer('total_orders')->default(0);
            
            // Sync
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            
            // Index
            $table->index(['brand_slug', 'total_sales']);
            $table->index(['status', 'stock_quantity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};