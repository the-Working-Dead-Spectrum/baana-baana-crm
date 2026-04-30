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
        Schema::table('orders', function (Blueprint $table) {
            // Champs pour l'intégration PAPS
            $table->string('paps_task_id')->nullable()->index()->after('tracking_number');
            $table->string('paps_order_uid')->nullable()->index()->after('paps_task_id');
            $table->enum('paps_status', [
                'pending',
                'to_pick',
                'picked',
                'in_transit',
                'delivered',
                'failed',
                'cancelled'
            ])->default('pending')->after('paps_order_uid');
            
            // Détails de la livraison
            $table->json('paps_delivery_details')->nullable()->after('paps_status');
            $table->timestamp('paps_pickup_scheduled_at')->nullable()->after('paps_delivery_details');
            $table->timestamp('paps_picked_at')->nullable()->after('paps_pickup_scheduled_at');
            $table->timestamp('paps_delivered_at')->nullable()->after('paps_picked_at');
            
            // Frais de livraison
            $table->decimal('paps_delivery_fee', 15, 2)->nullable()->after('paps_delivered_at');
            
            // Historique des statuts PAPS
            $table->json('paps_status_history')->nullable()->after('paps_delivery_fee');
            
            // Métadonnées additionnelles
            $table->json('paps_metadata')->nullable()->after('paps_status_history');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};
