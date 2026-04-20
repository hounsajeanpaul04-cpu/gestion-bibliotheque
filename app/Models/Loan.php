<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'borrowed_at',
        'due_date',
        'returned_at',
        'status',
        'penalty'
    ];

    // Important : On dit à Laravel que ce sont des dates pour utiliser Carbon
    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_date'    => 'datetime',
        'returned_at' => 'datetime',
        'penalty'     => 'decimal:2',
    ];

    // --- RELATIONS ---

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // --- SCOPES (Pour les requêtes personnalisées) ---

    /**
     * Récupère uniquement les emprunts en retard (non rendus et date dépassée)
     */
    public function scopeOverdue($query)
    {
        return $query->whereNull('returned_at')
                     ->where('due_date', '<', now());
    }

    /**
     * Récupère les emprunts en cours (non rendus)
     */
    public function scopeActive($query)
    {
        return $query->whereNull('returned_at');
    }

    // --- LOGIQUE MÉTIER (Pénalités) ---

    /**
     * Calcule la pénalité théorique actuelle (0.50€ par jour de retard)
     */
   public function calculatePenalty(): float
    {
    // Si pas de date ou si on n'a pas encore dépassé le jour de l'échéance -> 0
    if (!$this->due_date || now()->startOfDay() <= $this->due_date->startOfDay()) {
        return 0;
    }

    // Si déjà rendu, on retourne la pénalité qui a été figée en base
    if ($this->status === 'returned') {
        return (float) $this->penalty;
    }

    // Calcul du nombre de jours de retard (comparaison des dates sans les heures)
    $daysOverdue = now()->startOfDay()->diffInDays($this->due_date->startOfDay());

    // Ton tarif en FCFA (par exemple 500 FCFA par jour)
    $ratePerDay = 500; 

    return $daysOverdue * $ratePerDay;
  }

    // --- ACCESSORS (Pour l'affichage dans Blade) ---

    /**
     * Permet d'appeler $loan->is_overdue dans vos vues
     */
    protected function isOverdue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->returned_at === null && now() > $this->due_date,
        );
    }

    /**
     * Affiche la pénalité formatée : $loan->formatted_penalty
     */
    protected function formattedPenalty(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->penalty > 0 ? $this->penalty : $this->calculatePenalty(), 2) . ' fcfa',
        );
    }

   
    
}