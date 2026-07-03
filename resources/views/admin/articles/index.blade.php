@extends('layouts.admin')

@section('title', 'Kelola Artikel - Panel Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card card-custom p-4 bg-white mb-4 border border-light-subtle shadow-sm">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="fw-bold text-slate-800 mb-1"><i class="bi bi-journal-text me-2"></i>Manajemen Master Artikel</h4>
                <p class="text-muted small mb-0">Kelola publikasi artikel laporan tinjauan risiko dan analisis logistik maritim untuk dibaca oleh Pengguna.</p>
            </div>
            <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#createArticleModal">
                <i class="bi bi-plus-lg me-1"></i>Tulis Artikel
            </button>
        </div>
    </div>

    <!-- Error Alert -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show card-custom mb-4" role="alert">
            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal Menyimpan Artikel:</h6>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Data Table Card -->
    <div class="card card-custom p-4 bg-white border border-light-subtle shadow-sm">
        <!-- Search Form -->
        <form action="{{ route('admin.articles.index') }}" method="GET" class="row g-3 mb-4 justify-content-between align-items-center">
            <div class="col-md-5 col-lg-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-secondary-subtle text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-secondary-subtle" placeholder="Cari judul atau isi..." value="{{ $search }}">
                    @if($search)
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </div>
            <input type="hidden" name="sort" value="{{ $sortBy }}">
            <input type="hidden" name="direction" value="{{ $direction }}">
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'title', 'direction' => ($sortBy == 'title' && $direction == 'asc') ? 'desc' : 'asc']) }}" class="text-decoration-none text-slate-800 fw-bold">
                                Judul Artikel
                                @if($sortBy == 'title')
                                    <i class="bi bi-arrow-{{ $direction == 'asc' ? 'up' : 'down' }} small"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'slug', 'direction' => ($sortBy == 'slug' && $direction == 'asc') ? 'desc' : 'asc']) }}" class="text-decoration-none text-slate-800 fw-bold">
                                Slug URL
                                @if($sortBy == 'slug')
                                    <i class="bi bi-arrow-{{ $direction == 'asc' ? 'up' : 'down' }} small"></i>
                                @endif
                            </a>
                        </th>
                        <th>Penulis</th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'published_at', 'direction' => ($sortBy == 'published_at' && $direction == 'asc') ? 'desc' : 'asc']) }}" class="text-decoration-none text-slate-800 fw-bold">
                                Status Rilis
                                @if($sortBy == 'published_at')
                                    <i class="bi bi-arrow-{{ $direction == 'asc' ? 'up' : 'down' }} small"></i>
                                @endif
                            </a>
                        </th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $art)
                        <tr>
                            <td class="fw-bold text-slate-800">{{ Str::limit($art->title, 60) }}</td>
                            <td><code class="small">{{ $art->slug }}</code></td>
                            <td><span class="text-muted small">{{ $art->user->name ?? 'System' }}</span></td>
                            <td>
                                @if($art->published_at)
                                    <span class="badge bg-success">Rilis</span>
                                    <span class="text-muted small d-block" style="font-size: 0.75em;">
                                        {{ \Carbon\Carbon::parse($art->published_at)->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Draf</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-warning fw-bold edit-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editArticleModal"
                                            data-id="{{ $art->id }}"
                                            data-title="{{ $art->title }}"
                                            data-slug="{{ $art->slug }}"
                                            data-content="{{ $art->content }}"
                                            data-published="{{ $art->published_at ? '1' : '0' }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger fw-bold delete-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteArticleModal"
                                            data-id="{{ $art->id }}"
                                            data-title="{{ $art->title }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Tidak ada data artikel ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $articles->links() }}
        </div>
    </div>
</div>

<!-- ================= CREATE MODAL ================= -->
<div class="modal fade" id="createArticleModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.articles.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="createModalLabel">Tulis Laporan Analisis Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Judul Laporan</label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Dampak Hambatan Logistik Terkini" required value="{{ old('title') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Slug URL (Opsional)</label>
                        <input type="text" name="slug" class="form-control" placeholder="Contoh: dampak-hambatan-logistik-terkini" value="{{ old('slug') }}">
                        <span class="text-muted small" style="font-size: 0.8em;">Kosongkan jika ingin digenerasi otomatis dari judul.</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Isi Artikel / Tinjauan Analisis</label>
                        <textarea name="content" rows="8" class="form-control" placeholder="Tulis isi tulisan di sini..." required>{{ old('content') }}</textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" name="published_now" value="1" id="publish-switch" {{ old('published_now') ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-slate-700" for="publish-switch">Terbitkan Langsung Sekarang</label>
                        <span class="text-muted d-block small">Jika dinonaktifkan, artikel akan disimpan sebagai draf terlebih dahulu.</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold">Simpan & Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= EDIT MODAL ================= -->
<div class="modal fade" id="editArticleModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold" id="editModalLabel">Edit Laporan Analisis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Judul Laporan</label>
                        <input type="text" name="title" id="edit-title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Slug URL</label>
                        <input type="text" name="slug" id="edit-slug" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Isi Artikel / Tinjauan Analisis</label>
                        <textarea name="content" id="edit-content" rows="8" class="form-control" required></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" name="published_now" value="1" id="edit-publish">
                        <label class="form-check-label fw-bold text-slate-700" for="edit-publish">Terbitkan Artikel</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold text-dark">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= DELETE CONFIRM MODAL ================= -->
<div class="modal fade" id="deleteArticleModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="deleteModalLabel">Hapus Artikel</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Apakah Anda yakin ingin menghapus artikel <span id="delete-title" class="fw-bold"></span>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger fw-bold">Hapus Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Edit Modal population
        const editButtons = document.querySelectorAll('.edit-btn');
        editButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const title = this.getAttribute('data-title');
                const slug = this.getAttribute('data-slug');
                const content = this.getAttribute('data-content');
                const published = this.getAttribute('data-published');

                document.getElementById('editForm').action = `/admin/articles/${id}`;
                document.getElementById('edit-title').value = title;
                document.getElementById('edit-slug').value = slug;
                document.getElementById('edit-content').value = content;
                
                if (published === '1') {
                    document.getElementById('edit-publish').checked = true;
                } else {
                    document.getElementById('edit-publish').checked = false;
                }
            });
        });

        // Delete Modal population
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const title = this.getAttribute('data-title');

                document.getElementById('deleteForm').action = `/admin/articles/${id}`;
                document.getElementById('delete-title').innerText = title;
            });
        });
    });
</script>
@endpush
