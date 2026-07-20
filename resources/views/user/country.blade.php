@extends('layouts.app')

@section('title', 'Countries - RiskIntel')

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
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Country Select Header -->
    <div class="card card-custom p-4 bg-white mb-4" style="position: relative; z-index: 10;">
        <h5 class="fw-bold text-slate-800 mb-3">Pilih Negara Mitra Dagang</h5>
        <form action="{{ route('user.country') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
                <select name="code" class="form-select bg-light border-secondary-subtle" id="country-select" onchange="this.form.submit()">
                    <option value="" data-flag="">-- Pilih Negara --</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->code }}" data-flag="{{ $c->flag }}" {{ $selectedCode == $c->code ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold">Tampilkan</button>
            </div>
        </form>
    </div>

    @if($selectedCountry)
        <div class="row">
            <!-- Left Panel: Macroeconomy & Weather -->
            <div class="col-lg-8">
                <!-- Meta Info & Weather -->
                <div class="card card-custom p-4 bg-white mb-4">
                    <div class="d-flex align-items-center mb-3 flex-wrap gap-2">
                        @if($selectedCountry->flag)
                            <img src="{{ $selectedCountry->flag }}" alt="Flag" style="height: 28px; border-radius: 4px;" class="me-2 shadow-sm border">
                        @endif
                        <h4 class="fw-bold text-slate-800 mb-0">{{ $selectedCountry->name }}</h4>
                        <span class="badge bg-secondary fs-6 py-2 px-3 ms-auto">Mata Uang: {{ $selectedCountry->currency_code }}</span>
                    </div>
                    <div class="row row-cols-2 row-cols-md-5 g-3 text-center bg-light rounded p-3 mb-4">
                        <div class="col border-end border-light-subtle">
                            <span class="text-muted small d-block">Wilayah</span>
                            <span class="fw-bold text-slate-700 fs-6">{{ $selectedCountry->region }}</span>
                            @if($selectedCountry->subregion)
                                <span class="text-muted d-block" style="font-size: 0.72rem; color: #64748b;">{{ $selectedCountry->subregion }}</span>
                            @endif
                        </div>
                        <div class="col border-end border-light-subtle">
                            <span class="text-muted small d-block">Koordinat</span>
                            <span class="fw-bold text-slate-700 fs-6 d-block">{{ $selectedCountry->latitude }}, {{ $selectedCountry->longitude }}</span>
                        </div>
                        <div class="col border-end border-light-subtle">
                            <span class="text-muted small d-block">Populasi</span>
                            <span class="fw-bold text-slate-700 fs-6 d-block">
                                @if($selectedCountry->population)
                                    {{ $selectedCountry->population >= 1000000 ? number_format($selectedCountry->population / 1000000, 1) . ' Juta' : number_format($selectedCountry->population) }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                        <div class="col border-end border-light-subtle">
                            <span class="text-muted small d-block">Luas Wilayah</span>
                            <span class="fw-bold text-slate-700 fs-6 d-block">
                                @if($selectedCountry->area)
                                    {{ number_format($selectedCountry->area) }} km²
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                        <div class="col">
                            <span class="text-muted small d-block">Bahasa</span>
                            <span class="fw-bold text-slate-700 fs-6 d-block" title="{{ is_array($selectedCountry->languages) ? implode(', ', $selectedCountry->languages) : ($selectedCountry->languages ?? 'N/A') }}">
                                @if(is_array($selectedCountry->languages) && count($selectedCountry->languages) > 0)
                                    {{ Str::limit(implode(', ', $selectedCountry->languages), 12) }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                    </div>

                    @if($selectedCountry->weather)
                        <h6 class="fw-bold text-slate-800 mb-3"><i class="bi bi-cloud-sun me-1"></i>Indikator Cuaca Ekstrem</h6>
                        <div class="row text-center mb-2">
                            <div class="col-6 col-md-3">
                                <div class="p-2 border rounded mb-2">
                                    <span class="text-muted small d-block">Suhu</span>
                                    <span class="fw-bold text-slate-800">{{ $selectedCountry->weather->temperature }}°C</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-2 border rounded mb-2">
                                    <span class="text-muted small d-block">Curah Hujan</span>
                                    <span class="fw-bold text-slate-800">{{ $selectedCountry->weather->rain }} mm</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-2 border rounded mb-2">
                                    <span class="text-muted small d-block">Kecepatan Angin</span>
                                    <span class="fw-bold text-slate-800">{{ $selectedCountry->weather->wind_speed }} km/h</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-2 border rounded mb-2">
                                    <span class="text-muted small d-block">Risiko Badai</span>
                                    <span class="fw-bold text-danger">{{ $selectedCountry->weather->storm_risk }}%</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- GDP & Inflation Trends (Chart.js) -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card card-custom p-4 bg-white h-100">
                            <h5 class="fw-bold text-slate-800 mb-3">Tren PDB (GDP)</h5>
                            <div style="height: 250px;">
                                <canvas id="gdpChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card card-custom p-4 bg-white h-100">
                            <h5 class="fw-bold text-slate-800 mb-3">Tren Inflasi</h5>
                            <div style="height: 250px;">
                                <canvas id="inflationChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Export & Import Trends (Chart.js) -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card card-custom p-4 bg-white h-100">
                            <h5 class="fw-bold text-slate-800 mb-3">Tren Ekspor</h5>
                            <div style="height: 250px;">
                                <canvas id="exportChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card card-custom p-4 bg-white h-100">
                            <h5 class="fw-bold text-slate-800 mb-3">Tren Impor</h5>
                            <div style="height: 250px;">
                                <canvas id="importChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Risk Gauge -->
            <div class="col-lg-4">
                <div class="card card-custom p-4 bg-white h-100">
                    <h5 class="fw-bold text-slate-800 mb-4">Analisis Indeks Risiko</h5>

                    @if($latestRisk)
                        <div class="text-center mb-4">
                            <div class="d-inline-block rounded-circle p-4 mb-3 {{ $latestRisk->total_score >= 50 ? 'bg-danger bg-opacity-10 text-danger' : ($latestRisk->total_score >= 25 ? 'bg-warning bg-opacity-10 text-warning' : 'bg-success bg-opacity-10 text-success') }}"
                                 style="width: 140px; height: 140px; line-height: 92px;">
                                <span class="fs-1 fw-bold">{{ $latestRisk->total_score }}%</span>
                            </div>
                            <h4 class="fw-bold text-slate-800">
                                @if($latestRisk->total_score >= 50)
                                    Risiko Tinggi
                                @elseif($latestRisk->total_score >= 25)
                                    Risiko Sedang
                                @else
                                    Risiko Rendah
                                @endif
                            </h4>
                            <p class="text-muted small">Dihitung pada {{ \Carbon\Carbon::parse($latestRisk->calculated_at)->format('d M Y H:i') }}</p>
                        </div>

                        <!-- Scores Breakdown -->
                        <h6 class="fw-bold text-slate-800 mb-3">Rincian Skor Bobot Risiko</h6>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1 text-slate-700">
                                <span>Risiko Cuaca</span>
                                <span>{{ $latestRisk->weather_score }}%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $latestRisk->weather_score }}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1 text-slate-700">
                                <span>Risiko Inflasi</span>
                                <span>{{ $latestRisk->inflation_score }}%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $latestRisk->inflation_score }}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1 text-slate-700">
                                <span>Risiko Depresiasi Kurs</span>
                                <span>{{ $latestRisk->currency_score }}%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $latestRisk->currency_score }}%"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between small mb-1 text-slate-700">
                                <span>Risiko Sentimen Geopolitik</span>
                                <span>{{ $latestRisk->sentiment_score }}%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $latestRisk->sentiment_score }}%"></div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <h1 class="display-3 fw-bold text-muted">-</h1>
                            <span class="badge bg-secondary px-3 py-2">Belum Dikalkulasi</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="card card-custom p-5 bg-white text-center">
            <div class="text-slate-300 mb-3" style="font-size: 5rem;">
                <i class="bi bi-globe2"></i>
            </div>
            <h4 class="fw-bold text-slate-800">Silakan Pilih Negara Mitra</h4>
            <p class="text-muted col-md-6 mx-auto">
                Silakan pilih salah satu negara mitra dagang pada dropdown di atas untuk memantau data ekonomi historis, risiko cuaca ekstrem, dan performa rantai pasok secara detail.
            </p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var el = document.getElementById("country-select");
        if (el) {
            var ts = new TomSelect(el, {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
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
            ts.on('change', function(value) {
                if (value) {
                    el.form.submit();
                }
            });
        }
    });
