@extends('layouts.app')

@section('title', 'Weather - RiskIntel')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card card-custom p-4 bg-white mb-4 border border-light-subtle shadow-sm">
        <h4 class="fw-bold text-slate-800 mb-1"><i class="bi bi-cloud-sun me-2"></i>Weather Monitoring</h4>
        <p class="text-muted small mb-0">Pantau indikator cuaca ekstrem secara langsung untuk mengantisipasi potensi keterlambatan pengiriman logistik laut dan udara.</p>
    </div>

    <!-- Map & Weather List -->
    <div class="row">
        <!-- Leaflet Map Panel -->
        <div class="col-lg-7 mb-4">
            <div class="card card-custom p-4 bg-white h-100 border border-light-subtle shadow-sm">
                <h5 class="fw-bold text-slate-800 mb-3">Peta Cuaca Wilayah Mitra</h5>
                <div id="weather-map" class="rounded border" style="height: 450px; background-color: #f1f5f9;"></div>
            </div>
        </div>

        <!-- Weather Stats Cards -->
        <div class="col-lg-5 mb-4">
            <div class="card card-custom p-4 bg-white h-100 border border-light-subtle shadow-sm">
                <h5 class="fw-bold text-slate-800 mb-3">Status Cuaca Negara Mitra</h5>
                <div class="d-flex flex-column gap-3">
                    @forelse($countries as $c)
                        <div class="p-3 rounded border border-light-subtle bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <span class="fw-bold text-slate-800">{{ $c->name }}</span>
                                    <span class="badge bg-secondary ms-2">{{ $c->code }}</span>
                                </div>
                                @if($c->weather && $c->weather->storm_risk >= 10)
                                    <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>Storm Risk</span>
                                @else
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Aman</span>
                                @endif
                            </div>
                            <div class="row text-center mt-2 g-1">
                                <div class="col-3">
                                    <span class="text-muted small d-block" style="font-size: 0.75em;">Suhu</span>
                                    <span class="fw-bold text-slate-700">{{ $c->weather->temperature ?? 'N/A' }}°C</span>
                                </div>
                                <div class="col-3 border-start">
                                    <span class="text-muted small d-block" style="font-size: 0.75em;">Hujan</span>
                                    <span class="fw-bold text-slate-700">{{ $c->weather->rain ?? 'N/A' }} mm</span>
                                </div>
                                <div class="col-3 border-start">
                                    <span class="text-muted small d-block" style="font-size: 0.75em;">Angin</span>
                                    <span class="fw-bold text-slate-700">{{ $c->weather->wind_speed ?? 'N/A' }} km/h</span>
                                </div>
                                <div class="col-3 border-start">
                                    <span class="text-muted small d-block" style="font-size: 0.75em;">Kerawanan</span>
                                    <span class="fw-bold text-danger">{{ $c->weather->storm_risk ?? 'N/A' }}%</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">Belum ada data cuaca negara mitra.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Initialize Leaflet map
        const map = L.map('weather-map').setView([10.0, 110.0], 3);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Seed data parsed into JS markers
        const countries = {!! json_encode($countries) !!};

        countries.forEach(c => {
            if (c.latitude && c.longitude && c.weather) {
                const marker = L.marker([c.latitude, c.longitude]).addTo(map);
                const popupContent = `
                    <div style="font-family: 'Outfit', sans-serif; min-width: 150px;">
                        <h6 style="margin: 0 0 5px; font-weight: bold; color: #1e293b;">${c.name} (${c.code})</h6>
                        <hr style="margin: 5px 0;">
                        <span style="display: block; font-size: 0.85em; color: #475569;"><b>Suhu:</b> ${c.weather.temperature}°C</span>
                        <span style="display: block; font-size: 0.85em; color: #475569;"><b>Hujan:</b> ${c.weather.rain} mm</span>
                        <span style="display: block; font-size: 0.85em; color: #475569;"><b>Angin:</b> ${c.weather.wind_speed} km/h</span>
                        <span style="display: block; font-size: 0.85em; color: #ef4444;"><b>Kerawanan Badai:</b> ${c.weather.storm_risk}%</span>
                    </div>
                `;
                marker.bindPopup(popupContent);
            }
        });
    });
</script>
@endpush
