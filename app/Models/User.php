<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',   // Tu utilises 'role' au lieu de 'is_admin'
        'avatar',
        'is_admin' // AJOUTE CECI si tu as créé la colonne is_admin précédemment
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // <--- C'est lui qui transforme le texte en Bcrypt
        'is_admin' => 'boolean', // Force la conversion en vrai/faux
    ];

    // --- ACCESSEUR MAGIQUE (Optionnel mais recommandé) ---
    
    /**
     * Cette fonction permet de vérifier si l'user est admin 
     * même si tu utilises la colonne 'role' au lieu de 'is_admin'
     */
    public function getIsAdminAttribute()
    {
        // Si tu utilises une colonne 'role', on dit que 'admin' est le rôle suprême
        return $this->role === 'admin' || $this->attributes['is_admin'] == true;
    }

    // --- RELATIONS ---

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Book::class, 'favorites')->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}