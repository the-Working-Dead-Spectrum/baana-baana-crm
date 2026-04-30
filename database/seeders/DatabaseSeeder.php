<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer un utilisateur admin
        User::factory()->admin()->create([
            'name' => 'Administrateur',
            'email' => 'admin@example.com',
        ]);

        // Créer un utilisateur creator
        User::factory()->creator()->create([
            'name' => 'Créateur Test',
            'email' => 'creator@example.com',
        ]);

        // Créer un utilisateur logistic
        User::factory()->logistic()->create([
            'name' => 'Logisticien Test',
            'email' => 'logistic@example.com',
        ]);

        // Créer l'utilisateur test
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'admin', 
        ]);

        // Créer quelques utilisateurs aléatoires
        User::factory(5)->create();
    }
}