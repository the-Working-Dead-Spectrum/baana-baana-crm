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
            // Ajouter un suivi de complétion par créateur
            if (!Schema::hasColumn('creator_order', 'is_completed')) {
                $table->boolean('is_completed')->default(false)->after('metadata')
                      ->comment('Indique si ce créateur a marqué sa partie comme terminée');
            }

            if (!Schema::hasColumn('creator_order', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('is_completed')
                      ->comment('Date et heure où le créateur a terminé sa partie');
            }

            // Index pour les performances
            if (!Schema::hasIndex('creator_order', 'creator_order_is_completed_index')) {
                $table->index('is_completed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('creator_order', function (Blueprint $table) {
            $table->dropIndex('creator_order_is_completed_index');
            $table->dropColumn(['is_completed', 'completed_at']);
        });
    }
};