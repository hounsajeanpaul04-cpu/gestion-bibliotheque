<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        // On récupère tous les IDs de genres existants
        $genreIds = Genre::pluck('id');

        // Sécurité : on vérifie que la table genres n'est pas vide
        if ($genreIds->isEmpty()) {
            $this->command->error("Aucun genre trouvé ! Lancez le GenreSeeder d'abord.");
            return;
        }

        // 1. Tes livres manuels (Données fixes pour tes démos)
        Book::create([
            'title'    => 'Le Petit Prince',
            'author'   => 'Antoine de Saint-Exupéry',
            'isbn'     => '9782070612758',
            'slug'     => Str::slug('Le Petit Prince') . '-' . Str::random(5),
            'stock'    => 0, 
            'genre_id' => $genreIds->random(),
        ]);

        Book::create([
            'title'    => '1984',
            'author'   => 'George Orwell',
            'isbn'     => '9782070368228',
            'slug'     => Str::slug('1984') . '-' . Str::random(5),
            'stock'    => 3,
            'genre_id' => $genreIds->random(),
        ]);

        Book::create([
            'title'    => 'Les Misérables',
            'author'   => 'Victor Hugo',
            'isbn'     => '9782253005541',
            'slug'     => Str::slug('Les Misérables') . '-' . Str::random(5),
            'stock'    => 4,
            'genre_id' => $genreIds->random(),
        ]);

        // 2. Génération automatique de 50 livres via la Factory
        // Assure-toi que ton BookFactory est bien configuré !
        Book::factory()->count(50)->create();

        $this->command->info("BookSeeder : Catalogue rempli avec succès (3 manuels + 50 aléatoires) !");
    }
}