<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

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