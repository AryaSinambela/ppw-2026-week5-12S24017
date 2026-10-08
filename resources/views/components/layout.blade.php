@props(['title' => null])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title . ' — ' : '' }}SIPUS-Del · Institut Teknologi Del</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --del: #4C1D95; --del-dark: #3B0764; --del-soft: #EDE9FE; }
        body { font-family: 'Inter', system-ui, sans-serif; background: #F5F3FF; color: #1f1537; }
        .navbar-del { background: linear-gradient(135deg, var(--del), var(--del-dark)); }
        .navbar-del .nav-link { color: rgba(255,255,255,.75); border-radius: .5rem; padding: .45rem .9rem; }
        .navbar-del .nav-link:hover { color: #fff; background: rgba(255,255,255,.1); }
        .navbar-del .nav-link.active { color: #fff; background: rgba(255,255,255,.18); }
        .btn-del { background: var(--del); color: #fff; border: 0; }
        .btn-del:hover, .btn-del:focus { background: var(--del-dark); color: #fff; }
        .btn-outline-del { border: 1px solid var(--del); color: var(--del); }
        .btn-outline-del:hover { background: var(--del); color: #fff; }
        .card-del { border: 0; border-radius: 1rem; box-shadow: 0 4px 24px rgba(76,29,149,.08); }
        .table thead th { background: var(--del-soft); color: var(--del); font-size: .78rem;
                          text-transform: uppercase; letter-spacing: .04em; border: 0; }
        .badge-del { background: var(--del-soft); color: var(--del); font-weight: 600; }
        .form-control:focus, .form-select:focus { border-color: var(--del); box-shadow: 0 0 0 .2rem rgba(76,29,149,.15); }
        .page-title { color: var(--del); }
        .page-link { color: var(--del); }
        .active > .page-link { background: var(--del); border-color: var(--del); }
        .empty-state i { font-size: 3rem; color: #c4b5fd; }
        footer { color: #7c6fa0; font-size: .85rem; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-del shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('buku.index') }}">
                <i class="bi bi-book-half me-2"></i>SIPUS-Del
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav ms-auto gap-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('buku.*') ? 'active' : '' }}" href="{{ route('buku.index') }}">
                            <i class="bi bi-journal-bookmark me-1"></i>Buku
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('kategori.*') ? 'active' : '' }}" href="{{ route('kategori.index') }}">
                            <i class="bi bi-tags me-1"></i>Kategori
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container pb-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{ $slot }}
    </main>

    <footer class="text-center pb-4">
        SIPUS-Del · Perpustakaan Kampus Del · 12S3101 Pemrograman &amp; Pengujian Web
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>