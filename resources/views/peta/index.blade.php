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
    .country-popup { text-align: center; }
    .country-popup h6 { margin: 0 0 8px; font-weight: 700; color: #0F2B4B; }
    .leaflet-popup-content-wrapper { border-radius: 10px !important; background: #fff !important; }
    .leaflet-popup-content { margin: 14px 16px !important; min-width: 180px; }
    .leaflet-popup-tip { background: #fff !important; }
    .leaflet-tooltip { background: #fff !important; color: #0F2B4B !important; border: 1px solid #E2E8F0 !important; box-shadow: 0 2px 8px rgba(0,0,0,.1) !important; }
    .search-map-box {
        position: absolute; top: 20px; right: 20px; z-index: 1000;
        width: 320px; background: #fff; border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,.15); padding: 14px;
    }
    .search-map-box h6 { font-weight: 700; color: #0F2B4B; margin-bottom: 8px; }
    .info-panel {
        position: absolute; bottom: 30px; left: 20px; z-index: 1000;
        background: rgba(255,255,255,.95); border-radius: 10px;
        padding: 10px 16px; box-shadow: 0 2px 10px rgba(0,0,0,.1);
        font-size: .78rem; color: #64748B;
    }
    @media(max-width:767px){
        #worldMap { height: calc(100vh - 180px); border-radius: 0; }
        .search-map-box { top: 10px; right: 10px; left: 10px; width: auto; }
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-0" style="color:var(--primary);">🗺️ Peta Interaktif Dunia</h4>
        <p class="text-muted small mb-0">Klik negara di peta untuk cari informasinya</p>
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
            <input type="text" id="mapSearch" class="form-control" placeholder="Contoh: Indonesia"
                   style="border-radius:6px 0 0 6px;">
            <button class="btn btn-primary" style="border-radius:0 6px 6px 0;" onclick="flyToCountry()">
                <i class="bi bi-send-fill"></i>
            </button>
        </div>
        <div id="mapSearchResults" class="mt-1" style="max-height:150px;overflow-y:auto;"></div>
    </div>

    <div class="info-panel" id="countryCount">
        Memuat data...
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/topojson-client@3"></script>
<script>
var geoLayer = null;
var countryNames = [];

function initMap() {
    var map = L.map('worldMap', {
        center: [20, 30],
        zoom: 2, minZoom: 2, maxZoom: 8,
        zoomSnap: .5, zoomDelta: .5,
        wheelPxPerZoomLevel: 120,
        maxBounds: [[-90, -180], [90, 180]],
        maxBoundsViscosity: 1,
    });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        maxZoom: 19, noWrap: true,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>'
    }).addTo(map);

    loadMapData(map).then(function() {
        setTimeout(function() { map.invalidateSize(); }, 100);
    });

    setupSearch();

    window.flyToCountry = function() {
        var name = document.getElementById('mapSearch').value.trim();
        if (!name || !geoLayer) return;

        var found = null;
        geoLayer.eachLayer(function(layer) {
            if (layer.feature && layer.feature.properties.name.toLowerCase() === name.toLowerCase()) {
                found = layer;
            }
        });

        if (found) {
            showCountryPopup(name, found, map);
        } else {
            alert('Negara tidak ditemukan di peta. Coba nama lain.');
        }
    };
}

async function loadMapData(map) {
    try {
        var topoRes = await fetch('https://cdn.jsdelivr.net/npm/world-atlas@2/countries-110m.json');
        if (!topoRes.ok) throw new Error('Gagal memuat data peta');
        var topology = await topoRes.json();
        var countries = topojson.feature(topology, topology.objects.countries);

        countryNames = countries.features.map(function(f) { return f.properties.name || ''; }).filter(Boolean);

        geoLayer = L.geoJSON(countries, {
            style: {
                fillColor: '#CBD5E1',
                weight: .6, opacity: 1, color: '#fff', fillOpacity: .85,
            },
            onEachFeature: function(feature, layer) {
                var name = feature.properties.name || 'Unknown';
                layer.on({
                    click: function() { showCountryPopup(name, this, map); },
                    mouseover: function() {
                        this.setStyle({ fillOpacity: 1, weight: 1.2 });
                        this.bindTooltip(name, { sticky: true, direction: 'top' }).openTooltip();
                    },
                    mouseout: function() {
                        this.setStyle({ fillOpacity: .85, weight: .6 });
                    }
                });
            }
        }).addTo(map);

        document.getElementById('countryCount').innerHTML =
            '<i class="bi bi-globe2"></i> ' + countries.features.length + ' negara';
        document.getElementById('mapLoading').style.display = 'none';

    } catch (err) {
        document.getElementById('mapLoading').innerHTML =
            '<div style="text-align:center;color:#DC2626;max-width:300px;">' +
            '<div style="font-size:3rem;margin-bottom:8px;">😵</div>' +
            '<div class="fw-semibold mb-1">Gagal memuat peta</div>' +
            '<div class="small" style="color:#64748B;">' + err.message + '</div>' +
            '<button class="btn btn-primary btn-sm mt-2" onclick="location.reload()">' +
            '<i class="bi bi-arrow-clockwise"></i> Coba Lagi</button>' +
            '</div>';
    }
}

function showCountryPopup(name, layer, map) {
    var bounds = layer.getBounds();
    var sw = bounds.getSouthWest();
    var ne = bounds.getNorthEast();
    var clamped = L.latLngBounds(
        L.latLng(Math.max(-90, sw.lat), Math.max(-180, sw.lng)),
        L.latLng(Math.min(90, ne.lat), Math.min(180, ne.lng))
    );
    map.fitBounds(clamped, { padding: [30, 30], maxZoom: 5 });

    map.closePopup();
    var content =
        '<div class="country-popup">' +
        '<h6>🌍 ' + name + '</h6>' +
        '<a href="{{ route('negara.index') }}?q=' + encodeURIComponent(name) +
        '" class="btn btn-sm btn-primary" target="_blank">' +
        '<i class="bi bi-search"></i> Cari</a></div>';
    L.popup({ closeButton: false, className: '' })
        .setLatLng(layer.getBounds().getCenter())
        .setContent(content)
        .openOn(map);
}

function setupSearch() {
    var input = document.getElementById('mapSearch');
    var results = document.getElementById('mapSearchResults');

    input.addEventListener('input', function() {
        var q = this.value.trim().toLowerCase();
        if (!q || q.length < 2) { results.innerHTML = ''; return; }

        var matches = countryNames
            .filter(function(n) { return n.toLowerCase().includes(q); })
            .slice(0, 8);

        if (!matches.length) {
            results.innerHTML = '<div class="text-muted small px-2 py-1">Negara tidak ditemukan</div>';
            return;
        }

        results.innerHTML = matches.map(function(n) {
            var safe = n.replace(/'/g, "\\'");
            return '<div class="px-2 py-1 small" style="cursor:pointer;border-radius:4px;"' +
                ' onmouseover="this.style.background=\'#F1F5F9\'"' +
                ' onmouseout="this.style.background=\'\'"' +
                ' onclick="selectCountry(\'' + safe + '\')">🌍 ' + n + '</div>';
        }).join('');
    });
}

function selectCountry(name) {
    document.getElementById('mapSearch').value = name;
    document.getElementById('mapSearchResults').innerHTML = '';
    flyToCountry();
}

initMap();
</script>
@endpush
