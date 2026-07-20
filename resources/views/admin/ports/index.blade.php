@extends('layouts.admin')

@section('title', 'Kelola Pelabuhan - Panel Admin')

@push('styles')
<!-- Leaflet Map CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    .map-picker {
        height: 280px;
        min-height: 250px;
        width: 100%;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        z-index: 1;
    }
    body.dark-theme .map-picker {
        border-color: #334155;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card card-custom p-4 bg-white mb-4 border border-light-subtle shadow-sm">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="fw-bold text-slate-800 mb-1"><i class="bi bi-anchor me-2"></i>Manajemen Master Pelabuhan</h4>
                <p class="text-muted small mb-0">Kelola daftar pelabuhan laut utama global untuk mendukung pemantauan kemacetan kontainer dan kelancaran rute logistik maritim.</p>
            </div>
            <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#createPortModal">
                <i class="bi bi-plus-lg me-1"></i>Tambah Pelabuhan
            </button>
        </div>
    </div>

    <!-- Error Alert -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show card-custom mb-4" role="alert">
            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal Menyimpan Pelabuhan:</h6>
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
        <form action="{{ route('admin.ports.index') }}" method="GET" class="row g-3 mb-4 justify-content-between align-items-center">
            <div class="col-md-5 col-lg-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-secondary-subtle text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-secondary-subtle" placeholder="Cari nama, kode pelabuhan, negara..." value="{{ $search }}">
                    @if($search)
                        <a href="{{ route('admin.ports.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
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
                                Nama Pelabuhan
                                @if($sortBy == 'name')
                                    <i class="bi bi-arrow-{{ $direction == 'asc' ? 'up' : 'down' }} small"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'code', 'direction' => ($sortBy == 'code' && $direction == 'asc') ? 'desc' : 'asc']) }}" class="text-decoration-none text-slate-800 fw-bold">
                                Kode (LOCODE)
                                @if($sortBy == 'code')
                                    <i class="bi bi-arrow-{{ $direction == 'asc' ? 'up' : 'down' }} small"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'country_id', 'direction' => ($sortBy == 'country_id' && $direction == 'asc') ? 'desc' : 'asc']) }}" class="text-decoration-none text-slate-800 fw-bold">
                                Negara
                                @if($sortBy == 'country_id')
                                    <i class="bi bi-arrow-{{ $direction == 'asc' ? 'up' : 'down' }} small"></i>
                                @endif
                            </a>
                        </th>
                        <th>Kordinat (Lat / Lng)</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ports as $port)
                        <tr>
                            <td class="fw-bold text-slate-800">{{ $port->name }}</td>
                            <td><span class="badge bg-secondary">{{ $port->code ?? 'N/A' }}</span></td>
                            <td><span class="fw-bold text-slate-600">{{ $port->country->name }}</span></td>
                            <td><span class="small text-muted">{{ $port->latitude }}, {{ $port->longitude }}</span></td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-warning fw-bold edit-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editPortModal"
                                            data-id="{{ $port->id }}"
                                            data-name="{{ $port->name }}"
                                            data-code="{{ $port->code }}"
                                            data-country="{{ $port->country_id }}"
                                            data-lat="{{ $port->latitude }}"
                                            data-lng="{{ $port->longitude }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger fw-bold delete-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deletePortModal"
                                            data-id="{{ $port->id }}"
                                            data-name="{{ $port->name }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Tidak ada data pelabuhan ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $ports->links() }}
        </div>
    </div>
</div>

