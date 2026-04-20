<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Mail\OverdueLoanReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

#[Signature('app:check-overdue-loans')]
#[Description('Vérifie les prêts en retard et envoie des notifications par email')]
class CheckOverdueLoans extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Recherche des prêts en retard...");

        // On récupère les prêts (Eager loading 'user' et 'book' pour éviter les problèmes N+1)
        $overdueLoans = Loan::overdue()->with(['user', 'book'])->get();
        $count = $overdueLoans->count();

        if ($count === 0) {
            $this->warn("Aucun prêt en retard trouvé en base de données.");
            return SymfonyCommand::SUCCESS;
        }

        $this->info("$count prêt(s) en retard trouvé(s).");

        // Barre de progression pour le suivi visuel
        $bar = $this->output->createProgressBar($count);
        $bar->start();

       /** @var \App\Models\Loan $loan */
foreach ($overdueLoans as $loan) {
    if ($loan->user && $loan->user->email) {
        try {
            Mail::to($loan->user->email)->send(new OverdueLoanReminder($loan));
        } catch (\Exception $e) {
            $this->error("Échec pour le prêt ID {$loan->id}: " . $e->getMessage());
        }
    }
    $bar->advance();
}
        $bar->finish();
        $this->newLine(2);
        $this->info("Traitement terminé !");

        return SymfonyCommand::SUCCESS;
    }
}