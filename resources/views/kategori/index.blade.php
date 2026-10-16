<x-layout title="Kategori Buku">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h3 class="page-title mb-0">Kategori Buku</h3>
            <div class="text-muted small">Kelola klasifikasi koleksi perpustakaan</div>
        </div>
        <a href="{{ route('kategori.create') }}" class="btn btn-del"><i class="bi bi-plus-lg me-1"></i> Tambah Kategori</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('kategori.index') }}" class="row g-2">
                <div class="col-md-9">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                           placeholder="Cari kode atau nama kategori">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-del flex-fill">Cari</button>
                    <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th class="ps-4">Kode</th>
                    <th>Nama Kategori</th>
                    <th class="text-center">Jumlah Buku</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($kategoris as $kategori)
                    <tr>
                        <td class="ps-4"><code>{{ $kategori->kode_kategori }}</code></td>
                        <td class="fw-semibold">{{ $kategori->nama_kategori }}</td>
                        <td class="text-center">
                            <a href="{{ route('buku.index', ['kategori' => $kategori->id]) }}"
                               class="badge rounded-pill text-decoration-none"
                               style="background: var(--del-purple-soft); color: var(--del-purple);">
                                {{ $kategori->bukus_count }} buku
                            </a>
                        </td>
                        <td class="text-end pe-4 text-nowrap">
                            <a href="{{ route('kategori.edit', $kategori) }}" class="btn btn-sm btn-outline-del" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('kategori.destroy', $kategori) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bi bi-tags fs-1 d-block mb-2"></i> Belum ada kategori.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if ($kategoris->hasPages())
            <div class="card-footer bg-white border-0 py-3">{{ $kategoris->links() }}</div>
        @endif
    </div>
</x-layout>