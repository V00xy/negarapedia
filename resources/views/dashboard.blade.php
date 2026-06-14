@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold">👋 Halo, {{ $user->name }}!</h4>
        <p class="text-muted mb-0">Selamat datang di NegaraPedia — belajar IPS jadi lebih seru.</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card text-center p-3">
            <div style="font-size:2rem">🎯</div>
            <div class="fs-4 fw-bold text-primary">{{ $bestScore }}</div>
            <div class="text-muted small">Skor Terbaik</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card text-center p-3">
            <div style="font-size:2rem">📝</div>
            <div class="fs-4 fw-bold text-success">{{ $totalQuiz }}</div>
            <div class="text-muted small">Total Kuis</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card text-center p-3">
            <div style="font-size:2rem">❤️</div>
            <div class="fs-4 fw-bold text-danger">{{ $totalFav }}</div>
            <div class="text-muted small">Negara Favorit</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card text-center p-3">
            <div style="font-size:2rem">🏅</div>
            <div class="fs-4 fw-bold text-warning">{{ $user->isAdmin() ? 'Admin' : 'Siswa' }}</div>
            <div class="text-muted small">Role Akun</div>
        </div>
    </div>
</div>

@if($user->isAdmin())
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card p-3 border-warning">
            <h6 class="fw-bold text-warning">⚙️ Statistik Admin</h6>
            <p class="mb-1">Total Pengguna: <strong>{{ $totalUsers }}</strong></p>
            <p class="mb-0">Total Percobaan Kuis: <strong>{{ $totalResults }}</strong></p>
        </div>
    </div>
</div>
@endif

@if($lastQuiz)
<div class="card mb-4">
    <div class="card-header"><i class="bi bi-clock-history"></i> Kuis Terakhir</div>
    <div class="card-body">
        <div class="row text-center">
            <div class="col-4">
                <div class="fs-3 fw-bold text-primary">{{ $lastQuiz->score }}</div>
                <div class="text-muted small">Skor</div>
            </div>
            <div class="col-4">
                <div class="fs-3 fw-bold text-success">{{ $lastQuiz->correct_answers }}/{{ $lastQuiz->total_questions }}</div>
                <div class="text-muted small">Benar</div>
            </div>
            <div class="col-4">
                <div class="fs-3 fw-bold text-info">{{ $lastQuiz->percentage }}%</div>
                <div class="text-muted small">Persentase</div>
            </div>
        </div>
        <div class="text-muted small text-center mt-2">{{ $lastQuiz->created_at->diffForHumans() }}</div>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header"><i class="bi bi-lightning"></i> Menu Cepat</div>
    <div class="card-body">
        <p class="text-muted">Menu lengkap (Cari Negara, Kuis, Leaderboard) akan aktif di tahap berikutnya 🚧</p>
    </div>
</div>
@endsection