@extends('layouts.app')

@section('title', 'Global Ports - RiskIntel')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <style>
        .ts-wrapper .ts-control {
            background-color: #f8fafc !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 0.375rem !important;
            padding: 0.5rem 0.75rem !important;
            box-shadow: none !important;
        }
        body.dark-theme .ts-wrapper .ts-control {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }
        .ts-wrapper.focus .ts-control {
            border-color: #818cf8 !important;
            outline: 0 !important;
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25) !important;
        }
        .ts-dropdown {
            border-radius: 0.375rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1) !important;
            border: 1px solid #e2e8f0 !important;
        }
        body.dark-theme .ts-dropdown {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }
        .ts-dropdown .active {
            background-color: #4f46e5 !important;
            color: #ffffff !important;
        }
        /* Fixing layout spacing with ts-wrapper inside input-group */
        .input-group > .ts-wrapper {
            flex: 1 1 auto;
            width: 1%;
        }
        
        /* Scrollable ports list styling */
        .ports-list-container {
            height: 380px;
            overflow-y: auto;
            padding-right: 4px;
        }
        .ports-list-container::-webkit-scrollbar {
            width: 6px;
        }
        .ports-list-container::-webkit-scrollbar-track {
            background: transparent;
        }
        .ports-list-container::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 3px;
        }
        body.dark-theme .ports-list-container::-webkit-scrollbar-thumb {
            background-color: #475569;
        }

        .list-group-item-custom {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            margin-bottom: 8px;
            border-radius: 12px !important;
            transition: all 0.25s ease;
        }
        body.dark-theme .list-group-item-custom {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }
        .list-group-item-custom:hover {
            transform: translateY(-2px);
            background-color: #e0e4ff !important;
            border-color: #818cf8 !important;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.08) !important;
        }
        body.dark-theme .list-group-item-custom:hover {
            background-color: #1e1b4b !important;
            border-color: #6366f1 !important;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15) !important;
        }
    </style>
    <!-- Leaflet MarkerCluster CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card card-custom p-4 bg-white mb-4 border border-light-subtle shadow-sm">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h4 class="fw-bold text-slate-800 mb-1"><i class="bi bi-compass me-2 text-primary"></i>Global Ports Tracking</h4>
                <p class="text-muted small mb-0">Visualisasi sebaran posisi pelabuhan laut utama dunia serta pemantauan kelancaran lalu lintas kontainer logistik.</p>
            </div>
            <span class="badge bg-primary px-3 py-2 fs-6" style="border-radius: 8px;">Real-time Map</span>
        </div>
    </div>

    <!-- KPI Summary Row -->
    <div class="row mb-4">
        <!-- KPI 1 -->
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card card-custom card-kpi-ports p-3 border border-light-subtle h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-semibold">Total Pelabuhan</span>
                        <h3 class="fw-bold text-slate-900 mt-1 mb-0">{{ count($ports) }}</h3>
                    </div>
                    <div class="icon-box-blue">
                        <i class="bi bi-anchor fs-4"></i>
                    </div>
                </div>
                <div class="mt-2 text-muted small">
                    Pelabuhan utama dunia dipantau
                </div>
            </div>
        </div>
        <!-- KPI 2 -->
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card card-custom card-kpi-countries p-3 border border-light-subtle h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-semibold">Negara Terhubung</span>
                        <h3 class="fw-bold text-slate-900 mt-1 mb-0">{{ count($countries) }}</h3>
                    </div>
                    <div class="icon-box-green">
                        <i class="bi bi-globe fs-4"></i>
                    </div>
                </div>
                <div class="mt-2 text-muted small">
                    Jaringan logistik multi-nasional
                </div>
            </div>
        </div>
        <!-- KPI 3 -->
        <div class="col-md-4">
            <div class="card card-custom card-kpi-watchlist p-3 border border-light-subtle h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-semibold">Status Kepadatan</span>
                        <h3 class="fw-bold text-slate-900 mt-1 mb-0 text-success">Lancar</h3>
                    </div>
                    <div class="icon-box-amber">
                        <i class="bi bi-activity fs-4 text-warning"></i>
                    </div>
                </div>
                <div class="mt-2 text-muted small">
                    Waktu tunggu pelabuhan rata-rata rendah
                </div>
            </div>
        </div>
    </div>

    <!-- Main Workspace Layout -->
    <div class="row">
        <!-- Left Panel: Search, Filters & Ports List -->
        <div class="col-lg-4 mb-4">
            <div class="card card-custom p-4 bg-white border border-light-subtle shadow-sm h-100 d-flex flex-column" style="min-height: 600px;">
                <h5 class="fw-bold text-slate-800 mb-3"><i class="bi bi-funnel me-2"></i>Filter & Cari</h5>
                
                <!-- Search Input -->
                <div class="mb-3">
                    <label for="search-input" class="form-label small fw-semibold text-secondary">Cari Pelabuhan</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-light-subtle"><i class="bi bi-search text-secondary"></i></span>
                        <input type="text" id="search-input" class="form-control border-light-subtle" placeholder="Nama atau kode..." oninput="filterPorts()">
                    </div>
                </div>

                <!-- Country Filter -->
                <div class="mb-4">
                    <label for="country-filter" class="form-label small fw-semibold text-secondary">Filter Negara</label>
                    <div class="input-group" style="position: relative; z-index: 100;">
                        <span class="input-group-text bg-light border-light-subtle"><i class="bi bi-flag text-secondary"></i></span>
                        <select id="country-filter" class="form-select border-light-subtle" onchange="filterPorts()">
                            <option value="" data-flag="">-- Semua Negara --</option>
                            @foreach($countries as $c)
                                <option value="{{ $c->id }}" data-flag="{{ $c->flag }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr class="mt-0 mb-3">

                <!-- Scrollable Port List Group -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold text-slate-700">Daftar Pelabuhan</span>
                    <span id="active-count" class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1"></span>
                </div>
                
                <div class="ports-list-container flex-grow-1">
                    <div class="list-group list-group-flush" id="ports-list-group">
                        <!-- Populated via JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Immersive Map -->
        <div class="col-lg-8 mb-4">
            <div class="card card-custom p-4 bg-white border border-light-subtle shadow-sm h-100">
                <h5 class="fw-bold text-slate-800 mb-3"><i class="bi bi-map me-2"></i>Peta Posisi Pelabuhan</h5>
                <div id="ports-map" class="rounded border" style="height: 520px; background-color: #f1f5f9; z-index: 1;"></div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var el = document.getElementById("country-filter");
        if (el) {
            var ts = new TomSelect(el, {
                create: false,
                sortField: { field: "text", direction: "asc" },
                maxOptions: null,
                render: {
                    option: function(data, escape) {
                        var flagUrl = data.flag;
                        var img = flagUrl ? '<img class="me-2" style="width: 20px; height: 12px; object-fit: cover; border-radius: 2px; border: 1px solid #e2e8f0;" src="' + flagUrl + '" />' : '';
                        return '<div>' + img + '<span>' + escape(data.text) + '</span></div>';
                    },
                    item: function(data, escape) {
                        var flagUrl = data.flag;
                        var img = flagUrl ? '<img class="me-2" style="width: 20px; height: 12px; object-fit: cover; border-radius: 2px; border: 1px solid #e2e8f0;" src="' + flagUrl + '" />' : '';
                        return '<div>' + img + '<span>' + escape(data.text) + '</span></div>';
                    }
                }
            });
            // tom select overrides native change, so we trigger filterPorts manually on change
            ts.on('change', function(value) {
                filterPorts();
            });
        }
    });
</script>
<!-- Leaflet MarkerCluster JS -->
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<script>
    // Pass dynamic backend data to global JS scope
    window.portsData = {
        ports: {!! json_encode($ports) !!}
    };
</script>
<script src="{{ asset('js/ports.js') }}"></script>
@endpush
