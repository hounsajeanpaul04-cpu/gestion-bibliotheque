<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    public function index() {
        $books = Book::with('genre')->get();
        return view('admin.books.index', compact('books'));
    }

    public function importView()
    {
        return view('admin.books.import');
    }

    public function downloadTemplate()
    {
        $filename = "modele_import_livres.csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            fputcsv($file, ['title', 'author', 'isbn', 'genre_id', 'stock'], ';');
            fputcsv($file, ['Le Petit Prince', 'Saint-Exupéry', '9782070408504', '1', '10'], ';');
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * IMPORTATION CSV : VALIDATION STRICTE ET AUCUN DOUBLON
     */
    public function importProcess(Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv,txt|max:2048']);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        
        $header = fgetcsv($handle, 1000, ";"); 
        if ($header) {
            $header[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header[0]);
        }

        if (!$header || strtolower($header[0]) !== 'title') {
            fclose($handle);
            return redirect()->back()->with('error', "Format CSV invalide.");
        }

        $report = ['added' => 0, 'ignored' => 0, 'errors' => []];
        $row = 1; 

        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $row++;
            
            // 1. VÉRIFICATION : BIEN REMPLIR LES CHAMPS
            // On vérifie que les 3 colonnes principales ne sont pas vides
            if (empty($data[0]) || empty($data[1]) || empty($data[2])) {
                $report['errors'][] = "Ligne $row : Données manquantes. Veuillez bien remplir les champs Titre, Auteur et ISBN.";
                continue;
            }

            // 2. Nettoyage et validation ISBN
            $isbnRaw = str_replace([' ', ','], '', $data[2]);
            $isbn = number_format((float)$isbnRaw, 0, '', '');

            if (strlen($isbn) < 10) {
                $report['errors'][] = "Ligne $row : ISBN invalide ($isbn). Veuillez saisir un code à 10 ou 13 chiffres.";
                continue;
            }

            // 3. Vérification si le stock est numérique
            if (isset($data[4]) && !is_numeric($data[4])) {
                $report['errors'][] = "Ligne $row : Le champ stock doit être un nombre entier.";
                continue;
            }

            // 4. VÉRIFICATION D'EXISTENCE (Pas de doublon, pas de mise à jour)
            $alreadyExists = Book::where('isbn', $isbn)
                ->orWhere(function($query) use ($data) {
                    $query->where('title', $data[0])
                          ->where('author', $data[1]);
                })->exists();

            if ($alreadyExists) {
                $report['ignored']++;
                continue; 
            }

            // 5. CRÉATION DU LIVRE
            Book::create([
                'title'    => $data[0],
                'author'   => $data[1],
                'isbn'     => $isbn,
                'genre_id' => $data[3] ?? 1,
                'stock'    => $data[4] ?? 0,
                'slug'     => Str::slug($data[0]) . '-' . Str::random(5),
            ]);
            $report['added']++;
        }
        
        fclose($handle);

        $msg = "Importation terminée : {$report['added']} nouveaux livres ajoutés. {$report['ignored']} déjà présents.";
        
        return redirect()->route('admin.dashboard')
            ->with('success', $msg)
            ->with('skipped', $report['errors']); 
    }

    // ... (Reste des méthodes : search, show, edit, update, destroy, borrow)
    public function search(Request $request)
    {
        $query = $request->query('q');
        $books = Book::when($query, function ($q) use ($query) {
            return $q->where('title', 'ILIKE', "%{$query}%")
                     ->orWhere('author', 'ILIKE', "%{$query}%");
        })->with('genre')->get();
        return view('books.index', compact('books'));
    }

    public function show($id)
    {
        $book = Book::findOrFail($id);
        return view('admin.books.show', compact('book'));
    }

    public function edit($id)
    {
        $book = Book::findOrFail($id);
        $genres = Genre::all(); 
        return view('admin.books.edit', compact('book', 'genres'));
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required', 
            'author' => 'required',
            'isbn' => 'required|unique:books,isbn,' . $book->id,
            'stock' => 'required|integer', 
            'genre_id' => 'required',
        ]);
        $validated['slug'] = Str::slug($request->title) . '-' . Str::random(5);
        $book->update($validated);
        return redirect()->route('admin.dashboard')->with('success', 'Livre mis à jour.');
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Livre supprimé.');
    }

    public function borrow($id)
    {
        $book = Book::findOrFail($id);
        if ($book->stock <= 0) return redirect()->back()->with('error', 'Plus de stock.');

        Loan::create([
            'user_id' => Auth::id(), 
            'book_id' => $book->id,
            'status' => 'en attente',
            'loan_date' => now(),
        ]);

        $book->decrement('stock');
        return redirect()->back()->with('success', 'Demande envoyée !');
    }
}