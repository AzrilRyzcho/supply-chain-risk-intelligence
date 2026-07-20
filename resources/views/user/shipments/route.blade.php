@extends('layouts.app')

@section('title', 'Live Vessel Tracking - ' . $shipment->shipment_number)

@section('content')
<div class="container-fluid py-4" style="background: linear-gradient(135deg, #f8fafc, #f1f5f9); min-height: calc(100vh - 60px);">
    <!-- Header/Breadcrumb -->
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('user.shipments.index') }}" class="text-decoration-none small fw-semibold text-primary">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Shipment
            </a>
            <h3 class="fw-bold text-slate-800 mt-2 mb-0">Live Vessel Tracking - #{{ $shipment->shipment_number }}</h3>
        </div>
        <span class="badge bg-white text-slate-700 border border-light-subtle shadow-sm px-3 py-2 rounded-pill font-monospace" style="font-size: 0.85rem;">
            ID: {{ $shipment->shipment_number }}
        </span>
    </div>

    <!-- Main Live Position & Vessel Instruments Layout -->
    <div class="row">
        <!-- Left Panel: Vessel Instruments -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm p-4 rounded-4" style="background: linear-gradient(135deg, #fff1f2, #faf5ff); height: 100%;">
                <h4 class="fw-bold text-slate-800 mb-4" style="font-size: 1.25rem;">Vessel Instruments</h4>

                <div class="d-flex flex-column gap-3">
                    <!-- SPEED Card -->
                    <div class="card border-0 bg-white p-3 rounded-3 shadow-xs">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">Speed</span>
                                <h3 class="fw-bold text-slate-800 mt-1 mb-0" id="instrument-speed">-</h3>
                            </div>
                            <div class="text-primary bg-primary-subtle rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-speedometer2" style="font-size: 1.2rem;"></i>
                            </div>
                        </div>
                    </div>

                    <!-- HEADING Card -->
                    <div class="card border-0 bg-white p-3 rounded-3 shadow-xs">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">Heading</span>
                                <h3 class="fw-bold text-slate-800 mt-1 mb-0" id="instrument-heading">-</h3>
                            </div>
                            <div class="text-info bg-info-subtle rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" id="heading-icon-parent">
                                <i class="bi bi-compass" style="font-size: 1.2rem; display: block; transition: transform 0.1s linear;" id="heading-compass-icon"></i>
                            </div>
                        </div>
                    </div>

                    <!-- STATUS Card -->
                    <div class="card border-0 bg-white p-3 rounded-3 shadow-xs">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">Status</span>
                                <h5 class="fw-bold text-slate-800 mt-1 mb-0" id="instrument-status">-</h5>
                            </div>
                            <div class="text-success bg-success-subtle rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-arrow-left-right" style="font-size: 1.2rem;"></i>
                            </div>
                        </div>
                    </div>

                    <!-- LIVE COORDINATES Section -->
                    <div class="mt-3">
                        <span class="text-uppercase text-muted fw-bold d-block mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Live Coordinates</span>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="bg-white p-3 rounded-3 border text-center shadow-xs">
                                    <span class="text-muted d-block small" style="font-size: 0.72rem;">LAT</span>
                                    <span class="fw-bold text-slate-800 font-monospace text-truncate d-block" id="instrument-lat">-</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-white p-3 rounded-3 border text-center shadow-xs">
                                    <span class="text-muted d-block small" style="font-size: 0.72rem;">LNG</span>
                                    <span class="fw-bold text-slate-800 font-monospace text-truncate d-block" id="instrument-lng">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- JOURNEY PROGRESS Section -->
                    <div class="mt-4 p-3 bg-white rounded-3 shadow-xs border border-light-subtle">
                        <span class="text-uppercase text-muted fw-bold d-block mb-2.5" style="font-size: 0.72rem; letter-spacing: 0.5px;"><i class="bi bi-compass-fill text-primary me-1"></i>Journey Progress</span>
                        
                        <!-- Animated Progress Bar -->
                        <div class="progress mb-3 bg-light" style="height: 10px; border-radius: 5px;">
                            <div id="progress-bar-fill" class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%; border-radius: 5px;"></div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Progress Perjalanan</span>
                            <span id="progress-percent" class="fw-bold text-slate-800 font-monospace" style="font-size: 0.85rem;">0.0%</span>
                        </div>
                        
                        <div class="row g-2 mt-1">
                            <div class="col-6">
                                <span class="text-muted d-block" style="font-size: 0.65rem; text-transform: uppercase;">Jarak Tempuh</span>
                                <span id="progress-traveled" class="fw-bold text-slate-800 font-monospace" style="font-size: 0.85rem;">-</span>
                            </div>
                            <div class="col-6 text-end">
                                <span class="text-muted d-block" style="font-size: 0.65rem; text-transform: uppercase;">Sisa Jarak</span>
                                <span id="progress-remaining" class="fw-bold text-slate-800 font-monospace" style="font-size: 0.85rem;">-</span>
                            </div>
                        </div>
                        
                        <hr class="my-2 text-slate-200">
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Estimasi Tiba (ETA)</span>
                            <span id="progress-eta" class="fw-bold text-primary font-monospace" style="font-size: 0.82rem;">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Live Position (Map Container) -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white" style="height: 100%;">
                <!-- Live Position Header -->
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="fw-bold text-slate-800 mb-0">Live Position</h5>
                    <div class="d-flex align-items-center gap-2">
                        <button id="follow-vessel-btn" class="btn btn-primary btn-sm rounded-pill px-3 d-flex align-items-center gap-1.5 shadow-sm">
                            <i class="bi bi-cursor-fill"></i> Follow Vessel
                        </button>
                        <button id="fullscreen-map-btn" class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center p-2" style="width: 32px; height: 32px;">
                            <i class="bi bi-arrows-angle-expand text-slate-600"></i>
                        </button>
                    </div>
                </div>

                <!-- Leaflet Map Wrapper -->
                <div style="position: relative; height: 450px;">
                    <!-- Loading Overlay -->
                    <div id="map-loader" class="d-flex flex-column justify-content-center align-items-center bg-white" 
                         style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1000; opacity: 0.9;">
                         <div class="spinner-border text-primary mb-2" role="status">
                             <span class="visually-hidden">Loading...</span>
                         </div>
                         <span class="fw-semibold text-secondary">Menghitung rute pelayaran...</span>
                    </div>

                    <!-- Actual Map Container -->
                    <div id="route-map" style="height: 100%; width: 100%; z-index: 1;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Journey Details & Sea Route Corridor -->
    <div class="row">
        <!-- Journey Summary -->
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                <h5 class="fw-bold text-slate-800 mb-3"><i class="bi bi-signpost-2 text-primary me-2"></i>Detail Perjalanan</h5>
                <div class="d-flex flex-column gap-3" id="journey-details">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;background:#dcfce7;">
                            <i class="bi bi-geo-alt-fill text-success"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block" style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.5px;">Pelabuhan Asal</span>
                            <span class="fw-bold text-slate-800 d-block" id="detail-origin-name">-</span>
                            <span class="text-muted small" id="detail-origin-country">-</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;background:#fee2e2;">
                            <i class="bi bi-geo-alt-fill text-danger"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block" style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.5px;">Pelabuhan Tujuan</span>
                            <span class="fw-bold text-slate-800 d-block" id="detail-dest-name">-</span>
                            <span class="text-muted small" id="detail-dest-country">-</span>
                        </div>
                    </div>
                    <hr class="my-1">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <span class="text-muted d-block" style="font-size:0.68rem;text-transform:uppercase;">Total Jarak</span>
                                <span class="fw-bold text-slate-800 d-block" id="detail-distance" style="font-size:1.1rem;">-</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <span class="text-muted d-block" style="font-size:0.68rem;text-transform:uppercase;">Estimasi Durasi</span>
                                <span class="fw-bold text-slate-800 d-block" id="detail-duration" style="font-size:1.1rem;">-</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <span class="text-muted d-block" style="font-size:0.68rem;text-transform:uppercase;">Kecepatan Kapal</span>
                                <span class="fw-bold text-slate-800 d-block" id="detail-speed" style="font-size:1.1rem;">-</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <span class="text-muted d-block" style="font-size:0.68rem;text-transform:uppercase;">Mode Transportasi</span>
                                <span class="fw-bold text-slate-800 d-block" style="font-size:1.1rem;"><i class="bi bi-water text-primary me-1"></i>Sea Freight</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sea Route Corridor (Waypoints List) -->
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                <h5 class="fw-bold text-slate-800 mb-3"><i class="bi bi-tsunami text-primary me-2"></i>Sea Route Corridor</h5>
                <div id="corridor-list" style="max-height: 350px; overflow-y: auto; padding-right: 4px;">
                    <div class="text-center text-muted py-4">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        Memuat koridor pelayaran...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .shadow-xs {
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    body.dark-theme .bg-white {
        background-color: #1e293b !important;
    }
    body.dark-theme .text-slate-800 {
        color: #f1f5f9 !important;
    }
    body.dark-theme .card {
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    .corridor-step {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        position: relative;
        padding-bottom: 12px;
        padding-top: 4px;
        padding-left: 8px;
        padding-right: 8px;
        border-radius: 8px;
        transition: background-color 0.2s ease;
    }
    .corridor-step:hover {
        background-color: rgba(37, 99, 235, 0.05);
    }
    body.dark-theme .corridor-step:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }
    .corridor-step:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 23px;
        top: 36px;
        bottom: 0;
        width: 2px;
        background: linear-gradient(180deg, #93c5fd, #dbeafe);
    }
    .corridor-dot {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 700;
        flex-shrink: 0;
        z-index: 1;
        transition: transform 0.2s ease;
    }
    .corridor-step:hover .corridor-dot {
        transform: scale(1.1);
    }
    .corridor-dot.port { background: #2563eb; color: #fff; }
    .corridor-dot.waypoint { background: #dbeafe; color: #2563eb; border: 2px solid #93c5fd; }
    .corridor-name { font-weight: 600; font-size: 0.85rem; color: #1e293b; }
    body.dark-theme .corridor-name { color: #f1f5f9; }
    .corridor-region { font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.3px; }
    
    /* Glowing custom waypoint markers */
    .waypoint-map-marker {
        background: #3b82f6;
        border: 2px solid #ffffff;
        border-radius: 50%;
        box-shadow: 0 0 8px rgba(37, 99, 235, 0.9);
        width: 12px;
        height: 12px;
        transition: transform 0.2s ease;
    }
    .waypoint-map-marker:hover {
        transform: scale(1.3);
        background: #2563eb;
    }

    /* Radar pulsing halo under the ship */
    .animated-ship-marker {
        position: relative;
    }
    .animated-ship-marker::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 64px;
        height: 64px;
        margin-left: -32px;
        margin-top: -32px;
        border-radius: 50%;
        background: rgba(16, 185, 129, 0.25);
        border: 1px solid rgba(16, 185, 129, 0.55);
        animation: shipPulse 2s infinite ease-out;
        pointer-events: none;
        z-index: -1;
    }
    @keyframes shipPulse {
        0% {
            transform: scale(0.5);
            opacity: 1;
        }
        100% {
            transform: scale(1.4);
            opacity: 0;
        }
    }

    /* Risk Event Markers styling */
    .risk-marker-storm {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #ef4444;
        color: #ffffff;
        border: 2px solid #ffffff;
        border-radius: 50%;
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.85);
        cursor: pointer;
        animation: pulseStorm 1.8s infinite ease-in-out;
    }
    .risk-marker-congestion {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f97316;
        color: #ffffff;
        border: 2px solid #ffffff;
        border-radius: 50%;
        box-shadow: 0 0 10px rgba(249, 115, 22, 0.85);
        cursor: pointer;
    }
    .risk-marker-conflict {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eab308;
        color: #0f172a;
        border: 2px solid #0f172a;
        border-radius: 50%;
        box-shadow: 0 0 10px rgba(234, 179, 8, 0.85);
        cursor: pointer;
        animation: pulseConflict 2s infinite ease-in-out;
    }

    @keyframes pulseStorm {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.6); }
        70% { box-shadow: 0 0 0 12px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
    @keyframes pulseConflict {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Initialize Map
    const map = L.map('route-map', {
        minZoom: 3,
        maxZoom: 18,
        worldCopyJump: true
    }).setView([10, 110], 4);

    // 2. Add OpenStreetMap tile layer
    const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 18,
    }).addTo(map);

    // Dark Mode Map Tile Filter
    if (document.body.classList.contains('dark-theme')) {
        map.on('tileload', function() {
            document.querySelectorAll('.leaflet-tile-container img').forEach(img => {
                img.style.filter = 'invert(100%) hue-rotate(180deg) brightness(95%) contrast(90%)';
            });
        });
    }

    // 3. Define pin icons
    const originIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
        iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
    });

    const destIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
        iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
    });

    // Follow Vessel & Programmatic Movement state
    let followVessel = true;
    let isProgrammaticMove = false;
    let currentShipLatLng = null;
    const followBtn = document.getElementById('follow-vessel-btn');

    followBtn.addEventListener('click', function() {
        followVessel = !followVessel;
        if (followVessel) {
            followBtn.classList.remove('btn-outline-primary');
            followBtn.classList.add('btn-primary');
            followBtn.innerHTML = '<i class="bi bi-cursor-fill"></i> Follow Vessel';
            
            if (currentShipLatLng) {
                isProgrammaticMove = true;
                const newZoom = Math.max(map.getZoom(), 5);
                map.setView(currentShipLatLng, newZoom, { animate: false });
                isProgrammaticMove = false;
            }
        } else {
            followBtn.classList.remove('btn-primary');
            followBtn.classList.add('btn-outline-primary');
            followBtn.innerHTML = '<i class="bi bi-cursor"></i> Follow Vessel';
        }
    });

    // Disable follow vessel mode on user drag/zoom/move
    map.on('movestart', function() {
        if (!isProgrammaticMove) {
            followVessel = false;
            followBtn.classList.remove('btn-primary');
            followBtn.classList.add('btn-outline-primary');
            followBtn.innerHTML = '<i class="bi bi-cursor"></i> Follow Vessel';
        }
    });

    // Global interactive waypoint click handler
    window.focusCoordinate = function(lat, lng, name) {
        followVessel = false;
        followBtn.classList.remove('btn-primary');
        followBtn.classList.add('btn-outline-primary');
        followBtn.innerHTML = '<i class="bi bi-cursor"></i> Follow Vessel';

        isProgrammaticMove = true;
        map.setView([lat, lng], 6, { animate: true });
        isProgrammaticMove = false;

        L.popup()
            .setLatLng([lat, lng])
            .setContent(`<div style="font-family:'Outfit',sans-serif;font-size:0.85rem;padding:4px;"><strong>${name}</strong></div>`)
            .openOn(map);
    };

    // 4. Fetch Route Data via AJAX
    const routeUrl = "{{ route('user.api.shipments.route-data', $shipment) }}?t=" + new Date().getTime();
    
    fetch(routeUrl)
        .then(response => {
            if (!response.ok) throw new Error("HTTP error " + response.status);
            return response.json();
        })
        .then(data => {
            // Hide Loader
            document.getElementById('map-loader').classList.add('d-none');

            // Force Leaflet to recalculate container dimensions to fix the offset rendering bug
            setTimeout(function() {
                map.invalidateSize();
            }, 50);

            if (data.error) { alert(data.error); return; }

            // ── Set Instrument Values ──────────────────────────────────
            let speedText = '0 knots', statusText = 'Moored';
            if (data.status === 'In Transit') { speedText = '20 knots'; statusText = 'Under way using engine'; }
            else if (data.status === 'Delayed') { speedText = '8 knots'; statusText = 'Maneuvering with caution'; }
            else if (data.status === 'Pending') { speedText = '0 knots'; statusText = 'Anchored / Preparing'; }
            else if (data.status === 'Completed') { speedText = '0 knots'; statusText = 'Moored / Docked'; }

            document.getElementById('instrument-speed').innerText = speedText;
            document.getElementById('instrument-status').innerText = statusText;

            // ── Populate Journey Details Panel ─────────────────────────
            document.getElementById('detail-origin-name').innerText = `${data.origin.name} (${data.origin.code})`;
            document.getElementById('detail-origin-country').innerText = data.origin.country;
            document.getElementById('detail-dest-name').innerText = `${data.destination.name} (${data.destination.code})`;
            document.getElementById('detail-dest-country').innerText = data.destination.country;
            document.getElementById('detail-distance').innerText = data.distance_km ? `${Number(data.distance_km).toLocaleString()} km` : '-';
            document.getElementById('detail-duration').innerText = data.duration_formatted || '-';
            document.getElementById('detail-speed').innerText = speedText;

            // ── Build Sea Route Corridor List (Waypoints timeline) ─────
            const corridorEl = document.getElementById('corridor-list');
            let corridorHtml = '';
            let stepNum = 1;

            // Origin port
            corridorHtml += `<div class="corridor-step" style="cursor: pointer;" onclick="focusCoordinate(${data.origin.latitude}, ${data.origin.longitude}, '${data.origin.name}')">
                <div class="corridor-dot port">${stepNum++}</div>
                <div><span class="corridor-name">⚓ ${data.origin.name}</span><br><span class="corridor-region">${data.origin.country} — Pelabuhan Asal</span></div>
            </div>`;

            // Waypoints
            if (data.waypoints_passed && data.waypoints_passed.length) {
                data.waypoints_passed.forEach(wp => {
                    corridorHtml += `<div class="corridor-step" style="cursor: pointer;" onclick="focusCoordinate(${wp.lat}, ${wp.lng}, '${wp.name}')">
                        <div class="corridor-dot waypoint">${stepNum++}</div>
                        <div><span class="corridor-name">🌊 ${wp.name}</span><br><span class="corridor-region">${wp.region}</span></div>
                    </div>`;
                });
            }

            // Destination port
            corridorHtml += `<div class="corridor-step" style="cursor: pointer;" onclick="focusCoordinate(${data.destination.latitude}, ${data.destination.longitude}, '${data.destination.name}')">
                <div class="corridor-dot port">${stepNum++}</div>
                <div><span class="corridor-name">⚓ ${data.destination.name}</span><br><span class="corridor-region">${data.destination.country} — Pelabuhan Tujuan</span></div>
            </div>`;

            corridorEl.innerHTML = corridorHtml;

            // ── Add Port Markers ──────────────────────────────────────
            L.marker([data.origin.latitude, data.origin.longitude], {icon: originIcon})
                .addTo(map)
                .bindPopup(`<strong>Asal: ${data.origin.name}</strong><br>${data.origin.country}`);

            L.marker([data.destination.latitude, data.destination.longitude], {icon: destIcon})
                .addTo(map)
                .bindPopup(`<strong>Tujuan: ${data.destination.name}</strong><br>${data.destination.country}`);

            // ── Add Waypoint Markers on Map ────────────────────────────
            if (data.waypoints_passed && data.waypoints_passed.length) {
                data.waypoints_passed.forEach(wp => {
                    const wpIcon = L.divIcon({
                        className: 'waypoint-map-marker',
                        iconSize: [12, 12],
                        iconAnchor: [6, 6]
                    });
                    L.marker([wp.lat, wp.lng], {icon: wpIcon})
                        .addTo(map)
                        .bindPopup(`<div style="font-size:0.8rem;"><strong>${wp.name}</strong><br><span style="color:#64748b;">${wp.region}</span></div>`);
                });
            }

            // ── Draw Risk Event Markers from Weather/News ──────────────
            if (data.risk_events && data.risk_events.length) {
                data.risk_events.forEach(risk => {
                    let riskIconClass = 'risk-marker-storm';
                    let riskIconHtml = '<i class="bi bi-cloud-lightning-rain-fill"></i>';

                    if (risk.type === 'Congestion') {
                        riskIconClass = 'risk-marker-congestion';
                        riskIconHtml = '<i class="bi bi-clock-fill"></i>';
                    } else if (risk.type === 'Conflict') {
                        riskIconClass = 'risk-marker-conflict';
                        riskIconHtml = '<i class="bi bi-exclamation-triangle-fill"></i>';
                    }

                    const riskIcon = L.divIcon({
                        className: riskIconClass,
                        html: riskIconHtml,
                        iconSize: [28, 28],
                        iconAnchor: [14, 14]
                    });

                    const severityBadge = risk.severity === 'High' ? 'danger' : 'warning';
                    
                    L.marker([risk.lat, risk.lng], {icon: riskIcon})
                        .addTo(map)
                        .bindPopup(`
                            <div style="font-family:'Outfit',sans-serif;width:200px;font-size:0.82rem;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-${severityBadge} text-white px-2 py-0.5" style="font-size:0.65rem;">${risk.severity} Risk</span>
                                    <span class="text-muted" style="font-size:0.68rem;">${risk.type}</span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size:0.88rem;">${risk.title}</h6>
                                <p class="text-muted mb-0" style="font-size:0.75rem;line-height:1.4;">${risk.description}</p>
                            </div>
                        `);
                });
            }

            // ── Create Dynamic Colored Split Polylines ─────────────────
            // 1. Background glow line
            const polylineGlow = L.polyline(data.coordinates, {
                color: '#10b981',
                weight: 9,
                opacity: 0.15,
                lineJoin: 'round',
                lineCap: 'round'
            }).addTo(map);

            // 2. Traveled line: solid emerald green
            const polylinePassed = L.polyline([], {
                color: '#10b981',
                weight: 5,
                opacity: 0.95,
                lineJoin: 'round',
                lineCap: 'round'
            }).addTo(map);

            // 3. Remaining line: dashed royal blue
            const polylineRemaining = L.polyline([], {
                color: '#2563eb',
                weight: 4,
                opacity: 0.8,
                dashArray: '10, 8',
                lineJoin: 'round',
                lineCap: 'round'
            }).addTo(map);

            // ── Auto Zoom to fit route ────────────────────────────────
            if (data.coordinates && data.coordinates.length > 1) {
                isProgrammaticMove = true;
                const routeBounds = L.latLngBounds(data.coordinates);
                map.fitBounds(routeBounds, { padding: [40, 40] });
                isProgrammaticMove = false;
            }

            // ── Dash animation on the remaining polyline ──────────────
            let offset = 0;
            setInterval(function() {
                offset = (offset - 1) % 18;
                polylineRemaining.setStyle({ dashOffset: offset });
            }, 50);

            // ── Animated Ship Marker & Progress Panels ─────────────────
            if (data.coordinates && data.coordinates.length > 0) {
                // Calculate initial heading from first two coordinates
                let initHeading = 0;
                if (data.coordinates.length > 1) {
                    const p1 = data.coordinates[0];
                    const p2 = data.coordinates[1];
                    const dy = p2[0] - p1[0], dx = p2[1] - p1[1];
                    initHeading = (Math.atan2(dx, dy) * 180 / Math.PI + 360) % 360;
                }

                // Larger Ship Icon SVG (52x52 px) with glowing hull
                const shipIcon = L.divIcon({
                    html: `
                    <div id="ship-icon-container" style="transition: transform 0.08s linear; width: 52px; height: 52px; transform: rotate(${initHeading}deg);">
                        <svg width="52" height="52" viewBox="0 0 100 100" style="display: block; transform: translate(-2px, -2px);">
                            <path d="M 50 5 C 68 32, 63 72, 50 92 C 37 72, 32 32, 50 5 Z" fill="#0f172a" stroke="#10b981" stroke-width="4.5"/>
                            <rect x="36" y="42" width="28" height="30" fill="#1e293b" rx="3" stroke="#34d399" stroke-width="2"/>
                            <rect x="42" y="48" width="16" height="6" fill="#f8fafc" rx="1"/>
                            <line x1="50" y1="26" x2="50" y2="38" stroke="#ef4444" stroke-width="3"/>
                            <circle cx="50" cy="26" r="3.5" fill="#ef4444"/>
                        </svg>
                    </div>
                    `,
                    iconSize: [52, 52],
                    iconAnchor: [26, 26],
                    className: 'animated-ship-marker'
                });
                
                const shipMarker = L.marker(data.coordinates[0], {icon: shipIcon}).addTo(map);
                
                // Calculate actual progress based on timestamp
                const createdAt = new Date(data.created_at).getTime();
                const now = new Date().getTime();
                const elapsedHours = (now - createdAt) / (1000 * 60 * 60);
                
                let progress = 0;
                if (data.status === 'Completed') progress = 1.0;
                else if (data.status === 'Pending') progress = 0.0;
                else progress = Math.min(1.0, elapsedHours / data.duration_hours);

                function getPositionAtProgress(coords, p) {
                    if (!coords || coords.length === 0) return null;
                    if (p <= 0) return { latlng: coords[0], heading: 0, index: 0 };
                    if (p >= 1) {
                        const lastIdx = coords.length - 1;
                        const p1 = coords[lastIdx - 1] || coords[lastIdx];
                        const p2 = coords[lastIdx];
                        const dy = p2[0] - p1[0], dx = p2[1] - p1[1];
                        return { latlng: p2, heading: (Math.atan2(dx, dy) * 180 / Math.PI + 360) % 360, index: lastIdx };
                    }
                    const totalSegments = coords.length - 1;
                    const posInSegments = p * totalSegments;
                    const currentIdx = Math.floor(posInSegments);
                    const segmentProgress = posInSegments - currentIdx;
                    const p1 = coords[currentIdx], p2 = coords[currentIdx + 1];
                    const lat = p1[0] + (p2[0] - p1[0]) * segmentProgress;
                    const lng = p1[1] + (p2[1] - p1[1]) * segmentProgress;
                    const dy = p2[0] - p1[0], dx = p2[1] - p1[1];
                    return {
                        latlng: [lat, lng],
                        heading: (Math.atan2(dx, dy) * 180 / Math.PI + 360) % 360,
                        index: currentIdx
                    };
                }

                const remainingProgress = 1.0 - progress;
                // Speeds up the rendering transitions for better visual experience
                const totalAnimTimeMs = Math.max(30, Math.min(90, data.duration_hours)) * 1000;
                const startTime = Date.now();
                const initialProgress = progress;

                function moveShip() {
                    const elapsed = Date.now() - startTime;
                    let p = initialProgress;
                    if (initialProgress < 1.0) {
                        p = initialProgress + remainingProgress * (elapsed / totalAnimTimeMs);
                    }
                    if (p > 1.0) p = 1.0;

                    const pos = getPositionAtProgress(data.coordinates, p);
                    if (pos) {
                        currentShipLatLng = pos.latlng;
                        shipMarker.setLatLng(pos.latlng);
                        
                        // Update Coordinates Panel
                        document.getElementById('instrument-lat').innerText = pos.latlng[0].toFixed(5);
                        document.getElementById('instrument-lng').innerText = pos.latlng[1].toFixed(5);
                        document.getElementById('instrument-heading').innerText = pos.heading.toFixed(1) + '°';
                        
                        // Rotate compass needle
                        const compassNeedle = document.getElementById('heading-compass-icon');
                        if (compassNeedle) compassNeedle.style.transform = `rotate(${pos.heading}deg)`;
                        
                        // Rotate ship marker icon dynamically
                        const markerEl = shipMarker.getElement();
                        if (markerEl) {
                            const container = markerEl.querySelector('#ship-icon-container');
                            if (container) {
                                container.style.transform = `rotate(${pos.heading}deg)`;
                            }
                        }

                        // ── Update Splitted Polylines ──────────────────────────
                        const passedCoords = data.coordinates.slice(0, pos.index + 1);
                        passedCoords.push(pos.latlng);
                        polylinePassed.setLatLngs(passedCoords);

                        const remainingCoords = [pos.latlng, ...data.coordinates.slice(pos.index + 1)];
                        polylineRemaining.setLatLngs(remainingCoords);

                        // ── Update Journey Progress Panel Metrics ──────────────
                        const totalDist = Number(data.distance_km) || 0;
                        const traveledDist = totalDist * p;
                        const remainingDist = totalDist * (1 - p);
                        const progressPercent = p * 100;

                        document.getElementById('progress-bar-fill').style.width = `${progressPercent.toFixed(1)}%`;
                        document.getElementById('progress-percent').innerText = `${progressPercent.toFixed(1)}%`;
                        document.getElementById('progress-traveled').innerText = `${Math.round(traveledDist).toLocaleString()} km`;
                        document.getElementById('progress-remaining').innerText = `${Math.round(remainingDist).toLocaleString()} km`;

                        // Calculate and format ETA
                        const etaHours = remainingDist / 37.0; // 37 km/h speed
                        if (etaHours <= 0) {
                            document.getElementById('progress-eta').innerText = 'Tiba di Pelabuhan';
                        } else {
                            const days = Math.floor(etaHours / 24);
                            const hours = Math.round(etaHours % 24);
                            document.getElementById('progress-eta').innerText = days > 0 ? `${days} hari ${hours} jam` : `${hours} jam`;
                        }
                        
                        // Follow vessel snapping
                        if (followVessel) {
                            isProgrammaticMove = true;
                            const currentZoom = Math.max(map.getZoom(), 5);
                            map.setView(pos.latlng, currentZoom, { animate: false });
                            isProgrammaticMove = false;
                        }
                        
                        // Stop animation if completed
                        if (p >= 1.0) {
                            clearInterval(animInterval);
                            document.getElementById('instrument-status').innerText = 'Moored / Docked';
                            document.getElementById('instrument-speed').innerText = '0 knots';
                        }
                    }
                }

                const animInterval = setInterval(moveShip, 50);
            }
        })
        .catch(error => {
            document.getElementById('map-loader').classList.add('d-none');
            alert("Gagal memuat rute perjalanan: " + error.message);
            console.error("Routing error:", error);
        });
});
</script>
@endpush
