<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class BukuSeeder extends Seeder
{
    public function run(): void
    {
        $kat = Kategori::pluck('id', 'kode_kategori');

        // [isbn, judul, penulis, penerbit, tahun, kategori, stok, sinopsis]
        // ISBN adalah data contoh. stok 0: Introduction to Algorithms & Project Management.
        // stok 1: Computer Networking & Linear Algebra and Its Applications.
        $bukus = [
            ['978-623-7000-01-1', 'Clean Code', 'Robert C. Martin', 'Prentice Hall', 2008, 'INF', 5, 'Panduan menulis kode yang bersih dan mudah dirawat.'],
            ['978-623-7000-02-2', 'Design Patterns', 'Erich Gamma et al.', 'Addison-Wesley', 1994, 'INF', 3, 'Katalog pola desain perangkat lunak berorientasi objek.'],
            ['978-623-7000-03-3', 'Introduction to Algorithms', 'Thomas H. Cormen et al.', 'MIT Press', 2022, 'INF', 0, 'Referensi algoritma dan struktur data yang komprehensif.'],
            ['978-623-7000-04-4', 'Database System Concepts', 'Abraham Silberschatz et al.', 'McGraw-Hill', 2019, 'SIS', 4, 'Konsep basis data relasional, transaksi, dan indexing.'],
            ['978-623-7000-05-5', 'Computer Networking: A Top-Down Approach', 'James F. Kurose', 'Pearson', 2021, 'INF', 1, 'Jaringan komputer dari lapisan aplikasi hingga fisik.'],
            ['978-623-7000-06-6', 'Operating System Concepts', 'Abraham Silberschatz et al.', 'Wiley', 2018, 'INF', 6, 'Konsep sistem operasi modern.'],
            ['978-623-7000-07-7', 'Artificial Intelligence: A Modern Approach', 'Stuart Russell & Peter Norvig', 'Pearson', 2020, 'INF', 2, 'Buku teks kecerdasan buatan, termasuk search dan CSP.'],
            ['978-623-7000-08-8', 'Systems Analysis and Design', 'Kenneth E. Kendall', 'Pearson', 2019, 'SIS', 5, 'Analisis dan perancangan sistem informasi.'],
            ['978-623-7000-09-9', 'Management Information Systems', 'Kenneth C. Laudon', 'Pearson', 2022, 'SIS', 3, 'Sistem informasi dalam konteks manajemen organisasi.'],
            ['978-623-7000-10-0', 'Project Management: A Systems Approach', 'Harold Kerzner', 'Wiley', 2017, 'MNJ', 0, 'Perencanaan, penjadwalan, dan pengendalian proyek.'],
            ['978-623-7000-11-1', 'Introduction to Linear Algebra', 'Gilbert Strang', 'Wellesley-Cambridge Press', 2016, 'MAT', 4, 'Aljabar linier dengan intuisi geometris.'],
            ['978-623-7000-12-2', 'Linear Algebra and Its Applications', 'David C. Lay', 'Pearson', 2015, 'MAT', 1, 'Aljabar linier beserta aplikasinya.'],
            ['978-623-7000-13-3', 'Probability and Statistics for Engineers', 'Ronald E. Walpole', 'Pearson', 2016, 'MAT', 6, 'Probabilitas dan statistika untuk rekayasa.'],
            ['978-623-7000-14-4', 'Cryptography and Network Security', 'William Stallings', 'Pearson', 2017, 'INF', 3, 'Prinsip dan praktik kriptografi dan keamanan jaringan.'],
            ['978-623-7000-15-5', "Don't Make Me Think", 'Steve Krug', 'New Riders', 2014, 'SIS', 7, 'Prinsip usability dan desain web yang intuitif.'],
            ['978-623-7000-16-6', 'The Pragmatic Programmer', 'David Thomas & Andrew Hunt', 'Addison-Wesley', 2019, 'INF', 4, 'Kiat praktis menjadi programmer yang efektif.'],
            ['978-623-7000-17-7', 'Refactoring', 'Martin Fowler', 'Addison-Wesley', 2018, 'INF', 2, 'Teknik memperbaiki desain kode yang sudah ada.'],
            ['978-623-7000-18-8', 'Java: The Complete Reference', 'Herbert Schildt', 'McGraw-Hill', 2021, 'INF', 5, 'Referensi lengkap bahasa pemrograman Java dan OOP.'],
            ['978-623-7000-19-9', 'Digital Design', 'M. Morris Mano', 'Pearson', 2017, 'ELK', 3, 'Dasar rangkaian logika dan desain digital.'],
            ['978-623-7000-20-0', 'Signals and Systems', 'Alan V. Oppenheim', 'Pearson', 1996, 'ELK', 2, 'Analisis sinyal dan sistem linier.'],
            ['978-623-7000-21-1', 'Principles of Marketing', 'Philip Kotler', 'Pearson', 2020, 'MNJ', 4, 'Dasar-dasar pemasaran modern.'],
            ['978-623-7000-22-2', 'The Lean Startup', 'Eric Ries', 'Crown Business', 2011, 'MNJ', 6, 'Metode membangun bisnis rintisan secara efisien.'],
        ];

        foreach ($bukus as [$isbn, $judul, $penulis, $penerbit, $tahun, $k, $stok, $sinopsis]) {
            Buku::updateOrCreate(['isbn' => $isbn], [
                'judul'        => $judul,
                'penulis'      => $penulis,
                'penerbit'     => $penerbit,
                'tahun_terbit' => $tahun,
                'kategori_id'  => $kat[$k],
                'stok'         => $stok,
                'sinopsis'     => $sinopsis,
            ]);
        }
    }
}