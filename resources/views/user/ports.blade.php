@extends('layouts.app')

@section('title', 'Ports - RiskIntel')

@push('styles')
<!-- Leaflet MarkerCluster CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
<style>
    .table-responsive {
        max-height: 440px;
        overflow-y: auto;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card card-custom p-4 bg-white mb-4 border border-light-subtle shadow-sm">
        <h4 class="fw-bold text-slate-800 mb-1"><i class="bi bi-compass me-2"></i>Global Ports Tracking</h4>
        <p class="text-muted small mb-0">Visualisasi sebaran posisi pelabuhan laut utama dunia serta pemantauan kelancaran lalu lintas kontainer logistik.</p>
    </div>

    <!-- Filters Bar -->
    <div class="card card-custom p-3 bg-white mb-4 border border-light-subtle shadow-sm">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-light-subtle"><i class="bi bi-search text-secondary"></i></span>
                    <input type="text" id="search-input" class="form-control border-light-subtle" placeholder="Cari nama pelabuhan atau kode..." oninput="filterPorts()">
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-light-subtle"><i class="bi bi-flag text-secondary"></i></span>
                    <select id="country-filter" class="form-select border-light-subtle" onchange="filterPorts()">
                        <option value="">-- Semua Negara --</option>
                        @foreach($countries as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Map & Table -->
    <div class="row">
        <!-- Leaflet Map -->
        <div class="col-lg-7 mb-4">
            <div class="card card-custom p-4 bg-white h-100 border border-light-subtle shadow-sm">
                <h5 class="fw-bold text-slate-800 mb-3">Peta Posisi Pelabuhan</h5>
                <div id="ports-map" class="rounded border" style="height: 480px; background-color: #f1f5f9; z-index: 1;"></div>
            </div>
        </div>

        <!-- Port List Table -->
        <div class="col-lg-5 mb-4">
            <div class="card card-custom p-4 bg-white h-100 border border-light-subtle shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-slate-800 mb-0">Daftar Pelabuhan Aktif</h5>
                    <span id="active-count" class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1"></span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Pelabuhan</th>
                                <th>Negara</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populated via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet MarkerCluster JS -->
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<script>
    let map;
    let markerClusterGroup;
    let allPorts = [];
    let activeMarkers = {};

    document.addEventListener("DOMContentLoaded", function () {
        // Initialize Leaflet map
        map = L.map('ports-map').setView([10.0, 110.0], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Initialize Marker Cluster Group
        markerClusterGroup = L.markerClusterGroup();
        map.addLayer(markerClusterGroup);

        // Load seeded ports data from backend
        allPorts = {!! json_encode($ports) !!};

        // Render initially
        filterPorts();
    });

    function renderPortsOnMapAndTable(ports) {
        // Update active count badge
        document.getElementById('active-count').innerText = `${ports.length} Pelabuhan`;

        // Clear existing markers from map
        markerClusterGroup.clearLayers();
        activeMarkers = {};

        const bounds = [];

        // Repopulate map markers
        ports.forEach(port => {
            if (port.latitude && port.longitude) {
                const lat = parseFloat(port.latitude);
                const lon = parseFloat(port.longitude);

                const marker = L.marker([lat, lon]);
                const popupContent = `
                    <div style="font-family: 'Outfit', sans-serif; min-width: 160px; line-height: 1.4;">
                        <h6 style="margin: 0 0 5px; font-weight: bold; color: #1e293b; font-size: 1.05rem;">
                            <i class="bi bi-anchor text-primary me-1"></i>${port.name}
                        </h6>
                        <hr style="margin: 4px 0; border-color: #cbd5e1;">
                        <span style="display: block; font-size: 0.85em; color: #475569;"><b>Kode:</b> ${port.code ?? 'N/A'}</span>
                        <span style="display: block; font-size: 0.85em; color: #475569;"><b>Negara:</b> ${port.country ? port.country.name : 'N/A'}</span>
                        <span style="display: block; font-size: 0.85em; color: #475569;"><b>Koordinat:</b> ${lat}, ${lon}</span>
                    </div>
                `;
                marker.bindPopup(popupContent);
                markerClusterGroup.addLayer(marker);

                // Store reference
                activeMarkers[port.id] = marker;
                bounds.push([lat, lon]);
            }
        });

        // Fit map bounds to show all active markers nicely
        if (bounds.length > 0 && map) {
            map.fitBounds(bounds, { padding: [40, 40] });
        }

        // Repopulate sidebar table
        const tbody = document.querySelector('table tbody');
        tbody.innerHTML = '';

        if (ports.length === 0) {
            tbody.innerHTML = `<tr><td colspan="3" class="text-center text-muted py-4">Belum ada pelabuhan yang terdaftar atau cocok.</td></tr>`;
            return;
        }

        ports.forEach(port => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <span class="fw-bold text-slate-855 d-block">${port.name}</span>
                    <span class="text-muted small">Kode: ${port.code ?? 'N/A'}</span>
                </td>
                <td>
                    <span class="badge bg-secondary">${port.country ? port.country.name : 'N/A'}</span>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-info fw-bold" 
                            onclick="focusPort(${port.id}, ${port.latitude}, ${port.longitude}, '${port.name}')"
                            title="Fokus Lokasi">
                        <i class="bi bi-geo-alt"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function filterPorts() {
        const searchText = document.getElementById('search-input').value.toLowerCase();
        const countryId = document.getElementById('country-filter').value;

        const filteredPorts = allPorts.filter(port => {
            const matchesSearch = port.name.toLowerCase().includes(searchText) || 
                                  (port.code && port.code.toLowerCase().includes(searchText));
            const matchesCountry = !countryId || (port.country_id && port.country_id.toString() === countryId);

            return matchesSearch && matchesCountry;
        });

        renderPortsOnMapAndTable(filteredPorts);
    }

    function focusPort(portId, lat, lon, name) {
        if (map && markerClusterGroup) {
            const marker = activeMarkers[portId];
            if (marker) {
                markerClusterGroup.zoomToShowLayer(marker, function () {
                    marker.openPopup();
                });
            } else {
                map.setView([lat, lon], 12);
                L.popup()
                    .setLatLng([lat, lon])
                    .setContent(`<div style="font-family: 'Outfit', sans-serif; font-weight: bold;">${name}</div>`)
                    .openOn(map);
            }
        }
    }
</script>
@endpush
