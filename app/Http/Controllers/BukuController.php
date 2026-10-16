<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BukuController extends Controller
{
    public function index(Request $request): View
    {
        // Session state: histori pencarian terakhir
        if ($request->filled('q')) {
            session(['last_search' => $request->q]);
        }

        // Eager loading + withCount => bebas N+1 query
        $bukus = Buku::with('kategori')
            ->withCount('peminjamans')
            ->when($request->q, function ($query, $keyword) {
                $query->where(function ($w) use ($keyword) {
                    $w->where('judul', 'like', "%{$keyword}%")
                      ->orWhere('penulis', 'like', "%{$keyword}%")
                      ->orWhere('isbn', 'like', "%{$keyword}%");
                });
            })
            ->when($request->kategori, fn ($query, $id) => $query->where('kategori_id', $id))
            ->orderBy('judul')
            ->paginate(10)
            ->withQueryString();

        // Statistik via Fluent Query Builder
        $stats = [
            'total_judul'  => DB::table('bukus')->count(),
            'total_stok'   => (int) DB::table('bukus')->sum('stok'),
            'dipinjam'     => DB::table('peminjamans')->where('status', 'dipinjam')->count(),
            'dikembalikan' => DB::table('peminjamans')->where('status', 'dikembalikan')->count(),
        ];

        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('buku.index', compact('bukus', 'stats', 'kategoris'));
    }

    public function create(): View
    {
        return view('buku.form', [
            'buku'      => new Buku(),
            'kategoris' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $buku = Buku::create($this->validated($request));

        return redirect()->route('buku.index')
            ->with('success', "Buku \"{$buku->judul}\" berhasil ditambahkan.");
    }

    public function edit(Buku $buku): View
    {
        return view('buku.form', [
            'buku'      => $buku,
            'kategoris' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    public function update(Request $request, Buku $buku): RedirectResponse
    {
        $buku->update($this->validated($request, $buku));

        return redirect()->route('buku.index')
            ->with('success', "Buku \"{$buku->judul}\" berhasil diperbarui.");
    }

    public function destroy(Buku $buku): RedirectResponse
    {
        if ($buku->peminjamans()->where('status', 'dipinjam')->exists()) {
            return back()->with('error', 'Buku tidak dapat dihapus karena masih ada yang meminjam.');
        }

        $judul = $buku->judul;
        $buku->delete();

        return redirect()->route('buku.index')
            ->with('success', "Buku \"{$judul}\" berhasil dihapus.");
    }

    private function validated(Request $request, ?Buku $buku = null): array
    {
        return $request->validate([
            'isbn' => [
                'required',
                'string',
                'max:20',
                Rule::unique('bukus', 'isbn')->ignore($buku?->id),
            ],
            'judul'        => 'required|string|min:5|max:200',
            'penulis'      => 'required|string|max:150',
            'penerbit'     => 'required|string|max:120',
            'tahun_terbit' => 'required|integer|between:1900,' . now()->year,
            'kategori_id'  => 'required|exists:kategoris,id',
            'stok'         => 'required|integer|min:0',
            'sinopsis'     => 'nullable|string|max:2000',
        ]);
    }
}