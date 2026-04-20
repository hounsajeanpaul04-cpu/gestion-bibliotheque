<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // L'administrateur
    User::create([
        'name' => 'Admin Biblio',
        'email' => 'admin@test.com',
        'password' => Hash::make('password'),
    ]);

    // Des utilisateurs pour la file d'attente
    User::create([
        'name' => 'Jean Paul',
        'email' => 'jean@test.com',
        'password' => Hash::make('password'),
    ]);
    }
}
