<?php

namespace Database\Factories;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

class BukuFactory extends Factory
{
    public function definition(): array
    {
        $awalan = ['Pengantar', 'Dasar-Dasar', 'Panduan Praktis', 'Konsep Modern', 'Analisis dan Perancangan', 'Teknik Lanjut'];
        $topik  = ['Basis Data', 'Pemrograman Web', 'Sistem Informasi', 'Jaringan Komputer', 'Kecerdasan Buatan',
                   'Rekayasa Perangkat Lunak', 'Keamanan Informasi', 'Manajemen Proyek', 'Statistika', 'Aljabar Linier'];

        return [
            'isbn'         => fake()->unique()->isbn13(),
            'judul'        => fake()->randomElement($awalan) . ' ' . fake()->randomElement($topik),
            'penulis'      => fake()->name(),
            'penerbit'     => fake()->randomElement(['Informatika Bandung', 'Andi Offset', 'Erlangga', 'Gramedia Pustaka Utama', 'Deepublish']),
            'tahun_terbit' => fake()->numberBetween(2005, (int) date('Y')),
            'kategori_id'  => Kategori::factory(),
            'stok'         => fake()->numberBetween(0, 15),
            'sinopsis'     => fake()->paragraphs(2, true),
        ];
    }
}