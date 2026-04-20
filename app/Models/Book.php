<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; // 1. Importe le Trait
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory; // 2. Utilise le Trait à l'intérieur de la classe
    protected $fillable = [
    'title',
    'slug',
    'author',
    'isbn',
    'genre_id',
    'description',
    'cover',
    'stock'
];

// ✅ Relation avec Genre
    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }
}
