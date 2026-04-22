<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name'     => 'Administrateur',
            'email'    => 'admin@bibliotheque.com',
            'password' => bcrypt('MotDePasse123'),
            'role'     => 'admin'
        ]);
    }
}