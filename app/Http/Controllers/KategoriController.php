<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KategoriController extends Controller
{
    public function index(Request $request): View
    {
        $kategoris = Kategori::withCount('bukus')
            ->when($request->q, function ($query, $keyword) {
                $query->where(function ($w) use ($keyword) {
                    $w->where('kode_kategori', 'like', "%{$keyword}%")
                      ->orWhere('nama_kategori', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('nama_kategori')
            ->paginate(10)
            ->withQueryString();

        return view('kategori.index', compact('kategoris'));
    }

    public function create(): View
    {
        return view('kategori.form', ['kategori' => new Kategori()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $kategori = Kategori::create($data);

        return redirect()->route('kategori.index')
            ->with('success', "Kategori \"{$kategori->nama_kategori}\" berhasil ditambahkan.");
    }

    public function edit(Kategori $kategori): View
    {
        return view('kategori.form', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori): RedirectResponse
    {
        $kategori->update($this->validated($request, $kategori));

        return redirect()->route('kategori.index')
            ->with('success', "Kategori \"{$kategori->nama_kategori}\" berhasil diperbarui.");
    }

    public function destroy(Kategori $kategori): RedirectResponse
    {
        // cascadeOnDelete akan ikut menghapus buku, jadi tolak jika masih berisi buku.
        if ($kategori->bukus()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki buku.');
        }

        $nama = $kategori->nama_kategori;
        $kategori->delete();

        return redirect()->route('kategori.index')
            ->with('success', "Kategori \"{$nama}\" berhasil dihapus.");
    }

    private function validated(Request $request, ?Kategori $kategori = null): array
    {
        $data = $request->validate([
            'kode_kategori' => [
                'required',
                'string',
                'max:10',
                Rule::unique('kategoris', 'kode_kategori')->ignore($kategori?->id),
            ],
            'nama_kategori' => 'required|string|min:3|max:100',
        ]);

        $data['kode_kategori'] = strtoupper(trim($data['kode_kategori']));

        return $data;
    }
}