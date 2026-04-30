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
        Schema::table('users', function (Blueprint $table) {
            // Ajouter le champ role après l'email
            $table->enum('role', ['admin', 'creator', 'logistic'])
                  ->default('creator')
                  ->after('email');
            
            // Ajouter le lien vers WordPress (NULL si admin ou logistic)
            $table->unsignedBigInteger('wp_creator_id')
                  ->nullable()
                  ->unique()
                  ->after('role')
                  ->comment('ID du créateur dans WordPress');
            
            // Rendre le password nullable (créateurs créés depuis WP)
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'wp_creator_id']);
            
            // Remettre password NOT NULL
            $table->string('password')->nullable(false)->change();
        });
    }
};