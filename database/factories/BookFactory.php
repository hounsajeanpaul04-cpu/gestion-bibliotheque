<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Support\Str;
class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
{
    $title = fake()->sentence(3); // Génère un titre aléatoire
    return [
        'title' =>$title ,
        'author' => fake()->name(),
        'isbn' => fake()->isbn13(),
        'stock' => fake()->numberBetween(1, 10),
       // 'available_copies' => 1,//
       // AJOUTE CETTE LIGNE :
            'slug'     => Str::slug($title) . '-' . Str::random(5),
        'genre_id' => Genre::all()->random()->id,
    ];
}
}