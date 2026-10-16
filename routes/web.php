<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PeminjamanController;
use Illuminate\Support\Facades\Route;

/* ---------- Publik ---------- */
Route::redirect('/', '/buku');
Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');

/* ---------- Tamu (belum login) ---------- */
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.attempt');
});

/* ---------- Terproteksi: middleware auth ---------- */
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Buku: create, store, edit, update, destroy
    Route::resource('buku', BukuController::class)->except(['index', 'show']);

    // Kategori: index, create, store, edit, update, destroy
    Route::resource('kategori', KategoriController::class)->except(['show']);

    // Sirkulasi
    Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('/peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
    Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::patch('/peminjaman/{id}/kembali', [PeminjamanController::class, 'kembali'])
        ->whereNumber('id')
        ->name('peminjaman.kembali');
});