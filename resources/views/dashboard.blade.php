@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h3 class="fw-bold mb-1" style="color:var(--primary);">
            👋 Halo, {{ $user->name }}!
        </h3>
        <p class="text-muted mb-0" style="font-size:.95rem;">
            Selamat datang di <strong>NegaraPedia</strong> — belajar IPS jadi lebih seru.
        </p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="icon" style="background:linear-gradient(135deg,#DBEAFE,#93C5FD);">
                <span style="font-size:1.5rem;">🎯</span>
            </div>
            <div class="fs-3 fw-bold" style="color:var(--primary);">{{ $bestScore }}</div>
            <div class="text-muted small">Skor Terbaik</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="icon" style="background:linear-gradient(135deg,#D1FAE5,#6EE7B7);">
                <span style="font-size:1.5rem;">📝</span>
            </div>
            <div class="fs-3 fw-bold" style="color:#059669;">{{ $totalQuiz }}</div>
            <div class="text-muted small">Total Kuis</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="icon" style="background:linear-gradient(135deg,#FEE2E2,#FCA5A5);">
                <span style="font-size:1.5rem;">❤️</span>
            </div>
            <div class="fs-3 fw-bold" style="color:#DC2626;">{{ $totalFav }}</div>
            <div class="text-muted small">Negara Favorit</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="icon" style="background:linear-gradient(135deg,#FEF3C7,#FCD34D);">
                <span style="font-size:1.5rem;">🏅</span>
            </div>
            <div class="fs-3 fw-bold" style="color:#D97706;">{{ $user->isAdmin() ? 'Admin' : 'Siswa' }}</div>
            <div class="text-muted small">Role Akun</div>
        </div>
    </div>
</div>

@if($user->isAdmin())
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card p-3" style="border-left:4px solid var(--accent);">
            <div class="d-flex align-items-center gap-3">
                <div class="icon" style="background:linear-gradient(135deg,#FEF3C7,#FCD34D);width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;">
                    ⚙️
                </div>
                <div>
                    <h6 class="fw-bold mb-1" style="color:var(--primary);">Statistik Admin</h6>
                    <p class="mb-0 small text-muted">
                        Total Pengguna: <strong>{{ $totalUsers }}</strong> &middot;
                        Total Percobaan Kuis: <strong>{{ $totalResults }}</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if($lastQuiz)
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-clock-history"></i> Kuis Terakhir
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-4 text-center">
                <div class="stat-card mb-0 p-3">
                    <div class="fs-2 fw-bold" style="color:var(--primary);">{{ $lastQuiz->score }}</div>
                    <div class="text-muted small">Skor</div>
                </div>
            </div>
            <div class="col-4 text-center">
                <div class="stat-card mb-0 p-3">
                    <div class="fs-2 fw-bold" style="color:#059669;">{{ $lastQuiz->correct_answers }}/{{ $lastQuiz->total_questions }}</div>
                    <div class="text-muted small">Benar</div>
                </div>
            </div>
            <div class="col-4 text-center">
                <div class="stat-card mb-0 p-3">
                    <div class="fs-2 fw-bold" style="color:#0284C7;">{{ $lastQuiz->percentage }}%</div>
                    <div class="text-muted small">Persentase</div>
                </div>
            </div>
        </div>
        <div class="text-muted small text-center mt-3">
            <i class="bi bi-clock"></i> {{ $lastQuiz->created_at->diffForHumans() }}
        </div>
    </div>
</div>
@endif

<div class="row g-3">
    <div class="col-md-3 col-6">
        <a href="{{ route('negara.index') }}" class="text-decoration-none">
            <div class="stat-card p-3 h-100">
                <div class="icon" style="background:linear-gradient(135deg,#DBEAFE,#93C5FD);">
                    <i class="bi bi-search text-primary" style="font-size:1.3rem;"></i>
                </div>
                <div class="fw-semibold" style="color:var(--primary);">Cari Negara</div>
                <div class="text-muted small">Jelajahi dunia</div>
            </div>
        </a>
    </div>
    <div class="col-md-3 col-6">
        <a href="{{ route('peta.index') }}" class="text-decoration-none">
            <div class="stat-card p-3 h-100">
                <div class="icon" style="background:linear-gradient(135deg,#D1FAE5,#6EE7B7);">
                    <i class="bi bi-map" style="font-size:1.3rem;color:#059669;"></i>
                </div>
                <div class="fw-semibold" style="color:var(--primary);">Peta Dunia</div>
                <div class="text-muted small">Jelajahi peta interaktif</div>
            </div>
        </a>
    </div>
    <div class="col-md-3 col-6">
        <a href="{{ route('kuis.index') }}" class="text-decoration-none">
            <div class="stat-card p-3 h-100">
                <div class="icon" style="background:linear-gradient(135deg,#FEE2E2,#FCA5A5);">
                    <i class="bi bi-patch-question text-danger" style="font-size:1.3rem;"></i>
                </div>
                <div class="fw-semibold" style="color:var(--primary);">Kuis Bendera</div>
                <div class="text-muted small">Uji pengetahuan</div>
            </div>
        </a>
    </div>
    <div class="col-md-3 col-6">
        <a href="{{ route('ai-chat.index') }}" class="text-decoration-none">
            <div class="stat-card p-3 h-100">
                <div class="icon" style="background:linear-gradient(135deg,#EDE9FE,#C4B5FD);">
                    <i class="bi bi-robot text-purple" style="font-size:1.3rem;color:#7C3AED;"></i>
                </div>
                <div class="fw-semibold" style="color:var(--primary);">AI Chat</div>
                <div class="text-muted small">Tanya AI tentang negara</div>
            </div>
        </a>
    </div>
</div>
@endsection
