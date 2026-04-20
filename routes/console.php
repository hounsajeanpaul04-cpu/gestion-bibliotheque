<?php
use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
// Exécute la vérification des retards chaque jour à minuit
Schedule::command('library:check-overdue')->daily();
// Exécute la vérification chaque jour à 08:00
Schedule::command('app:check-overdue-loans')->dailyAt('08:00');