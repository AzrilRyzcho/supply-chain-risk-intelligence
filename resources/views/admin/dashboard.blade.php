@extends('layouts.admin')

@section('title', 'Admin Panel - Ringkasan')

@section('content')
<div class="container-fluid py-4">
    <!-- Clean Welcome Card -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card card-custom p-4 bg-white border border-light-subtle shadow-sm">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-danger" style="width: 50px; height: 50px; background-color: #fef2f2;">
                        <i class="bi bi-shield-lock-fill fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-slate-800 mb-1">Selamat Datang, Administrator!</h4>
                        <p class="text-muted small mb-0">Kelola pengguna, artikel analisis, data pelabuhan global, cache berita, dan data watchlist secara terpusat.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Premium SaaS KPI Metrics Cards -->
    <div class="row mb-4">
        <!-- Total User -->
        <div class="col-6 col-lg-2 mb-3">
            <div class="card card-custom p-3 bg-white border border-light-subtle shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total User</span>
                        <h3 class="fw-bold text-slate-800 mb-0">{{ $usersCount }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 40px; height: 40px; background-color: #eff6ff;">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Artikel -->
        <div class="col-6 col-lg-2 mb-3">
            <div class="card card-custom p-3 bg-white border border-light-subtle shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Artikel</span>
                        <h3 class="fw-bold text-slate-800 mb-0">{{ $articlesCount }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-success" style="width: 40px; height: 40px; background-color: #ecfdf5;">
                        <i class="bi bi-journal-text fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pelabuhan -->
        <div class="col-6 col-lg-2 mb-3">
            <div class="card card-custom p-3 bg-white border border-light-subtle shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Pelabuhan</span>
                        <h3 class="fw-bold text-slate-800 mb-0">{{ $portsCount }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-info" style="width: 40px; height: 40px; background-color: #f0fdfa;">
                        <i class="bi bi-anchor fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Negara -->
        <div class="col-6 col-lg-2 mb-3">
            <div class="card card-custom p-3 bg-white border border-light-subtle shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Negara</span>
                        <h3 class="fw-bold text-slate-800 mb-0">{{ $countriesCount }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-warning" style="width: 40px; height: 40px; background-color: #fffbeb;">
                        <i class="bi bi-globe fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Watchlists -->
        <div class="col-6 col-lg-2 mb-3">
            <div class="card card-custom p-3 bg-white border border-light-subtle shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Watchlists</span>
                        <h3 class="fw-bold text-slate-800 mb-0">{{ $watchlistsCount }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-danger" style="width: 40px; height: 40px; background-color: #fef2f2;">
                        <i class="bi bi-star-fill fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cache Berita -->
        <div class="col-6 col-lg-2 mb-3">
            <div class="card card-custom p-3 bg-white border border-light-subtle shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Cache Berita</span>
                        <h3 class="fw-bold text-slate-800 mb-0">{{ $newsCount }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-secondary" style="width: 40px; height: 40px; background-color: #f8fafc;">
                        <i class="bi bi-database-fill fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Grid -->
    <div class="row">
        <!-- Recent Registered Users -->
        <div class="col-lg-4 mb-4">
            <div class="card card-custom p-4 bg-white border border-light-subtle shadow-sm h-100">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-person-plus text-primary fs-5 me-2"></i>
                    <h5 class="fw-bold text-slate-800 mb-0">Pendaftar Terbaru</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentUsers as $ru)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-slate-700 d-block">{{ $ru->name }}</span>
                                        <span class="text-muted small" style="font-size: 0.78rem;">{{ $ru->email }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $ru->role === 'admin' ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-light text-secondary border' }} px-2 py-1" style="font-size: 0.7rem;">
                                            {{ strtoupper($ru->role) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-3">Belum ada user terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Published Articles -->
        <div class="col-lg-4 mb-4">
            <div class="card card-custom p-4 bg-white border border-light-subtle shadow-sm h-100">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-journal-text text-success fs-5 me-2"></i>
                    <h5 class="fw-bold text-slate-800 mb-0">Artikel Terbaru</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Judul</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentArticles as $ra)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-slate-700 d-block text-truncate" style="max-width: 170px;">{{ $ra->title }}</span>
                                        <span class="text-muted small" style="font-size: 0.78rem;">Oleh: {{ $ra->user ? $ra->user->name : 'System' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $ra->published_at ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' }} px-2 py-1" style="font-size: 0.7rem;">
                                            {{ $ra->published_at ? 'TERBIT' : 'DRAF' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-3">Belum ada artikel.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Latest Risk Scores Calculations -->
        <div class="col-lg-4 mb-4">
            <div class="card card-custom p-4 bg-white border border-light-subtle shadow-sm h-100">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-shield-check text-warning fs-5 me-2"></i>
                    <h5 class="fw-bold text-slate-800 mb-0">Skor Risiko Terbaru</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Negara</th>
                                <th>Skor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestRiskScores as $lrs)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-slate-700 d-block">{{ $lrs->country ? $lrs->country->name : 'N/A' }}</span>
                                        <span class="text-muted small" style="font-size: 0.78rem;">{{ $lrs->calculated_at ? \Carbon\Carbon::parse($lrs->calculated_at)->diffForHumans() : 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $lrs->total_score >= 50 ? 'bg-danger' : ($lrs->total_score >= 25 ? 'bg-warning text-dark' : 'bg-success') }} px-2 py-1 fw-bold" style="font-size: 0.72rem;">
                                            {{ $lrs->total_score }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-3">Belum ada skor risiko.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
