@php $isEdit = $kategori->exists; @endphp
<x-layout :title="$isEdit ? 'Edit Kategori' : 'Tambah Kategori'">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body p-4">
                    <h4 class="page-title mb-4">
                        <i class="bi {{ $isEdit ? 'bi-pencil-square' : 'bi-plus-circle' }} me-1"></i>
                        {{ $isEdit ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
                    </h4>

                    <form action="{{ $isEdit ? route('kategori.update', $kategori) : route('kategori.store') }}" method="POST" novalidate>
                        @csrf
                        @if ($isEdit)
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kode Kategori</label>
                            <input type="text" name="kode_kategori" maxlength="10"
                                   value="{{ old('kode_kategori', $kategori->kode_kategori) }}"
                                   class="form-control @error('kode_kategori') is-invalid @enderror" placeholder="mis. INF" required>
                            @error('kode_kategori') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Kategori</label>
                            <input type="text" name="nama_kategori"
                                   value="{{ old('nama_kategori', $kategori->nama_kategori) }}"
                                   class="form-control @error('nama_kategori') is-invalid @enderror" required>
                            @error('nama_kategori') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary">Batal</a>
                            <button class="btn btn-del px-4"><i class="bi bi-save me-1"></i> Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>