@extends('layouts.app')

@section('title', 'Risk Analysis - RiskIntel')



@section('content')
<div class="ri-page container-fluid py-4">
    
    <!-- Top Dashboard Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold text-slate-800 mb-1" style="font-size: 1.45rem;">Supply Chain Risk Intelligence</h4>
            <p class="text-muted small mb-0">Dasbor komprehensif analisis ancaman dan kerentanan rantai pasok global</p>
        </div>
    </div>

    <!-- 5 KPI Cards Row -->
    <div class="row g-3 mb-4">
        <!-- 1. Global Risk Index -->
        <div class="col-md">
            <div class="ri-kpi-card shadow-sm h-100">
                <div class="ri-kpi-icon bg-primary-subtle text-primary" style="background:#eff6ff; color:#2563eb;">
                    <i class="bi bi-shield-exclamation"></i>
                </div>
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase" style="font-size:0.68rem; letter-spacing:0.05em;">Global Risk Index</span>
                    <h3 class="fw-bold my-1 text-slate-900" style="font-size:1.45rem;">
                        {{ $globalRiskIndex }}<span class="fs-6 fw-normal text-muted">/100</span>
                    </h3>
                    @php
                        $idxText = 'Low Risk';
                        $idxColor = '#10b981';
                        $idxBg = '#ecfdf5';
                        if ($globalRiskIndex >= 61) { 
                            $idxText = 'High Risk'; $idxColor = '#ef4444'; $idxBg = '#fef2f2'; 
                        } elseif ($globalRiskIndex >= 31) { 
                            $idxText = 'Medium Risk'; $idxColor = '#f59e0b'; $idxBg = '#fffbeb'; 
                        }
                    @endphp
                    <span class="badge border-0 px-2 py-0.5" style="font-size: 0.65rem; font-weight: 700; border-radius: 5px; color:{{ $idxColor }}; background:{{ $idxBg }};">
                        {{ $idxText }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 2. High Risk Countries -->
        <div class="col-md">
            <div class="ri-kpi-card shadow-sm h-100">
                <div class="ri-kpi-icon bg-danger-subtle text-danger" style="background:#fef2f2; color:#ef4444;">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase" style="font-size:0.68rem; letter-spacing:0.05em;">Negara High Risk</span>
                    <h3 class="fw-bold my-1 text-danger" style="font-size:1.45rem;">{{ $highRiskCount }}</h3>
                    <span class="text-muted small" style="font-size:0.72rem;">{{ round(($highRiskCount / max(1, $totalCountries)) * 100, 1) }}% dari total</span>
                </div>
            </div>
        </div>

        <!-- 3. Medium Risk Countries -->
        <div class="col-md">
            <div class="ri-kpi-card shadow-sm h-100">
                <div class="ri-kpi-icon bg-warning-subtle text-warning" style="background:#fffbeb; color:#f59e0b;">
                    <i class="bi bi-activity"></i>
                </div>
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase" style="font-size:0.68rem; letter-spacing:0.05em;">Negara Medium Risk</span>
                    <h3 class="fw-bold my-1 text-warning" style="font-size:1.45rem;">{{ $mediumRiskCount }}</h3>
                    <span class="text-muted small" style="font-size:0.72rem;">{{ round(($mediumRiskCount / max(1, $totalCountries)) * 100, 1) }}% dari total</span>
                </div>
            </div>
        </div>

        <!-- 4. Low Risk Countries -->
        <div class="col-md">
            <div class="ri-kpi-card shadow-sm h-100">
                <div class="ri-kpi-icon bg-success-subtle text-success" style="background:#ecfdf5; color:#10b981;">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase" style="font-size:0.68rem; letter-spacing:0.05em;">Negara Low Risk</span>
                    <h3 class="fw-bold my-1 text-success" style="font-size:1.45rem;">{{ $lowRiskCount }}</h3>
                    <span class="text-muted small" style="font-size:0.72rem;">{{ round(($lowRiskCount / max(1, $totalCountries)) * 100, 1) }}% dari total</span>
                </div>
            </div>
        </div>

        <!-- 5. Last Updated Status -->
        <div class="col-md">
            <div class="ri-kpi-card shadow-sm h-100">
                <div class="ri-kpi-icon bg-secondary-subtle text-secondary" style="background:#f1f5f9; color:#475569;">
                    <i class="bi bi-clock"></i>
                </div>
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase" style="font-size:0.68rem; letter-spacing:0.05em;">Pembaruan Sistem</span>
                    <h5 class="fw-bold mt-1 mb-0 text-slate-800" style="font-size:0.86rem;">{{ now()->translatedFormat('d M Y') }} 11:00</h5>
                    <span class="text-muted small d-block" style="font-size:0.68rem; margin-top:2px;">Kalkulasi berkala tiap 2 jam</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Middle Row: Interactive Global Risk Profiles & Radar Dimensions -->
    <div class="row g-3 mb-4">
        <!-- 1. Radar Dimensions Chart -->
        <div class="col-lg-5">
            <div class="ri-card">
                <div class="border-bottom pb-2 mb-3 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-slate-800 mb-0">Analisis Vektor Risiko Global</h6>
                    <span class="badge bg-light text-dark text-uppercase small" style="font-size:0.62rem;">4 Dimensi</span>
                </div>
                <div class="radar-wrapper">
                    <canvas id="globalRadarChart"></canvas>
                </div>
                <div class="text-center mt-2 text-muted small" style="font-size:0.72rem;">
                    Bagan ini memvisualisasikan kontribusi rata-rata global dari pemicu risiko rantai pasok utama.
                </div>
            </div>
        </div>

        <!-- 2. Middle Center: Risk Factors Status List -->
        <div class="col-lg-4">
            <div class="ri-card">
                <div class="border-bottom pb-2 mb-3">
                    <h6 class="fw-bold text-slate-800 mb-0">Rincian Faktor Risiko Global</h6>
                </div>
                <div class="d-flex flex-column gap-3 py-1">
                    <!-- Cuaca Ekstrem -->
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1.5" style="font-size: 0.78rem;">
                            <span class="text-slate-700 fw-semibold"><i class="bi bi-cloud-lightning-rain text-primary me-2"></i>Cuaca Ekstrem</span>
                            <strong class="text-slate-900">{{ $avgWeather }} /100</strong>
                        </div>
                        <div class="ri-progress-track">
                            <div class="ri-progress-fill bg-danger" style="width: {{ $avgWeather }}%"></div>
                        </div>
                    </div>

                    <!-- Inflasi -->
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1.5" style="font-size: 0.78rem;">
                            <span class="text-slate-700 fw-semibold"><i class="bi bi-graph-up-arrow text-primary me-2"></i>Tingkat Inflasi</span>
                            <strong class="text-slate-900">{{ $avgInflation }} /100</strong>
                        </div>
                        <div class="ri-progress-track">
                            <div class="ri-progress-fill bg-warning" style="width: {{ $avgInflation }}%"></div>
                        </div>
                    </div>

                    <!-- Nilai Tukar -->
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1.5" style="font-size: 0.78rem;">
                            <span class="text-slate-700 fw-semibold"><i class="bi bi-cash-stack text-primary me-2"></i>Volatilitas Kurs</span>
                            <strong class="text-slate-900">{{ $avgCurrency }} /100</strong>
                        </div>
                        <div class="ri-progress-track">
                            <div class="ri-progress-fill" style="background:#facc15; width: {{ $avgCurrency }}%"></div>
                        </div>
                    </div>

                    <!-- Sentimen Berita -->
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1.5" style="font-size: 0.78rem;">
                            <span class="text-slate-700 fw-semibold"><i class="bi bi-chat-heart text-primary me-2"></i>Sentimen Publik</span>
                            <strong class="text-slate-900">{{ $avgSentiment }} /100</strong>
                        </div>
                        <div class="ri-progress-track">
                            <div class="ri-progress-fill bg-success" style="width: {{ $avgSentiment }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Middle Right: Distribution of Risk Categories (Donut) -->
        <div class="col-lg-3">
            <div class="ri-card">
                <div class="border-bottom pb-2 mb-3">
                    <h6 class="fw-bold text-slate-800 mb-0">Porsi Tingkat Risiko</h6>
                </div>
                <div class="position-relative d-flex justify-content-center py-2" style="height: 150px;">
                    <canvas id="riskDistributionChart"></canvas>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                        <h4 class="fw-bold text-slate-900 mb-0" style="font-size: 1.35rem;">{{ $totalCountries }}</h4>
                        <span class="text-muted small" style="font-size: 0.65rem; text-transform:uppercase;">Negara</span>
                    </div>
                </div>
                <div class="d-flex flex-column gap-2 mt-3" style="font-size: 0.74rem;">
                    @php
                        $highPercent = $totalCountries > 0 ? round(($highRiskCount / $totalCountries) * 100, 1) : 0;
                        $mediumPercent = $totalCountries > 0 ? round(($mediumRiskCount / $totalCountries) * 100, 1) : 0;
                        $lowPercent = $totalCountries > 0 ? round(($lowRiskCount / $totalCountries) * 100, 1) : 0;
                    @endphp
                    <div class="d-flex justify-content-between align-items-center pb-1 border-bottom border-light-subtle">
                        <span><span class="legend-dot" style="background-color: #ef4444;"></span>High Risk</span>
                        <strong class="text-slate-800">{{ $highRiskCount }} ({{ $highPercent }}%)</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pb-1 border-bottom border-light-subtle">
                        <span><span class="legend-dot" style="background-color: #f59e0b;"></span>Medium Risk</span>
                        <strong class="text-slate-800">{{ $mediumRiskCount }} ({{ $mediumPercent }}%)</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span><span class="legend-dot" style="background-color: #10b981;"></span>Low Risk</span>
                        <strong class="text-slate-800">{{ $lowRiskCount }} ({{ $lowPercent }}%)</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Table & Sparklines with Realtime Client Search -->
    <div class="row g-3" id="risk-table-section">
        <!-- 1. Country Rankings Table Card -->
        <div class="col-lg-8">
            <div class="ri-card">
                
                <!-- Table Header with Search Bar -->
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <h6 class="fw-bold text-slate-800 mb-0">Peringkat Risiko Negara</h6>
                    <div class="search-input-wrap shadow-sm" style="max-width:240px; padding: 6px 12px; border-radius: 8px;">
                        <i class="bi bi-search text-muted" style="font-size:0.85rem;"></i>
                        <input type="text" id="countrySearchInput" placeholder="Cari negara..." onkeyup="filterCountryTable()" style="border:none; background:transparent; outline:none; font-size:0.8rem; width:100%;">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-hover" style="font-size: 0.84rem;">
                        <thead>
                            <tr class="text-muted small uppercase text-slate-500" style="font-size: 0.74rem; border-bottom: 2px solid #f1f5f9;">
                                <th style="width: 40px;">#</th>
                                <th>NEGARA</th>
                                <th class="text-center">RISK INDEX</th>
                                <th class="text-center">LEVEL</th>
                                <th class="text-center">CUACA</th>
                                <th class="text-center">INFLASI</th>
                                <th class="text-center">KURS</th>
                                <th class="text-center">SENTIMEN</th>
                                <th class="text-center" style="width: 90px;">TREND</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $rank = ($riskScores->currentPage() - 1) * $riskScores->perPage() + 1;
                            @endphp
                            @forelse($riskScores as $risk)
                                @php
                                    $score = $risk->total_score;
                                    $levelBg = '#f1f5f9'; $levelColor = '#64748b'; $levelText = 'Low';
                                    if ($score >= 61) {
                                        $levelBg = '#fee2e2'; $levelColor = '#ef4444'; $levelText = 'High';
                                    } elseif ($score >= 31) {
                                        $levelBg = '#fffbeb'; $levelColor = '#f59e0b'; $levelText = 'Medium';
                                    }
                                    
                                    $history = \App\Models\RiskScore::where('country_id', $risk->country_id)
                                        ->orderBy('id', 'desc')
                                        ->take(6)
                                        ->get()
                                        ->reverse()
                                        ->pluck('total_score');
                                    
                                    $path = '';
                                    if ($history->count() > 1) {
                                        $points = [];
                                        $index = 0;
                                        $step = 80 / ($history->count() - 1);
                                        foreach ($history as $val) {
                                            $x = round($index * $step);
                                            $y = round(23 - (($val / 100) * 20));
                                            $points[] = "$x $y";
                                            $index++;
                                        }
                                        $path = "M " . implode(" L ", $points);
                                    } else {
                                        $path = "M 0 12 L 80 12";
                                    }
                                    
                                    $strokeColor = $score >= 61 ? '#ef4444' : ($score >= 31 ? '#f59e0b' : '#10b981');
                                @endphp
                                <tr class="table-risk-row" data-country-name="{{ $risk->country ? $risk->country->name : '' }}" data-country-iso="{{ $risk->country ? $risk->country->iso3_code : '' }}">
                                    <td>{{ $rank++ }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($risk->country && $risk->country->flag)
                                                <img src="{{ $risk->country->flag }}" style="width: 18px; height: 12px; object-fit: cover; border-radius: 2px; border: 1px solid rgba(0,0,0,0.08);">
                                            @endif
                                            <div>
                                                <span class="fw-bold text-slate-800">{{ $risk->country ? $risk->country->name : 'N/A' }}</span>
                                                <span class="text-muted small uppercase ms-1" style="font-size: 0.68rem;">{{ $risk->country ? $risk->country->iso3_code : '' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold {{ $score >= 61 ? 'text-danger' : ($score >= 31 ? 'text-warning' : 'text-success') }}">
                                        {{ round($score) }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge border-0 px-2 py-1 small" style="background-color: {{ $levelBg }}; color: {{ $levelColor }}; font-weight: 600; border-radius: 6px;">
                                            {{ $levelText }}
                                        </span>
                                    </td>
                                    <td class="text-center text-slate-600">{{ $risk->weather_score }}%</td>
                                    <td class="text-center text-slate-600">{{ $risk->inflation_score }}%</td>
                                    <td class="text-center text-slate-600">{{ $risk->currency_score }}%</td>
                                    <td class="text-center text-slate-600">{{ $risk->sentiment_score }}%</td>
                                    <td class="text-center">
                                        <svg class="sparkline-svg" width="80" height="25" stroke="{{ $strokeColor }}" stroke-width="2" fill="none">
                                            <path d="{{ $path }}" />
                                        </svg>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">Belum ada riwayat kalkulasi indeks risiko.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex align-items-center justify-content-end mt-3 flex-wrap gap-2">
                    @if($riskScores->hasPages())
                        <div>
                            {{ $riskScores->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. Right Side: Highest Increase Countries Sidebar -->
        <div class="col-lg-4">
            <div class="ri-card">
                <div class="border-bottom pb-2 mb-3">
                    <h6 class="fw-bold text-slate-800 mb-0">Lonjakan Risiko Tertinggi</h6>
                </div>
                <div class="d-flex flex-column gap-3 pt-1">
                    @php
                        $incRank = 1;
                    @endphp
                    @forelse($highestIncreases as $inc)
                        @php
                            $country = $inc['country'];
                            $changeVal = $inc['change'];
                            
                            $history = \App\Models\RiskScore::where('country_id', $inc['score_model']->country_id)
                                ->orderBy('id', 'desc')
                                ->take(6)
                                ->get()
                                ->reverse()
                                ->pluck('total_score');
                            
                            $path = '';
                            if ($history->count() > 1) {
                                $points = [];
                                $index = 0;
                                $step = 70 / ($history->count() - 1);
                                foreach ($history as $val) {
                                    $x = round($index * $step);
                                    $y = round(23 - (($val / 100) * 20));
                                    $points[] = "$x $y";
                                    $index++;
                                }
                                $path = "M " . implode(" L ", $points);
                            } else {
                                $path = "M 0 12 L 70 12";
                            }
                        @endphp
                        <div class="d-flex align-items-center justify-content-between py-2 border-bottom border-light-subtle last-no-border" style="font-size: 0.82rem;">
                            <!-- Rank & Flag -->
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-secondary fw-semibold" style="width:14px;">{{ $incRank++ }}</span>
                                @if($country && $country->flag)
                                    <img src="{{ $country->flag }}" style="width: 20px; height: 13px; object-fit: cover; border-radius: 2px; border: 1px solid rgba(0,0,0,0.08);">
                                @endif
                                <div>
                                    <div class="fw-bold text-slate-800 text-truncate" style="max-width:110px;">{{ $country ? $country->name : 'N/A' }}</div>
                                    <span class="text-muted small uppercase" style="font-size: 0.65rem; font-weight: 500;">{{ $country ? $country->iso3_code : '' }}</span>
                                </div>
                            </div>
                            
                            <!-- Change Stats -->
                            <div class="text-end px-1" style="min-width:70px;">
                                <strong class="text-danger" style="font-size: 0.84rem;">↑ {{ number_format($changeVal, 1) }}</strong>
                                <div class="text-muted small" style="font-size: 0.65rem;">vs kemarin</div>
                            </div>

                            <!-- Sparkline path -->
                            <div>
                                <svg width="70" height="25" stroke="#ef4444" stroke-width="1.8" fill="none">
                                    <path d="{{ $path }}" />
                                </svg>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small">Tidak ada data peningkatan risiko.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // --- 1. RADAR CHART: GLOBAL RISK VECTOR ---
        const radarCtx = document.getElementById('globalRadarChart').getContext('2d');
        const isDark = document.body.classList.contains('dark-theme');
        
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(226, 232, 240, 0.8)';
        const labelColor = isDark ? '#94a3b8' : '#475569';
        
        new Chart(radarCtx, {
            type: 'radar',
            data: {
                labels: ['Cuaca Ekstrem', 'Tingkat Inflasi', 'Volatilitas Kurs', 'Sentimen Publik'],
                datasets: [{
                    label: 'Indeks Rata-rata Global',
                    data: [{{ $avgWeather }}, {{ $avgInflation }}, {{ $avgCurrency }}, {{ $avgSentiment }}],
                    backgroundColor: 'rgba(37, 99, 235, 0.15)',
                    borderColor: '#2563eb',
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#2563eb',
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
                    r: {
                        angleLines: { color: gridColor },
                        grid: { color: gridColor },
                        pointLabels: {
                            font: { family: 'Outfit', size: 10.5, weight: '600' },
                            color: labelColor
                        },
                        ticks: { display: false },
                        suggestedMin: 0,
                        suggestedMax: 100
                    }
                }
            }
        });

        // --- 2. DONUT CHART: RISK PORTION DISTRIBUTION ---
        const distCtx = document.getElementById('riskDistributionChart').getContext('2d');
        new Chart(distCtx, {
            type: 'doughnut',
            data: {
                labels: ['High Risk', 'Medium Risk', 'Low Risk'],
                datasets: [{
                    data: [{{ $highRiskCount }}, {{ $mediumRiskCount }}, {{ $lowRiskCount }}],
                    backgroundColor: [
                        '#ef4444', // high (red)
                        '#f59e0b', // medium (orange)
                        '#10b981'  // low (green)
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });

    // --- 3. CLIENT SIDE COUNTRY TABLE SEARCH FILTER ---
    function filterCountryTable() {
        const query = document.getElementById('countrySearchInput').value.toLowerCase();
        const rows = document.querySelectorAll('.table-risk-row');
        rows.forEach(row => {
            const countryName = row.getAttribute('data-country-name').toLowerCase();
            const countryIso = row.getAttribute('data-country-iso').toLowerCase();
            if (countryName.includes(query) || countryIso.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endpush
