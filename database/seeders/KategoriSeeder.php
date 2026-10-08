<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
{
    $data = [
        ['INF', 'Informatika & Komputer'],
        ['SIS', 'Sistem Informasi'],
        ['MAT', 'Matematika & Statistika'],
        ['MNJ', 'Manajemen & Bisnis'],
        ['UMM', 'Referensi Umum'],
    ];

    foreach ($data as [$kode, $nama]) {
        \App\Models\Kategori::updateOrCreate(
            ['kode_kategori' => $kode],
            ['nama_kategori' => $nama]
        );
    }
}
}
