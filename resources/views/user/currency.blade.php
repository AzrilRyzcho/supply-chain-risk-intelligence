@extends('layouts.app')

@section('title', 'Currency - RiskIntel')

@push('styles')
<!-- Tom Select CSS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .table-sticky-header th {
        position: sticky;
        top: 0;
        background-color: #f8fafc !important;
        z-index: 2;
        box-shadow: inset 0 -2px 0 #e2e8f0;
    }
    /* Customize scrollbar style for premium feel */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card card-custom p-4 bg-white mb-4 border border-light-subtle shadow-sm">
        <h4 class="fw-bold text-slate-800 mb-1"><i class="bi bi-currency-exchange me-2"></i>Currency Exchange Rates</h4>
        <p class="text-muted small mb-0">Analisis perbandingan kekuatan nilai tukar valuta asing terhadap USD untuk mengukur stabilitas biaya material impor dan beban inflasi logistik.</p>
    </div>

    <div class="row">
        <!-- Exchange Rates List (Scrollable Card to match right column height) -->
        <div class="col-lg-6 mb-4">
            <div class="card card-custom p-4 bg-white border border-light-subtle shadow-sm d-flex flex-column" style="height: 520px; position: relative; z-index: 1;">
                <h5 class="fw-bold text-slate-800 mb-3">Nilai Tukar Saat Ini (Terhadap 1 USD)</h5>
                <div class="table-responsive custom-scrollbar flex-grow-1 overflow-y-auto" style="max-height: 420px;">
                    <table class="table align-middle mb-0">
                        <thead class="table-light table-sticky-header">
                            <tr>
                                <th>Valuta Asing</th>
                                <th class="text-end">Nilai Tukar per USD</th>
                                <th class="text-center">Diperbarui Pada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($currencies as $curr)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold me-2" 
                                                 style="width: 36px; height: 36px; font-size: 0.85rem;">
                                                {{ $curr->code }}
                                            </div>
                                            <span class="fw-bold text-slate-800">{{ $curr->code }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold text-slate-700">
                                        {{ number_format($curr->rate_to_usd, 4) }}
                                    </td>
                                    <td class="text-center text-muted small" style="font-size: 0.85em;">
                                        {{ \Carbon\Carbon::parse($curr->fetched_at)->format('d M H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada data nilai tukar mata uang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Currency Converter & Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card card-custom p-4 bg-white border border-light-subtle shadow-sm d-flex flex-column" style="height: 520px; position: relative; z-index: 10;">
                <h5 class="fw-bold text-slate-800 mb-3">Simulasi Konverter Kurs USD</h5>
                <div class="row g-3 bg-light rounded p-3 mb-4">
                    <div class="col-md-5">
                        <label class="form-label text-muted small">Nominal USD</label>
                        <input type="number" id="usd-amount" class="form-control" value="100" min="1" oninput="convertCurrency()">
                    </div>
                    <div class="col-md-2 text-center align-self-end py-2">
                        <i class="bi bi-arrow-left-right fs-4 text-secondary"></i>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-muted small">Target Mata Uang</label>
                        <select id="target-currency" class="form-select">
                            @foreach($currencies as $curr)
                                <option value="{{ $curr->rate_to_usd }}" data-code="{{ $curr->code }}">
                                    {{ $curr->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 mt-3 text-center">
                        <h4 class="fw-bold text-slate-800 mb-0" id="conversion-result">-</h4>
                    </div>
                </div>

                <!-- Currency Strength Visualizer Chart.js -->
                <h6 class="fw-bold text-slate-800 mb-2">Indeks Kekuatan Kurs Utama (Rasio per USD)</h6>
                <div class="flex-grow-1" style="height: 180px; min-height: 180px; position: relative;">
                    <canvas id="currencyStrengthChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Historical Trend Chart Row -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-custom p-4 bg-white border border-light-subtle shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h5 class="fw-bold text-slate-800 mb-0">
                        <i class="bi bi-graph-up text-primary me-2"></i>Tren Nilai Tukar Historis (30 Hari Terakhir)
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <select id="trend-base" class="form-select form-select-sm" style="width: 120px;">
                            @foreach($currencies as $curr)
                                <option value="{{ $curr->code }}" {{ $curr->code == 'USD' ? 'selected' : '' }}>
                                    {{ $curr->code }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-muted small">ke</span>
                        <select id="trend-target" class="form-select form-select-sm" style="width: 120px;">
                            @foreach($currencies as $curr)
                                <option value="{{ $curr->code }}" {{ $curr->code == 'IDR' ? 'selected' : '' }}>
                                    {{ $curr->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="height: 300px; position: relative;">
                    <!-- Loading Overlay -->
                    <div id="trend-loading" class="position-absolute top-50 start-50 translate-middle text-center d-none" style="z-index: 10;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted small mt-2 mb-0">Memuat data tren historis...</p>
                    </div>
                    <canvas id="currencyTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Tom Select JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    let tomSelectInstance = null;

    function convertCurrency() {
        const amount = parseFloat(document.getElementById('usd-amount').value) || 0;
        const select = document.getElementById('target-currency');
        const rate = parseFloat(select.value) || 0;
        const code = select.options[select.selectedIndex].getAttribute('data-code') || '';

        const result = amount * rate;
        document.getElementById('conversion-result').innerText = `${amount.toLocaleString()} USD = ${result.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})} ${code}`;
    }

    let trendChart = null;

    function loadTrendChart() {
        const base = document.getElementById('trend-base').value;
        const target = document.getElementById('trend-target').value;
        const loadingEl = document.getElementById('trend-loading');

        if (base === target) {
            alert('Mata uang asal dan target tidak boleh sama.');
            return;
        }

        loadingEl.classList.remove('d-none');

        fetch(`/dashboard/api/currency/${target}?base=${base}`)
            .then(response => response.json())
            .then(res => {
                loadingEl.classList.add('d-none');
                if (res.status === 'success' && res.trend) {
                    const labels = res.trend.labels;
                    const data = res.trend.values;

                    if (trendChart) {
                        trendChart.destroy();
                    }

                    const ctx = document.getElementById('currencyTrendChart').getContext('2d');
                    trendChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: `${base} ke ${target}`,
                                data: data,
                                borderColor: '#2563eb',
                                backgroundColor: 'rgba(37, 99, 235, 0.05)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.3,
                                pointRadius: 2,
                                pointHoverRadius: 5
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: true, position: 'top' }
                            },
                            scales: {
                                y: {
                                    grid: { color: '#f1f5f9' },
                                    ticks: {
                                        callback: function(value) {
                                            return value.toLocaleString();
                                        }
                                    }
                                },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                }
            })
            .catch(err => {
                loadingEl.classList.add('d-none');
                console.error('Error loading trend chart:', err);
            });
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Initialize Tom Select for converter dropdown
        const selectEl = document.getElementById('target-currency');
        if (selectEl) {
            tomSelectInstance = new TomSelect(selectEl, {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                maxOptions: null,
                onChange: function() {
                    convertCurrency();
                }
            });
        }

        // Initialize Tom Select for trend base dropdown
        const trendBaseEl = document.getElementById('trend-base');
        if (trendBaseEl) {
            new TomSelect(trendBaseEl, {
                create: false,
                sortField: { field: "text", direction: "asc" },
                maxOptions: null,
                onChange: function() {
                    loadTrendChart();
                }
            });
        }

        // Initialize Tom Select for trend target dropdown
        const trendTargetEl = document.getElementById('trend-target');
        if (trendTargetEl) {
            new TomSelect(trendTargetEl, {
                create: false,
                sortField: { field: "text", direction: "asc" },
                maxOptions: null,
                onChange: function() {
                    loadTrendChart();
                }
            });
        }

        convertCurrency();
        loadTrendChart();

        // Major trade currencies to display in the strength visualizer chart
        const majorCurrencies = ['EUR', 'GBP', 'JPY', 'CNY', 'AUD', 'CAD', 'SGD', 'CHF', 'HKD'];
        const currencies = {!! json_encode($currencies) !!};
        
        // Filter out currencies to keep only major ones so that the chart scales and labels look clean and readable
        const filtered = currencies.filter(c => majorCurrencies.includes(c.code));
        const labelsFiltered = filtered.map(c => c.code);
        const dataFiltered = filtered.map(c => c.rate_to_usd);

        const ctx = document.getElementById('currencyStrengthChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labelsFiltered,
                datasets: [{
                    label: 'Nilai terhadap 1 USD',
                    data: dataFiltered,
                    backgroundColor: '#10b981',
                    borderRadius: 4
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
    });
</script>
@endpush
