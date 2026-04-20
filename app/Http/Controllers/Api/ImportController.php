<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    public function importCSV(Request $request)
    {
        // 1. Validation du fichier
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        
        // Sauter la première ligne (les entêtes)
        fgetcsv($handle);

        $count = 0;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            
            if (empty($data[0])) continue;

            $slug = Str::slug($data[0]);

            // --- LA CORRECTION EST ICI ---
            Book::updateOrCreate(
                ['slug' => $slug], // On cherche par le slug pour éviter le doublon
                [
                    'title'        => $data[0],
                    'author'       => $data[1],
                    'isbn'         => $data[2],
                    'genre_id'     => $data[3] ?? 1, // Sécurité si la colonne est vide
                    'is_available' => true,
                ]
            );
            // -----------------------------
            
            $count++;
        }

        fclose($handle);

        return response()->json([
            'status'  => 'success',
            'message' => "$count livres ont été traités (ajoutés ou mis à jour) !"
        ], 201);
    }
}