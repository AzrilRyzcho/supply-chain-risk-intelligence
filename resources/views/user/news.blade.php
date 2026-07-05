@extends('layouts.app')

@section('title', 'News - RiskIntel')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card card-custom p-4 bg-white mb-4 border border-light-subtle shadow-sm">
        <h4 class="fw-bold text-slate-800 mb-1"><i class="bi bi-newspaper me-2"></i>News Intelligence</h4>
        <p class="text-muted small mb-0">Pemantauan sentimen berita logistik dan geopolitik menggunakan integrasi analisis skor polaritas teks (positif, netral, negatif).</p>
    </div>

    <!-- Sentiment Stats Summary -->
    <div class="row mb-4 text-center">
        <div class="col-md-4 mb-3">
            <div class="card card-custom p-3 bg-white border border-light-subtle shadow-sm h-100">
                <span class="text-muted small fw-bold text-uppercase">Berita Positif</span>
                <h2 class="fw-bold mt-2 mb-0 text-success">{{ $positiveCount }}</h2>
                <span class="text-secondary small mt-1">Mengindikasikan kelancaran jalur pasok</span>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card card-custom p-3 bg-white border border-light-subtle shadow-sm h-100">
                <span class="text-muted small fw-bold text-uppercase">Berita Netral</span>
                <h2 class="fw-bold mt-2 mb-0 text-secondary">{{ $neutralCount }}</h2>
                <span class="text-secondary small mt-1">Informasi logistik harian rutin</span>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card card-custom p-3 bg-white border border-light-subtle shadow-sm h-100">
                <span class="text-muted small fw-bold text-uppercase">Berita Negatif</span>
                <h2 class="fw-bold mt-2 mb-0 text-danger">{{ $negativeCount }}</h2>
                <span class="text-secondary small mt-1">Tanda hambatan pasokan & perang dagang</span>
            </div>
        </div>
    </div>

    <!-- News List -->
    <div class="card card-custom p-4 bg-white border border-light-subtle shadow-sm">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 border-bottom pb-3">
            <h5 class="fw-bold text-slate-800 mb-0">Umpan Berita Logistik Global</h5>
            <form action="{{ route('user.news') }}" method="GET" class="d-flex gap-2 align-items-center" style="max-width: 400px; width: 100%;">
                <div class="input-group">
                    <input type="text" name="q" class="form-control form-control-sm border-secondary-subtle" placeholder="Cari berita..." value="{{ $search }}">
                    @if($search)
                        <a href="{{ route('user.news') }}" class="btn btn-outline-secondary btn-sm" title="Hapus pencarian"><i class="bi bi-x-lg"></i></a>
                    @endif
                    <button class="btn btn-primary btn-sm px-3" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
        
        <div class="d-flex flex-column gap-3">
            @forelse($news as $item)
                <div class="p-3 rounded border border-light-subtle bg-light hover-shadow" style="transition: 0.2s;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
                        <div class="d-flex align-items-center">
                            @if($item->country)
                                <span class="badge bg-secondary me-2">{{ $item->country->name }}</span>
                            @else
                                <span class="badge bg-secondary me-2">Global</span>
                            @endif
                            <span class="text-muted small"><i class="bi bi-building me-1"></i>{{ $item->source }}</span>
                        </div>
                        
                        <div class="d-flex align-items-center gap-2">
                            <!-- Sentiment Badge -->
                            @if($item->sentiment === 'positive')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                                    <i class="bi bi-emoji-smile-fill me-1"></i>Positif
                                </span>
                            @elseif($item->sentiment === 'negative')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 small">
                                    <i class="bi bi-emoji-frown-fill me-1"></i>Negatif
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 small">
                                    <i class="bi bi-emoji-neutral-fill me-1"></i>Netral
                                </span>
                            @endif
                            
                            <!-- Sentiment Scores Breakdown -->
                            <span class="text-muted small">
                                (+{{ $item->positive_score }} / -{{ $item->negative_score }})
                            </span>
                        </div>
                    </div>

                    <h6 class="fw-bold text-slate-800 mb-2">
                        @if($item->url)
                            <a href="{{ $item->url }}" target="_blank" class="text-decoration-none text-slate-800 hover-underline">
                                {{ $item->title }} <i class="bi bi-box-arrow-up-right small ms-1" style="font-size: 0.8em;"></i>
                            </a>
                        @else
                            {{ $item->title }}
                        @endif
                    </h6>

                    <div class="text-secondary small d-flex justify-content-between">
                        <span>Diterbitkan: {{ \Carbon\Carbon::parse($item->published_at)->diffForHumans() }}</span>
                        <span class="text-muted">ID: #N{{ $item->id }}</span>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-5">Umpan berita belum terisi data.</div>
            @endforelse
        </div>

        @if($news->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $news->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
