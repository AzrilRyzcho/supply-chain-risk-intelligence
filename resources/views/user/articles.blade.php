@extends('layouts.app')

@section('title', 'Articles - RiskIntel')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card card-custom p-4 bg-white mb-4 border border-light-subtle shadow-sm">
        <h4 class="fw-bold text-slate-800 mb-1"><i class="bi bi-journal-text me-2"></i>Supply Chain Intelligence Articles</h4>
        <p class="text-muted small mb-0">Tinjauan mendalam dan analisis strategis dari administrator mengenai hambatan logistik maritim, eskalasi geopolitik, dan mitigasi pasokan.</p>
    </div>

    <!-- Articles Feed -->
    <div class="row">
        @forelse($articles as $article)
            <div class="col-lg-6 mb-4" id="{{ $article->slug }}">
                <div class="card card-custom p-4 bg-white h-100 border border-light-subtle shadow-sm">
                    <span class="badge bg-primary bg-opacity-10 text-primary align-self-start mb-2 px-3 py-1.5 fw-bold">
                        Internal Report
                    </span>
                    <h5 class="fw-bold text-slate-800 mb-2">{{ $article->title }}</h5>
                    <p class="text-secondary small mb-3" style="line-height: 1.6;">
                        {{ $article->content }}
                    </p>
                    <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-light-subtle">
                        <div class="d-flex align-items-center">
                            <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center fw-bold me-2" 
                                 style="width: 32px; height: 32px; font-size: 0.85em;">
                                AD
                            </div>
                            <div>
                                <span class="fw-bold text-slate-700 d-block small">{{ $article->user->name }}</span>
                                <span class="text-muted small" style="font-size: 0.75em;">Analyst Author</span>
                            </div>
                        </div>
                        <span class="text-muted small">
                            <i class="bi bi-calendar-event me-1"></i>
                            {{ $article->published_at ? \Carbon\Carbon::parse($article->published_at)->format('d M Y') : 'Draf' }}
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card card-custom p-5 bg-white text-center text-muted">
                    <h5>Belum ada artikel yang diterbitkan</h5>
                    <p class="small mb-0">Administrator belum mempublikasikan tinjauan logistik terbaru.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
