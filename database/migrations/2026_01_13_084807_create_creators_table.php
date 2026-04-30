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
        Schema::create('creators', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null')
                ->comment('Lien vers le compte utilisateur Laravel');

            $table->unsignedBigInteger('wp_creator_id')
                ->unique()
                ->comment('ID du créateur dans WordPress');

            // Données de base (synchronisées depuis WordPress)
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('brand_slug', 100)->index();

            // Extensions CRM (gérées uniquement dans Laravel)
            $table->enum('status', ['active', 'inactive', 'suspended'])
                ->default('active')
                ->index();

            // Cache stats (mis à jour par les jobs de sync)
            $table->integer('total_orders')->default(0);
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->timestamp('last_order_date')->nullable();

            // Tracking synchronisation
            $table->timestamp('last_synced_at')->nullable();

            $table->timestamps();

            // Index composites pour performance
            $table->index(['brand_slug', 'status']);
            $table->index(['status', 'total_sales']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creators');
    }
};
