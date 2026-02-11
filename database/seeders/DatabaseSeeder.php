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
        // Utilisateur de test (optionnel)
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Données Coupe du Monde 2026
        $this->call([
            PhaseSeeder::class,
            PouleSeeder::class,
            EquipeSeeder::class,
            MatchPouleSeeder::class,
        ]);
    }
}
