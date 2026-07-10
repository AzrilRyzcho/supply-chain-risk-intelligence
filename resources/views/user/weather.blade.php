@extends('layouts.app')

@section('title', 'Weather - RiskIntel')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.Default.css" />
<style>
    .marker-pin {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        transition: all 0.2s ease-in-out;
        cursor: pointer;
    }
    .marker-pin:hover {
        transform: scale(1.3);
    }
    @keyframes pulse-red {
        0% {
            box-shadow: 0 0 0 0px rgba(239, 68, 68, 0.8);
        }
        100% {
            box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
        }
    }
    .pulse-red {
        animation: pulse-red 1.5s infinite;
    }
    @keyframes pulse-orange {
        0% {
            box-shadow: 0 0 0 0px rgba(245, 158, 11, 0.8);
        }
        100% {
            box-shadow: 0 0 0 10px rgba(245, 158, 11, 0);
        }
    }
    .pulse-orange {
        animation: pulse-orange 1.5s infinite;
    }
    /* Style customization for Leaflet popups */
    .leaflet-popup-content-wrapper {
        border-radius: 12px !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        border: 1px solid #e2e8f0;
        padding: 6px !important;
    }
    .leaflet-popup-content {
        margin: 8px 12px !important;
        font-family: 'Outfit', sans-serif !important;
    }
    .leaflet-popup-tip {
        background: white !important;
        box-shadow: 0 3px 14px rgba(0,0,0,0.1) !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card card-custom p-4 bg-white mb-4 border border-light-subtle shadow-sm">
        <h4 class="fw-bold text-slate-800 mb-1"><i class="bi bi-cloud-sun me-2"></i>Weather Monitoring</h4>
        <p class="text-muted small mb-0">Pantau indikator cuaca ekstrem secara langsung untuk mengantisipasi potensi keterlambatan pengiriman logistik laut dan udara.</p>
    </div>

    <!-- Map Panel (Full Width & Enlarged) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom p-4 bg-white border border-light-subtle shadow-sm">
                <h5 class="fw-bold text-slate-800 mb-3"><i class="bi bi-globe-americas text-primary me-2"></i>Peta Cuaca Wilayah Mitra</h5>
                <div id="weather-map" class="rounded border" style="height: 520px; background-color: #f1f5f9; z-index: 1;"></div>
            </div>
        </div>
    </div>

    <!-- Weather Stats Grid (Below Map) -->
    <div class="row">
        <div class="col-12">
            <div class="card card-custom p-4 bg-white border border-light-subtle shadow-sm">
                <h5 class="fw-bold text-slate-800 mb-4"><i class="bi bi-list-task text-primary me-2"></i>Status Cuaca Negara Mitra</h5>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                    @forelse($countries as $c)
                        <div class="col">
                            <div class="p-3 rounded border border-light-subtle bg-light h-100 shadow-sm d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center">
                                        @if($c->flag)
                                            <img src="{{ $c->flag }}" alt="Flag" style="width: 20px; height: 12px; object-fit: cover; border-radius: 2px;" class="me-2 shadow-sm border">
                                        @endif
                                        <span class="fw-bold text-slate-800">{{ $c->name }}</span>
                                        <span class="badge bg-secondary ms-2">{{ $c->code }}</span>
                                    </div>
                                    @if($c->weather && $c->weather->storm_risk >= 15)
                                        <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>Storm Risk</span>
                                    @elseif($c->weather && $c->weather->storm_risk >= 8)
                                        <span class="badge bg-warning text-dark"><i class="bi bi-cloud-lightning me-1"></i>Waspada</span>
                                    @else
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Aman</span>
                                    @endif
                                </div>
                                <div class="row text-center mt-auto pt-2 g-1 border-top border-light-subtle">
                                    <div class="col-3">
                                        <span class="text-muted small d-block" style="font-size: 0.72em;">Suhu</span>
                                        <span class="fw-bold text-slate-700" style="font-size: 0.9em;">{{ $c->weather->temperature ?? 'N/A' }}°C</span>
                                    </div>
                                    <div class="col-3 border-start">
                                        <span class="text-muted small d-block" style="font-size: 0.72em;">Hujan</span>
                                        <span class="fw-bold text-slate-700" style="font-size: 0.9em;">{{ $c->weather->rain ?? 'N/A' }} mm</span>
                                    </div>
                                    <div class="col-3 border-start">
                                        <span class="text-muted small d-block" style="font-size: 0.72em;">Angin</span>
                                        <span class="fw-bold text-slate-700" style="font-size: 0.9em;">{{ $c->weather->wind_speed ?? 'N/A' }} km/h</span>
                                    </div>
                                    <div class="col-3 border-start">
                                        <span class="text-muted small d-block" style="font-size: 0.72em;">Kerawanan</span>
                                        <span class="fw-bold text-danger" style="font-size: 0.9em;">{{ $c->weather->storm_risk ?? 'N/A' }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-center text-muted">Belum ada data cuaca negara mitra.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/leaflet.markercluster.js"></script>
<script>
    // Pass dynamic backend data to global JS scope
    window.weatherData = {
        countries: {!! json_encode($countries) !!}
    };
</script>
<script src="{{ asset('js/weather.js') }}"></script>
@endpush
