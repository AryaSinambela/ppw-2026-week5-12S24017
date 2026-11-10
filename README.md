# SIPUS-Del Tahap 3: Arsitektur Web Services (SOAP Legacy & RESTful API v1)

## 1. Identitas Mahasiswa
* **Nama Lengkap**: Arya Pratama Sinambela
* **NIM**: 12S24017
* **Program Studi**: S1 Sistem Informasi — Fakultas Informatika dan Teknik Elektro, Institut Teknologi Del
* **Mata Kuliah**: Pemrograman dan Pengujian Aplikasi Web (12S3101) — Minggu 9
* **Dosen Pengampu**: Chandro Pardede, S.Kom., M.Sc.
* **Branch**: `week9-rest-api` (Melanjutkan `week6-db-session`)

---

## 2. Analisis Kebutuhan & Kelayakan (Feasibility) Web Services pada SIPUS-Del

### A. Pemetaan Peran Arsitektur Layanan (Service Provider vs Service Consumer)

| Komponen Integrasi | Peran SIPUS-Del | Mitra / Eksternal | Protokol & Format | Rasionalisasi Bisnis & Teknis |
| :--- | :--- | :--- | :--- | :--- |
| **Layanan Cek Bebas Pustaka Yudisium** | **Service Provider** | Sistem Administrasi Yudisium Kampus (Legacy) | SOAP 1.1 (`text/xml` + WSDL) | Sistem Yudisium lama membutuhkan kontrak WSDL formal dan amplop `<soap:Envelope>` untuk mengecek tanggungan pinjaman buku (`active_loans` & `is_cleared`). |
| **Layanan Katalog & Sirkulasi Mobile/Kiosk** | **Service Provider** | Aplikasi Mobile Mahasiswa & Kiosk Mandiri Perpustakaan | RESTful API v1 (`application/json`) | Aplikasi mobile dan kiosk membutuhkan pertukaran data ringan, *stateless* (Sanctum Bearer Token), mendukung paginasi, HATEOAS, serta jaminan transaksi ACID (`lockForUpdate`). |
| **Pengayaan Metadata Buku ISBN** | **Service Consumer** | OpenLibrary REST API (`openlibrary.org`) | REST via Laravel `Http` Facade (JSON) | Informasi jumlah halaman, sampul buku, dan tautan pratinjau dikelola oleh repositori buku global; mengonsumsi API eksternal menghemat beban penyimpanan lokal. |
| **Portofolio Koding Mahasiswa** | **Service Consumer** | GitHub REST API v3 (`api.github.com`) | REST via Laravel `Http` Facade (JSON) | Mengambil profil dan repositori publik terbaru mahasiswa langsung dari otoritas data GitHub. |

### B. Alasan Pemilihan RESTful API Dibanding SOAP untuk Mobile & Kiosk Mandiri
1. **Efisiensi Payload & Bandwidth**: Payload JSON pada REST API 5–8 kali lebih ringkas dibanding dokumen XML `<soap:Envelope>`, sehingga sangat responsif pada jaringan seluler mahasiswa.
2. **Skalabilitas Statelessness**: Dengan autentikasi `Authorization: Bearer <token>` (Laravel Sanctum), server tidak menyimpan sesi klien di memori/disk sehingga siap diskalakan secara horizontal di belakang Load Balancer.
3. **Semantik HTTP & HATEOAS (RMM Level 3)**: Klien Mobile/Kiosk dapat mengenali status operasi langsung dari HTTP Status Code (`200`, `201`, `204`, `401`, `404`, `409`, `422`) dan menavigasi aksi berikutnya secara dinamis melalui atribut `_links` (`pinjam_buku` & `kembalikan_buku`).

---

## 3. Tabel Katalog Endpoint Web Services (SOAP & REST API v1)

| Method | URI Endpoint | Proteksi Auth | Deskripsi Layanan | Kode Status Respons |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/soap/sipus.wsdl` | Publik | Menyajikan kontrak dokumen XML WSDL `SipusLibrarySoapService` | `200` |
| `POST` | `/soap/sipus` | Publik | Operasi SOAP `CheckClearanceStatus` berdasarkan `<nim_peminjam>` | `200`, `500 (soap:Fault)` |
| `POST` | `/api/v1/auth/login` | Publik | Login pustakawan & menerbitkan Sanctum Bearer Token | `200`, `401`, `422` |
| `GET` | `/api/v1/auth/me` | `auth:sanctum` | Mengambil profil pemilik Bearer Token aktif | `200`, `401` |
| `POST` | `/api/v1/auth/logout` | `auth:sanctum` | Mencabut (*revoke*) Bearer Token yang sedang aktif | `200`, `401` |
| `GET` | `/api/v1/bukus` | Publik | Daftar buku terpaginasi (`?search=`, `?kategori_id=`, `?tersedia=1`, `?sort=`) bebas N+1 | `200` |
| `GET` | `/api/v1/bukus/{id}` | Publik | Detail satu buku beserta kategori, riwayat pinjam, & HATEOAS `_links` | `200`, `404` |
| `POST` | `/api/v1/bukus` | `auth:sanctum` | Menambahkan buku baru ke katalog perpustakaan | `201 (+Header Location)`, `401`, `422` |
| `PUT` | `/api/v1/bukus/{id}` | `auth:sanctum` | Mengganti seluruh representasi data buku (*Full Replacement*) | `200`, `401`, `404`, `422` |
| `PATCH` | `/api/v1/bukus/{id}` | `auth:sanctum` | Memperbarui sebagian atribut buku (*Partial Update*, misal `stok` saja) | `200`, `400`, `401`, `404`, `422` |
| `DELETE` | `/api/v1/bukus/{id}` | `auth:sanctum` | Menghapus buku (ditolak `409 Conflict` jika masih ada pinjaman aktif) | `204`, `401`, `404`, `409` |
| `GET` | `/api/v1/bukus/{id}/referensi-eksternal` | Publik | Mengambil metadata tambahan buku dari OpenLibrary API berdasarkan ISBN | `200`, `404`, `502`, `504` |
| `GET` | `/api/v1/peminjamans` | `auth:sanctum` | Daftar transaksi sirkulasi peminjaman (`?status=dipinjam\|dikembalikan`, `?nim=`) | `200`, `401` |
| `POST` | `/api/v1/peminjamans` | `auth:sanctum` | Mencatat peminjaman buku baru secara atomik (`DB::transaction` + `lockForUpdate`) | `201 (+Header Location)`, `404`, `409`, `422` |
| `PATCH` | `/api/v1/peminjamans/{id}/kembalikan` | `auth:sanctum` | Mengembalikan buku secara atomik & menambah stok buku (`+1`) | `200`, `404`, `409` |
| `GET` | `/api/v1/portfolios/github/{username}` | Publik | Orkestrasi konsumsi GitHub REST API untuk profil mahasiswa | `200`, `404`, `502` |

---

## 4. Panduan Instalasi & Pengujian Cepat

```bash
# 1. Clone repositori dan pindah ke branch week9-rest-api
git clone -b week9-rest-api [https://github.com/AryaSinambela/ppw-2026-week5-12S24017.git](https://github.com/AryaSinambela/ppw-2026-week5-12S24017.git)
cd ppw-2026-week5-12S24017

# 2. Instal dependensi Composer & siapkan environment
composer install
cp .env.example .env
php artisan key:generate

# 3. Jalankan migrasi & database seeder
php artisan migrate:fresh --seed

# 4. Jalankan server pengembangan lokal
php artisan serve