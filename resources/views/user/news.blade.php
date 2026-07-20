@extends('layouts.app')

@section('title', 'News Intelligence - RiskIntel')



@section('content')
@php
$newsMap = [];
foreach ($news->items() as $ni) {
    $newsMap[$ni->id] = [
        'title'     => preg_replace('/^(Logistics|Trade|Shipping|Economy):\s*/i', '', $ni->title),
        'source'    => $ni->source,
        'sentiment' => $ni->sentiment,
        'risk'      => $ni->risk_score,
        'published' => (string) $ni->published_at,
        'url'       => $ni->url,
        'country'   => $ni->country ? $ni->country->name : 'Global',
        'flag'      => $ni->country ? ($ni->country->flag ?? '') : '',
    ];
}

$totalSent = $positiveCount + $neutralCount + $negativeCount;
$pctPos = $totalSent > 0 ? round(($positiveCount / $totalSent) * 100) : 0;
$pctNeu = $totalSent > 0 ? round(($neutralCount / $totalSent) * 100) : 0;
$pctNeg = $totalSent > 0 ? round(($negativeCount / $totalSent) * 100) : 0;
@endphp
<div class="ni-page container-fluid px-0">

    <!-- Page Title -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:#0f172a;">Global Intelligence Center</h4>
            <p class="text-muted mb-0" style="font-size:0.84rem;">Real-time supply chain disruptions feed, risk metrics, and country analyses.</p>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="ni-filter-card mb-4">
        <form method="GET" action="{{ route('user.news') }}" class="d-flex flex-wrap align-items-center gap-3">
            <div class="search-input-wrap">
                <i class="bi bi-search text-muted"></i>
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari isu, pelabuhan, kargo, perusahaan...">
            </div>

            <select name="category" onchange="this.form.submit()" class="filter-select">
                <option value="">Semua Kategori</option>
                <option value="Logistics" {{ $category === 'Logistics' ? 'selected' : '' }}>Logistics</option>
                <option value="Trade" {{ $category === 'Trade' ? 'selected' : '' }}>Trade</option>
                <option value="Shipping" {{ $category === 'Shipping' ? 'selected' : '' }}>Shipping</option>
                <option value="Economy" {{ $category === 'Economy' ? 'selected' : '' }}>Economy</option>
            </select>

            <select name="country_id" onchange="this.form.submit()" class="filter-select">
                <option value="">Semua Negara</option>
                @foreach($countries as $c)
                    <option value="{{ $c->id }}" {{ (string)$countryId === (string)$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>

            <select name="sentiment" onchange="this.form.submit()" class="filter-select">
                <option value="">Semua Sentiment</option>
                <option value="positive" {{ $sentiment === 'positive' ? 'selected' : '' }}>Positif</option>
                <option value="neutral" {{ $sentiment === 'neutral' ? 'selected' : '' }}>Netral</option>
                <option value="negative" {{ $sentiment === 'negative' ? 'selected' : '' }}>Negatif</option>
            </select>

            <select name="period" onchange="this.form.submit()" class="filter-select">
                <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Semua Waktu</option>
                <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Hari Ini</option>
                <option value="week" {{ $period === 'week' ? 'selected' : '' }}>7 Hari Terakhir</option>
                <option value="month" {{ $period === 'month' ? 'selected' : '' }}>30 Hari Terakhir</option>
            </select>
        </form>
    </div>

    <!-- Layout Grid -->
    <div class="row g-4">
        
        <!-- Left 8-col: Modern Grid Feed -->
        <div class="col-lg-8" id="news-feed-section">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="fw-bold" style="font-size:0.92rem;color:#0f172a;">Berita & Analisis Terbaru</span>
                <span class="text-muted fw-semibold" style="font-size:0.75rem;">Menampilkan {{ $news->count() }} dari {{ $news->total() }} berita</span>
            </div>

            <div class="news-grid">
                @forelse($news as $item)
                    @php
                        $catClean = 'Logistics';
                        $lowerTitle = strtolower($item->title);
                        if (str_contains($lowerTitle, 'shipping') || str_contains($lowerTitle, 'vessel') || str_contains($lowerTitle, 'maritime')) $catClean = 'Shipping';
                        elseif (str_contains($lowerTitle, 'trade') || str_contains($lowerTitle, 'export') || str_contains($lowerTitle, 'import') || str_contains($lowerTitle, 'tariff')) $catClean = 'Trade';
                        elseif (str_contains($lowerTitle, 'economy') || str_contains($lowerTitle, 'inflation') || str_contains($lowerTitle, 'gdp') || str_contains($lowerTitle, 'currency')) $catClean = 'Economy';

                        $catColors = [
                            'Logistics' => ['bg'=>'#eff6ff','color'=>'#2563eb'],
                            'Trade' => ['bg'=>'#ecfdf5','color'=>'#10b981'],
                            'Shipping' => ['bg'=>'#f0f9ff','color'=>'#0284c7'],
                            'Economy' => ['bg'=>'#fffbeb','color'=>'#f59e0b'],
                        ];
                        $catStyle = $catColors[$catClean] ?? $catColors['Logistics'];

                        $sentStyle = ['bg'=>'#f1f5f9','color'=>'#64748b','label'=>'Netral'];
                        if ($item->sentiment === 'positive') $sentStyle = ['bg'=>'#ecfdf5','color'=>'#10b981','label'=>'Positif'];
                        elseif ($item->sentiment === 'negative') $sentStyle = ['bg'=>'#fef2f2','color'=>'#ef4444','label'=>'Negatif'];

                        $riskColor = $item->risk_score >= 61 ? '#ef4444' : ($item->risk_score >= 31 ? '#f59e0b' : '#10b981');
                        $riskBg = $item->risk_score >= 61 ? '#fef2f2' : ($item->risk_score >= 31 ? '#fffbeb' : '#ecfdf5');

                        $thumbImages = [
                            'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=350&q=80',
                            'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=350&q=80',
                            'https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?auto=format&fit=crop&w=350&q=80',
                            'https://images.unsplash.com/photo-1518241353330-0f7941c2d9b5?auto=format&fit=crop&w=350&q=80',
                            'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?auto=format&fit=crop&w=350&q=80',
                        ];
                        $thumb = $thumbImages[$item->id % 5];
                    @endphp
                    <div class="news-card-wrapper" onclick="openDrawer({{ $item->id }})">
                        <div class="news-card-image-wrap">
                            <div class="news-card-image" style="background-image:url('{{ $thumb }}');"></div>
                            <span class="position-absolute top-3 start-3 ni-tag fw-bold" style="background:{{ $catStyle['bg'] }};color:{{ $catStyle['color'] }}; top: 12px; left: 12px;">
                                {{ $catClean }}
                            </span>
                        </div>
                        <div class="news-card-body">
                            <div>
                                <div class="news-card-title">{{ $newsMap[$item->id]['title'] }}</div>
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="ni-tag" style="background:{{ $sentStyle['bg'] }};color:{{ $sentStyle['color'] }};">{{ $sentStyle['label'] }}</span>
                                    <span class="text-muted d-flex align-items-center gap-1" style="font-size:0.7rem; font-weight: 500;">
                                        @if($item->country && $item->country->flag)
                                            <img src="{{ $item->country->flag }}" style="width:14px;height:9px;object-fit:cover;border-radius:1px;">
                                        @else
                                            <i class="bi bi-globe"></i>
                                        @endif
                                        {{ $item->country ? $item->country->name : 'Global' }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <div class="pt-2 border-top">
                                    <div class="d-flex align-items-center justify-content-between mb-1" style="font-size:0.72rem;">
                                        <span class="text-muted">Disruption Risk Score</span>
                                        <strong style="color:{{ $riskColor }};">{{ $item->risk_score }}/100</strong>
                                    </div>
                                    <div class="mini-progress-track">
                                        <div class="mini-progress-fill" style="width:{{ $item->risk_score }}%; background:{{ $riskColor }};"></div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top text-muted" style="font-size:0.68rem;">
                                    <span><i class="bi bi-building me-1"></i>{{ Str::limit($item->source, 15) }}</span>
                                    <span><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($item->published_at)->format('d M H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 py-5 text-center bg-white border rounded-3">
                        <i class="bi bi-journal-x text-muted" style="font-size:2.4rem;"></i>
                        <p class="text-muted mt-3 mb-0" style="font-size:0.86rem;">Tidak ada berita atau artikel ditemukan.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($news->hasPages())
                <div class="mt-4 pt-3 border-top d-flex justify-content-center">
                    {{ $news->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        <!-- Right 4-col: Analytics Sidebar -->
        <div class="col-lg-4">
            
            <!-- Overall Risk Hub -->
            <div class="ni-sidebar-card mb-4 text-center py-4 bg-primary text-white" style="border:none;">
                <h6 class="text-white-50 fw-semibold mb-2" style="font-size:0.78rem; text-transform: uppercase; letter-spacing:0.08em;">Global News Risk Index</h6>
                <div class="display-5 fw-bold mb-1">{{ $riskIndex }} <span style="font-size:1.2rem;opacity:0.6;">/100</span></div>
                <div class="d-inline-block px-3 py-1 fw-bold rounded-pill" style="font-size:0.74rem; background:rgba(255,255,255,0.25);">
                    RATING: {{ $riskIndex >= 61 ? 'High Risk' : ($riskIndex >= 31 ? 'Medium Risk' : 'Low Risk') }}
                </div>
            </div>

            <!-- Sentiment Overview donut -->
            <div class="ni-sidebar-card mb-4">
                <div class="ni-card-title mb-3">Distribusi Sentiment</div>
                <div class="d-flex align-items-center gap-3">
                    <div style="position: relative; width: 120px; height: 120px; flex-shrink:0;">
                        <canvas id="sentimentDonut"></canvas>
                    </div>
                    <div style="font-size:0.75rem; flex:1;">
                        <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                            <span><span class="d-inline-block me-1 rounded-circle" style="width:8px;height:8px;background:#10b981;"></span>Positif</span>
                            <strong>{{ $positiveCount }} ({{ $pctPos }}%)</strong>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                            <span><span class="d-inline-block me-1 rounded-circle" style="width:8px;height:8px;background:#f59e0b;"></span>Netral</span>
                            <strong>{{ $neutralCount }} ({{ $pctNeu }}%)</strong>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span><span class="d-inline-block me-1 rounded-circle" style="width:8px;height:8px;background:#ef4444;"></span>Negatif</span>
                            <strong>{{ $negativeCount }} ({{ $pctNeg }}%)</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category overview bar -->
            <div class="ni-sidebar-card mb-4">
                <div class="ni-card-title mb-3">Isu per Kategori</div>
                <div style="position: relative; height: 150px; width: 100%;">
                    <canvas id="categoryBar"></canvas>
                </div>
            </div>

            <!-- Popular Tags cloud -->
            <div class="ni-sidebar-card">
                <div class="ni-card-title mb-3">Sinyal Trending</div>
                <div>
                    @php
                        $tags = [
                            ['txt'=>'maritime','clr'=>'#2563eb','bg'=>'#eff6ff'],
                            ['txt'=>'congestion','clr'=>'#ef4444','bg'=>'#fef2f2'],
                            ['txt'=>'inflation','clr'=>'#f59e0b','bg'=>'#fffbeb'],
                            ['txt'=>'vessel','clr'=>'#0284c7','bg'=>'#f0f9ff'],
                            ['txt'=>'tariff','clr'=>'#475569','bg'=>'#f1f5f9'],
                            ['txt'=>'export','clr'=>'#10b981','bg'=>'#ecfdf5'],
                            ['txt'=>'oil price','clr'=>'#b45309','bg'=>'#fffbeb'],
                            ['txt'=>'conflict','clr'=>'#ef4444','bg'=>'#fef2f2'],
                        ];
                    @endphp
                    @foreach($tags as $t)
                        <a href="{{ route('user.news') }}?q={{ urlencode($t['txt']) }}" class="keyword-pill">
                            #{{ $t['txt'] }}
                        </a>
                    @endforeach
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Drawer Slideout Details panel overlay -->
<div class="news-drawer-backdrop" id="drawerBackdrop" onclick="closeDrawer()"></div>
<div class="news-drawer" id="newsDrawer">
    <div class="drawer-header" id="drawerBanner">
        <button class="drawer-close-btn" onclick="closeDrawer()"><i class="bi bi-x"></i></button>
    </div>
    <div class="drawer-body">
        <div class="d-flex align-items-center gap-2 mb-2">
            <span class="ni-tag" id="drawerCategory">-</span>
            <span class="ni-tag" id="drawerSentiment">-</span>
        </div>
        <h5 class="fw-bold mb-3" id="drawerTitle" style="line-height:1.4; color:#0f172a;">-</h5>

        <div class="d-flex flex-wrap align-items-center gap-3 text-muted mb-4 pb-3 border-bottom" style="font-size:0.75rem;">
            <span id="drawerSource"><i class="bi bi-building me-1"></i>-</span>
            <span id="drawerDate"><i class="bi bi-clock me-1"></i>-</span>
            <span id="drawerCountry"></span>
        </div>

        <div class="mb-4">
            <h6 class="fw-bold mb-2 text-muted" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.05em;">Analisis Dampak Kejadian</h6>
            <p id="drawerDesc" class="text-muted" style="font-size:0.82rem; line-height:1.6;">-</p>
        </div>

        <!-- Supply chain progress meters -->
        <div class="mb-4 p-3 bg-light rounded-3">
            <h6 class="fw-bold mb-3 text-dark" style="font-size:0.78rem;">Supply Chain Impact Levels</h6>
            
            <div class="mb-2">
                <div class="d-flex align-items-center justify-content-between mb-1" style="font-size:0.72rem;">
                    <span>Pelayaran & Pelabuhan (Shipping)</span>
                    <strong id="barLabelShipping">-</strong>
                </div>
                <div class="progress-bar-custom">
                    <div class="progress-fill" id="barFillShipping" style="width: 0%;"></div>
                </div>
            </div>

            <div class="mb-2">
                <div class="d-flex align-items-center justify-content-between mb-1" style="font-size:0.72rem;">
                    <span>Volume Ekspor & Impor (Trade)</span>
                    <strong id="barLabelTrade">-</strong>
                </div>
                <div class="progress-bar-custom">
                    <div class="progress-fill" id="barFillTrade" style="width: 0%;"></div>
                </div>
            </div>

            <div class="mb-2">
                <div class="d-flex align-items-center justify-content-between mb-1" style="font-size:0.72rem;">
                    <span>Kurs & Nilai Tukar (Currency)</span>
                    <strong id="barLabelCurrency">-</strong>
                </div>
                <div class="progress-bar-custom">
                    <div class="progress-fill" id="barFillCurrency" style="width: 0%;"></div>
                </div>
            </div>

            <div class="mb-3">
                <div class="d-flex align-items-center justify-content-between mb-1" style="font-size:0.72rem;">
                    <span>Tingkat Harga & Inflasi (Inflation)</span>
                    <strong id="barLabelInflation">-</strong>
                </div>
                <div class="progress-bar-custom">
                    <div class="progress-fill" id="barFillInflation" style="width: 0%;"></div>
                </div>
            </div>

            <div class="pt-2 border-top">
                <div class="d-flex align-items-center justify-content-between" style="font-size:0.78rem;">
                    <span class="fw-bold">Overall Disruption Score</span>
                    <strong id="drawerRiskScore" class="px-2 py-1 rounded-2" style="font-size:0.8rem;">-</strong>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a id="drawerReadBtn" href="#" target="_blank" rel="noopener noreferrer"
               class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2 fw-semibold"
               style="border-radius:10px; font-size:0.84rem; background:#2563eb; border-color:#2563eb; padding:10px; color:#fff;">
                <i class="bi bi-box-arrow-up-right"></i>
                Kunjungi Sumber Berita
            </a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    // ========= Charts =========
    // Sentiment Donut
    new Chart(document.getElementById('sentimentDonut'), {
        type: 'doughnut',
        data: {
            labels: ['Positif', 'Netral', 'Negatif'],
            datasets: [{
                data: [{{ $positiveCount }}, {{ $neutralCount }}, {{ $negativeCount }}],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                borderWidth: 0,
                borderRadius: 5,
                borderSkipped: false,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Category Bar Chart
    new Chart(document.getElementById('categoryBar'), {
        type: 'bar',
        data: {
            labels: ['Logistics', 'Trade', 'Shipping', 'Economy'],
            datasets: [{
                data: [{{ $logisticsCount }}, {{ $tradeCount }}, {{ $shippingCount }}, {{ $economyCount }}],
                backgroundColor: ['#2563eb', '#10b981', '#0284c7', '#f59e0b'],
                borderWidth: 0,
                borderRadius: 4,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 9 } } },
                x: { grid: { display: false }, ticks: { font: { size: 9 } } }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // ========= Drawer Detail Panel =========
    const newsMap = {!! json_encode($newsMap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};

    const bannerImages = {
        'Shipping': 'https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?auto=format&fit=crop&w=800&q=80',
        'Trade':    'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=800&q=80',
        'Economy':  'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?auto=format&fit=crop&w=800&q=80',
        'Logistics':'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=800&q=80',
    };

    function detectCategory(title) {
        const t = title.toLowerCase();
        if (/shipping|vessel|maritime|pelabuhan|kapal|laut/.test(t)) return 'Shipping';
        if (/trade|export|import|tariff|perdagangan|ekspor|impor/.test(t)) return 'Trade';
        if (/economy|inflation|gdp|currency|ekonomi|inflasi|rupiah|depresiasi/.test(t)) return 'Economy';
        return 'Logistics';
    }

    function descriptionFor(title, category, countryName) {
        countryName = countryName || 'Global';
        if (/Red Sea|Laut Merah/.test(title)) return 'Eskalasi konflik di Laut Merah memaksa kapal kontainer besar mengalihkan rute memutar Tanjung Harapan. Hal ini menambah waktu transit 10-14 hari dan memicu lonjakan tarif pengiriman kontainer internasional.';
        if (/NLE|Logistik Nasional|Ecosystem/.test(title)) return 'Pemerintah Indonesia mengintegrasikan National Logistics Ecosystem (NLE) untuk menyederhanakan birokrasi, menurunkan biaya logistik, dan meningkatkan daya saing ekspor di pasar global.';
        if (/congestion|kemacetan|Macet/.test(title)) return 'Terjadi penumpukan kargo di pelabuhan ' + countryName + '. Kondisi ini berdampak langsung pada keterlambatan distribusi barang di seluruh rantai pasok regional.';
        if (/inflation|inflasi|depresiasi|stagnate/.test(title)) return 'Tingkat inflasi dan tekanan nilai tukar di ' + countryName + ' mendorong penyesuaian harga komoditas dan biaya produksi di seluruh rantai pasok.';
        if (/tariff|bea|duty/.test(title)) return 'Kebijakan tarif baru di ' + countryName + ' berdampak pada biaya impor komoditas strategis. Pelaku usaha logistik perlu menyesuaikan struktur biaya dan jalur distribusi.';
        if (category === 'Shipping') return 'Perkembangan terbaru di sektor pelayaran dari ' + countryName + ' memengaruhi tarif kontainer dan jadwal pengiriman di koridor perdagangan utama global.';
        if (category === 'Trade') return 'Dinamika perdagangan internasional di ' + countryName + ' memberikan dampak pada volume ekspor-impor dan perjanjian dagang bilateral yang berlaku.';
        if (category === 'Economy') return 'Indikator ekonomi di ' + countryName + ' menunjukkan fluktuasi yang berdampak pada daya beli, inflasi, dan stabilitas mata uang dalam konteks rantai pasok global.';
        return 'Perkembangan logistik terkini di ' + countryName + ' berpotensi mempengaruhi efisiensi distribusi dan waktu pengiriman dalam jaringan rantai pasok internasional.';
    }

    function setProgressBar(barId, labelId, percentage, activeClass) {
        const fill = document.getElementById(barId);
        const label = document.getElementById(labelId);
        
        fill.style.width = percentage + '%';
        fill.style.background = percentage >= 70 ? '#ef4444' : (percentage >= 40 ? '#f59e0b' : '#10b981');
        
        const labelText = percentage >= 70 ? 'High (' + percentage + '%)' : (percentage >= 40 ? 'Medium (' + percentage + '%)' : 'Low (' + percentage + '%)');
        label.textContent = labelText;
        label.style.color = percentage >= 70 ? '#ef4444' : (percentage >= 40 ? '#d97706' : '#10b981');
    }

    function renderImpact(category, riskScore) {
        let shippingPct, tradePct, currencyPct, inflationPct;
        
        if (riskScore >= 70) {
            shippingPct = riskScore;
            tradePct = Math.max(30, riskScore - 15);
            currencyPct = Math.max(20, riskScore - 30);
            inflationPct = Math.max(20, riskScore - 25);
        } else if (riskScore >= 40) {
            shippingPct = riskScore;
            tradePct = Math.max(25, riskScore - 10);
            currencyPct = 20;
            inflationPct = 25;
        } else {
            shippingPct = riskScore;
            tradePct = 20;
            currencyPct = 15;
            inflationPct = 15;
        }

        if (category === 'Shipping') {
            setProgressBar('barFillShipping', 'barLabelShipping', shippingPct);
            setProgressBar('barFillTrade', 'barLabelTrade', tradePct);
            setProgressBar('barFillCurrency', 'barLabelCurrency', 15);
            setProgressBar('barFillInflation', 'barLabelInflation', 15);
        } else if (category === 'Trade') {
            setProgressBar('barFillShipping', 'barLabelShipping', Math.max(15, shippingPct - 15));
            setProgressBar('barFillTrade', 'barLabelTrade', tradePct);
            setProgressBar('barFillCurrency', 'barLabelCurrency', 15);
            setProgressBar('barFillInflation', 'barLabelInflation', 15);
        } else if (category === 'Economy') {
            setProgressBar('barFillShipping', 'barLabelShipping', 15);
            setProgressBar('barFillTrade', 'barLabelTrade', tradePct);
            setProgressBar('barFillCurrency', 'barLabelCurrency', currencyPct);
            setProgressBar('barFillInflation', 'barLabelInflation', inflationPct);
        } else {
            setProgressBar('barFillShipping', 'barLabelShipping', shippingPct);
            setProgressBar('barFillTrade', 'barLabelTrade', 15);
            setProgressBar('barFillCurrency', 'barLabelCurrency', 15);
            setProgressBar('barFillInflation', 'barLabelInflation', 15);
        }
    }

    function openDrawer(newsId) {
        const item = newsMap[newsId];
        if (!item) return;

        const cat = detectCategory(item.title);
        const riskScore = parseInt(item.risk) || 0;

        // Banner Image
        document.getElementById('drawerBanner').style.backgroundImage = "url('" + (bannerImages[cat] || bannerImages['Logistics']) + "')";

        // Category Badge
        const catColors = { Shipping:'#0284c7', Trade:'#10b981', Economy:'#f59e0b', Logistics:'#2563eb' };
        const catBgs    = { Shipping:'#f0f9ff', Trade:'#ecfdf5', Economy:'#fffbeb', Logistics:'#eff6ff' };
        const catEl = document.getElementById('drawerCategory');
        catEl.textContent = cat;
        catEl.style.background = catBgs[cat] || '#eff6ff';
        catEl.style.color = catColors[cat] || '#2563eb';

        // Sentiment Badge
        const sentMap = {
            positive: { label:'Positif', bg:'#ecfdf5', color:'#10b981' },
            neutral:  { label:'Netral',  bg:'#f1f5f9', color:'#64748b' },
            negative: { label:'Negatif', bg:'#fef2f2', color:'#ef4444' }
        };
        const s = sentMap[item.sentiment] || sentMap.neutral;
        const sentEl = document.getElementById('drawerSentiment');
        sentEl.textContent = s.label;
        sentEl.style.background = s.bg;
        sentEl.style.color = s.color;

        // Title
        document.getElementById('drawerTitle').textContent = item.title;

        // Source
        document.getElementById('drawerSource').innerHTML = '<i class="bi bi-building me-1"></i>' + item.source;

        // Date
        const d = new Date(item.published);
        const dateStr = isNaN(d.getTime()) ? item.published :
            d.toLocaleDateString('id-ID', {day:'numeric', month:'short', year:'numeric'}) +
            ' ' + String(d.getHours()).padStart(2,'0') + ':' + String(d.getMinutes()).padStart(2,'0') + ' WIB';
        document.getElementById('drawerDate').innerHTML = '<i class="bi bi-clock me-1"></i>' + dateStr;

        // Country Flag & Name
        const countryEl = document.getElementById('drawerCountry');
        countryEl.innerHTML = (item.flag ? '<img src="' + item.flag + '" style="width:16px;height:11px;object-fit:cover;border-radius:2px;margin-right:4px;">' : '<i class="bi bi-globe me-1"></i>') + item.country;

        // Desc
        document.getElementById('drawerDesc').textContent = descriptionFor(item.title, cat, item.country);

        // Progress bars
        renderImpact(cat, riskScore);

        // Risk Score Big badge
        const rsEl = document.getElementById('drawerRiskScore');
        rsEl.textContent = riskScore + ' / 100';
        rsEl.style.background = riskScore >= 61 ? '#fef2f2' : (riskScore >= 31 ? '#fffbeb' : '#ecfdf5');
        rsEl.style.color = riskScore >= 61 ? '#ef4444' : (riskScore >= 31 ? '#f59e0b' : '#10b981');

        // Link button
        const readBtn = document.getElementById('drawerReadBtn');
        if (item.url && item.url !== '#' && item.url.trim() !== '') {
            readBtn.href = item.url;
            readBtn.style.opacity = '1';
            readBtn.style.pointerEvents = 'auto';
        } else {
            readBtn.href = '#';
            readBtn.style.opacity = '0.5';
            readBtn.style.pointerEvents = 'none';
        }

        // Open drawer animations
        document.getElementById('drawerBackdrop').classList.add('active');
        document.getElementById('newsDrawer').classList.add('open');
    }

    function closeDrawer() {
        document.getElementById('drawerBackdrop').classList.remove('active');
        document.getElementById('newsDrawer').classList.remove('open');
    }
</script>
@endpush
