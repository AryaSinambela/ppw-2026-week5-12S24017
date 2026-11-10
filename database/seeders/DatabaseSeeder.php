<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
\App\Models\User::updateOrCreate(
    ['email' => 'pustakawan@del.ac.id'],
    [
        'name' => 'Pustakawan SIPUS-Del',
        'password' => bcrypt('password123'),
    ]
);

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PustakawanSeeder::class,
            KategoriSeeder::class,
            BukuSeeder::class,
            SirkulasiSeeder::class, // harus terakhir: butuh data buku
        ]);
    }
    
}