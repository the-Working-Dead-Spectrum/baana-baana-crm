<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            
            // Identifiants produits
            $table->bigInteger('wp_product_id')->index();
            $table->bigInteger('wp_variation_id')->nullable();
            
            // Informations produit
            $table->string('product_name');
            $table->string('sku')->nullable()->index();
            $table->string('brand_slug')->nullable()->index();
            
            // Quantité et prix
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            
            // Métadonnées
            $table->json('variation_data')->nullable(); // Taille, couleur, etc.
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index(['brand_slug', 'order_id']);
            $table->index(['wp_product_id', 'order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};