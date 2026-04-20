<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendLoanConfirmationJob;

class LoanController extends Controller
{
    /**
     * Enregistrer un nouvel emprunt (POST /emprunter/{bookId})
     */
    public function store(Request $request, $bookId)
    {
        $user = Auth::user();

        // 1. VÉRIFICATION DU RÔLE : Un admin ne fait pas de demande d'emprunt 🛑
        if ($user->role === 'admin' || $user->role === 'librarian') {
            return redirect()->back()->with('error', 'Les administrateurs ne peuvent pas effectuer de demandes d\'emprunt.');
        }

        return DB::transaction(function () use ($bookId, $user) {
            $book = Book::findOrFail($bookId);

            // 2. BLOCAGE SI L'UTILISATEUR A UN LIVRE EN RETARD 🛑
            $hasOverdue = Loan::where('user_id', $user->id)
                ->whereIn('status', ['approved', 'active', 'overdue'])
                ->where('due_date', '<', now())
                ->exists();

            if ($hasOverdue) {
                return redirect()->route('accueil')
                    ->with('error', 'Emprunt bloqué : vous avez un livre en retard. Merci de le rendre d\'abord.');
            }

            // 3. VÉRIFICATION DU STOCK
            if ($book->stock <= 0) {
                return redirect()->route('accueil')->with('error', 'Désolé, ce livre est épuisé.');
            }

            // 4. VÉRIFICATION DE LA LIMITE (Max 3)
            $activeCount = Loan::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'approved', 'active', 'overdue'])
                ->count();

            if ($activeCount >= 3) {
                return redirect()->route('accueil')->with('error', 'Vous avez déjà 3 demandes ou emprunts en cours.');
            }

            // 5. CRÉATION DE L'EMPRUNT
            $loan = Loan::create([
                'user_id'     => $user->id,
                'book_id'     => $book->id,
                'borrowed_at' => now(),
                'due_date'    => now()->addDays(14),
                'status'      => 'pending'
            ]);

            // Envoi de l'email via la file d'attente
            SendLoanConfirmationJob::dispatch($loan);

            return redirect()->route('accueil')->with('success', 'Votre demande est en attente de validation !');
        });
    }

    /**
     * Marquer un livre comme rendu (PATCH /admin/loans/{loan}/return)
     */
    public function markAsReturned(Loan $loan)
    {
        if ($loan->status === 'returned') {
            return back()->with('error', "Ce livre a déjà été rendu.");
        }

        return DB::transaction(function () use ($loan) {
            $loan->update([
                'returned_at' => now(),
                'penalty'     => $loan->calculatePenalty(),
                'status'      => 'returned'
            ]);
            
            // On rend le livre disponible
            if ($loan->book) {
                $loan->book->increment('stock');
            }

            return back()->with('success', "Retour enregistré. Pénalité : {$loan->penalty} €");
        });
    }

    /**
     * Historique : Global pour l'Admin, Personnel pour l'User
     */
    public function history()
    {
        $user = Auth::user();

        // Si Admin : voit tout l'historique de la bibliothèque
        if ($user->role === 'admin' || $user->role === 'librarian') {
            $history = Loan::with(['user', 'book'])
                         ->latest()
                         ->get();
        } 
        // Si User : ne voit que ses propres livres
        else {
            $history = Loan::where('user_id', $user->id)
                         ->with('book')
                         ->latest()
                         ->get();
        }

        // On utilise ta vue existante
        return view('admin.users.history', compact('history'));
    }

    /**
     * Version API (Mobile / JS)
     */
    public function apiReturnBook(Loan $loan)
    {
        if ($loan->status === 'returned') {
            return response()->json(['message' => 'Déjà rendu.'], 400);
        }

        $loan->update([
            'returned_at' => now(),
            'penalty'     => $loan->calculatePenalty(),
            'status'      => 'returned'
        ]);

        $loan->book->increment('stock');

        return response()->json(['message' => 'Retour enregistré avec succès.']);
    }
}