<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'INF' => 'Informatika & Pemrograman',
            'SIS' => 'Sistem Informasi',
            'MAT' => 'Matematika & Statistika',
            'ELK' => 'Teknik Elektro',
            'MNJ' => 'Manajemen & Bisnis',
            'UMM' => 'Umum & Referensi',
        ];

        foreach ($data as $kode => $nama) {
            Kategori::updateOrCreate(
                ['kode_kategori' => $kode],
                ['nama_kategori' => $nama]
            );
        }
    }
}