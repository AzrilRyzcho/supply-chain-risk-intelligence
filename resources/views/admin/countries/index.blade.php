@extends('layouts.admin')

@section('title', 'Kelola Negara - Panel Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card card-custom p-4 bg-white mb-4 border border-light-subtle shadow-sm">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="fw-bold text-slate-800 mb-1"><i class="bi bi-globe me-2"></i>Manajemen Master Negara</h4>
                <p class="text-muted small mb-0">Kelola daftar negara mitra dagang strategis yang akan masuk dalam analisis komposit risiko rantai pasok.</p>
            </div>
            <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#createCountryModal">
                <i class="bi bi-plus-lg me-1"></i>Tambah Negara
            </button>
        </div>
    </div>

    <!-- Error Alert (Validation Errors) -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show card-custom mb-4" role="alert">
            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal Menyimpan Data:</h6>
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
        <!-- Search & Filter form -->
        <form action="{{ route('admin.countries.index') }}" method="GET" class="row g-3 mb-4 justify-content-between align-items-center">
            <div class="col-md-5 col-lg-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-secondary-subtle text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-secondary-subtle" placeholder="Cari nama, kode, region..." value="{{ $search }}">
                    @if($search)
                        <a href="{{ route('admin.countries.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
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
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => ($sortBy == 'name' && $direction == 'asc') ? 'desc' : 'asc']) }}" class="text-decoration-none text-slate-800 fw-bold">
                                Nama Negara 
                                @if($sortBy == 'name')
                                    <i class="bi bi-arrow-{{ $direction == 'asc' ? 'up' : 'down' }} small"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'code', 'direction' => ($sortBy == 'code' && $direction == 'asc') ? 'desc' : 'asc']) }}" class="text-decoration-none text-slate-800 fw-bold">
                                Kode ISO
                                @if($sortBy == 'code')
                                    <i class="bi bi-arrow-{{ $direction == 'asc' ? 'up' : 'down' }} small"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'currency_code', 'direction' => ($sortBy == 'currency_code' && $direction == 'asc') ? 'desc' : 'asc']) }}" class="text-decoration-none text-slate-800 fw-bold">
                                Kode Valas
                                @if($sortBy == 'currency_code')
                                    <i class="bi bi-arrow-{{ $direction == 'asc' ? 'up' : 'down' }} small"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'region', 'direction' => ($sortBy == 'region' && $direction == 'asc') ? 'desc' : 'asc']) }}" class="text-decoration-none text-slate-800 fw-bold">
                                Region
                                @if($sortBy == 'region')
                                    <i class="bi bi-arrow-{{ $direction == 'asc' ? 'up' : 'down' }} small"></i>
                                @endif
                            </a>
                        </th>
                        <th>Kordinat (Lat / Lng)</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($countries as $country)
                        <tr>
                            <td class="fw-bold text-slate-700">{{ $country->name }}</td>
                            <td><span class="badge bg-secondary">{{ $country->code }}</span></td>
                            <td><span class="fw-bold text-slate-600">{{ $country->currency_code }}</span></td>
                            <td>{{ $country->region }}</td>
                            <td><span class="small text-muted">{{ $country->latitude }}, {{ $country->longitude }}</span></td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-warning fw-bold edit-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editCountryModal"
                                            data-id="{{ $country->id }}"
                                            data-name="{{ $country->name }}"
                                            data-code="{{ $country->code }}"
                                            data-currency="{{ $country->currency_code }}"
                                            data-region="{{ $country->region }}"
                                            data-lat="{{ $country->latitude }}"
                                            data-lng="{{ $country->longitude }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger fw-bold delete-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteCountryModal"
                                            data-id="{{ $country->id }}"
                                            data-name="{{ $country->name }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Tidak ada data negara ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $countries->links() }}
        </div>
    </div>
</div>

<!-- ================= CREATE MODAL ================= -->
<div class="modal fade" id="createCountryModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.countries.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="createModalLabel">Tambah Negara Mitra Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Nama Negara</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Indonesia" required value="{{ old('name') }}">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Kode ISO (2 Karakter)</label>
                            <input type="text" name="code" class="form-control" placeholder="Contoh: ID" maxlength="2" required value="{{ old('code') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Kode Kurs (3 Karakter)</label>
                            <input type="text" name="currency_code" class="form-control" placeholder="Contoh: IDR" maxlength="3" required value="{{ old('currency_code') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Wilayah (Region)</label>
                        <input type="text" name="region" class="form-control" placeholder="Contoh: Asia" required value="{{ old('region') }}">
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Latitude</label>
                            <input type="number" step="any" name="latitude" class="form-control" placeholder="-0.789" required value="{{ old('latitude') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Longitude</label>
                            <input type="number" step="any" name="longitude" class="form-control" placeholder="113.921" required value="{{ old('longitude') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold">Simpan Negara</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= EDIT MODAL ================= -->
<div class="modal fade" id="editCountryModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold" id="editModalLabel">Edit Negara Mitra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Nama Negara</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Kode ISO (2 Karakter)</label>
                            <input type="text" name="code" id="edit-code" class="form-control" maxlength="2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Kode Kurs (3 Karakter)</label>
                            <input type="text" name="currency_code" id="edit-currency" class="form-control" maxlength="3" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Wilayah (Region)</label>
                        <input type="text" name="region" id="edit-region" class="form-control" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Latitude</label>
                            <input type="number" step="any" name="latitude" id="edit-latitude" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Longitude</label>
                            <input type="number" step="any" name="longitude" id="edit-longitude" class="form-control" required>
                        </div>
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
<div class="modal fade" id="deleteCountryModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="deleteModalLabel">Hapus Negara Mitra</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Apakah Anda yakin ingin menghapus negara <span id="delete-name" class="fw-bold"></span>?</p>
                    <span class="text-danger small mt-2 d-block"><i class="bi bi-exclamation-triangle me-1"></i>Tindakan ini akan menghapus seluruh data cuaca, inflasi, GDP, pelabuhan, dan log risiko terkait negara tersebut!</span>
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
                const name = this.getAttribute('data-name');
                const code = this.getAttribute('data-code');
                const currency = this.getAttribute('data-currency');
                const region = this.getAttribute('data-region');
                const lat = this.getAttribute('data-lat');
                const lng = this.getAttribute('data-lng');

                document.getElementById('editForm').action = `/admin/countries/${id}`;
                document.getElementById('edit-name').value = name;
                document.getElementById('edit-code').value = code;
                document.getElementById('edit-currency').value = currency;
                document.getElementById('edit-region').value = region;
                document.getElementById('edit-latitude').value = lat;
                document.getElementById('edit-longitude').value = lng;
            });
        });

        // Delete Modal population
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');

                document.getElementById('deleteForm').action = `/admin/countries/${id}`;
                document.getElementById('delete-name').innerText = name;
            });
        });
    });
</script>
@endpush
