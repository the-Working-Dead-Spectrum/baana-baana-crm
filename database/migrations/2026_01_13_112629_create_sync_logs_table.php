<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('sync_type', ['creators', 'orders', 'products', 'all']);
            $table->enum('status', ['pending', 'success', 'failed', 'partial']);
            
            // Statistiques
            $table->integer('total_records')->default(0);
            $table->integer('created_records')->default(0);
            $table->integer('updated_records')->default(0);
            $table->integer('failed_records')->default(0);
            
            // Erreurs
            $table->text('error_message')->nullable();
            $table->json('error_details')->nullable();
            
            // Performance
            $table->integer('duration_ms')->default(0);
            
            // Métadonnées
            $table->json('metadata')->nullable();
            
            // Timestamps
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index(['sync_type', 'status']);
            $table->index(['started_at', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};