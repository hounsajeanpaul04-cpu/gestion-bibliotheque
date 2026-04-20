<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\Api\LoanController;
use App\Http\Controllers\Api\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes - Pour Insomnia / Postman (Réponses JSON uniquement)
|--------------------------------------------------------------------------
*/

// 1. Authentification par Token (Sanctum)
Route::post('/tokens/create', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Identifiants incorrects'], 401);
    }

    return response()->json([
        'token' => $user->createToken('api-token')->plainTextToken
    ]);
});

// 2. Routes protégées par Token (Bearer Token)
Route::middleware('auth:sanctum')->group(function () {
    
    // Infos brutes du profil
    Route::get('/profile/me', [ProfileController::class, 'me']);
    
    // Actions de données (API)
    Route::post('/books/{id}/favorite', [ProfileController::class, 'toggleFavorite']);
    Route::post('/books/{id}/review', [ProfileController::class, 'addReview']);
    Route::post('/books/{id}/reserve', [LoanController::class, 'reserve']);

    // Gestion des emprunts via API
    Route::get('/borrowings', [LoanController::class, 'index']);
    Route::post('/borrowings', [LoanController::class, 'store']);
    Route::post('/borrowings/{id}/return', [LoanController::class, 'returnBook']);
});

// 3. Tests techniques
Route::post('/test-email', function (Request $request) {
    // ... ton code d'envoi d'email actuel ...
});