</script>

@if($selectedCountry)
<!-- Load Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // GDP Trend Chart
    const gdpCtx = document.getElementById('gdpChart').getContext('2d');
    new Chart(gdpCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($gdpData->pluck('year')->toArray()) !!},
            datasets: [{
                label: 'GDP (Miliar USD)',
                data: {!! json_encode($gdpData->pluck('value')->toArray()) !!},
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                fill: true,
                tension: 0.3,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Inflation Trend Chart
    const inflationCtx = document.getElementById('inflationChart').getContext('2d');
    new Chart(inflationCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($inflationData->pluck('year')->toArray()) !!},
            datasets: [{
                label: 'Inflasi (%)',
                data: {!! json_encode($inflationData->pluck('rate')->toArray()) !!},
                backgroundColor: '#f59e0b',
                borderRadius: 4,
                barThickness: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Export Trend Chart
    const exportCtx = document.getElementById('exportChart').getContext('2d');
    new Chart(exportCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($exportData->pluck('year')->toArray()) !!},
            datasets: [{
                label: 'Ekspor (Miliar USD)',
                data: {!! json_encode($exportData->pluck('value')->toArray()) !!},
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.3,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Import Trend Chart
    const importCtx = document.getElementById('importChart').getContext('2d');
    new Chart(importCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($importData->pluck('year')->toArray()) !!},
            datasets: [{
                label: 'Impor (Miliar USD)',
                data: {!! json_encode($importData->pluck('value')->toArray()) !!},
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                fill: true,
                tension: 0.3,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endif
@endpush
