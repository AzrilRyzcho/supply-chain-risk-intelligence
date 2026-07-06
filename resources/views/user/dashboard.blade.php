@extends('layouts.app')

@section('title', 'Dashboard Analytics - RiskIntel')

@push('styles')
<!-- Leaflet MarkerCluster CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
<style>
    .chart-container {
        position: relative;
        height: 250px;
        width: 100%;
    }
    .radar-chart-container {
        position: relative;
        height: 320px;
        width: 100%;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Quick Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card card-custom card-kpi-countries p-3 bg-white h-100 border border-light-subtle shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted text-uppercase small fw-bold">Countries Tracked</span>
                        <h3 class="fw-bold mt-2 mb-0 text-slate-800">{{ $totalCountries }}</h3>
                    </div>
                    <div class="icon-box-blue">
                        <i class="bi bi-globe fs-3"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-success small fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Aktif Terpantau</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-custom card-kpi-ports p-3 bg-white h-100 border border-light-subtle shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted text-uppercase small fw-bold">Active Ports</span>
                        <h3 class="fw-bold mt-2 mb-0 text-slate-800">{{ $totalPorts }}</h3>
                    </div>
                    <div class="icon-box-green">
                        <i class="bi bi-anchor fs-3"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-muted small">Titik koordinat terekam</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-custom card-kpi-watchlist p-3 bg-white h-100 border border-light-subtle shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted text-uppercase small fw-bold">Watchlist Items</span>
                        <h3 class="fw-bold mt-2 mb-0 text-slate-800">{{ $watchlistCount }}</h3>
                    </div>
                    <div class="icon-box-amber">
                        <i class="bi bi-star-fill fs-3"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-muted small">Negara dalam pantauan Anda</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-custom card-kpi-threat p-3 bg-white h-100 border border-light-subtle shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted text-uppercase small fw-bold">Threat Status</span>
                        <h3 class="fw-bold mt-2 mb-0 text-danger">Maks {{ $highRiskCountries->max('total_score') ?? 0 }}%</h3>
                    </div>
                    <div class="icon-box-red">
                        <i class="bi bi-exclamation-triangle-fill fs-3"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-danger small fw-bold"><i class="bi bi-shield-fill-exclamation me-1"></i>Tingkat Risiko Tertinggi</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Map & Radar Risk Breakdown Row -->
    <div class="row mb-4">
        <!-- Global Risk & Ports Map -->
        <div class="col-lg-8 mb-4">
            <div class="card card-custom p-4 bg-white h-100 border border-light-subtle shadow-sm">
                <h5 class="fw-bold text-slate-800 mb-2"><i class="bi bi-map me-2 text-primary"></i>Peta Risiko & Sebaran Pelabuhan Global</h5>
                <p class="text-muted small mb-3">Lingkaran berwarna memetakan tingkat risiko komposit negara. Marker klaster menampilkan posisi pelabuhan aktif.</p>
                <div id="risk-map" class="rounded border" style="height: 380px; background-color: #f1f5f9; z-index: 1;"></div>
            </div>
        </div>

        <!-- Radar Risk Factor Profile Comparison -->
        <div class="col-lg-4 mb-4">
            <div class="card card-custom p-4 bg-white h-100 border border-light-subtle shadow-sm">
                <h5 class="fw-bold text-slate-800 mb-2"><i class="bi bi-shield-alert me-2 text-primary"></i>Profil Faktor Risiko</h5>
                <p class="text-muted small mb-3">Komparasi 4 pilar indikator ancaman rantai pasok antar negara mitra.</p>
                <div class="radar-chart-container">
                    <canvas id="riskRadarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- GDP, Inflation, Currency & Weather Analytics Charts -->
    <div class="row mb-4">
        <!-- GDP vs Inflation Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card card-custom p-4 bg-white h-100 border border-light-subtle shadow-sm">
                <h5 class="fw-bold text-slate-800 mb-2"><i class="bi bi-bar-chart-line me-2 text-primary"></i>GDP vs Inflasi Makroekonomi</h5>
                <p class="text-muted small mb-3">Membandingkan GDP tahunan terbaru (Miliar USD) dengan tingkat inflasi (%) per negara.</p>
                <div class="chart-container">
                    <canvas id="gdpInflationChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Currency Strength vs USD -->
        <div class="col-lg-3 mb-4">
            <div class="card card-custom p-4 bg-white h-100 border border-light-subtle shadow-sm">
                <h5 class="fw-bold text-slate-800 mb-2"><i class="bi bi-currency-exchange me-2 text-primary"></i>Kurs terhadap USD</h5>
                <p class="text-muted small mb-3">Kekuatan nilai tukar mata uang lokal per 1 USD (Skala IDR ter-filter).</p>
                <div class="chart-container">
                    <canvas id="currencyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Weather Indicators -->
        <div class="col-lg-3 mb-4">
            <div class="card card-custom p-4 bg-white h-100 border border-light-subtle shadow-sm">
                <h5 class="fw-bold text-slate-800 mb-2"><i class="bi bi-cloud-sun me-2 text-primary"></i>Kondisi Cuaca Lokal</h5>
                <p class="text-muted small mb-3">Komparasi suhu rata-rata (°C) dan kecepatan angin (m/s) per negara.</p>
                <div class="chart-container">
                    <canvas id="weatherChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Risk Table & Recent Articles -->
    <div class="row">
        <!-- Risk Intelligence Table -->
        <div class="col-lg-8 mb-4">
            <div class="card card-custom p-4 bg-white h-100 border border-light-subtle shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-slate-800 mb-0">Indeks Risiko Komposit Negara</h5>
                    <a href="{{ route('user.risk') }}" class="btn btn-sm btn-outline-primary fw-bold">Lihat Semua</a>
                </div>
                <p class="text-muted small">Indeks risiko dihitung berdasarkan agregasi metrik Cuaca, Inflasi, Fluktuasi Kurs, dan Sentimen Berita Geopolitik.</p>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Negara</th>
                                <th class="text-center">Cuaca</th>
                                <th class="text-center">Inflasi</th>
                                <th class="text-center">Kurs</th>
                                <th class="text-center">Sentimen</th>
                                <th class="text-center">Total Risiko</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($highRiskCountries as $risk)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="fw-bold text-slate-700">{{ $risk->country->name }}</span>
                                            <span class="badge bg-secondary ms-2">{{ $risk->country->code }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $risk->weather_score }}%</td>
                                    <td class="text-center">{{ $risk->inflation_score }}%</td>
                                    <td class="text-center">{{ $risk->currency_score }}%</td>
                                    <td class="text-center">{{ $risk->sentiment_score }}%</td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <span class="fw-bold text-slate-800 me-2">{{ $risk->total_score }}%</span>
                                            <div class="progress w-100" style="height: 6px; min-width: 50px;">
                                                <div class="progress-bar {{ $risk->total_score >= 50 ? 'bg-danger' : ($risk->total_score >= 30 ? 'bg-warning' : 'bg-success') }}" 
                                                     role="progressbar" 
                                                     style="width: {{ $risk->total_score }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($risk->total_score >= 50)
                                            <span class="badge bg-danger">Tinggi</span>
                                        @elseif($risk->total_score >= 25)
                                            <span class="badge bg-warning text-dark">Sedang</span>
                                        @else
                                            <span class="badge bg-success">Rendah</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Belum ada log perhitungan indeks risiko.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Articles Feed -->
        <div class="col-lg-4 mb-4">
            <div class="card card-custom p-4 bg-white h-100 border border-light-subtle shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-slate-800 mb-0">Analisis Rantai Pasok</h5>
                    <a href="{{ route('user.articles') }}" class="btn btn-sm btn-outline-secondary fw-bold">Semua Artikel</a>
                </div>
                
                <div class="d-flex flex-column gap-3">
                    @forelse($recentArticles as $article)
                        <div class="p-3 rounded border border-light-subtle bg-light hover-shadow" style="transition: 0.2s;">
                            <span class="badge bg-primary bg-opacity-10 text-primary small mb-2">Internal Analysis</span>
                            <h6 class="fw-bold text-slate-800 mb-1">
                                <a href="{{ route('user.articles') }}#{{ $article->slug }}" class="text-decoration-none text-slate-800">
                                    {{ Str::limit($article->title, 50) }}
                                </a>
                            </h6>
                            <p class="text-muted small mb-2">{{ Str::limit($article->content, 90) }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text-secondary small"><i class="bi bi-person me-1"></i>{{ $article->user->name }}</span>
                                <span class="text-secondary small">{{ $article->published_at ? \Carbon\Carbon::parse($article->published_at)->format('d M Y') : 'Draf' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">Belum ada artikel analisis logistik yang diterbitkan.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js and Leaflet MarkerCluster JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // --- 1. INITIALIZE MAP (Leaflet.js) ---
        const map = L.map('risk-map', {
            minZoom: 2,
            maxBounds: [
                [-90, -180],
                [90, 180]
            ],
            maxBoundsViscosity: 1.0
        }).setView([10.0, 40.0], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            noWrap: true,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Clustered Port Markers
        const portClusterGroup = L.markerClusterGroup();
        map.addLayer(portClusterGroup);

        const ports = {!! json_encode($ports) !!};
        ports.forEach(port => {
            if (port.latitude && port.longitude) {
                const marker = L.marker([parseFloat(port.latitude), parseFloat(port.longitude)]);
                const popup = `
                    <div style="font-family: 'Outfit', sans-serif;">
                        <h6 style="margin: 0 0 5px; font-weight: bold; color: #0f172a;"><i class="bi bi-anchor text-primary me-1"></i>${port.name}</h6>
                        <span style="font-size: 0.85em; display: block; color: #475569;"><b>Kode:</b> ${port.code ?? 'N/A'}</span>
                        <span style="font-size: 0.85em; display: block; color: #475569;"><b>Negara:</b> ${port.country ? port.country.name : 'N/A'}</span>
                    </div>
                `;
                marker.bindPopup(popup);
                portClusterGroup.addLayer(marker);
            }
        });

        // Color-Coded Country Risk Markers
        const riskScores = {!! json_encode($highRiskCountries) !!};
        riskScores.forEach(score => {
            if (score.country && score.country.latitude && score.country.longitude) {
                const lat = parseFloat(score.country.latitude);
                const lon = parseFloat(score.country.longitude);

                // Determine risk color
                let riskColor = '#10b981'; // Low risk
                if (score.total_score >= 50) {
                    riskColor = '#ef4444'; // High risk
                } else if (score.total_score >= 25) {
                    riskColor = '#f59e0b'; // Medium risk
                }

                // Add nice circular marker for Country Center
                const circle = L.circleMarker([lat, lon], {
                    radius: 12,
                    fillColor: riskColor,
                    color: '#ffffff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.8
                }).addTo(map);

                const popupContent = `
                    <div style="font-family: 'Outfit', sans-serif; min-width: 180px; line-height: 1.4;">
                        <h6 style="margin: 0 0 5px; font-weight: bold; color: #1e293b;">
                            <i class="bi bi-globe2 text-secondary me-1"></i>${score.country.name}
                        </h6>
                        <hr style="margin: 4px 0; border-color: #e2e8f0;">
                        <span style="display: block; font-size: 0.9em; font-weight: bold; color: ${riskColor};">
                            Risiko Komposit: ${score.total_score}%
                        </span>
                        <span style="display: block; font-size: 0.8em; color: #64748b; margin-top: 4px;"><b>Cuaca:</b> ${score.weather_score}%</span>
                        <span style="display: block; font-size: 0.8em; color: #64748b;"><b>Inflasi:</b> ${score.inflation_score}%</span>
                        <span style="display: block; font-size: 0.8em; color: #64748b;"><b>Kurs:</b> ${score.currency_score}%</span>
                        <span style="display: block; font-size: 0.8em; color: #64748b;"><b>Sentimen:</b> ${score.sentiment_score}%</span>
                    </div>
                `;
                circle.bindPopup(popupContent);
            }
        });


        // --- 2. RADAR CHART (Risk Factor Profiles) ---
        const radarLabels = ['Cuaca', 'Inflasi', 'Kurs', 'Sentimen'];
        const radarColors = ['#2563eb', '#10b981', '#f59e0b', '#ef4444'];
        
        const radarDatasets = riskScores.slice(0, 4).map((score, index) => {
            return {
                label: score.country.name,
                data: [score.weather_score, score.inflation_score, score.currency_score, score.sentiment_score],
                backgroundColor: radarColors[index] + '20', // transparent
                borderColor: radarColors[index],
                borderWidth: 2,
                pointBackgroundColor: radarColors[index]
            };
        });

        const ctxRadar = document.getElementById('riskRadarChart').getContext('2d');
        new Chart(ctxRadar, {
            type: 'radar',
            data: {
                labels: radarLabels,
                datasets: radarDatasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } }
                },
                scales: {
                    r: {
                        angleLines: { display: true },
                        suggestedMin: 0,
                        suggestedMax: 100
                    }
                }
            }
        });


        // --- 3. GDP vs INFLATION CHART (Dual Axis Bar & Line) ---
        // Format labels and datasets
        const rawGdps = {!! json_encode($gdps) !!};
        const rawInflations = {!! json_encode($inflations) !!};
        const countriesMap = {!! json_encode($countries) !!};

        const gdpLabels = [];
        const gdpValues = [];
        const inflationValues = [];

        countriesMap.forEach(country => {
            gdpLabels.push(country.name);
            
            // Get latest year GDP
            const countryGdps = rawGdps[country.id] || [];
            const latestGdp = countryGdps.length > 0 ? countryGdps[0].value : 0;
            gdpValues.push(latestGdp);

            // Get latest year Inflation
            const countryInflations = rawInflations[country.id] || [];
            const latestInflation = countryInflations.length > 0 ? countryInflations[0].rate : 0;
            inflationValues.push(latestInflation);
        });

        const ctxGdp = document.getElementById('gdpInflationChart').getContext('2d');
        new Chart(ctxGdp, {
            type: 'bar',
            data: {
                labels: gdpLabels,
                datasets: [
                    {
                        label: 'GDP (Miliar USD)',
                        data: gdpValues,
                        backgroundColor: 'rgba(37, 99, 235, 0.75)',
                        borderColor: '#2563eb',
                        borderWidth: 1,
                        yAxisID: 'yGdp'
                    },
                    {
                        label: 'Inflasi (%)',
                        data: inflationValues,
                        type: 'line',
                        borderColor: '#ef4444',
                        backgroundColor: '#ef4444',
                        borderWidth: 3,
                        fill: false,
                        yAxisID: 'yInflation',
                        tension: 0.2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    yGdp: {
                        type: 'linear',
                        position: 'left',
                        title: { display: true, text: 'Miliar USD' },
                        grid: { drawOnChartArea: true }
                    },
                    yInflation: {
                        type: 'linear',
                        position: 'right',
                        title: { display: true, text: 'Tingkat Inflasi (%)' },
                        grid: { drawOnChartArea: false } // prevent overlapping grid lines
                    }
                }
            }
        });


        // --- 4. CURRENCY STRENGTH CHART ---
        const rawCurrencies = {!! json_encode($currencies) !!};
        // Exclude IDR from bar chart to preserve readable visual scale for smaller values
        const filteredCurrencies = rawCurrencies.filter(c => c.code !== 'IDR' && c.code !== 'USD');
        const currencyLabels = filteredCurrencies.map(c => c.code);
        const currencyValues = filteredCurrencies.map(c => c.rate_to_usd);

        const ctxCur = document.getElementById('currencyChart').getContext('2d');
        new Chart(ctxCur, {
            type: 'bar',
            data: {
                labels: currencyLabels,
                datasets: [{
                    label: 'Nilai per 1 USD',
                    data: currencyValues,
                    backgroundColor: '#10b981',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });


        // --- 5. WEATHER INDICATORS ---
        const rawWeathers = {!! json_encode($weathers) !!};
        const weatherLabels = rawWeathers.map(w => w.country ? w.country.name : 'N/A');
        const weatherTemps = rawWeathers.map(w => w.temperature);
        const weatherWinds = rawWeathers.map(w => w.wind_speed);

        const ctxWea = document.getElementById('weatherChart').getContext('2d');
        new Chart(ctxWea, {
            type: 'bar',
            data: {
                labels: weatherLabels,
                datasets: [
                    {
                        label: 'Suhu (°C)',
                        data: weatherTemps,
                        backgroundColor: '#f59e0b',
                        borderRadius: 4
                    },
                    {
                        label: 'Angin (m/s)',
                        data: weatherWinds,
                        backgroundColor: '#3b82f6',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true, position: 'top' } },
                scales: {
                    y: { grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endpush
