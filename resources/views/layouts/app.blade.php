<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NegaraPedia') — Ensiklopedia Negara Dunia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #0F2B4B;
            --primary-light: #1A3F6A;
            --primary-dark: #0A1E35;
            --accent: #F5A623;
            --accent-light: #FFE4A0;
            --bg: #F0F4F8;
            --card-shadow: 0 4px 6px -1px rgba(15,43,75,.08), 0 2px 4px -2px rgba(15,43,75,.05);
            --card-shadow-hover: 0 10px 15px -3px rgba(15,43,75,.1), 0 4px 6px -4px rgba(15,43,75,.05);
            --radius: 12px;
            --radius-sm: 8px;
            --transition: all .25s cubic-bezier(.4,0,.2,1);
        }

        * { box-sizing: border-box; }

        body {
            background: var(--bg);
            font-family: 'Inter', 'Segoe UI', sans-serif;
            color: #1a2332;
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%) !important;
            box-shadow: 0 2px 12px rgba(15,43,75,.2);
            padding: .6rem 0;
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 1.35rem;
            letter-spacing: -.5px;
            color: #fff !important;
        }
        .navbar-brand span { color: var(--accent); }
        .navbar .nav-link { color: rgba(255,255,255,.85) !important; transition: var(--transition); }
        .navbar .nav-link:hover { color: #fff !important; }
        .nav-user-badge {
            background: rgba(255,255,255,.12);
            padding: 4px 14px 4px 10px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Sidebar */
        .sidebar {
            min-height: calc(100vh - 60px);
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding-top: 1rem;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.7);
            padding: .65rem 1.2rem;
            margin: 2px 10px;
            border-radius: 8px;
            transition: var(--transition);
            font-size: .9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar .nav-link i {
            width: 20px;
            font-size: 1rem;
            text-align: center;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,.1);
            color: #fff;
            transform: translateX(3px);
        }
        .sidebar .nav-link.active {
            background: rgba(255,255,255,.15);
            color: var(--accent) !important;
            box-shadow: inset 3px 0 0 var(--accent);
        }
        .sidebar hr {
            border-color: rgba(255,255,255,.1);
            margin: 8px 16px;
        }
        .sidebar-section-label {
            color: rgba(255,255,255,.35);
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 8px 20px 4px;
            font-weight: 700;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            overflow: hidden;
        }
        .card:hover {
            box-shadow: var(--card-shadow-hover);
        }
        .card-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: #fff;
            border-radius: var(--radius) var(--radius) 0 0 !important;
            padding: .85rem 1.25rem;
            font-weight: 600;
            border-bottom: none;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border: none;
            border-radius: var(--radius-sm);
            padding: .5rem 1.25rem;
            font-weight: 600;
            transition: var(--transition);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15,43,75,.3);
        }
        .btn-primary:active { transform: translateY(0); }
        .btn-warning {
            background: linear-gradient(135deg, var(--accent) 0%, #e89820 100%);
            border: none;
            color: #1a2332;
            font-weight: 600;
            border-radius: var(--radius-sm);
            transition: var(--transition);
        }
        .btn-warning:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245,166,35,.3);
            color: #1a2332;
        }
        .btn-outline-light:hover {
            background: rgba(255,255,255,.15);
            border-color: rgba(255,255,255,.5);
        }
        .btn-outline-primary {
            border-color: var(--primary);
            color: var(--primary);
        }
        .btn-outline-primary:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        /* Form */
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(15,43,75,.12);
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: var(--radius-sm);
            padding: .75rem 1rem;
        }
        .alert-success { background: #D1FAE5; color: #065F46; }
        .alert-danger { background: #FEE2E2; color: #991B1B; }
        .alert-warning { background: #FEF3C7; color: #92400E; }
        .alert-info { background: #DBEAFE; color: #1E40AF; }

        /* Badge ranks */
        .badge-rank-1 { background: linear-gradient(135deg, #FFD700, #FFA800); color: #333; }
        .badge-rank-2 { background: linear-gradient(135deg, #E8E8E8, #C0C0C0); color: #333; }
        .badge-rank-3 { background: linear-gradient(135deg, #E8A87C, #CD7F32); color: #fff; }

        /* Stats card variant */
        .stat-card {
            text-align: center;
            padding: 1.25rem .75rem;
            border-radius: var(--radius);
            background: #fff;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--card-shadow-hover);
        }
        .stat-card .icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 1.4rem;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

        /* Mobile sidebar */
        @media(max-width:991px){
            .sidebar { min-height: auto; }
            .navbar-collapse { background: var(--primary-dark); border-radius: 0 0 12px 12px; padding: 8px 0; }
            .navbar-collapse .nav-link {
                color: rgba(255,255,255,.8) !important;
                padding: .6rem 1.2rem;
                border-radius: 6px;
                margin: 1px 8px;
            }
            .navbar-collapse .nav-link:hover,
            .navbar-collapse .nav-link.active {
                background: rgba(255,255,255,.1);
                color: var(--accent) !important;
            }
            .navbar-collapse hr { border-color: rgba(255,255,255,.1); margin: 6px 12px; }
        }

        @media(max-width:575px){
            .stat-card { padding: 1rem .5rem; }
            .stat-card .icon { width: 40px; height: 40px; font-size: 1.1rem; }
        }
    </style>
    @stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid px-3">
        <a class="navbar-brand" href="{{ route('dashboard') }}">🌍 <span>Negara</span>Pedia</a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon" style="filter:invert(1)"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav d-lg-none mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('negara.index') ? 'active' : '' }}" href="{{ route('negara.index') }}">
                        <i class="bi bi-search"></i> Cari Negara
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('peta.index') ? 'active' : '' }}" href="{{ route('peta.index') }}">
                        <i class="bi bi-map"></i> Peta Interaktif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('negara.favorites') ? 'active' : '' }}" href="{{ route('negara.favorites') }}">
                        <i class="bi bi-heart"></i> Favorit Saya
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('ai-chat.*') ? 'active' : '' }}" href="{{ route('ai-chat.index') }}">
                        <i class="bi bi-robot"></i> AI Chat Negara
                    </a>
                </li>
                <hr>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kuis.*') ? 'active' : '' }}" href="{{ route('kuis.index') }}">
                        <i class="bi bi-patch-question"></i> Kuis Bendera
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('leaderboard.*') ? 'active' : '' }}" href="{{ route('leaderboard.index') }}">
                        <i class="bi bi-trophy"></i> Leaderboard
                    </a>
                </li>
                <hr>
            </ul>

            <ul class="navbar-nav ms-auto align-items-center gap-2">
                <li class="nav-item">
                    <div class="nav-user-badge">
                        <i class="bi bi-person-circle text-warning"></i>
                        <span class="text-white">{{ auth()->user()->name }}</span>
                        @if(auth()->user()->isAdmin())
                            <span class="badge" style="background:var(--accent);color:var(--primary);font-weight:700;">Admin</span>
                        @endif
                    </div>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline-light rounded-pill px-3">
                            <i class="bi bi-box-arrow-right"></i>
                            <span class="d-none d-md-inline">Keluar</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-2 col-md-3 sidebar d-none d-lg-block p-0">
            <div class="sidebar-section-label">Menu Utama</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('negara.index') ? 'active' : '' }}" href="{{ route('negara.index') }}">
                        <i class="bi bi-search"></i> Cari Negara
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('peta.index') ? 'active' : '' }}" href="{{ route('peta.index') }}">
                        <i class="bi bi-map"></i> Peta Interaktif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('negara.favorites') ? 'active' : '' }}" href="{{ route('negara.favorites') }}">
                        <i class="bi bi-heart"></i> Favorit Saya
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('ai-chat.*') ? 'active' : '' }}" href="{{ route('ai-chat.index') }}">
                        <i class="bi bi-robot"></i> AI Chat Negara
                    </a>
                </li>
            </ul>
            <hr>
            <div class="sidebar-section-label">Game & Ranking</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kuis.*') ? 'active' : '' }}" href="{{ route('kuis.index') }}">
                        <i class="bi bi-patch-question"></i> Kuis Bendera
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('leaderboard.*') ? 'active' : '' }}" href="{{ route('leaderboard.index') }}">
                        <i class="bi bi-trophy"></i> Leaderboard
                    </a>
                </li>
            </ul>
        </div>

        <div class="col-lg-10 col-md-12 p-4" style="min-height:calc(100vh - 60px);">
            @foreach(['success','error','info','warning'] as $type)
                @if(session($type))
                    <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show shadow-sm d-flex align-items-center gap-2">
                        <i class="bi {{ $type === 'success' ? 'bi-check-circle' : ($type === 'error' ? 'bi-exclamation-circle' : ($type === 'warning' ? 'bi-exclamation-triangle' : 'bi-info-circle')) }}"></i>
                        {{ session($type) }}
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
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
