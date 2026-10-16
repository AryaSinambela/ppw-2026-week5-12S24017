@props(['title' => 'SIPUS-Del'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} | SIPUS-Del</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --del-purple: #4C1D95;
            --del-purple-dark: #3B1475;
            --del-purple-soft: #EDE9FE;
            --del-gold: #F5B301;
        }
        body { font-family: 'Inter', sans-serif; background: #F8F7FC; color: #1F1B2E; }
        .navbar-del { background: linear-gradient(90deg, var(--del-purple-dark), var(--del-purple)); }
        .navbar-del .nav-link { color: rgba(255,255,255,.85); font-weight: 500; border-radius: .5rem; padding: .5rem .9rem; }
        .navbar-del .nav-link:hover, .navbar-del .nav-link.active { color: #fff; background: rgba(255,255,255,.14); }
        .brand-mark { background: var(--del-gold); color: var(--del-purple-dark); width: 34px; height: 34px;
                      border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem; }
        .btn-del { background: var(--del-purple); border-color: var(--del-purple); color: #fff; }
        .btn-del:hover { background: var(--del-purple-dark); border-color: var(--del-purple-dark); color: #fff; }
        .btn-outline-del { color: var(--del-purple); border-color: var(--del-purple); }
        .btn-outline-del:hover { background: var(--del-purple); color: #fff; }
        .card { border: 0; border-radius: 1rem; box-shadow: 0 2px 14px rgba(76,29,149,.08); }
        .stat-card .icon { width: 48px; height: 48px; border-radius: 12px; background: var(--del-purple-soft);
                           color: var(--del-purple); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        .stat-card .value { font-size: 1.75rem; font-weight: 700; color: var(--del-purple); line-height: 1.1; }
        .table thead th { background: var(--del-purple-soft); color: var(--del-purple-dark); border: 0;
                          font-size: .78rem; text-transform: uppercase; letter-spacing: .04em; }
        .page-title { font-weight: 700; color: var(--del-purple-dark); }
        .form-control:focus, .form-select:focus { border-color: var(--del-purple); box-shadow: 0 0 0 .2rem rgba(76,29,149,.15); }
        .page-link { color: var(--del-purple); }
        .page-item.active .page-link { background: var(--del-purple); border-color: var(--del-purple); color: #fff; }
        footer { color: #6b6585; font-size: .85rem; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-del navbar-dark sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('buku.index') }}">
            <span class="brand-mark"><i class="bi bi-book-half"></i></span> SIPUS-Del
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto gap-1">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('buku.index') ? 'active' : '' }}" href="{{ route('buku.index') }}">
                        <i class="bi bi-grid me-1"></i> Katalog
                    </a>
                </li>
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('peminjaman.*') ? 'active' : '' }}" href="{{ route('peminjaman.index') }}">
                            <i class="bi bi-arrow-left-right me-1"></i> Sirkulasi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('kategori.*') ? 'active' : '' }}" href="{{ route('kategori.index') }}">
                            <i class="bi bi-tags me-1"></i> Kategori
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('buku.create') ? 'active' : '' }}" href="{{ route('buku.create') }}">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Buku
                        </a>
                    </li>
                @endauth
            </ul>

            @auth
                <div class="dropdown">
                    <a class="btn btn-sm btn-light dropdown-toggle fw-semibold" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i> {{ auth()->user()->name }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-end p-3 shadow border-0" style="min-width: 260px;">
                        <div class="small text-muted">Login sejak</div>
                        <div class="fw-semibold mb-2">{{ session('login_timestamp', '-') }}</div>
                        @if (session('last_borrower'))
                            <div class="small text-muted">Peminjam terakhir</div>
                            <div class="fw-semibold mb-2">{{ session('last_borrower') }}</div>
                        @endif
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger w-100">
                                <i class="bi bi-box-arrow-right me-1"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-light fw-semibold">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Login Pustakawan
                </a>
            @endauth
        </div>
    </div>
</nav>

<main class="container py-4">
    {{-- Flash Session --}}
    @foreach (['success' => 'success', 'error' => 'danger'] as $key => $type)
        @if (session($key))
            <div class="alert alert-{{ $type }} alert-dismissible fade show shadow-sm border-0" role="alert">
                <i class="bi {{ $key === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }} me-2"></i>
                {{ session($key) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0">
            <div class="fw-semibold mb-1"><i class="bi bi-exclamation-octagon-fill me-1"></i> Periksa kembali isian Anda:</div>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{ $slot }}
</main>

<footer class="container pb-4 text-center">
    SIPUS-Del &copy; {{ now()->year }} &middot; Institut Teknologi Del &middot; 12S3101 PPW
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Flash sukses hilang otomatis setelah 5 detik
    document.querySelectorAll('.alert-success').forEach(function (el) {
        setTimeout(function () { bootstrap.Alert.getOrCreateInstance(el).close(); }, 5000);
    });
</script>
</body>
</html>