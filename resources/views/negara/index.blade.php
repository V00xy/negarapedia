@extends('layouts.app')
@section('title', 'Cari Negara')

@push('styles')
<style>
    .country-card { transition: transform .25s, box-shadow .25s; cursor: pointer; }
    .country-card:hover { transform: translateY(-5px); box-shadow: var(--card-shadow-hover); }
    .flag-img { width: 100%; height: 140px; object-fit: cover; border-radius: 8px 8px 0 0; background: #eee; }
    #loadingSpinner { display: none; }
    .info-label { font-size: .7rem; color: #94A3B8; text-transform: uppercase; letter-spacing: .5px; font-weight: 600; }
    .info-value { font-weight: 600; font-size: .9rem; color: #1a2332; }
    .search-wrap {
        background: linear-gradient(135deg, #0F2B4B 0%, #1A3F6A 100%);
        border-radius: var(--radius); padding: 1.75rem; margin-bottom: 1.5rem;
    }
    .search-wrap .form-control {
        border: 2px solid transparent;
        border-radius: 10px 0 0 10px;
        font-size: 1rem;
    }
    .search-wrap .form-control:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(245,166,35,.15);
    }
    #detailModal .modal-content { border: none; border-radius: var(--radius); overflow: hidden; }
    #detailModal .modal-header {
        background: linear-gradient(135deg, #0F2B4B, #1A3F6A);
        border-bottom: none;
    }
    #detailModal .modal-header .btn-close { filter: brightness(0) invert(1); }
    #detailModal .table td { border: none; padding: .4rem .75rem; }
</style>
@endpush

@section('content')
<h4 class="fw-bold mb-1" style="color:var(--primary);">🔍 Pencarian Negara</h4>
<p class="text-muted mb-3">Cari profil lengkap negara manapun di dunia menggunakan RestCountries API</p>

<div class="search-wrap">
    <div class="input-group input-group-lg">
        <input type="text" id="searchInput" class="form-control"
               placeholder="Ketik nama negara, contoh: Indonesia, Japan, Brazil...">
        <button class="btn btn-warning fw-bold px-4" id="btnSearch">
            <i class="bi bi-search"></i> Cari
        </button>
    </div>
    <div class="mt-2" style="color:rgba(255,255,255,.5);font-size:.8rem;">
        <i class="bi bi-lightbulb"></i> Contoh: Indonesia · Japan · Germany · Brazil · Egypt
    </div>
</div>

<div id="loadingSpinner" class="text-center py-5">
    <div class="spinner-border" style="width:3rem;height:3rem;color:var(--primary);"></div>
    <div class="mt-2 text-muted">Mengambil data dari RestCountries API...</div>
</div>

<div id="errorBox" class="alert alert-danger d-none"></div>

<div id="resultsContainer" class="row g-3"></div>

<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Detail Negara</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer d-flex gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id="btnSaveFav">
                    <i class="bi bi-heart"></i> Simpan ke Favorit
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let currentCountry = null;

document.getElementById('btnSearch').addEventListener('click', doSearch);
document.getElementById('searchInput').addEventListener('keydown', e => {
    if (e.key === 'Enter') doSearch();
});

async function doSearch() {
    const q = document.getElementById('searchInput').value.trim();
    if (!q) return;

    setLoading(true);
    document.getElementById('errorBox').classList.add('d-none');
    document.getElementById('resultsContainer').innerHTML = '';

    try {
        const res = await fetch(`{{ route('negara.search') }}?query=${encodeURIComponent(q)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        if (!res.ok) {
            showError(data.error || 'Terjadi kesalahan.');
            return;
        }

        renderResults(data.countries);
    } catch (err) {
        showError('Koneksi gagal. Periksa internet kamu.');
    } finally {
        setLoading(false);
    }
}

function renderResults(countries) {
    const container = document.getElementById('resultsContainer');
    if (!countries.length) {
        container.innerHTML = '<div class="col"><div class="alert alert-warning">Tidak ada hasil ditemukan.</div></div>';
        return;
    }

    container.innerHTML = countries.map(c => `
        <div class="col-md-4 col-sm-6">
            <div class="card country-card h-100" onclick='showDetail(${JSON.stringify(c).replace(/'/g,"&#39;")})'>
                <img src="${c.flag_png}" alt="Bendera ${c.name}" class="flag-img" onerror="this.src='https://via.placeholder.com/300x140?text=No+Flag'">
                <div class="card-body">
                    <h6 class="fw-bold mb-1">${c.name}</h6>
                    <span class="badge" style="background:#DBEAFE;color:#1E40AF;font-weight:500;">${c.region}</span>
                    <div class="row g-1 mt-2">
                        <div class="col-6">
                            <div class="info-label">Ibu Kota</div>
                            <div class="info-value small">${c.capital}</div>
                        </div>
                        <div class="col-6">
                            <div class="info-label">Populasi</div>
                            <div class="info-value small">${c.pop_format}</div>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="info-label">Mata Uang</div>
                        <div class="info-value small">${c.currencies}</div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function showDetail(c) {
    currentCountry = c;
    document.getElementById('modalTitle').textContent = `🌍 ${c.name}`;

    document.getElementById('modalBody').innerHTML = `
        <div class="row">
            <div class="col-md-5 text-center mb-3">
                <img src="${c.flag_png}" class="img-fluid rounded shadow-sm mb-3" style="max-height:180px;" alt="Bendera ${c.name}"
                     onerror="this.src='https://via.placeholder.com/300x180?text=No+Flag'">
            </div>
            <div class="col-md-7">
                <table class="table table-sm table-borderless">
                    <tr><td class="text-muted" width="40%">Nama Resmi</td><td><strong>${c.official}</strong></td></tr>
                    <tr><td class="text-muted">Ibu Kota</td><td>${c.capital}</td></tr>
                    <tr><td class="text-muted">Populasi</td><td>${c.pop_format} jiwa</td></tr>
                    <tr><td class="text-muted">Luas Wilayah</td><td>${c.area}</td></tr>
                    <tr><td class="text-muted">Wilayah</td><td>${c.region} — ${c.subregion}</td></tr>
                    <tr><td class="text-muted">Mata Uang</td><td>${c.currencies}</td></tr>
                    <tr><td class="text-muted">Bahasa</td><td>${c.languages}</td></tr>
                    <tr><td class="text-muted">Zona Waktu</td><td>${c.timezones}</td></tr>
                </table>
                <div class="d-flex gap-2 mt-2">
                    ${c.map_google ? `<a href="${c.map_google}" target="_blank" class="btn btn-sm btn-outline-danger"><i class="bi bi-geo-alt"></i> Google Maps</a>` : ''}
                    ${c.map_osm ? `<a href="${c.map_osm}" target="_blank" class="btn btn-sm btn-outline-info"><i class="bi bi-map"></i> OpenStreetMap</a>` : ''}
                </div>
            </div>
        </div>
    `;

    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
    modal.show();

    // Reset save button
    const btn = document.getElementById('btnSaveFav');
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-heart"></i> Simpan ke Favorit';
    btn.className = 'btn btn-danger';
}

document.getElementById('btnSaveFav')?.addEventListener('click', async () => {
    if (!currentCountry) return;
    const btn = document.getElementById('btnSaveFav');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';

    try {
        const res = await fetch('{{ route("negara.favorites.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                country_code: currentCountry.code,
                country_name: currentCountry.name,
                flag_url:     currentCountry.flag_png,
                capital:      currentCountry.capital,
                population:   currentCountry.population,
                currency:     currentCountry.currencies,
            })
        });
        const data = await res.json();
        if (res.ok) {
            btn.innerHTML = '✅ Tersimpan!';
            btn.className = 'btn btn-success';
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-heart"></i> Simpan ke Favorit';
            alert(data.error || 'Gagal menyimpan.');
        }
    } catch {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-heart"></i> Simpan ke Favorit';
        alert('Koneksi gagal.');
    }
});

function setLoading(show) {
    document.getElementById('loadingSpinner').style.display = show ? 'block' : 'none';
}
function showError(msg) {
    const el = document.getElementById('errorBox');
    el.textContent = msg;
    el.classList.remove('d-none');
}
</script>
@endpush
