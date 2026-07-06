@extends('layouts.admin')

@section('title', 'Admin Panel - Ringkasan')

@section('content')
<div class="container-fluid py-4">
    <!-- Welcome Banner -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card card-custom p-4 bg-dark text-white border-0 shadow-sm">
                <h3 class="fw-bold"><i class="bi bi-shield-lock-fill text-danger me-2"></i>Selamat Datang, {{ auth()->user()->name }}!</h3>
                <p class="mb-0 text-white-50">Gunakan panel administrator ini untuk memantau aktivitas sistem, mengelola user, artikel analisis, data pelabuhan global, cache berita, dan data watchlist secara menyeluruh.</p>
            </div>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-6 col-lg-2 mb-3">
            <div class="card card-custom p-3 bg-white border border-light-subtle shadow-sm text-center h-100">
                <div class="text-primary mb-2" style="font-size: 2rem;"><i class="bi bi-people-fill"></i></div>
                <h6 class="text-muted small fw-bold text-uppercase mb-1">Total User</h6>
                <h3 class="fw-bold text-slate-800 mb-0">{{ $usersCount }}</h3>
            </div>
        </div>
        <div class="col-6 col-lg-2 mb-3">
            <div class="card card-custom p-3 bg-white border border-light-subtle shadow-sm text-center h-100">
                <div class="text-success mb-2" style="font-size: 2rem;"><i class="bi bi-journal-text"></i></div>
                <h6 class="text-muted small fw-bold text-uppercase mb-1">Artikel</h6>
                <h3 class="fw-bold text-slate-800 mb-0">{{ $articlesCount }}</h3>
            </div>
        </div>
        <div class="col-6 col-lg-2 mb-3">
            <div class="card card-custom p-3 bg-white border border-light-subtle shadow-sm text-center h-100">
                <div class="text-info mb-2" style="font-size: 2rem;"><i class="bi bi-anchor"></i></div>
                <h6 class="text-muted small fw-bold text-uppercase mb-1">Pelabuhan</h6>
                <h3 class="fw-bold text-slate-800 mb-0">{{ $portsCount }}</h3>
            </div>
        </div>
        <div class="col-6 col-lg-2 mb-3">
            <div class="card card-custom p-3 bg-white border border-light-subtle shadow-sm text-center h-100">
                <div class="text-warning mb-2" style="font-size: 2rem;"><i class="bi bi-globe"></i></div>
                <h6 class="text-muted small fw-bold text-uppercase mb-1">Negara</h6>
                <h3 class="fw-bold text-slate-800 mb-0">{{ $countriesCount }}</h3>
            </div>
        </div>
        <div class="col-6 col-lg-2 mb-3">
            <div class="card card-custom p-3 bg-white border border-light-subtle shadow-sm text-center h-100">
                <div class="text-danger mb-2" style="font-size: 2rem;"><i class="bi bi-star-fill"></i></div>
                <h6 class="text-muted small fw-bold text-uppercase mb-1">Watchlists</h6>
                <h3 class="fw-bold text-slate-800 mb-0">{{ $watchlistsCount }}</h3>
            </div>
        </div>
        <div class="col-6 col-lg-2 mb-3">
            <div class="card card-custom p-3 bg-white border border-light-subtle shadow-sm text-center h-100">
                <div class="text-secondary mb-2" style="font-size: 2rem;"><i class="bi bi-database-fill"></i></div>
                <h6 class="text-muted small fw-bold text-uppercase mb-1">Cache Berita</h6>
                <h3 class="fw-bold text-slate-800 mb-0">{{ $newsCount }}</h3>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Registered Users -->
        <div class="col-lg-4 mb-4">
            <div class="card card-custom p-4 bg-white border border-light-subtle shadow-sm h-100">
                <h5 class="fw-bold text-slate-800 mb-3"><i class="bi bi-person-plus text-primary me-2"></i>Pendaftar Terbaru</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead>
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
                                        <span class="text-muted small">{{ $ru->email }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $ru->role === 'admin' ? 'bg-danger' : 'bg-secondary' }}">{{ $ru->role }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Belum ada user terdaftar.</td>
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
                <h5 class="fw-bold text-slate-800 mb-3"><i class="bi bi-journal-text text-success me-2"></i>Artikel Terbaru</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentArticles as $ra)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-slate-700 d-block text-truncate" style="max-width: 180px;">{{ $ra->title }}</span>
                                        <span class="text-muted small">Oleh: {{ $ra->user ? $ra->user->name : 'System' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $ra->published_at ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ $ra->published_at ? 'Terbit' : 'Draf' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Belum ada artikel.</td>
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
                <h5 class="fw-bold text-slate-800 mb-3"><i class="bi bi-shield-check text-warning me-2"></i>Skor Risiko Terbaru</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead>
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
                                        <span class="text-muted small">{{ $lrs->calculated_at ? \Carbon\Carbon::parse($lrs->calculated_at)->diffForHumans() : 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $lrs->total_score >= 50 ? 'bg-danger' : ($lrs->total_score >= 25 ? 'bg-warning text-dark' : 'bg-success') }}">
                                            {{ $lrs->total_score }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Belum ada skor risiko.</td>
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
