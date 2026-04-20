<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = ['Roman', 'Science-Fiction', 'Biographie', 'Histoire', 'Bande Dessinée', 'Informatique'];

    foreach ($genres as $name) {
        Genre::create([
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name)
        ]);
    }
    }
}
