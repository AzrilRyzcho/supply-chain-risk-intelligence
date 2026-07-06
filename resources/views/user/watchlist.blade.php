@extends('layouts.app')

@section('title', 'Watchlist - RiskIntel')

@section('content')
<div class="container-fluid py-4">
    <!-- Success Feedback Alert -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Add Favorite Form Card -->
    <div class="card card-custom p-4 bg-white border border-light-subtle shadow-sm mb-4">
        <h5 class="fw-bold text-slate-800 mb-3"><i class="bi bi-star-fill text-warning me-2"></i>Tambah Negara ke Daftar Pantauan</h5>
        <form action="{{ route('user.watchlist.add') }}" method="POST" class="row g-2 align-items-center">
            @csrf
            <div class="col-md-8 col-lg-5">
                <select name="country_id" class="form-select @error('country_id') is-invalid @enderror" required>
                    <option value="" disabled selected>Pilih Negara Mitra...</option>
                    @forelse($availableCountries as $ac)
                        <option value="{{ $ac->id }}">{{ $ac->name }} ({{ $ac->code }}) - {{ $ac->region }}</option>
                    @empty
                        <option value="" disabled>Semua negara mitra sudah ada di daftar pantauan Anda.</option>
                    @endforelse
                </select>
                @error('country_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary fw-bold" @if($availableCountries->isEmpty()) disabled @endif>
                    <i class="bi bi-plus-lg me-1"></i>Tambah Favorit
                </button>
            </div>
        </form>
    </div>

    <div class="card card-custom p-4 bg-white border border-light-subtle shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold text-slate-800 mb-1">Daftar Pantauan Strategis</h4>
                <p class="text-muted small mb-0">Kelompokkan negara-negara utama rantai pasok Anda untuk melacak kondisi risiko dan cuaca ekstrem secara instan.</p>
            </div>
            <span class="badge bg-primary fs-6 py-2 px-3">{{ $watchedCountries->count() }} Negara Dipantau</span>
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Negara</th>
                        <th>Wilayah</th>
                        <th>Kode Valas</th>
                        <th class="text-center">Suhu Cuaca</th>
                        <th class="text-center">Risiko Badai</th>
                        <th class="text-center">Indeks Risiko</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($watchedCountries as $c)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-light text-slate-800 rounded-circle d-flex align-items-center justify-content-center fw-bold me-2" 
                                         style="width: 38px; height: 38px;">
                                        {{ $c->code }}
                                    </div>
                                    <div>
                                        <span class="fw-bold text-slate-800 d-block">{{ $c->name }}</span>
                                        <span class="text-muted small">ID: {{ $c->id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $c->region }}</td>
                            <td><span class="fw-bold text-slate-700">{{ $c->currency_code }}</span></td>
                            <td class="text-center">
                                @if($c->weather)
                                    {{ $c->weather->temperature }}°C
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($c->weather)
                                    <span class="fw-bold {{ $c->weather->storm_risk >= 10 ? 'text-danger' : 'text-slate-700' }}">
                                        {{ $c->weather->storm_risk }}%
                                    </span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php $score = $c->riskScores->sortByDesc('calculated_at')->first()->total_score ?? null; @endphp
                                @if($score !== null)
                                    <span class="badge {{ $score >= 50 ? 'bg-danger' : ($score >= 25 ? 'bg-warning text-dark' : 'bg-success') }} py-2 px-3 fw-bold fs-7">
                                        {{ $score }}%
                                    </span>
                                @else
                                    <span class="badge bg-secondary py-2 px-3 fw-bold fs-7">N/A</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('user.country', ['code' => $c->code]) }}" class="btn btn-sm btn-outline-primary fw-bold">
                                        <i class="bi bi-eye me-1"></i>Analisis
                                    </a>
                                    <form action="{{ route('user.watchlist.remove', $c->id) }}" method="POST" onsubmit="return confirm('Hapus {{ $c->name }} dari daftar pantauan Anda?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger fw-bold">
                                            <i class="bi bi-trash me-1"></i>Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <div class="text-slate-300 mb-2" style="font-size: 3rem;">
                                    <i class="bi bi-star"></i>
                                </div>
                                <p class="mb-0">Belum ada negara mitra dagang strategis di daftar favorit Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
