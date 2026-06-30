@extends('layouts.app')
@section('title', 'Leaderboard')

@section('content')
<h4 class="fw-bold mb-1" style="color:var(--primary);">🏆 Leaderboard</h4>
<p class="text-muted mb-3">Ranking skor tertinggi kuis tebak bendera</p>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-trophy"></i> Top 20 Pemain</span>
                @if($myRank)
                    <span class="badge" style="background:var(--accent);color:#1a2332;font-weight:600;">
                        Rankmu: #{{ $myRank }}
                    </span>
                @endif
            </div>
            <div class="card-body p-0">
                @if($leaderboard->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <div style="font-size:3rem;margin-bottom:.5rem;">📊</div>
                        <p class="mt-2">Belum ada data. Jadilah yang pertama!</p>
                        <a href="{{ route('kuis.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-play"></i> Main Kuis
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="60" class="ps-3">Rank</th>
                                    <th>Nama</th>
                                    <th class="text-center">Skor Terbaik</th>
                                    <th class="text-center">Benar</th>
                                    <th class="text-center pe-3">Percobaan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaderboard as $idx => $row)
                                <tr class="{{ $row->user_id === auth()->id() ? 'table-warning' : '' }}">
                                    <td class="ps-3">
                                        @if($idx === 0)
                                            <span class="badge badge-rank-1 px-2 py-1 fs-6">🥇</span>
                                        @elseif($idx === 1)
                                            <span class="badge badge-rank-2 px-2 py-1 fs-6">🥈</span>
                                        @elseif($idx === 2)
                                            <span class="badge badge-rank-3 px-2 py-1 fs-6">🥉</span>
                                        @else
                                            <span class="fw-bold" style="color:#94A3B8;">{{ $idx + 1 }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $row->user->name ?? 'Anonim' }}</span>
                                        @if($row->user_id === auth()->id())
                                            <span class="badge" style="background:var(--accent);color:#1a2332;font-size:.65rem;">Kamu</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold fs-5" style="color:var(--primary);">{{ $row->best_score }}</span>
                                    </td>
                                    <td class="text-center fw-semibold">
                                        {{ $row->best_correct }}/{{ $row->total_q }}
                                    </td>
                                    <td class="text-center text-muted pe-3">{{ $row->total_attempts }}x</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history"></i> Riwayat Kuismu</div>
            <div class="card-body p-0">
                @if($myHistory->isEmpty())
                    <div class="text-center py-4 text-muted small">
                        <p>Belum ada riwayat.</p>
                        <a href="{{ route('kuis.index') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-play"></i> Main Sekarang
                        </a>
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($myHistory as $h)
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold" style="color:var(--primary);">Skor: {{ $h->score }}</div>
                                <div class="text-muted small">
                                    {{ $h->correct_answers }}/{{ $h->total_questions }} benar ·
                                    {{ $h->duration_seconds }}dtk
                                </div>
                                <div style="font-size:.7rem;color:#94A3B8;">{{ $h->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="d-flex flex-column align-items-end gap-1">
                                <span class="badge {{ $h->percentage >= 80 ? 'bg-success' : ($h->percentage >= 50 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ $h->percentage }}%
                                </span>
                                @if(auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('kuis.destroy', $h->id) }}" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger py-0 px-1"
                                            onclick="return confirm('Hapus data ini?')">
                                        <i class="bi bi-trash" style="font-size:.7rem"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            @if(!$myHistory->isEmpty())
            <div class="card-footer text-center bg-white">
                <a href="{{ route('kuis.index') }}" class="btn btn-primary btn-sm px-4">
                    <i class="bi bi-play"></i> Main Lagi
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
