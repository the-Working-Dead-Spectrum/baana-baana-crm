<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // 🔐 Vérifier si la colonne existe avant toute action
            if (Schema::hasColumn('orders', 'creator_id')) {

                // ⚠️ Supprimer la FK seulement si elle existe
                try {
                    $table->dropForeign(['creator_id']);
                } catch (\Throwable $e) {
                    // FK inexistante → on ignore proprement
                }

                // Supprimer la colonne
                $table->dropColumn('creator_id');
            }

            // Supprimer creator_total si présent
            if (Schema::hasColumn('orders', 'creator_total')) {
                $table->dropColumn('creator_total');
            }

            // Ajouter metadata si absente
            if (! Schema::hasColumn('orders', 'metadata')) {
                $table->json('metadata')->nullable()->after('notes');
            }

            // Ajouter wp_updated_at si absente
            if (! Schema::hasColumn('orders', 'wp_updated_at')) {
                $table->timestamp('wp_updated_at')->nullable()->after('order_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            if (! Schema::hasColumn('orders', 'creator_id')) {
                $table->foreignId('creator_id')
                    ->nullable()
                    ->constrained('creators')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('orders', 'creator_total')) {
                $table->decimal('creator_total', 15, 2)->default(0);
            }

            if (Schema::hasColumn('orders', 'metadata')) {
                $table->dropColumn('metadata');
            }

            if (Schema::hasColumn('orders', 'wp_updated_at')) {
                $table->dropColumn('wp_updated_at');
            }
        });
    }
};
