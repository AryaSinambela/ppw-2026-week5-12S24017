<x-layout title="Katalog Buku">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h3 class="page-title mb-0">Dashboard &amp; Katalog Buku</h3>
            <div class="text-muted small">Rekapitulasi perpustakaan kampus Del</div>
        </div>
        @auth
            <a href="{{ route('buku.create') }}" class="btn btn-del"><i class="bi bi-plus-lg me-1"></i> Tambah Buku</a>
        @endauth
    </div>

    {{-- Statistik (DB::table) --}}
    @php
        $cards = [
            ['Total Judul Buku',    $stats['total_judul'],  'bi-journal-bookmark'],
            ['Total Stok Tersedia', $stats['total_stok'],   'bi-box-seam'],
            ['Sedang Dipinjam',     $stats['dipinjam'],     'bi-person-lines-fill'],
            ['Total Dikembalikan',  $stats['dikembalikan'], 'bi-check2-circle'],
        ];
    @endphp
    <div class="row g-3 mb-4">
        @foreach ($cards as $card)
            <div class="col-6 col-lg-3">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="icon"><i class="bi {{ $card[2] }}"></i></div>
                        <div>
                            <div class="value">{{ number_format($card[1]) }}</div>
                            <div class="small text-muted">{{ $card[0] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Filter dinamis --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('buku.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold mb-1">Cari judul / penulis / ISBN</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="mis. Clean Code">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Kategori</label>
                    <select name="kategori" class="form-select">
                        <option value="">Semua kategori</option>
                        @foreach ($kategoris as $kat)
                            <option value="{{ $kat->id }}" @selected(request('kategori') == $kat->id)>{{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-del flex-fill"><i class="bi bi-funnel me-1"></i> Terapkan</button>
                    <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            @if (session('last_search') && ! request('q'))
                <div class="mt-3 small text-muted">
                    <i class="bi bi-clock-history me-1"></i> Pencarian terakhir:
                    <a href="{{ route('buku.index', ['q' => session('last_search')]) }}"
                       class="badge rounded-pill text-decoration-none"
                       style="background: var(--del-purple-soft); color: var(--del-purple);">
                        {{ session('last_search') }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Tabel katalog --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th class="ps-4">Buku</th>
                    <th>Kategori</th>
                    <th class="text-center">Tahun</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Dipinjam (total)</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($bukus as $buku)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-semibold">{{ $buku->judul }}</div>
                            <div class="small text-muted">{{ $buku->penulis }} &middot; {{ $buku->penerbit }}</div>
                            <div class="small text-muted"><i class="bi bi-upc me-1"></i>{{ $buku->isbn }}</div>
                        </td>
                        <td>
                            <span class="badge rounded-pill" style="background: var(--del-purple-soft); color: var(--del-purple);">
                                {{ $buku->kategori->nama_kategori }}
                            </span>
                        </td>
                        <td class="text-center">{{ $buku->tahun_terbit }}</td>
                        <td class="text-center">
                            @if ($buku->stok === 0)
                                <span class="badge text-bg-danger">Habis</span>
                            @elseif ($buku->stok <= 2)
                                <span class="badge text-bg-warning">{{ $buku->stok }}</span>
                            @else
                                <span class="badge text-bg-success">{{ $buku->stok }}</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $buku->peminjamans_count }}x</td>
                        <td class="text-end pe-4 text-nowrap">
                            @auth
                                <a href="{{ route('peminjaman.create', ['buku_id' => $buku->id]) }}"
                                   class="btn btn-sm btn-del" title="Pinjamkan"><i class="bi bi-box-arrow-up-right"></i></a>
                                <a href="{{ route('buku.edit', $buku) }}"
                                   class="btn btn-sm btn-outline-del" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('buku.destroy', $buku) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus buku ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="small text-muted text-decoration-none">Login untuk kelola</a>
                            @endauth
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                            Tidak ada buku yang cocok dengan pencarian Anda.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if ($bukus->hasPages())
            <div class="card-footer bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="small text-muted">
                    Menampilkan {{ $bukus->firstItem() }}&ndash;{{ $bukus->lastItem() }} dari {{ $bukus->total() }} buku
                </div>
                {{ $bukus->links() }}
            </div>
        @endif
    </div>
</x-layout>