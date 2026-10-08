<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriController extends Controller
{
    public function index()
    {
        // withCount: jumlah buku per kategori dalam satu query (tanpa N+1)
        $kategoris = Kategori::withCount('bukus')
            ->orderBy('nama_kategori')
            ->paginate(10);

        return view('kategori.index', compact('kategoris'));
    }

    public function create()
    {
        return view('kategori.create', ['kategori' => new Kategori()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(), $this->messages(), $this->attributes());
        Kategori::create($validated);

        return redirect()->route('kategori.index')
            ->with('success', "Kategori \"{$validated['nama_kategori']}\" berhasil ditambahkan!");
    }

    public function show(Kategori $kategori)
    {
        $bukus = $kategori->bukus()->latest()->paginate(10);
        return view('kategori.show', compact('kategori', 'bukus'));
    }

    public function edit(Kategori $kategori)
    {
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori)
    {
        $kategori->update($request->validate($this->rules($kategori), $this->messages(), $this->attributes()));

        return redirect()->route('kategori.index')
            ->with('success', "Kategori \"{$kategori->nama_kategori}\" berhasil diperbarui!");
    }

    public function destroy(Kategori $kategori)
    {
        if ($kategori->bukus()->exists()) {
            return redirect()->route('kategori.index')
                ->with('error', "Kategori \"{$kategori->nama_kategori}\" tidak dapat dihapus karena masih memiliki buku.");
        }

        $nama = $kategori->nama_kategori;
        $kategori->delete();

        return redirect()->route('kategori.index')
            ->with('success', "Kategori \"{$nama}\" berhasil dihapus.");
    }

    private function rules(?Kategori $kategori = null): array
    {
        return [
            'kode_kategori' => ['required', 'alpha_dash', 'max:10', Rule::unique('kategoris', 'kode_kategori')->ignore($kategori)],
            'nama_kategori' => ['required', 'string', 'min:3', 'max:100'],
        ];
    }

    private function messages(): array
    {
        return [
            'required'        => ':attribute wajib diisi.',
            'unique'          => ':attribute sudah terdaftar.',
            'alpha_dash'      => ':attribute hanya boleh berisi huruf, angka, strip, dan garis bawah.',
            'nama_kategori.min' => 'Nama kategori minimal :min karakter.',
            'max.string'      => ':attribute maksimal :max karakter.',
        ];
    }

    private function attributes(): array
    {
        return ['kode_kategori' => 'Kode kategori', 'nama_kategori' => 'Nama kategori'];
    }
}