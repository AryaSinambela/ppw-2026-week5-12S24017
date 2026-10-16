<x-layout title="Login Pustakawan">
    <div class="row justify-content-center py-4">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="brand-mark mx-auto mb-3" style="width:56px;height:56px;font-size:1.6rem;border-radius:16px;">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <h4 class="page-title mb-1">Login Pustakawan</h4>
                        <p class="text-muted small mb-0">Masuk untuk mengelola buku dan sirkulasi</p>
                    </div>

                    <form action="{{ route('login.attempt') }}" method="POST" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="email">Email</label>
                            <input type="email" id="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', request()->cookie('del_last_user_email')) }}"
                                   placeholder="pustakawan@del.ac.id" required autofocus>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="password">Password</label>
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                            <label class="form-check-label small" for="remember">Ingat saya</label>
                        </div>
                        <button class="btn btn-del w-100 py-2 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                        </button>
                    </form>

                    <div class="mt-4 p-3 rounded-3 small" style="background: var(--del-purple-soft);">
                        <div class="fw-semibold mb-1"><i class="bi bi-info-circle me-1"></i> Akun uji</div>
                        <code>pustakawan@del.ac.id</code> / <code>password123</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>