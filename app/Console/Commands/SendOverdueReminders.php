<?php

namespace App\Console\Commands;
use App\Mail\OverdueLoanReminder;
use Illuminate\Console\Command;
use App\Models\Loan;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendOverdueReminders extends Command
{
    // Le nom de la commande à taper dans le terminal
    protected $signature = 'library:send-reminders';

    protected $description = 'Envoie un mail aux utilisateurs ayant des emprunts en retard';

    public function handle()
    {
        // 1. On récupère les emprunts en retard (date passée et non rendus)
        // On suppose que tu as une colonne 'status' ou 'returned_at'
        $overdueLoans = Loan::where('due_date', '<', Carbon::now())
            ->whereNull('returned_at') // Uniquement ceux qui n'ont pas encore rendu le livre
            ->with(['user', 'book'])   // On charge l'utilisateur et le livre pour le mail
            ->get();

        if ($overdueLoans->isEmpty()) {
            $this->info("Aucun emprunt en retard trouvé.");
            return;
        }

        $this->info("Envoi de " . $overdueLoans->count() . " rappels...");

        // 2. On boucle sur chaque emprunt pour envoyer le mail
        /** @var \App\Models\Loan $loan */
       foreach ($overdueLoans as $loan) {
    // On vérifie que l'utilisateur existe bien pour éviter une erreur sur un objet null
    if ($loan->user) {
          Mail::to($loan->user)->send(new OverdueLoanReminder($loan));
        
        // Affiche un message dans le terminal pour voir l'avancement
        $this->info("Mail envoyé à : " . $loan->user->email);
    } else {
        $this->error("Impossible d'envoyer le rappel pour l'emprunt ID {$loan->id} : Utilisateur introuvable.");
    }
}

        $this->info('Tous les rappels ont été envoyés avec succès !');
    }
}