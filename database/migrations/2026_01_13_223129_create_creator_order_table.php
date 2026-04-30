<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creator_order', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('creator_id')
                  ->constrained()
                  ->onDelete('cascade');
            
            $table->foreignId('order_id')
                  ->constrained()
                  ->onDelete('cascade');
            
            // Montant spécifique à ce créateur dans cette commande
            $table->decimal('creator_total', 15, 2)->default(0);
            
            // Metadata pour stocker les détails (produits du créateur, etc.)
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Contrainte unique
            $table->unique(['creator_id', 'order_id']);
            
            // Index
            $table->index('creator_id');
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creator_order');
    }
};