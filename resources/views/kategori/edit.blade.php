<x-layout title="Edit Kategori">
    <h1 class="h3 fw-bold page-title mb-4">Edit Kategori</h1>
    <div class="card card-del p-4">
        <form action="{{ route('kategori.update', $kategori) }}" method="POST" novalidate>
            @csrf
            @method('PUT')
            @include('kategori._fields')
            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-del"><i class="bi bi-save me-1"></i>Perbarui</button>
                <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-layout>