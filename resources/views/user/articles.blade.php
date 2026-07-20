@extends('layouts.app')

@section('title', 'Articles - RiskIntel')

@section('content')
<div class="art-page container-fluid py-4">

    @php
        $thumbImages = [
            'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=700&q=80',
            'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=700&q=80',
            'https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?auto=format&fit=crop&w=700&q=80',
            'https://images.unsplash.com/photo-1518241353330-0f7941c2d9b5?auto=format&fit=crop&w=700&q=80',
            'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?auto=format&fit=crop&w=700&q=80',
            'https://images.unsplash.com/photo-1591696205602-2f950c417cb9?auto=format&fit=crop&w=700&q=80',
        ];
        $featuredArticle = $articles->first();
        $recentArticles  = $articles->skip(1);
    @endphp

    {{-- ===== HERO HEADER ===== --}}
    <div class="art-hero mb-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <span class="art-eyebrow">Supply Chain Intelligence</span>
                <h1 class="art-headline mt-2 mb-3">
                    Analyst<br><span class="art-headline-accent">Articles</span>
                </h1>
                <p class="art-subtext">
                    "Tinjauan mendalam dan analisis strategis mengenai hambatan logistik maritim,
                    eskalasi geopolitik, dan mitigasi pasokan global dari tim analis kami."
                </p>
            </div>
            <div class="col-lg-6">
                <div class="art-hero-mosaic">
                    <div class="art-mosaic-img art-mosaic-1" style="background-image:url('https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=400&q=80');"></div>
                    <div class="art-mosaic-img art-mosaic-2" style="background-image:url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=400&q=80');"></div>
                    <div class="art-mosaic-img art-mosaic-3" style="background-image:url('https://images.unsplash.com/photo-1591696205602-2f950c417cb9?auto=format&fit=crop&w=400&q=80');"></div>
                </div>
            </div>
        </div>
    </div>

    @if($featuredArticle)
    {{-- ===== FEATURED ARTICLE ===== --}}
    <div class="mb-5">
        <h2 class="art-section-title mb-4">Artikel Unggulan</h2>

        @php
            $wordCount = str_word_count(strip_tags($featuredArticle->content));
            $readingTime = max(1, round($wordCount / 200));
            $featuredThumb = $thumbImages[$featuredArticle->id % 6];
        @endphp

        <div class="art-featured-card" id="{{ $featuredArticle->slug }}">
            <div class="art-featured-image" style="background-image:url('{{ $featuredThumb }}');"></div>
            <div class="art-featured-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="art-badge"><i class="bi bi-shield-shaded me-1"></i>INTERNAL REPORT</span>
                    <span class="art-meta"><i class="bi bi-hourglass-split me-1"></i>{{ $readingTime }} min read</span>
                </div>
                <span class="art-date-label">{{ $featuredArticle->published_at ? \Carbon\Carbon::parse($featuredArticle->published_at)->format('d M Y') : 'Draft' }}</span>
                <h3 class="art-featured-title">{{ $featuredArticle->title }}</h3>
                <p class="art-featured-excerpt">
                    {{ \Illuminate\Support\Str::limit(strip_tags($featuredArticle->content), 220, '...') }}
                </p>
                <div class="art-author-row mt-auto">
                    <div class="art-avatar">{{ strtoupper(substr($featuredArticle->user->name, 0, 2)) }}</div>
                    <div>
                        <span class="art-author-name">{{ $featuredArticle->user->name }}</span>
                        <span class="art-author-role">Analyst Author</span>
                    </div>
                    @if(strlen($featuredArticle->content) > 220)
                        <button class="art-read-btn ms-auto" data-bs-toggle="modal" data-bs-target="#modal-{{ $featuredArticle->slug }}">
                            Baca Selengkapnya <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== RECENT ARTICLES GRID ===== --}}
    @if($recentArticles->count() > 0)
    <div class="mb-4">
        <h2 class="art-section-title mb-4">Artikel Terbaru</h2>
        <div class="row g-4">
            @foreach($recentArticles as $article)
                @php
                    $wordCount = str_word_count(strip_tags($article->content));
                    $readingTime = max(1, round($wordCount / 200));
                    $thumb = $thumbImages[$article->id % 6];
                @endphp
                <div class="col-lg-4 col-md-6" id="{{ $article->slug }}">
                    <div class="art-card h-100">
                        <div class="art-card-img" style="background-image:url('{{ $thumb }}');"></div>
                        <div class="art-card-body">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="art-badge"><i class="bi bi-shield-shaded me-1"></i>INTERNAL REPORT</span>
                                <span class="art-meta"><i class="bi bi-hourglass-split me-1"></i>{{ $readingTime }} min read</span>
                            </div>
                            <span class="art-date-label">{{ $article->published_at ? \Carbon\Carbon::parse($article->published_at)->format('d M Y') : 'Draft' }}</span>
                            <h5 class="art-card-title">{{ $article->title }}</h5>
                            <p class="art-card-excerpt">
                                {{ \Illuminate\Support\Str::limit(strip_tags($article->content), 130, '...') }}
                            </p>
                            <div class="art-author-row mt-auto">
                                <div class="art-avatar art-avatar-sm">{{ strtoupper(substr($article->user->name, 0, 2)) }}</div>
                                <div>
                                    <span class="art-author-name">{{ $article->user->name }}</span>
                                    <span class="art-author-role">Analyst Author</span>
                                </div>
                            </div>
                            @if(strlen($article->content) > 130)
                                <button class="art-read-btn-outline mt-3 w-100" data-bs-toggle="modal" data-bs-target="#modal-{{ $article->slug }}">
                                    Baca Selengkapnya <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($articles->isEmpty())
        <div class="art-empty-state">
            <i class="bi bi-journal-x"></i>
            <h5>Belum ada artikel yang diterbitkan</h5>
            <p>Administrator belum mempublikasikan tinjauan logistik terbaru.</p>
        </div>
    @endif

    {{-- ===== READ MORE MODALS ===== --}}
    @foreach($articles as $article)
        @if(strlen($article->content) > 130)
            @php
                $wordCount = str_word_count(strip_tags($article->content));
                $readingTime = max(1, round($wordCount / 200));
            @endphp
            <div class="modal fade" id="modal-{{ $article->slug }}" tabindex="-1" aria-labelledby="modalLabel-{{ $article->slug }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                    <div class="modal-content art-modal-content">
                        <div class="art-modal-header">
                            <div class="d-flex align-items-center gap-2">
                                <span class="art-badge"><i class="bi bi-shield-shaded me-1"></i>INTERNAL REPORT</span>
                                <span class="art-meta"><i class="bi bi-hourglass-split me-1"></i>{{ $readingTime }} min read</span>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="art-modal-hero" style="background-image:url('{{ $thumbImages[$article->id % 6] }}');"></div>
                        <div class="art-modal-body">
                            <span class="art-date-label">{{ $article->published_at ? \Carbon\Carbon::parse($article->published_at)->format('d M Y') : 'Draft' }}</span>
                            <h3 class="art-modal-title" id="modalLabel-{{ $article->slug }}">{{ $article->title }}</h3>
                            <div class="art-author-row mb-4 pb-3 border-bottom">
                                <div class="art-avatar">{{ strtoupper(substr($article->user->name, 0, 2)) }}</div>
                                <div>
                                    <span class="art-author-name">{{ $article->user->name }}</span>
                                    <span class="art-author-role">Analyst Author</span>
                                </div>
                            </div>
                            <div class="art-modal-text">{{ $article->content }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

</div>
@endsection
