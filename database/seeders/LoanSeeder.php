<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Vérification de sécurité
        if (User::count() == 0 || Book::count() == 0) {
            $this->command->error("Erreur : Les tables Users ou Books sont vides !");
            return;
        }

        // 2. Création des 100 emprunts avec mise à jour du stock
        Loan::factory()
            ->count(100)
            ->create()
            ->each(function ($loan) {
                // Si l'emprunt n'est pas encore rendu (en cours ou en retard)
                if ($loan->status === 'active' || $loan->status === 'overdue') {
                    // On décrémente le stock disponible du livre associé
                    // Note : Assurez-vous que la colonne s'appelle bien 'stock' ou 'available_copies'
                    $loan->book->decrement('stock'); 
                }
            });

        $this->command->info("LoanSeeder : 100 emprunts créés et stocks mis à jour.");
    }
}