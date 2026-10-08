<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BukuController extends Controller
{
    public function index(Request $request)
    {
        // Eager Loading: mencegah N+1 Query Problem
        $bukus = Buku::with('kategori')
            ->when($request->q, function ($query, $kw) {
                $query->where(fn ($w) => $w
                    ->where('judul', 'like', "%{$kw}%")
                    ->orWhere('penulis', 'like', "%{$kw}%")
                    ->orWhere('isbn', 'like', "%{$kw}%"));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('buku.index', compact('bukus'));
    }

    public function create()
    {
        return view('buku.create', [
            'buku'      => new Buku(),
            'kategoris' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(), $this->messages(), $this->attributes());
        Buku::create($validated);

        return redirect()->route('buku.index')
            ->with('success', "Buku \"{$validated['judul']}\" berhasil ditambahkan!");
    }

    // Implicit Route Model Binding: {buku} otomatis menjadi instance Buku
    public function show(Buku $buku)
    {
        $buku->load('kategori');
        return view('buku.show', compact('buku'));
    }

    public function edit(Buku $buku)
    {
        return view('buku.edit', [
            'buku'      => $buku,
            'kategoris' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    public function update(Request $request, Buku $buku)
    {
        $validated = $request->validate($this->rules($buku), $this->messages(), $this->attributes());
        $buku->update($validated);

        return redirect()->route('buku.index')
            ->with('success', "Buku \"{$buku->judul}\" berhasil diperbarui!");
    }

    public function destroy(Buku $buku)
    {
        $judul = $buku->judul;
        $buku->delete();

        return redirect()->route('buku.index')
            ->with('success', "Buku \"{$judul}\" berhasil dihapus.");
    }

    private function rules(?Buku $buku = null): array
    {
        return [
            'isbn'         => ['required', 'digits_between:10,13', Rule::unique('bukus', 'isbn')->ignore($buku)],
            'judul'        => ['required', 'string', 'min:5', 'max:200'],
            'penulis'      => ['required', 'string', 'min:3', 'max:150'],
            'penerbit'     => ['required', 'string', 'max:150'],
            'tahun_terbit' => ['required', 'integer', 'between:1900,' . date('Y')],
            'kategori_id'  => ['required', 'exists:kategoris,id'],
            'stok'         => ['required', 'integer', 'min:0'],
            'sinopsis'     => ['nullable', 'string', 'max:2000'],
        ];
    }

    private function messages(): array
    {
        return [
            'required'       => ':attribute wajib diisi.',
            'unique'         => ':attribute sudah terdaftar.',
            'digits_between' => ':attribute harus berupa 10–13 digit angka.',
            'judul.min'      => 'Judul minimal :min karakter.',
            'penulis.min'    => 'Nama penulis minimal :min karakter.',
            'stok.min'       => 'Stok minimal :min.',
            'integer'        => ':attribute harus berupa bilangan bulat.',
            'between'        => ':attribute harus antara :min dan :max.',
            'exists'         => ':attribute yang dipilih tidak valid.',
            'max.string'     => ':attribute maksimal :max karakter.',
        ];
    }

    private function attributes(): array
    {
        return [
            'isbn' => 'ISBN', 'judul' => 'Judul', 'penulis' => 'Penulis',
            'penerbit' => 'Penerbit', 'tahun_terbit' => 'Tahun terbit',
            'kategori_id' => 'Kategori', 'stok' => 'Stok', 'sinopsis' => 'Sinopsis',
        ];
    }
}