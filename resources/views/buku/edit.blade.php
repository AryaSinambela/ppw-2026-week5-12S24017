<x-layout title="Edit Buku">
    <h1 class="h3 fw-bold page-title mb-4">Edit Buku</h1>
    <div class="card card-del p-4">
        <form action="{{ route('buku.update', $buku) }}" method="POST" novalidate>
            @csrf
            @method('PUT')
            @include('buku._fields')
            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-del"><i class="bi bi-save me-1"></i>Perbarui</button>
                <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-layout>