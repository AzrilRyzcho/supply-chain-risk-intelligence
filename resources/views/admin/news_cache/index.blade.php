@extends('layouts.admin')

@section('title', 'Admin Panel - News Cache')

@section('content')
<div class="container-fluid py-4">
    <div class="card card-custom p-4 bg-white border border-light-subtle shadow-sm">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <div>
                <h4 class="fw-bold text-slate-800 mb-1"><i class="bi bi-database-fill text-secondary me-2"></i>Kelola Cache Berita</h4>
                <p class="text-muted small mb-0">Lihat berita GNews yang tersimpan di cache lokal database, hapus per artikel, atau bersihkan seluruh cache.</p>
            </div>
            
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <form action="{{ route('admin.news-cache.clear') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus SELURUH berita di cache dan membersihkan cache GNews?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger fw-bold"><i class="bi bi-trash3-fill me-2"></i>Kosongkan Cache</button>
                </form>

                <form action="{{ route('admin.news-cache.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="Cari judul, sumber, negara..." value="{{ $search }}">
                    <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Judul Berita</th>
                        <th>Sumber</th>
                        <th>Negara Terdeteksi</th>
                        <th>Sentimen</th>
                        <th class="text-center">Diterbitkan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($news as $article)
                        <tr>
                            <td>{{ $article->id }}</td>
                            <td>
                                <a href="{{ $article->url }}" target="_blank" class="fw-bold text-decoration-none text-slate-800 d-block text-truncate" style="max-width: 320px;">
                                    {{ $article->title }}
                                </a>
                            </td>
                            <td><span class="text-muted small">{{ $article->source }}</span></td>
                            <td>
                                @if($article->country)
                                    <span class="badge bg-info text-dark">{{ $article->country->name }} ({{ $article->country->code }})</span>
                                @else
                                    <span class="text-muted small">Global / N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($article->sentiment === 'positive')
                                    <span class="badge bg-success"><i class="bi bi-emoji-smile me-1"></i>Positif</span>
                                @elseif($article->sentiment === 'negative')
                                    <span class="badge bg-danger"><i class="bi bi-emoji-frown me-1"></i>Negatif</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-emoji-neutral me-1"></i>Netral</span>
                                @endif
                            </td>
                            <td class="text-center"><span class="small text-muted">{{ $article->published_at ? \Carbon\Carbon::parse($article->published_at)->format('d M Y') : '-' }}</span></td>
                            <td class="text-center">
                                <form action="{{ route('admin.news-cache.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Hapus artikel ini dari cache database?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger fw-bold">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">Tidak ada berita dalam cache database.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $news->links() }}
        </div>
    </div>
</div>
@endsection
