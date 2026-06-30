<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — NegaraPedia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        :root { --primary: #0F2B4B; --primary-light: #1A3F6A; --accent: #F5A623; }
        body {
            background: linear-gradient(135deg, #0F2B4B 0%, #1A3F6A 50%, #1E4D7A 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }
        .register-card {
            border: none; border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
            overflow: hidden; width: 100%;
        }
        .register-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            padding: 2rem 2rem 1.5rem;
            text-align: center;
        }
        .register-header .brand { font-size: 1.8rem; font-weight: 800; color: #fff; }
        .register-header .brand span { color: var(--accent); }
        .register-header p { color: rgba(255,255,255,.7); font-size: .85rem; }
        .register-body { padding: 2rem; background: #fff; }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(15,43,75,.12); }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border: none; border-radius: 8px; padding: .65rem; font-weight: 600;
            transition: all .25s;
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(15,43,75,.3); }
        a { color: var(--primary); font-weight: 600; }
        a:hover { color: var(--primary-light); }
    </style>
</head>
<body>
<div class="container" style="max-width:440px;">
    <div class="register-card">
        <div class="register-header">
            <div class="brand">🌍 <span>Negara</span>Pedia</div>
            <p class="mb-0">Buat akun baru</p>
        </div>
        <div class="register-body">
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-uppercase" style="color:#64748B;">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="Nama kamu" required autofocus>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-uppercase" style="color:#64748B;">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="email@contoh.com" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-uppercase" style="color:#64748B;">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                           placeholder="Minimal 6 karakter" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold small text-uppercase" style="color:#64748B;">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control"
                           placeholder="Ulangi password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-person-plus me-1"></i> Daftar
                </button>
            </form>

            <hr class="my-4">
            <p class="text-center text-muted small mb-0">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-decoration-none">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
