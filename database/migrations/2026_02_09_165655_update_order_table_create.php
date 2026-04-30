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
        // Si la table existe déjà, on ajoute juste les colonnes manquantes
        if (Schema::hasTable('creator_order')) {
            Schema::table('creator_order', function (Blueprint $table) {
                if (!Schema::hasColumn('creator_order', 'product_count')) {
                    $table->integer('product_count')->default(0)->after('creator_total');
                }
                
                if (!Schema::hasColumn('creator_order', 'total_quantity')) {
                    $table->integer('total_quantity')->default(0)->after('product_count');
                }
                
                // Modifier metadata pour être nullable si ce n'est pas le cas
                if (Schema::hasColumn('creator_order', 'metadata')) {
                    $table->json('metadata')->nullable()->change();
                }
            });
        } else {
            // Sinon, on crée la table complète
            Schema::create('creator_order', function (Blueprint $table) {
                $table->id();
                
                // Relations
                $table->foreignId('creator_id')->constrained('creators')->onDelete('cascade');
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                
                // Données calculées pour le créateur
                $table->decimal('creator_total', 15, 2)->default(0);
                $table->integer('product_count')->default(0);
                $table->integer('total_quantity')->default(0);
                
                // Métadonnées JSON pour stocker des infos supplémentaires
                $table->json('metadata')->nullable();
                
                $table->timestamps();
                
                // Index
                $table->unique(['creator_id', 'order_id']);
                $table->index('creator_total');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('creator_order')) {
            Schema::table('creator_order', function (Blueprint $table) {
                if (Schema::hasColumn('creator_order', 'product_count')) {
                    $table->dropColumn('product_count');
                }
                
                if (Schema::hasColumn('creator_order', 'total_quantity')) {
                    $table->dropColumn('total_quantity');
                }
            });
        }
    }
};