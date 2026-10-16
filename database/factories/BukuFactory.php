<?php

namespace Database\Factories;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Buku>
 */
class BukuFactory extends Factory
{
    protected $model = Buku::class;

    public function definition(): array
    {
        return [
            'isbn'         => fake()->unique()->isbn13(),
            'judul'        => ucfirst(fake()->words(4, true)),
            'penulis'      => fake()->name(),
            'penerbit'     => fake()->company(),
            'tahun_terbit' => fake()->numberBetween(1990, 2025),
            'kategori_id'  => Kategori::factory(),
            'stok'         => fake()->numberBetween(0, 10),
            'sinopsis'     => fake()->paragraph(),
        ];
    }
}