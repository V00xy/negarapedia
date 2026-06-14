<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NegaraPedia') — Ensiklopedia Negara Dunia</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f0f4f8; font-family: 'Segoe UI', sans-serif; }
        .navbar-brand { font-weight: 700; font-size: 1.4rem; }
        .navbar { background: #1a3c6e !important; }
        .navbar a, .navbar .navbar-brand { color: #fff !important; }
        .nav-link:hover { color: #ffd700 !important; }
        .sidebar { min-height: calc(100vh - 56px); background: #1a3c6e; padding-top: 1rem; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: .6rem 1.2rem; border-radius: 6px; margin: 2px 8px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.15); color: #fff; }
        .sidebar .nav-link i { width: 22px; }
        .card { border: none; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .card-header { background: #1a3c6e; color: #fff; border-radius: 10px 10px 0 0 !important; }
        .btn-primary { background: #1a3c6e; border-color: #1a3c6e; }
        .btn-primary:hover { background: #0f2a52; border-color: #0f2a52; }
        .badge-rank-1 { background: #FFD700; color: #333; }
        .badge-rank-2 { background: #C0C0C0; color: #333; }
        .badge-rank-3 { background: #CD7F32; color: #fff; }
        .alert { border-radius: 8px; }
        @media(max-width:768px){ .sidebar { min-height: auto; } }
    </style>
    @stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid px-3">
        <a class="navbar-brand" href="{{ route('dashboard') }}">🌍 NegaraPedia</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon" style="filter:invert(1)"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <span class="nav-link text-warning">
                        <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                        @if(auth()->user()->isAdmin())
                            <span class="badge bg-warning text-dark ms-1">Admin</span>
                        @endif
                    </span>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline-light ms-2">
                            <i class="bi bi-box-arrow-right"></i> Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-2 col-md-3 sidebar d-none d-md-block p-0">
            <ul class="nav flex-column pt-2">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="bi bi-search"></i> Cari Negara</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="bi bi-heart"></i> Favorit Saya</a>
                </li>
                <hr style="border-color:rgba(255,255,255,.2); margin:6px 12px;">
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="bi bi-patch-question"></i> Kuis Bendera</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="bi bi-trophy"></i> Leaderboard</a>
                </li>
            </ul>
        </div>

        <div class="col-lg-10 col-md-9 p-4">
            @foreach(['success','error','info','warning'] as $type)
                @if(session($type))
                    <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show">
                        {{ session($type) }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            @endforeach

            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>