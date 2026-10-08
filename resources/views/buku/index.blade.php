<x-layout title="Koleksi Buku">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold page-title mb-1">Koleksi Buku</h1>
            <p class="text-muted mb-0">Total {{ $bukus->total() }} judul tercatat di perpustakaan.</p>
        </div>
        <a href="{{ route('buku.create') }}" class="btn btn-del">
            <i class="bi bi-plus-lg me-1"></i>Tambah Buku
        </a>
    </div>

    <form method="GET" action="{{ route('buku.index') }}" class="card card-del p-3 mb-4">
        <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                   placeholder="Cari judul, penulis, atau ISBN...">
            <button class="btn btn-del">Cari</button>
            @if (request('q'))
                <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </div>
    </form>

    <div class="card card-del overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Buku</th>
                        <th>Kategori</th>
                        <th>Tahun</th>
                        <th class="text-center">Stok</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bukus as $buku)
                        <tr>
                            <td class="ps-4 text-muted">{{ $bukus->firstItem() + $loop->index }}</td>
                            <td>
                                <a href="{{ route('buku.show', $buku) }}" class="fw-semibold text-decoration-none" style="color:var(--del)">
                                    {{ $buku->judul }}
                                </a>
                                <div class="small text-muted">{{ $buku->penulis }} · ISBN {{ $buku->isbn }}</div>
                            </td>
                            <td><span class="badge badge-del">{{ $buku->kategori->nama_kategori }}</span></td>
                            <td>{{ $buku->tahun_terbit }}</td>
                            <td class="text-center">
                                <span class="badge {{ $buku->stok == 0 ? 'text-bg-danger' : ($buku->stok <= 3 ? 'text-bg-warning' : 'text-bg-success') }}">
                                    {{ $buku->stok == 0 ? 'Habis' : $buku->stok }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('buku.edit', $buku) }}" class="btn btn-sm btn-outline-del" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('buku.destroy', $buku) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus buku &quot;{{ addslashes($buku->judul) }}&quot;?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 empty-state">
                                <i class="bi bi-journal-x d-block mb-2"></i>
                                <p class="mb-2 text-muted">
                                    {{ request('q') ? 'Tidak ada buku yang cocok dengan pencarian.' : 'Belum ada data buku.' }}
                                </p>
                                <a href="{{ route('buku.create') }}" class="btn btn-sm btn-del">Tambah buku pertama</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">{{ $bukus->links() }}</div>
</x-layout>