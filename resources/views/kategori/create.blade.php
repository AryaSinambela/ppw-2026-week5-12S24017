<x-layout title="Tambah Kategori">
    <h1 class="h3 fw-bold page-title mb-4">Tambah Kategori</h1>
    <div class="card card-del p-4">
        <form action="{{ route('kategori.store') }}" method="POST" novalidate>
            @csrf
            @include('kategori._fields')
            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-del"><i class="bi bi-save me-1"></i>Simpan</button>
                <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-layout>