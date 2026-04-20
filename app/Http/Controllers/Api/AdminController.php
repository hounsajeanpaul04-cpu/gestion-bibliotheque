<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
// --- AJOUT DES IMPORTS POUR L'EMAIL ---
use App\Mail\LoanApprovedMail;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function index() 
    {
        $activeLoansCount = Loan::whereIn('status', ['approved', 'active', 'overdue'])->count();

        $stats = [
            'total_books'       => Book::count(),
            'total_users'       => User::count(),
            'active_borrowings' => $activeLoansCount,
            'available'         => Book::sum('stock'), 
            'pending_reviews'   => Review::where('approved', false)->count(),
        ];

        $pendingLoans = Loan::where('status', 'pending')
            ->with(['user', 'book']) 
            ->latest()
            ->get();

        $recentBooks = Book::latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentBooks', 'pendingLoans'));
    }

    // Changé 'approve' en 'approveLoan' pour stopper l'erreur
    public function approveLoan(Loan $loan)
    {
        // 1. Mise à jour du statut dans la base de données
        $loan->update(['status' => 'approved']);
        
        // 2. Décrémentation du stock si le livre existe
        if ($loan->book) {
            $loan->book->decrement('stock');
        }

        // 3. ENVOI DE L'EMAIL DE VALIDATION 📧
        // On récupère l'email de l'utilisateur lié à cet emprunt
        if ($loan->user && $loan->user->email) {
            Mail::to($loan->user->email)->send(new LoanApprovedMail($loan));
        }

        return back()->with('success', 'L\'emprunt a été validé et l\'utilisateur a été notifié par email !');
    }

    public function rejectLoan(Loan $loan)
    {
        $loan->update(['status' => 'rejected']);
        
        return back()->with('success', 'L\'emprunt a été refusé.');
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request) 
    {
        $name     = $request->input('name');
        $email    = $request->input('email');
        $password = $request->input('password'); 
        $role     = $request->input('role');

        User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => $password, 
            'role'     => $role,
            'is_admin' => ($role === 'admin'), 
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Compte créé avec succès !');
    }
}