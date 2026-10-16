@php $isEdit = $buku->exists; @endphp
<x-layout :title="$isEdit ? 'Edit Buku' : 'Tambah Buku'">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <h4 class="page-title mb-4">
                        <i class="bi {{ $isEdit ? 'bi-pencil-square' : 'bi-plus-circle' }} me-1"></i>
                        {{ $isEdit ? 'Edit Buku' : 'Tambah Buku Baru' }}
                    </h4>

                    <form action="{{ $isEdit ? route('buku.update', $buku) : route('buku.store') }}" method="POST" novalidate>
                        @csrf
                        @if ($isEdit)
                            @method('PUT')
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">ISBN</label>
                                <input type="text" name="isbn" value="{{ old('isbn', $buku->isbn) }}"
                                       class="form-control @error('isbn') is-invalid @enderror" required>
                                @error('isbn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Kategori</label>
                                <select name="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih kategori --</option>
                                    @foreach ($kategoris as $kat)
                                        <option value="{{ $kat->id }}" @selected(old('kategori_id', $buku->kategori_id) == $kat->id)>
                                            {{ $kat->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kategori_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Judul</label>
                                <input type="text" name="judul" value="{{ old('judul', $buku->judul) }}"
                                       class="form-control @error('judul') is-invalid @enderror" required>
                                @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Penulis</label>
                                <input type="text" name="penulis" value="{{ old('penulis', $buku->penulis) }}"
                                       class="form-control @error('penulis') is-invalid @enderror" required>
                                @error('penulis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Penerbit</label>
                                <input type="text" name="penerbit" value="{{ old('penerbit', $buku->penerbit) }}"
                                       class="form-control @error('penerbit') is-invalid @enderror" required>
                                @error('penerbit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tahun Terbit</label>
                                <input type="number" name="tahun_terbit" value="{{ old('tahun_terbit', $buku->tahun_terbit) }}"
                                       class="form-control @error('tahun_terbit') is-invalid @enderror" required>
                                @error('tahun_terbit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Stok</label>
                                <input type="number" min="0" name="stok" value="{{ old('stok', $buku->stok ?? 0) }}"
                                       class="form-control @error('stok') is-invalid @enderror" required>
                                @error('stok') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Sinopsis</label>
                                <textarea name="sinopsis" rows="4"
                                          class="form-control @error('sinopsis') is-invalid @enderror">{{ old('sinopsis', $buku->sinopsis) }}</textarea>
                                @error('sinopsis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary">Batal</a>
                            <button class="btn btn-del px-4"><i class="bi bi-save me-1"></i> Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>