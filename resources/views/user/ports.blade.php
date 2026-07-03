@extends('layouts.app')

@section('title', 'Ports - RiskIntel')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card card-custom p-4 bg-white mb-4 border border-light-subtle shadow-sm">
        <h4 class="fw-bold text-slate-800 mb-1"><i class="bi bi-compass me-2"></i>Global Ports Tracking</h4>
        <p class="text-muted small mb-0">Visualisasi sebaran posisi pelabuhan laut utama dunia serta pemantauan kelancaran lalu lintas kontainer logistik.</p>
    </div>

    <!-- Map & Table -->
    <div class="row">
        <!-- Leaflet Map -->
        <div class="col-lg-7 mb-4">
            <div class="card card-custom p-4 bg-white h-100 border border-light-subtle shadow-sm">
                <h5 class="fw-bold text-slate-800 mb-3">Peta Posisi Pelabuhan</h5>
                <div id="ports-map" class="rounded border" style="height: 450px; background-color: #f1f5f9;"></div>
            </div>
        </div>

        <!-- Port List Table -->
        <div class="col-lg-5 mb-4">
            <div class="card card-custom p-4 bg-white h-100 border border-light-subtle shadow-sm">
                <h5 class="fw-bold text-slate-800 mb-3">Daftar Pelabuhan Aktif</h5>
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
                            @forelse($ports as $port)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-slate-855 d-block">{{ $port->name }}</span>
                                        <span class="text-muted small">Kode: {{ $port->code ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $port->country->name }}</span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-info fw-bold" 
                                                onclick="focusPort({{ $port->latitude }}, {{ $port->longitude }}, '{{ $port->name }}')">
                                            <i class="bi bi-geo-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada pelabuhan yang terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let map;
    document.addEventListener("DOMContentLoaded", function () {
        // Initialize Leaflet map
        map = L.map('ports-map').setView([10.0, 110.0], 3);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Load seeded ports data
        const ports = {!! json_encode($ports) !!};

        ports.forEach(port => {
            if (port.latitude && port.longitude) {
                const marker = L.marker([port.latitude, port.longitude]).addTo(map);
                const popupContent = `
                    <div style="font-family: 'Outfit', sans-serif; min-width: 140px;">
                        <h6 style="margin: 0 0 5px; font-weight: bold; color: #1e293b;">${port.name}</h6>
                        <span style="display: block; font-size: 0.85em; color: #64748b;"><b>Kode:</b> ${port.code ?? 'N/A'}</span>
                        <span style="display: block; font-size: 0.85em; color: #64748b;"><b>Negara:</b> ${port.country.name}</span>
                        <span style="display: block; font-size: 0.85em; color: #64748b;"><b>Koordinat:</b> ${port.latitude}, ${port.longitude}</span>
                    </div>
                `;
                marker.bindPopup(popupContent);
            }
        });
    });

    function focusPort(lat, lon, name) {
        if (map) {
            map.setView([lat, lon], 7);
            L.popup()
                .setLatLng([lat, lon])
                .setContent(`<div style="font-family: 'Outfit', sans-serif; font-weight: bold;">${name}</div>`)
                .openOn(map);
        }
    }
</script>
@endpush
