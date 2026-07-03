@extends('layouts.app')

@section('title', 'Dashboard - RiskIntel')

@section('content')
<div class="container-fluid py-4">
    <!-- Welcome Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom p-4 text-white position-relative overflow-hidden" 
                 style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border: 1px solid rgba(255, 255, 255, 0.08);">
                <div class="position-absolute top-50 end-0 translate-middle-y me-4 opacity-10" style="font-size: 6rem; pointer-events: none;">
                    <i class="bi bi-shield-fill-check"></i>
                </div>
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="fw-bold mb-2">Global Supply Chain Risk Intelligence</h1>
                        <p class="lead text-secondary-emphasis mb-0">
                            Pantau cuaca ekstrem, nilai tukar mata uang, kemacetan pelabuhan logistik, inflasi makroekonomi, dan analisis sentimen berita geopolitik global secara *real-time*.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card card-custom p-3 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted text-uppercase small fw-bold">Countries Tracked</span>
                        <h3 class="fw-bold mt-2 mb-0 text-slate-800">{{ $totalCountries }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded p-3">
                        <i class="bi bi-globe fs-3"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-success small fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Aktif Terpantau</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-custom p-3 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted text-uppercase small fw-bold">Active Ports</span>
                        <h3 class="fw-bold mt-2 mb-0 text-slate-800">{{ $totalPorts }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info rounded p-3">
                        <i class="bi bi-anchor fs-3"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-muted small">Titik koordinat terekam</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-custom p-3 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted text-uppercase small fw-bold">Watchlist Items</span>
                        <h3 class="fw-bold mt-2 mb-0 text-slate-800">{{ $watchlistCount }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning rounded p-3">
                        <i class="bi bi-star-fill fs-3"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-muted small">Negara dalam pantauan Anda</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-custom p-3 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted text-uppercase small fw-bold">Threat Status</span>
                        <h3 class="fw-bold mt-2 mb-0 text-danger">Maks {{ $highRiskCountries->max('total_score') ?? 0 }}%</h3>
                    </div>
                    <div class="bg-danger bg-opacity-10 text-danger rounded p-3">
                        <i class="bi bi-exclamation-triangle-fill fs-3"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-danger small fw-bold"><i class="bi bi-shield-fill-exclamation me-1"></i>Tingkat Risiko Tertinggi</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Section: Risk Table & Recent Articles -->
    <div class="row">
        <!-- Risk Intelligence Table -->
        <div class="col-lg-8 mb-4">
            <div class="card card-custom p-4 bg-white h-100">
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
            <div class="card card-custom p-4 bg-white h-100">
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
