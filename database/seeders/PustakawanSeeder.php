<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class PustakawanSeeder extends Seeder
{
    public function run(): void
    {
        // Password di-hash otomatis oleh cast 'hashed' pada model User (Laravel 11)
        User::updateOrCreate(
            ['email' => 'pustakawan@del.ac.id'],
            ['name' => 'Pustakawan Del', 'password' => 'password123']
        );
    }
}