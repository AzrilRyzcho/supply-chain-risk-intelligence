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
    .ts-wrapper.focus .ts-control {
        border-color: #86b7fe !important;
        outline: 0 !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
    }
    .ts-dropdown {
        border-radius: 0.375rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1) !important;
        border: 1px solid #e2e8f0 !important;
    }
    .ts-dropdown .active {
        background-color: #3b82f6 !important;
        color: #ffffff !important;
    }
    /* Fixing layout spacing with ts-wrapper inside input-group */
    .input-group > .ts-wrapper {
        flex: 1 1 auto;
        width: 1%;
    }
</style>
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
                        <option value="" data-flag="">-- Semua Negara --</option>
                        @foreach($countries as $c)
                            <option value="{{ $c->id }}" data-flag="{{ $c->flag }}">{{ $c->name }}</option>
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
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var el = document.getElementById("country-filter");
        if (el) {
            var ts = new TomSelect(el, {
                create: false,
                sortField: { field: "text", direction: "asc" },
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
