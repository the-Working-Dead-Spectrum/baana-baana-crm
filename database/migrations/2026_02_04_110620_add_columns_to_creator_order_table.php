<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('creator_order', function (Blueprint $table) {
            // Vérifier si les colonnes existent déjà
            if (!Schema::hasColumn('creator_order', 'creator_total')) {
                $table->decimal('creator_total', 15, 2)->default(0)->after('order_id');
            }
            
            if (!Schema::hasColumn('creator_order', 'product_count')) {
                $table->integer('product_count')->default(0)->after('creator_total');
            }
            
            if (!Schema::hasColumn('creator_order', 'total_quantity')) {
                $table->integer('total_quantity')->default(0)->after('product_count');
            }
            
            if (!Schema::hasColumn('creator_order', 'created_at')) {
                $table->timestamps();
            }
            
            // Ajouter des index pour les performances
            if (!Schema::hasIndex('creator_order', 'creator_order_creator_total_index')) {
                $table->index('creator_total');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('creator_order', function (Blueprint $table) {
            $table->dropColumn([
                'creator_total',
                'product_count',
                'total_quantity',
            ]);
            
            $table->dropTimestamps();
            
            $table->dropIndex('creator_order_creator_total_index');
        });
    }
};