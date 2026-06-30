@extends('layouts.app')
@section('title', 'Peta Interaktif')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #worldMap { height: calc(100vh - 200px); border-radius: var(--radius); box-shadow: var(--card-shadow); z-index: 1; }
    .map-loading {
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        display: flex; align-items: center; justify-content: center;
        background: #F8FAFC; z-index: 1000; border-radius: var(--radius);
        flex-direction: column; gap: 12px;
    }
    .country-popup img { width: 40px; height: 26px; object-fit: cover; border-radius: 4px; }
    .country-popup h6 { margin: 4px 0 2px; font-weight: 700; color: #0F2B4B; }
    .country-popup .info { font-size: .78rem; color: #64748B; }
    .leaflet-popup-content-wrapper { border-radius: 10px !important; }
    .leaflet-popup-content { margin: 12px 14px !important; min-width: 200px; }
    .search-map-box {
        position: absolute; top: 20px; right: 20px; z-index: 1000;
        width: 320px; background: #fff; border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,.15); padding: 14px;
    }
    .search-map-box h6 { font-weight: 700; color: #0F2B4B; margin-bottom: 8px; }
    .region-legend {
        position: absolute; bottom: 30px; right: 20px; z-index: 1000;
        background: rgba(255,255,255,.95); border-radius: 10px;
        padding: 12px 16px; box-shadow: 0 2px 10px rgba(0,0,0,.1);
        font-size: .78rem; max-width: 180px;
    }
    .region-legend .item { display: flex; align-items: center; gap: 8px; margin: 3px 0; }
    .region-legend .color-box { width: 14px; height: 14px; border-radius: 3px; }
    .info-panel {
        position: absolute; bottom: 30px; left: 20px; z-index: 1000;
        background: rgba(255,255,255,.95); border-radius: 10px;
        padding: 10px 16px; box-shadow: 0 2px 10px rgba(0,0,0,.1);
        font-size: .78rem; color: #64748B;
    }

    @media(max-width:767px){
        #worldMap { height: calc(100vh - 180px); border-radius: 0; }
        .search-map-box { top: 10px; right: 10px; left: 10px; width: auto; }
        .region-legend { display: none; }
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-0" style="color:var(--primary);">🗺️ Peta Interaktif Dunia</h4>
        <p class="text-muted small mb-0">Klik negara di peta untuk lihat profil lengkapnya</p>
    </div>
</div>

<div style="position:relative;">
    <div class="map-loading" id="mapLoading">
        <div class="spinner-border" style="width:3rem;height:3rem;color:var(--primary);"></div>
        <div class="text-muted">Memuat peta dunia...</div>
    </div>

    <div id="worldMap"></div>

    <div class="search-map-box">
        <h6><i class="bi bi-search"></i> Cari Negara</h6>
        <div class="input-group input-group-sm">
            <input type="text" id="mapSearch" class="form-control" placeholder="Ketik nama negara..."
                   style="border-radius:6px 0 0 6px;">
            <button class="btn btn-primary" style="border-radius:0 6px 6px 0;" onclick="flyToCountry()">
                <i class="bi bi-send"></i>
            </button>
        </div>
        <div id="mapSearchResults" class="mt-1" style="max-height:150px;overflow-y:auto;"></div>
    </div>

    <div class="region-legend" id="regionLegend">
        <div class="fw-semibold mb-1" style="color:#0F2B4B;font-size:.82rem;">Wilayah</div>
        <div class="item"><span class="color-box" style="background:#3B82F6;"></span> Asia</div>
        <div class="item"><span class="color-box" style="background:#22C55E;"></span> Afrika</div>
        <div class="item"><span class="color-box" style="background:#F59E0B;"></span> Eropa</div>
        <div class="item"><span class="color-box" style="background:#EF4444;"></span> Amerika</div>
        <div class="item"><span class="color-box" style="background:#8B5CF6;"></span> Oseania</div>
        <div class="item"><span class="color-box" style="background:#EC4899;"></span> Lainnya</div>
    </div>

    <div class="info-panel" id="countryCount">
        Memuat data...
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/topojson-client@3/+esm"></script>
<script>
const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
const regionColors = {
    'Asia': '#3B82F6', 'Africa': '#22C55E', 'Europe': '#F59E0B',
    'Americas': '#EF4444', 'Oceania': '#8B5CF6',
};
const defaultColor = '#EC4899';

const map = L.map('worldMap', {
    center: [20, 0], zoom: 2,
    minZoom: 2, maxZoom: 6,
    worldCopyJump: true,
});

L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    maxZoom: 19, attribution: '&copy; OpenStreetMap &copy; CARTO'
}).addTo(map);

let geoLayer = null;
let countryData = {};
let allCountryNames = [];

async function loadMap() {
    try {
        const topoRes = await fetch('https://cdn.jsdelivr.net/npm/world-atlas@2/countries-110m.json');
        const topology = await topoRes.json();

        // Import topojson-client dynamically
        const { feature } = await import('https://cdn.jsdelivr.net/npm/topojson-client@3/+esm');
        const countries = feature(topology, topology.objects.countries);

        // Fetch country names and data from RestCountries API
        const apiRes = await fetch('{{ route("negara.search") }}?query=a', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const apiData = await apiRes.json();

        if (apiData.countries) {
            apiData.countries.forEach(c => {
                countryData[c.name.toLowerCase()] = c;
            });
        }

        geoLayer = L.geoJSON(countries, {
            style: function(feature) {
                const name = feature.properties.name || '';
                return {
                    fillColor: getRegionColor(name),
                    weight: .8,
                    opacity: 1,
                    color: '#fff',
                    fillOpacity: .7,
                };
            },
            onEachFeature: function(feature, layer) {
                const name = feature.properties.name || 'Unknown';
                allCountryNames.push(name);

                layer.on('click', function() {
                    showCountryPopup(name, layer);
                });

                layer.on('mouseover', function() {
                    this.setStyle({ fillOpacity: .9, weight: 1.5 });
                    this.bindTooltip(name, { sticky: true, direction: 'top' }).openTooltip();
                });

                layer.on('mouseout', function() {
                    this.setStyle({ fillOpacity: .7, weight: .8 });
                });
            }
        }).addTo(map);

        document.getElementById('countryCount').innerHTML =
            `<i class="bi bi-globe2"></i> ${countries.features.length} negara`;

        document.getElementById('mapLoading').style.display = 'none';

        // Populate search datalist
        setupSearch();

    } catch (err) {
        document.getElementById('mapLoading').innerHTML = `
            <div style="text-align:center;color:#DC2626;">
                <div style="font-size:3rem;margin-bottom:8px;">😵</div>
                <div class="fw-semibold">Gagal memuat peta</div>
                <div class="small text-muted">${err.message}</div>
                <button class="btn btn-primary btn-sm mt-2" onclick="location.reload()">Coba Lagi</button>
            </div>
        `;
    }
}

function getRegionColor(countryName) {
    const data = countryData[countryName.toLowerCase()];
    if (data && regionColors[data.region]) return regionColors[data.region];
    // Guess region from common names
    const asia = ['china','japan','india','indonesia','korea','thailand','vietnam','malaysia','philippines','myanmar','bangladesh','pakistan','sri lanka','nepal','cambodia','laos','mongolia','taiwan','singapore','brunei','timor','maldives','bhutan','afghanistan','iran','iraq','yemen','oman','uae','qatar','bahrain','kuwait','jordan','lebanon','syria','israel','palestine','saudi','turkey','azerbaijan','georgia','armenia','kazakhstan','uzbekistan','turkmenistan','kyrgyzstan','tajikistan'];
    const africa = ['algeria','angola','benin','botswana','burkina','burundi','cameroon','cape verde','central african','chad','comoros','congo','djibouti','egypt','equatorial guinea','eritrea','ethiopia','gabon','gambia','ghana','guinea','kenya','lesotho','liberia','libya','madagascar','malawi','mali','mauritania','mauritius','morocco','mozambique','namibia','niger','nigeria','rwanda','sao tome','senegal','seychelles','sierra leone','somalia','south africa','south sudan','sudan','swaziland','tanzania','togo','tunisia','uganda','zambia','zimbabwe'];
    const europe = ['albania','andorra','austria','belarus','belgium','bosnia','bulgaria','croatia','cyprus','czech','denmark','estonia','finland','france','germany','greece','hungary','iceland','ireland','italy','latvia','liechtenstein','lithuania','luxembourg','malta','moldova','monaco','montenegro','netherlands','north macedonia','norway','poland','portugal','romania','russia','san marino','serbia','slovakia','slovenia','spain','sweden','switzerland','ukraine','united kingdom','vatican'];
    const americas = ['argentina','bahamas','barbados','belize','bolivia','brazil','canada','chile','colombia','costa rica','cuba','dominica','dominican republic','ecuador','el salvador','grenada','guatemala','guyana','haiti','honduras','jamaica','mexico','nicaragua','panama','paraguay','peru','suriname','trinidad','united states','uruguay','venezuela'];
    const oceania = ['australia','fiji','kiribati','marshall islands','micronesia','nauru','new zealand','palau','papua new guinea','samoa','solomon islands','tonga','tuvalu','vanuatu'];

    const lower = countryName.toLowerCase();
    if (asia.some(c => lower.includes(c))) return regionColors['Asia'];
    if (africa.some(c => lower.includes(c))) return regionColors['Africa'];
    if (europe.some(c => lower.includes(c))) return regionColors['Europe'];
    if (americas.some(c => lower.includes(c))) return regionColors['Americas'];
    if (oceania.some(c => lower.includes(c))) return regionColors['Oceania'];
    return defaultColor;
}

function showCountryPopup(name, layer) {
    const data = countryData[name.toLowerCase()];
    const bounds = layer.getBounds();
    map.fitBounds(bounds, { padding: [30, 30], maxZoom: 5 });

    let content = `
        <div class="country-popup">
            <h6>🌍 ${name}</h6>
    `;

    if (data) {
        content += `
            <img src="${data.flag_png}" alt="${name}" onerror="this.style.display='none'">
            <div class="info">Ibu Kota: ${data.capital}</div>
            <div class="info">Populasi: ${data.pop_format}</div>
            <div class="info">Wilayah: ${data.region}</div>
            <a href="{{ route('negara.index') }}?q=${encodeURIComponent(name)}" class="btn btn-sm btn-primary mt-2 w-100" target="_blank">
                <i class="bi bi-box-arrow-up-right"></i> Lihat Detail
            </a>
        </div>`;
    } else {
        content += `
            <div class="info text-muted">Klik untuk cari detail negara ini</div>
            <a href="{{ route('negara.index') }}?q=${encodeURIComponent(name)}" class="btn btn-sm btn-primary mt-2 w-100" target="_blank">
                <i class="bi bi-search"></i> Cari di NegaraPedia
            </a>
        </div>`;
    }

    layer.bindPopup(content).openPopup();
}

function setupSearch() {
    const input = document.getElementById('mapSearch');
    const results = document.getElementById('mapSearchResults');

    input.addEventListener('input', function() {
        const q = this.value.trim().toLowerCase();
        if (!q || q.length < 2) { results.innerHTML = ''; return; }

        const matches = allCountryNames
            .filter(n => n.toLowerCase().includes(q))
            .slice(0, 8);

        if (!matches.length) {
            results.innerHTML = '<div class="text-muted small px-2 py-1">Negara tidak ditemukan</div>';
            return;
        }

        results.innerHTML = matches.map(n => `
            <div class="px-2 py-1 small" style="cursor:pointer;border-radius:4px;"
                 onmouseover="this.style.background='#F1F5F9'"
                 onmouseout="this.style.background=''"
                 onclick="selectCountry('${n.replace(/'/g, "\\'")}')">
                🌍 ${n}
            </div>
        `).join('');
    });
}

function selectCountry(name) {
    document.getElementById('mapSearch').value = name;
    document.getElementById('mapSearchResults').innerHTML = '';
    flyToCountry();
}

function flyToCountry() {
    const name = document.getElementById('mapSearch').value.trim();
    if (!name || !geoLayer) return;

    let found = null;
    geoLayer.eachLayer(function(layer) {
        if (layer.feature && layer.feature.properties.name.toLowerCase() === name.toLowerCase()) {
            found = layer;
        }
    });

    if (found) {
        showCountryPopup(name, found);
        map.fitBounds(found.getBounds(), { padding: [30, 30], maxZoom: 5 });
    } else {
        // Try searching via API
        showCountryPopup(name, L.circleMarker([0,0]));
        const bounds = [[-30, -180], [30, 180]];
        map.fitBounds(bounds);
    }
}

loadMap();
</script>
@endpush
