<x-layout title="Sirkulasi Peminjaman">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h3 class="page-title mb-0">Sirkulasi Peminjaman</h3>
            <div class="text-muted small">Transaksi ACID dengan pessimistic locking</div>
        </div>
        <a href="{{ route('peminjaman.create') }}" class="btn btn-del"><i class="bi bi-plus-lg me-1"></i> Peminjaman Baru</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('peminjaman.index') }}" class="row g-2">
                <div class="col-md-6">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                           placeholder="Cari kode pinjam / NIM / nama peminjam">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua status</option>
                        <option value="dipinjam" @selected(request('status') === 'dipinjam')>Dipinjam</option>
                        <option value="dikembalikan" @selected(request('status') === 'dikembalikan')>Dikembalikan</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-del flex-fill">Filter</button>
                    <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                    <th>Buku</th>
                    <th>Peminjam</th>
                    <th>Pinjam</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($peminjamans as $p)
                    <tr>
                        <td class="ps-4"><code>{{ $p->kode_pinjam }}</code></td>
                        <td class="fw-semibold">{{ $p->buku->judul }}</td>
                        <td>
                            {{ $p->nama_peminjam }}
                            <div class="small text-muted">{{ $p->nim_peminjam }}</div>
                        </td>
                        <td>{{ $p->tanggal_pinjam->format('d M Y') }}</td>
                        <td>
                            {{ $p->tanggal_jatuh_tempo->format('d M Y') }}
                            @if ($p->terlambat)
                                <span class="badge text-bg-danger ms-1">Terlambat</span>
                            @endif
                        </td>
                        <td>
                            @if ($p->status === 'dipinjam')
                                <span class="badge text-bg-warning">Dipinjam</span>
                            @else
                                <span class="badge text-bg-success">Dikembalikan</span>
                                @if ($p->tanggal_kembali)
                                    <div class="small text-muted">{{ $p->tanggal_kembali->format('d M Y') }}</div>
                                @endif
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @if ($p->status === 'dipinjam')
                                <form action="{{ route('peminjaman.kembali', $p->id) }}" method="POST"
                                      onsubmit="return confirm('Proses pengembalian buku ini?')">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm btn-outline-del">
                                        <i class="bi bi-arrow-return-left me-1"></i> Kembalikan
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small">&mdash;</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inboxes fs-1 d-block mb-2"></i> Belum ada transaksi peminjaman.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if ($peminjamans->hasPages())
            <div class="card-footer bg-white border-0 py-3">{{ $peminjamans->links() }}</div>
        @endif
    </div>
</x-layout>