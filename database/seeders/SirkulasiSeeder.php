<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Database\Seeder;

class SirkulasiSeeder extends Seeder
{
    public function run(): void
    {
        $idBuku = fn (string $isbn) => Buku::where('isbn', $isbn)->value('id');

        // Stok di BukuSeeder adalah stok tersedia SETELAH transaksi ini,
        // sehingga seeder ini sengaja tidak mengubah stok.
        // [kode, isbn, nim, nama, offsetPinjam(hari), lamaPinjam(hari), offsetKembali, status]
        $transaksi = [
            ['PJM-SEED-0001', '978-623-7000-03-3', '12S23001', 'Arya Pratama Sinambela', -5, 7, null, 'dipinjam'],
            ['PJM-SEED-0002', '978-623-7000-03-3', '12S23002', 'Rina Simanjuntak', -3, 7, null, 'dipinjam'],
            ['PJM-SEED-0003', '978-623-7000-10-0', '12S23003', 'Budi Hutapea', -2, 7, null, 'dipinjam'],
            ['PJM-SEED-0004', '978-623-7000-05-5', '12S23004', 'Siti Nababan', -12, 7, null, 'dipinjam'], // terlambat
            ['PJM-SEED-0005', '978-623-7000-01-1', '12S23005', 'Andi Panjaitan', -14, 7, -8, 'dikembalikan'],
            ['PJM-SEED-0006', '978-623-7000-04-4', '12S23006', 'Maria Sitorus', -10, 7, -4, 'dikembalikan'],
        ];

        foreach ($transaksi as [$kode, $isbn, $nim, $nama, $offPinjam, $lama, $offKembali, $status]) {
            $tglPinjam = now()->addDays($offPinjam);

            Peminjaman::updateOrCreate(['kode_pinjam' => $kode], [
                'buku_id'             => $idBuku($isbn),
                'nim_peminjam'        => $nim,
                'nama_peminjam'       => $nama,
                'tanggal_pinjam'      => $tglPinjam->toDateString(),
                'tanggal_jatuh_tempo' => $tglPinjam->copy()->addDays($lama)->toDateString(),
                'tanggal_kembali'     => $offKembali !== null ? now()->addDays($offKembali)->toDateString() : null,
                'status'              => $status,
            ]);
        }
    }
}