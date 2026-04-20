<?php

namespace Database\Seeders;

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
        // L'ordre est CRUCIAL ici pour éviter les erreurs d'ID manquants
        $this->call([
            // 1. Les Utilisateurs (pour les emprunteurs)
            UserSeeder::class,

            // 2. Les Genres (pour classer les livres)
            GenreSeeder::class,

            // 3. Les Livres (qui ont besoin d'un genre_id)
            BookSeeder::class,

            // 4. Les Emprunts (qui ont besoin d'un user_id et d'un book_id)
            LoanSeeder::class,
        ]);
    }
}