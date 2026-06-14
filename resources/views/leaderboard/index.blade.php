@extends('layouts.app')
@section('title', 'Leaderboard')

@section('content')
<h4 class="fw-bold mb-1">🏆 Leaderboard</h4>
<p class="text-muted mb-3">Ranking skor tertinggi kuis tebak bendera</p>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-trophy"></i> Top 20 Pemain</span>
                @if($myRank)
                    <span class="badge bg-warning text-dark">Rankmu: #{{ $myRank }}</span>
                @endif
            </div>
            <div class="card-body p-0">
                @if($leaderboard->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <div style="font-size:3rem">📊</div>
                        <p class="mt-2">Belum ada data. Jadilah yang pertama!</p>
                        <a href="{{ route('kuis.index') }}" class="btn btn-primary btn-sm">Main Kuis</a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">Rank</th>
                                    <th>Nama</th>
                                    <th class="text-center">Skor Terbaik</th>
                                    <th class="text-center">Jawaban Benar</th>
                                    <th class="text-center">Percobaan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaderboard as $idx => $row)
                                <tr class="{{ $row->user_id === auth()->id() ? 'table-warning' : '' }}">
                                    <td>
                                        @if($idx === 0)
                                            <span class="badge badge-rank-1 px-2 py-1">🥇 1</span>
                                        @elseif($idx === 1)
                                            <span class="badge badge-rank-2 px-2 py-1">🥈 2</span>
                                        @elseif($idx === 2)
                                            <span class="badge badge-rank-3 px-2 py-1">🥉 3</span>
                                        @else
                                            <span class="text-muted fw-semibold">{{ $idx + 1 }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $row->user->name ?? 'Anonim' }}</span>
                                        @if($row->user_id === auth()->id())
                                            <span class="badge bg-warning text-dark ms-1 small">Kamu</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-primary fs-5">{{ $row->best_score }}</span>
                                    </td>
                                    <td class="text-center">
                                        {{ $row->best_correct }}/{{ $row->total_q }}
                                    </td>
                                    <td class="text-center text-muted">{{ $row->total_attempts }}x</td>
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
                        <a href="{{ route('kuis.index') }}" class="btn btn-sm btn-primary">Main Sekarang</a>
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($myHistory as $h)
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold">Skor: {{ $h->score }}</div>
                                <div class="text-muted small">
                                    {{ $h->correct_answers }}/{{ $h->total_questions }} benar ·
                                    {{ $h->duration_seconds }}dtk
                                </div>
                                <div class="text-muted" style="font-size:.7rem">{{ $h->created_at->diffForHumans() }}</div>
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
            <div class="card-footer text-center">
                <a href="{{ route('kuis.index') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-play"></i> Main Lagi
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection