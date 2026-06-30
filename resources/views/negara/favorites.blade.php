@extends('layouts.app')
@section('title', 'Favorit Saya')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-0" style="color:var(--primary);">❤️ Negara Favorit</h4>
        <p class="text-muted small mb-0">{{ $favorites->count() }} negara tersimpan</p>
    </div>
    <a href="{{ route('negara.index') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Negara
    </a>
</div>

@if($favorites->isEmpty())
    <div class="card text-center py-5">
        <div style="font-size:4rem;margin-bottom:.5rem;">🗺️</div>
        <h5 class="fw-bold" style="color:var(--primary);">Belum ada favorit</h5>
        <p class="text-muted">Cari negara dulu dan simpan yang menarik!</p>
        <a href="{{ route('negara.index') }}" class="btn btn-primary mx-auto" style="width:fit-content">
            <i class="bi bi-search"></i> Cari Negara
        </a>
    </div>
@else
    <div class="row g-3">
        @foreach($favorites as $fav)
        <div class="col-md-4 col-sm-6" id="fav-{{ $fav->id }}">
            <div class="card h-100">
                <img src="{{ $fav->flag_url }}" alt="{{ $fav->country_name }}"
                     style="width:100%;height:130px;object-fit:cover;border-radius:10px 10px 0 0;"
                     onerror="this.src='https://via.placeholder.com/300x130?text=No+Flag'">
                <div class="card-body pb-2">
                    <h6 class="fw-bold mb-2" style="color:var(--primary);">{{ $fav->country_name }}</h6>
                    <div class="small d-flex align-items-center gap-1 mb-1" style="color:#64748B;">
                        <i class="bi bi-geo-alt"></i> {{ $fav->capital ?? '-' }}
                    </div>
                    <div class="small d-flex align-items-center gap-1 mb-1" style="color:#64748B;">
                        <i class="bi bi-people"></i>
                        {{ $fav->population ? number_format($fav->population, 0, ',', '.') . ' jiwa' : '-' }}
                    </div>
                    <div class="small d-flex align-items-center gap-1" style="color:#64748B;">
                        <i class="bi bi-cash-coin"></i> {{ $fav->currency ?? '-' }}
                    </div>
                </div>
                <div class="card-footer bg-transparent d-flex gap-2 border-0 pt-0">
                    <a href="{{ route('negara.favorites.edit', $fav->id) }}"
                       class="btn btn-sm btn-outline-primary flex-fill rounded-pill">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <button class="btn btn-sm btn-outline-danger flex-fill rounded-pill"
                            onclick="deleteFav({{ $fav->id }}, '{{ $fav->country_name }}')">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection

@push('scripts')
<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;

async function deleteFav(id, name) {
    if (!confirm(`Hapus "${name}" dari favorit?`)) return;

    try {
        const res = await fetch(`/negara/favorites/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (res.ok) {
            const el = document.getElementById(`fav-${id}`);
            el.style.transition = 'all .3s';
            el.style.opacity = '0';
            el.style.transform = 'scale(.9)';
            setTimeout(() => {
                el.remove();
                const remaining = document.querySelectorAll('[id^="fav-"]').length;
                document.querySelector('.text-muted.small').textContent = remaining + ' negara tersimpan';
                if (remaining === 0) location.reload();
            }, 300);
        } else {
            alert('Gagal menghapus.');
        }
    } catch {
        alert('Koneksi gagal.');
    }
}
</script>
@endpush
