<x-layout :title="$kategori->nama_kategori">
    <a href="{{ route('kategori.index') }}" class="text-decoration-none small">
        <i class="bi bi-arrow-left"></i> Kembali ke kategori
    </a>

    <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
        <div>
            <span class="badge badge-del mb-1">{{ $kategori->kode_kategori }}</span>
            <h1 class="h3 fw-bold page-title mb-0">{{ $kategori->nama_kategori }}</h1>
        </div>
        <span class="text-muted">{{ $bukus->total() }} buku</span>
    </div>

    <div class="card card-del overflow-hidden">
        <ul class="list-group list-group-flush">
            @forelse ($bukus as $buku)
                <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                    <div>
                        <a href="{{ route('buku.show', $buku) }}" class="fw-semibold text-decoration-none" style="color:var(--del)">{{ $buku->judul }}</a>
                        <div class="small text-muted">{{ $buku->penulis }} · {{ $buku->tahun_terbit }}</div>
                    </div>
                    <span class="badge {{ $buku->stok == 0 ? 'text-bg-danger' : 'text-bg-success' }}">
                        {{ $buku->stok == 0 ? 'Habis' : 'Stok ' . $buku->stok }}
                    </span>
                </li>
            @empty
                <li class="list-group-item text-center py-5 empty-state">
                    <i class="bi bi-journal-x d-block mb-2"></i>
                    <span class="text-muted">Belum ada buku di kategori ini.</span>
                </li>
            @endforelse
        </ul>
    </div>

    <div class="mt-4 d-flex justify-content-center">{{ $bukus->links() }}</div>
</x-layout>