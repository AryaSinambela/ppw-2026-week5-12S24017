<?php

namespace Database\Factories;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kategori>
 */
class KategoriFactory extends Factory
{
    public function definition(): array
{
    return [
        'kode_kategori' => strtoupper(fake()->unique()->lexify('???')),
        'nama_kategori' => fake()->unique()->words(2, true),
    ];
}

}