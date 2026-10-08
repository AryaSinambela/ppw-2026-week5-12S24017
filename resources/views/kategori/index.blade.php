<x-layout title="Kategori">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold page-title mb-1">Kategori Buku</h1>
            <p class="text-muted mb-0">Kelola pengelompokan koleksi perpustakaan.</p>
        </div>
        <a href="{{ route('kategori.create') }}" class="btn btn-del">
            <i class="bi bi-plus-lg me-1"></i>Tambah Kategori
        </a>
    </div>

    <div class="card card-del overflow-hidden">
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
                            <td class="ps-4"><span class="badge badge-del">{{ $kategori->kode_kategori }}</span></td>
                            <td>
                                <a href="{{ route('kategori.show', $kategori) }}" class="fw-semibold text-decoration-none" style="color:var(--del)">
                                    {{ $kategori->nama_kategori }}
                                </a>
                            </td>
                            <td class="text-center">{{ $kategori->bukus_count }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('kategori.edit', $kategori) }}" class="btn btn-sm btn-outline-del">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('kategori.destroy', $kategori) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 empty-state">
                                <i class="bi bi-tags d-block mb-2"></i>
                                <p class="text-muted mb-2">Belum ada kategori.</p>
                                <a href="{{ route('kategori.create') }}" class="btn btn-sm btn-del">Tambah kategori pertama</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">{{ $kategoris->links() }}</div>
</x-layout>