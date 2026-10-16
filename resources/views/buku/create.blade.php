<x-layout title="Tambah Buku - SIPUS-Del">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-xl font-bold text-del-purple mb-6">Tambah Buku Baru</h2>

        <form action="{{ route('bukus.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">ISBN</label>
                    <input type="text" name="isbn" value="{{ old('isbn') }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                    @error('isbn')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kategori</label>
                    <select name="kategori_id" required class="w-full px-3 py-2 border rounded-lg text-sm">
                        <option value="">-- Pilih --</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Judul Buku</label>
                <input type="text" name="judul" value="{{ old('judul') }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                @error('judul')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Penulis</label>
                    <input type="text" name="penulis" value="{{ old('penulis') }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Penerbit</label>
                    <input type="text" name="penerbit" value="{{ old('penerbit') }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tahun Terbit</label>
                    <input type="number" name="tahun_terbit" value="{{ old('tahun_terbit', date('Y')) }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Stok Awal</label>
                <input type="number" name="stok" value="{{ old('stok', 5) }}" min="0" required class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('bukus.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-bold">Batal</a>
                <button type="submit" class="px-5 py-2 bg-del-purple text-white rounded-lg text-sm font-bold">Simpan Buku</button>
            </div>
        </form>
    </div>
</x-layout>