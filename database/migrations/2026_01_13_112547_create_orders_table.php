<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            // Identifiants
            $table->bigInteger('wp_order_id')->unique()->index();
            $table->string('order_number')->nullable()->index();
            
            // Lien créateur
            $table->foreignId('creator_id')->nullable()->constrained('creators')->onDelete('set null');
            
            // Dates
            $table->timestamp('order_date')->nullable()->index();
            $table->timestamp('wp_updated_at')->nullable();
            
            // Statuts
            $table->string('status')->default('pending')->index();
            $table->enum('logistics_status', ['pending', 'processing', 'shipped', 'delivered'])->default('pending')->index();
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid')->index();
            
            // Montants
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('shipping', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('creator_total', 15, 2)->default(0); // Part du créateur
            
            // Client
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('shipping_address')->nullable();
            
            // Logistique
            $table->string('tracking_number')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            
            // Paiement créateur
            $table->timestamp('payment_date')->nullable();
            $table->decimal('commission_amount', 15, 2)->nullable();
            
            // Métadonnées
            $table->json('metadata')->nullable();
            $table->text('notes')->nullable();
            
            // Sync
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            
            // Index
            $table->index(['creator_id', 'order_date']);
            $table->index(['status', 'logistics_status']);
            $table->index(['payment_status', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};