<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Http\RedirectResponse; // Ajouté pour le type de retour
use Illuminate\View\View; // Ajouté pour le type de retour

class ProfileController extends Controller
{
    /**
     * Affiche le formulaire de modification du profil (Pour le Web).
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Récupère le profil du membre connecté avec son historique (Pour l'API).
     */
    public function me()
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        $user = User::with(['loans.book', 'favorites', 'reviews.book'])
                    ->findOrFail($userId);

        return response()->json([
            'user' => $user,
            'stats' => [
                'total_emprunts'    => $user->loans->count(),
                'livres_rendus'     => $user->loans->where('status', 'rendu')->count(),
                'livres_en_attente' => $user->loans->where('status', 'en_cours')->count(),
            ]
        ]);
    }

    /**
     * Ajoute ou retire un livre des favoris
     */
    public function toggleFavorite($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $book = Book::findOrFail($id);

        $user->favorites()->toggle($book->id);

        return response()->json(['message' => 'Liste de favoris mise à jour']);
    }

    /**
     * Ajoute un avis sur un livre
     */
    public function addReview(Request $request, $id)
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $review = $user->reviews()->create([
            'book_id' => $id,
            'rating'  => $request->rating,
            'comment' => $request->comment,
            'approved'=> false 
        ]);

        return response()->json([
            'message' => 'Avis ajouté avec succès',
            'review'  => $review
        ]);
    }
}