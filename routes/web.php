<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ProfileController; 
use App\Http\Controllers\Api\BookController; 
use App\Http\Controllers\Api\LoanController; 
use Illuminate\Support\Facades\Route;
use App\Models\Loan;
use App\Mail\OverdueLoanReminder;
// Force la déconnexion en GET pour éviter ton erreur 405
use App\Http\Controllers\Auth\AuthenticatedSessionController;
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');
// --- 1. ACCUEIL ET CATALOGUE PUBLIC ---
Route::get('/accueil', [BookController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('accueil');

Route::get('/', function () {
    return redirect()->route('accueil');
});

Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/search', [BookController::class, 'search'])->name('books.search');

Route::get('/books/{id}', [BookController::class, 'show'])
    ->name('books.show')
    ->where('id', '[0-9]+'); 

// --- 2. ROUTES PROTÉGÉES ---
Route::middleware('auth')->group(function () {
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/admin', function () {
    return redirect()->route('admin.dashboard');
    });
    // --- 3. ACTIONS UTILISATEUR ---
    Route::post('/emprunter/{bookId}', [LoanController::class, 'store'])->name('loans.store');
    Route::get('/user/mon-historique', [LoanController::class, 'history'])->name('loans.history');

    // --- 4. ZONE ADMINISTRATIVE ---
    Route::middleware(['role:admin,librarian'])->prefix('admin')->group(function () {
        
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');

        // Gestion des Livres
        Route::prefix('books')->group(function () {
            // --- LA ROUTE AJOUTÉE ICI ---
            Route::get('/download-template', [BookController::class, 'downloadTemplate'])
                ->name('books.download-template');

            Route::get('/import', [BookController::class, 'importView'])->name('books.import.view');
            Route::post('/import', [BookController::class, 'importProcess'])->name('books.import.process');
            
            Route::get('/create', [BookController::class, 'create'])->name('books.create');
            Route::post('/', [BookController::class, 'store'])->name('books.store');
            Route::get('/{id}/edit', [BookController::class, 'edit'])->name('books.edit')->where('id', '[0-9]+');
            Route::put('/{id}', [BookController::class, 'update'])->name('books.update')->where('id', '[0-9]+');
            Route::delete('/{id}', [BookController::class, 'destroy'])->name('books.destroy')->where('id', '[0-9]+');
        });

        Route::post('/admin/loans/{loan}/approve', [AdminController::class, 'approveLoan'])->name('admin.loans.approve');
        Route::post('/admin/loans/{loan}/reject', [AdminController::class, 'rejectLoan'])->name('admin.loans.reject');

        Route::patch('/loans/{loan}/return', [LoanController::class, 'markAsReturned'])->name('loans.return');
    });
});

// --- 5. TESTS ---
Route::get('/test-mail', function () {
    $loan = Loan::with(['user', 'book'])->first();
    if (!$loan) return "Erreur : Aucun prêt trouvé.";
    return new OverdueLoanReminder($loan);
});

require __DIR__.'/auth.php';