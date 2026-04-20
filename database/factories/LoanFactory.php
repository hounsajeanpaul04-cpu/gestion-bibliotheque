<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition(): array
    {
        // --- LOGIQUE DES DATES (Gardons le réalisme) ---
        $borrowedAt = fake()->dateTimeBetween('-2 months', 'now');
        $dueDate = (clone $borrowedAt)->modify('+14 days');
        
        $isReturned = fake()->boolean(70); 
        $returnedAt = $isReturned ? fake()->dateTimeBetween($borrowedAt, 'now') : null;

        // --- ATTRIBUTION (Ta sécurité anti-crash) ---
        return [
            // On cherche un membre, sinon on crée un user
            'user_id' => User::where('role', 'member')->inRandomOrder()->first()?->id ?? User::factory(),
            
            // On cherche un livre, sinon on en crée un
            'book_id' => Book::inRandomOrder()->first()?->id ?? Book::factory(),
            
            'borrowed_at' => $borrowedAt,
            'due_date' => $dueDate,
            'returned_at' => $returnedAt,
            
            // Le statut se calcule tout seul en fonction des dates générées
            'status' => $returnedAt ? 'returned' : ($dueDate < now() ? 'overdue' : 'active'),
            'penalty' => 0, 
        ];
    }
}