<!-- ================= CREATE MODAL ================= -->
<div class="modal fade" id="createPortModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.ports.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="createModalLabel"><i class="bi bi-geo-alt-fill me-2"></i>Tambah Pelabuhan Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Nama Pelabuhan</label>
                                <input type="text" name="name" class="form-control" placeholder="Contoh: Tanjung Priok" required value="{{ old('name') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Kode Pelabuhan (LOCODE)</label>
                                <input type="text" name="code" class="form-control" placeholder="Contoh: IDTPP" required value="{{ old('code') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Negara Lokasi</label>
                                <select name="country_id" id="create-country" class="form-select" required>
                                    <option value="">-- Pilih Negara --</option>
                                    @foreach($countries as $c)
                                        <option value="{{ $c->id }}" data-lat="{{ $c->latitude }}" data-lng="{{ $c->longitude }}" {{ old('country_id') == $c->id ? 'selected' : '' }}>
                                            {{ $c->name }} ({{ $c->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Latitude</label>
                                    <input type="number" step="any" name="latitude" id="create-latitude" class="form-control" placeholder="-6.103" required value="{{ old('latitude') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Longitude</label>
                                    <input type="number" step="any" name="longitude" id="create-longitude" class="form-control" placeholder="106.879" required value="{{ old('longitude') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 d-flex flex-column">
                            <label class="form-label fw-bold small text-muted d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-map me-1"></i>Pilih Lokasi di Peta</span>
                                <span class="badge bg-info-subtle text-info small fw-normal"><i class="bi bi-hand-index-thumb me-1"></i>Klik / Geser Marker</span>
                            </label>
                            <div id="createMap" class="map-picker shadow-sm flex-grow-1"></div>
                            <div class="form-text small text-muted mt-1">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>Klik lokasi pada peta atau geser marker untuk mengisi latitude & longitude.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold">Simpan Pelabuhan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= EDIT MODAL ================= -->
<div class="modal fade" id="editPortModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold" id="editModalLabel"><i class="bi bi-pencil-square me-2"></i>Edit Data Pelabuhan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Nama Pelabuhan</label>
                                <input type="text" name="name" id="edit-name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Kode Pelabuhan (LOCODE)</label>
                                <input type="text" name="code" id="edit-code" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Negara Lokasi</label>
                                <select name="country_id" id="edit-country" class="form-select" required>
                                    @foreach($countries as $c)
                                        <option value="{{ $c->id }}" data-lat="{{ $c->latitude }}" data-lng="{{ $c->longitude }}">
                                            {{ $c->name }} ({{ $c->code }})
                                        </option>
                                    @endforeach
                                </select>
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
                        <div class="col-lg-6 d-flex flex-column">
                            <label class="form-label fw-bold small text-muted d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-map me-1"></i>Pilih Lokasi di Peta</span>
                                <span class="badge bg-info-subtle text-info small fw-normal"><i class="bi bi-hand-index-thumb me-1"></i>Klik / Geser Marker</span>
                            </label>
                            <div id="editMap" class="map-picker shadow-sm flex-grow-1"></div>
                            <div class="form-text small text-muted mt-1">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>Klik lokasi pada peta atau geser marker untuk memperbarui koordinat.
                            </div>
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
<div class="modal fade" id="deletePortModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="deleteModalLabel">Hapus Pelabuhan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Apakah Anda yakin ingin menghapus data pelabuhan <span id="delete-name" class="fw-bold"></span>?</p>
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
<!-- Leaflet Map JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const defaultLat = -2.548926; // Center of Indonesia / World
        const defaultLng = 118.014863;
        const tileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        const tileAttr = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';

        let createMap = null;
        let createMarker = null;
        let editMap = null;
        let editMarker = null;

        // --- CREATE PORT MAP LOGIC ---
        const createModalEl = document.getElementById('createPortModal');
        const createLatInput = document.getElementById('create-latitude');
        const createLngInput = document.getElementById('create-longitude');
        const createCountrySelect = document.getElementById('create-country');

        createModalEl.addEventListener('shown.bs.modal', function () {
            let initialLat = parseFloat(createLatInput.value) || defaultLat;
            let initialLng = parseFloat(createLngInput.value) || defaultLng;
            let initialZoom = (createLatInput.value && createLngInput.value) ? 8 : 4;

            if (!createMap) {
                createMap = L.map('createMap').setView([initialLat, initialLng], initialZoom);
                L.tileLayer(tileUrl, { attribution: tileAttr }).addTo(createMap);

                createMarker = L.marker([initialLat, initialLng], { draggable: true }).addTo(createMap);

                createMarker.on('dragend', function (e) {
                    const pos = e.target.getLatLng();
                    createLatInput.value = pos.lat.toFixed(6);
                    createLngInput.value = pos.lng.toFixed(6);
                });

                createMap.on('click', function (e) {
                    const lat = e.latlng.lat;
                    const lng = e.latlng.lng;
                    createMarker.setLatLng([lat, lng]);
                    createLatInput.value = lat.toFixed(6);
                    createLngInput.value = lng.toFixed(6);
                });
            } else {
                createMap.invalidateSize();
                createMap.setView([initialLat, initialLng], initialZoom);
                createMarker.setLatLng([initialLat, initialLng]);
            }
        });

        function syncCreateMapFromInputs() {
            const lat = parseFloat(createLatInput.value);
            const lng = parseFloat(createLngInput.value);
            if (!isNaN(lat) && !isNaN(lng) && createMap && createMarker) {
                createMarker.setLatLng([lat, lng]);
                createMap.panTo([lat, lng]);
            }
        }
        createLatInput.addEventListener('input', syncCreateMapFromInputs);
        createLngInput.addEventListener('input', syncCreateMapFromInputs);

        createCountrySelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const cLat = parseFloat(selectedOption.getAttribute('data-lat'));
            const cLng = parseFloat(selectedOption.getAttribute('data-lng'));

            if (!isNaN(cLat) && !isNaN(cLng)) {
                createLatInput.value = cLat.toFixed(6);
                createLngInput.value = cLng.toFixed(6);
                if (createMap && createMarker) {
                    createMap.setView([cLat, cLng], 6);
                    createMarker.setLatLng([cLat, cLng]);
                }
            }
        });

        // --- EDIT PORT MAP LOGIC ---
        const editButtons = document.querySelectorAll('.edit-btn');
        const editModalEl = document.getElementById('editPortModal');
        const editLatInput = document.getElementById('edit-latitude');
        const editLngInput = document.getElementById('edit-longitude');
        const editCountrySelect = document.getElementById('edit-country');

        editButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const code = this.getAttribute('data-code');
                const country = this.getAttribute('data-country');
                const lat = this.getAttribute('data-lat');
                const lng = this.getAttribute('data-lng');

                document.getElementById('editForm').action = `/admin/ports/${id}`;
                document.getElementById('edit-name').value = name;
                document.getElementById('edit-code').value = code;
                editCountrySelect.value = country;
                editLatInput.value = lat;
                editLngInput.value = lng;
            });
        });

        editModalEl.addEventListener('shown.bs.modal', function () {
            let latVal = parseFloat(editLatInput.value) || defaultLat;
            let lngVal = parseFloat(editLngInput.value) || defaultLng;

            if (!editMap) {
                editMap = L.map('editMap').setView([latVal, lngVal], 8);
                L.tileLayer(tileUrl, { attribution: tileAttr }).addTo(editMap);

                editMarker = L.marker([latVal, lngVal], { draggable: true }).addTo(editMap);

                editMarker.on('dragend', function (e) {
                    const pos = e.target.getLatLng();
                    editLatInput.value = pos.lat.toFixed(6);
                    editLngInput.value = pos.lng.toFixed(6);
                });

                editMap.on('click', function (e) {
                    const lat = e.latlng.lat;
                    const lng = e.latlng.lng;
                    editMarker.setLatLng([lat, lng]);
                    editLatInput.value = lat.toFixed(6);
                    editLngInput.value = lng.toFixed(6);
                });
            } else {
                editMap.invalidateSize();
                editMap.setView([latVal, lngVal], 8);
                editMarker.setLatLng([latVal, lngVal]);
            }
        });

        function syncEditMapFromInputs() {
            const lat = parseFloat(editLatInput.value);
            const lng = parseFloat(editLngInput.value);
            if (!isNaN(lat) && !isNaN(lng) && editMap && editMarker) {
                editMarker.setLatLng([lat, lng]);
                editMap.panTo([lat, lng]);
            }
        }
        editLatInput.addEventListener('input', syncEditMapFromInputs);
        editLngInput.addEventListener('input', syncEditMapFromInputs);

        editCountrySelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const cLat = parseFloat(selectedOption.getAttribute('data-lat'));
            const cLng = parseFloat(selectedOption.getAttribute('data-lng'));

            if (!isNaN(cLat) && !isNaN(cLng)) {
                editLatInput.value = cLat.toFixed(6);
                editLngInput.value = cLng.toFixed(6);
                if (editMap && editMarker) {
                    editMap.setView([cLat, cLng], 6);
                    editMarker.setLatLng([cLat, cLng]);
                }
            }
        });

        // Delete Modal population
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');

                document.getElementById('deleteForm').action = `/admin/ports/${id}`;
                document.getElementById('delete-name').innerText = name;
            });
        });
    });
</script>
@endpush

