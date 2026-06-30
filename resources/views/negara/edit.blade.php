@extends('layouts.app')
@section('title', 'Edit Favorit')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-pencil"></i> Edit Favorit — {{ $favorite->country_name }}
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <img src="{{ $favorite->flag_url }}" alt="{{ $favorite->country_name }}"
                         style="height:80px;border-radius:8px;border:2px solid #E2E8F0;"
                         onerror="this.src='https://via.placeholder.com/120x80?text=No+Flag'">
                    <div class="fw-bold mt-2" style="color:var(--primary);">{{ $favorite->country_name }}</div>
                </div>

                <form method="POST" action="{{ route('negara.favorites.update', $favorite->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase" style="color:#64748B;">Ibu Kota</label>
                        <input type="text" name="capital" class="form-control @error('capital') is-invalid @enderror"
                               value="{{ old('capital', $favorite->capital) }}" placeholder="Nama ibu kota">
                        @error('capital')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase" style="color:#64748B;">Populasi</label>
                        <input type="number" name="population" class="form-control @error('population') is-invalid @enderror"
                               value="{{ old('population', $favorite->population) }}" min="0" placeholder="Jumlah penduduk">
                        @error('population')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-uppercase" style="color:#64748B;">Mata Uang</label>
                        <input type="text" name="currency" class="form-control @error('currency') is-invalid @enderror"
                               value="{{ old('currency', $favorite->currency) }}" placeholder="Contoh: Rupiah (Rp)">
                        @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-check-lg"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('negara.favorites') }}" class="btn btn-outline-secondary flex-fill">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
