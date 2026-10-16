<x-layout title="Peminjaman Baru">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body p-4">
                    <h4 class="page-title mb-1"><i class="bi bi-box-arrow-up-right me-1"></i> Peminjaman Buku</h4>
                    <p class="text-muted small mb-4">
                        Seluruh proses dibungkus <code>DB::transaction()</code> dengan <code>lockForUpdate()</code>.
                    </p>

                    <form action="{{ route('peminjaman.store') }}" method="POST" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Buku</label>
                            <select name="buku_id" class="form-select @error('buku_id') is-invalid @enderror" required>
                                <option value="">-- Pilih buku --</option>
                                @foreach ($bukus as $b)
                                    <option value="{{ $b->id }}" @selected(old('buku_id', $selected) == $b->id)>
                                        {{ $b->judul }} (stok: {{ $b->stok }})
                                    </option>
                                @endforeach
                            </select>
                            @error('buku_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">NIM Peminjam</label>
                                <input type="text" name="nim_peminjam" value="{{ old('nim_peminjam') }}"
                                       class="form-control @error('nim_peminjam') is-invalid @enderror" required>
                                @error('nim_peminjam') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-7">
                                <label class="form-label fw-semibold">Nama Peminjam</label>
                                <input type="text" name="nama_peminjam" value="{{ old('nama_peminjam') }}"
                                       class="form-control @error('nama_peminjam') is-invalid @enderror" required>
                                @error('nama_peminjam') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Lama Pinjam</label>
                                <select name="lama_pinjam" class="form-select @error('lama_pinjam') is-invalid @enderror">
                                    @foreach ([3, 7, 14, 21, 30] as $hari)
                                        <option value="{{ $hari }}" @selected(old('lama_pinjam', 7) == $hari)>{{ $hari }} hari</option>
                                    @endforeach
                                </select>
                                @error('lama_pinjam') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary">Batal</a>
                            <button class="btn btn-del px-4"><i class="bi bi-check2-circle me-1"></i> Proses Peminjaman</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>