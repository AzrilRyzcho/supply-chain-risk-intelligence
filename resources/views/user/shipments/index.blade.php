@extends('layouts.app')

@section('title', 'Shipment Control Tower - RiskIntel')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1 text-slate-800"><i class="bi bi-box-seam text-primary me-2"></i>Shipment Control Tower</h3>
            <p class="text-muted small mb-0">Visualisasi peta rute pelayaran komoditas impor dan manajemen rantai pasok maritim secara terpadu.</p>
        </div>
    </div>
    
    <!-- Alert Success -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm p-3 mb-4 rounded-3 d-flex align-items-center" role="alert" style="border-radius: 12px;">
            <i class="bi bi-check-circle-fill text-success fs-5 me-2"></i>
            <div class="fw-semibold text-success">
                {{ session('success') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Map & Form Row -->
    <div class="row g-4 mb-4">
        <!-- Map Container (col-lg-8) -->
        <div class="col-lg-8">
            <div class="card card-custom p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fw-bold text-slate-800"><i class="bi bi-map-fill text-primary me-2"></i>Peta Jalur Pelayaran & Pelabuhan Global</span>
                    <span class="badge bg-light text-slate-500 border rounded-pill px-2.5 py-1.5 small text-uppercase">
                        {{ $ports->count() }} Pelabuhan Aktif
                    </span>
                </div>
                <!-- Map div -->
                <div id="shipments-map" style="height: 480px; border-radius: 12px; border: 1px solid #e2e8f0; z-index: 1;"></div>

            </div>
        </div>

        <!-- Inline Creation Form (col-lg-4) -->
        <div class="col-lg-4">
            <div class="card card-custom p-4 h-100">
                <h5 class="fw-bold text-slate-800 mb-3"><i class="bi bi-plus-circle text-primary me-2"></i>Rencanakan Impor</h5>
                
                <form action="{{ route('user.shipments.store') }}" method="POST">
                    @csrf

                    <!-- Nomor Shipment -->
                    <div class="mb-3">
                        <label for="shipment_number" class="form-label fw-semibold text-slate-700" style="font-size: 0.8rem;">Nomor Shipment / Referensi</label>
                        <input type="text" class="form-control form-control-sm @error('shipment_number') is-invalid @enderror" 
                               id="shipment_number" name="shipment_number" 
                               value="{{ old('shipment_number', 'SHP-' . date('Ymd') . '-' . rand(100, 999)) }}" 
                               required placeholder="Contoh: SHP-2026-0001" style="border-radius: 8px;">
                        @error('shipment_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Pelabuhan Asal -->
                    <div class="mb-3">
                        <label for="origin_port_id" class="form-label fw-semibold text-slate-700" style="font-size: 0.8rem;">Pelabuhan Asal (Origin Port)</label>
                        <select class="form-select form-select-sm @error('origin_port_id') is-invalid @enderror" 
                                id="origin_port_id" name="origin_port_id" required style="border-radius: 8px;">
                            <option value="" disabled {{ old('origin_port_id') ? '' : 'selected' }}>-- Pilih Pelabuhan Asal --</option>
                            @foreach($ports->groupBy('country.name') as $countryName => $countryPorts)
                                <optgroup label="{{ $countryName }}">
                                    @foreach($countryPorts as $port)
                                        <option value="{{ $port->id }}" {{ old('origin_port_id') == $port->id ? 'selected' : '' }}>
                                            {{ $port->name }} ({{ $port->code }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('origin_port_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Pelabuhan Tujuan -->
                    <div class="mb-3">
                        <label for="destination_port_id" class="form-label fw-semibold text-slate-700" style="font-size: 0.8rem;">Pelabuhan Tujuan (Destination Port)</label>
                        <select class="form-select form-select-sm @error('destination_port_id') is-invalid @enderror" 
                                id="destination_port_id" name="destination_port_id" required style="border-radius: 8px;">
                            <option value="" disabled {{ old('destination_port_id') ? '' : 'selected' }}>-- Pilih Pelabuhan Tujuan --</option>
                            @foreach($ports->groupBy('country.name') as $countryName => $countryPorts)
                                <optgroup label="{{ $countryName }}">
                                    @foreach($countryPorts as $port)
                                        <option value="{{ $port->id }}" {{ old('destination_port_id') == $port->id ? 'selected' : '' }}>
                                            {{ $port->name }} ({{ $port->code }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('destination_port_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status is automatically 'In Transit' so the vessel moves immediately -->
                    <input type="hidden" id="status" name="status" value="In Transit">


                    <button type="submit" class="btn btn-primary btn-sm w-100 py-2.5 fw-bold" style="border-radius: 8px;">
                        <i class="bi bi-plus-lg me-2"></i>Simpan Rencana Impor
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Shipments Table/List Below -->
    <div class="card card-custom p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-bold text-slate-800 mb-0">Daftar Rencana Pengiriman Impor</h6>
            <span class="text-muted small">Menampilkan {{ $shipments->count() }} rencana pengiriman</span>
        </div>

        @if($shipments->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-box-seam text-muted" style="font-size: 2.5rem;"></i>
                <p class="text-muted mt-3 mb-0">Belum ada rencana pengiriman impor yang dibuat. Gunakan form di atas untuk memulai rute pelayaran baru.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle table-hover" style="font-size: 0.86rem;">
                    <thead>
                        <tr class="text-muted small text-uppercase" style="border-bottom: 2px solid #f1f5f9;">
                            <th>Nomor Shipment</th>
                            <th>Pelabuhan Asal</th>
                            <th>Pelabuhan Tujuan</th>
                            <th class="text-center">Jalur</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shipments as $shipment)
                            @php
                                $statusBg = '#f1f5f9'; $statusColor = '#64748b';
                                if ($shipment->status === 'In Transit') { $statusBg = '#eff6ff'; $statusColor = '#2563eb'; }
                                elseif ($shipment->status === 'Completed') { $statusBg = '#ecfdf5'; $statusColor = '#10b981'; }
                                elseif ($shipment->status === 'Delayed') { $statusBg = '#fef2f2'; $statusColor = '#ef4444'; }
                            @endphp
                            <tr>
                                <td class="fw-bold text-slate-800">{{ $shipment->shipment_number }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-light border text-muted" style="font-size:0.62rem; font-weight:700;">{{ $shipment->originPort->country->code ?? '??' }}</span>
                                        <div>
                                            <span class="fw-semibold text-slate-700 d-block">{{ $shipment->originPort->name }}</span>
                                            <small class="text-muted text-uppercase" style="font-size:0.68rem;">{{ $shipment->originPort->code }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-light border text-muted" style="font-size:0.62rem; font-weight:700;">{{ $shipment->destinationPort->country->code ?? '??' }}</span>
                                        <div>
                                            <span class="fw-semibold text-slate-700 d-block">{{ $shipment->destinationPort->name }}</span>
                                            <small class="text-muted text-uppercase" style="font-size:0.68rem;">{{ $shipment->destinationPort->code }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge border-0 px-2.5 py-1 text-slate-600 bg-light" style="border-radius: 6px; font-weight: 500;">
                                        <i class="bi bi-anchor text-primary me-1"></i> Laut
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge border-0 px-3 py-1.5" style="background-color: {{ $statusBg }}; color: {{ $statusColor }}; border-radius: 6px; font-weight: 600;">
                                        {{ $shipment->status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1.5">
                                        <a href="{{ route('user.shipments.route', $shipment) }}" class="btn btn-sm btn-outline-primary px-2.5 py-1" style="border-radius: 6px;">
                                            Detail
                                        </a>
                                        <form action="{{ route('user.shipments.destroy', $shipment) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus rencana pengiriman ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-2.5 py-1" style="border-radius: 6px;">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

@php
    $portsJson = $ports->map(function($p) {
        return [
            'id'           => $p->id,
            'name'         => $p->name,
            'code'         => $p->code,
            'latitude'     => (float) $p->latitude,
            'longitude'    => (float) $p->longitude,
            'country_name' => optional($p->country)->name ?? '',
            'country_code' => optional($p->country)->code ?? '',
        ];
    })->values()->toArray();

    $shipmentsJson = $shipments->map(function($s) {
        return [
            'shipment_number' => $s->shipment_number,
            'status'          => $s->status,
            'origin_port'     => $s->originPort ? [
                'id'        => $s->originPort->id,
                'name'      => $s->originPort->name,
                'latitude'  => (float) $s->originPort->latitude,
                'longitude' => (float) $s->originPort->longitude,
            ] : null,
            'destination_port' => $s->destinationPort ? [
                'id'        => $s->destinationPort->id,
                'name'      => $s->destinationPort->name,
                'latitude'  => (float) $s->destinationPort->latitude,
                'longitude' => (float) $s->destinationPort->longitude,
            ] : null,
        ];
    })->values()->toArray();
@endphp

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    // ============================================================
    //  MAP INITIALIZATION
    // ============================================================
    const map = L.map('shipments-map', {
        minZoom: 2,
        maxZoom: 18,
        worldCopyJump: true
    }).setView([15, 100], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(map);

    setTimeout(() => map.invalidateSize(), 150);

    if (document.body.classList.contains('dark-theme')) {
        document.querySelectorAll('.leaflet-tile-container')
            .forEach(c => c.style.filter = 'invert(100%) hue-rotate(180deg) brightness(95%) contrast(90%)');
    }

    const ports     = @json($portsJson);
    const shipments = @json($shipmentsJson);

    // ============================================================
    //  PORT MARKERS
    // ============================================================
    const activePortIds = new Set();
    shipments.forEach(shp => {
        if (shp.origin_port)      activePortIds.add(shp.origin_port.id);
        if (shp.destination_port) activePortIds.add(shp.destination_port.id);
    });

    const portLookup = {};
    const portMarkers = {};
    const anchorIcon = L.divIcon({
        className: 'custom-anchor-marker',
        html: '<div style="background:#2563eb;width:22px;height:22px;border-radius:50%;border:2px solid #fff;display:flex;align-items:center;justify-content:center;color:#fff;box-shadow:0 2px 5px rgba(0,0,0,0.3);"><i class="bi bi-anchor" style="font-size:10px;"></i></div>',
        iconSize: [22, 22], iconAnchor: [11, 11]
    });

    ports.forEach(port => {
        portLookup[port.id] = port;
        const marker = L.marker([port.latitude, port.longitude], { icon: anchorIcon });
        portMarkers[port.id] = marker;
        marker.bindPopup(`
            <div style="font-family:'Outfit',sans-serif;width:210px;padding:4px;">
                <div style="font-size:0.9rem;font-weight:700;color:#0f172a;margin-bottom:2px;">${port.name}</div>
                <div style="font-size:0.7rem;color:#94a3b8;text-transform:uppercase;margin-bottom:8px;">${port.code} &bull; ${port.country_name}</div>
                <div style="display:flex;gap:6px;">
                    <button style="flex:1;font-size:0.68rem;padding:5px 8px;border-radius:6px;background:#2563eb;color:#fff;border:none;cursor:pointer;font-weight:600;" onclick="setPortAs('origin',${port.id})">&#9634; Asal</button>
                    <button style="flex:1;font-size:0.68rem;padding:5px 8px;border-radius:6px;background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;cursor:pointer;font-weight:600;" onclick="setPortAs('destination',${port.id})">&#9635; Tujuan</button>
                </div>
            </div>`);
        if (activePortIds.has(port.id)) marker.addTo(map);
    });

    // ============================================================
    //  DRAW SAVED SHIPMENT ROUTES via Backend API
    // ============================================================
    shipments.forEach(shp => {
        if (!shp.origin_port || !shp.destination_port) return;

        let color = '#ef4444';
        if (shp.status === 'Completed')   color = '#10b981';
        else if (shp.status === 'In Transit') color = '#2563eb';
        else if (shp.status === 'Pending')    color = '#f59e0b';

        fetch('{{ route("user.api.route-preview") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                origin_port_id:      shp.origin_port.id,
                destination_port_id: shp.destination_port.id
            })
        })
        .then(r => r.json())
        .then(data => {
            if (!data.coordinates || data.error) return;
            const route = data.coordinates.map(c => [c[0], c[1]]);
            const line  = L.polyline(route, {
                color, weight: 3, opacity: 0.8,
                dashArray: shp.status === 'Pending' ? '6,8' : null
            }).addTo(map);

            line.bindPopup(`
                <div style="font-family:'Outfit',sans-serif;font-size:0.8rem;">
                    <strong>Shipment #${shp.shipment_number}</strong><br>
                    <span style="color:#64748b;">${shp.origin_port.name} &rarr; ${shp.destination_port.name}</span><br>
                    Status: <strong style="color:${color};">${shp.status}</strong>
                </div>`);

            if (shp.status === 'In Transit') {
                const mid  = route[Math.floor(route.length / 2)];
                const icon = L.divIcon({
                    html: `<div style="animation:pulse 1.5s infinite;background:${color};width:12px;height:12px;border-radius:50%;border:2px solid #fff;box-shadow:0 0 8px ${color};"></div>`,
                    iconSize: [12, 12], iconAnchor: [6, 6]
                });
                L.marker(mid, { icon }).addTo(map)
                    .bindPopup(`Shipment #${shp.shipment_number} — In Transit`);
            }
        })
        .catch(err => console.warn('Route fetch error:', err));
    });

    // ============================================================
    //  LIVE PREVIEW — fetch route from backend when ports selected
    // ============================================================
    let previewLayer         = null;
    let previewMarkerIds     = [];
    let previewAbortCtrl     = null;

    function clearPreview() {
        if (previewLayer)   { map.removeLayer(previewLayer); previewLayer = null; }
        previewMarkerIds.forEach(pid => {
            if (!activePortIds.has(pid) && portMarkers[pid]) map.removeLayer(portMarkers[pid]);
        });
        previewMarkerIds = [];
    }

    function drawPreviewRoute() {
        clearPreview();

        const oid = parseInt(document.getElementById('origin_port_id').value);
        const did = parseInt(document.getElementById('destination_port_id').value);
        const op  = portLookup[oid];
        const dp  = portLookup[did];

        // Show single port marker & zoom if only one is selected
        if (op && !dp) {
            portMarkers[oid].addTo(map); previewMarkerIds.push(oid);
            map.setView([op.latitude, op.longitude], 5, { animate: true });
            return;
        }
        if (!op && dp) {
            portMarkers[did].addTo(map); previewMarkerIds.push(did);
            map.setView([dp.latitude, dp.longitude], 5, { animate: true });
            return;
        }
        if (!op || !dp || oid === did) return;

        // Show both markers immediately
        [oid, did].forEach(pid => {
            portMarkers[pid].addTo(map); previewMarkerIds.push(pid);
        });

        // Cancel any in-flight request
        if (previewAbortCtrl) previewAbortCtrl.abort();
        previewAbortCtrl = new AbortController();

        fetch('{{ route("user.api.route-preview") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                origin_port_id:      oid,
                destination_port_id: did
            }),
            signal: previewAbortCtrl.signal
        })
        .then(r => r.json())
        .then(data => {
            if (!data.coordinates || data.error) return;
            const route = data.coordinates.map(c => [c[0], c[1]]);

            previewLayer = L.polyline(route, {
                color: '#4f46e5', weight: 3, opacity: 0.9, dashArray: '10,7'
            }).addTo(map);

            map.fitBounds(L.latLngBounds(route), { padding: [50, 50], maxZoom: 5 });

            // Brief scale-up animation on the markers
            [oid, did].forEach(pid => {
                const el = portMarkers[pid]?.getElement();
                if (el) { el.style.transform = 'scale(1.5)'; setTimeout(() => el.style.transform = '', 500); }
            });
        })
        .catch(err => { if (err.name !== 'AbortError') console.warn('Preview error:', err); });
    }

    document.getElementById('origin_port_id').addEventListener('change',      drawPreviewRoute);
    document.getElementById('destination_port_id').addEventListener('change',  drawPreviewRoute);

    window.setPortAs = function (role, portId) {
        document.getElementById(role === 'origin' ? 'origin_port_id' : 'destination_port_id').value = portId;
        map.closePopup();
        drawPreviewRoute();
    };
});
</script>

<style>
@keyframes pulse {
    0%   { transform: scale(0.8); opacity: 0.5; box-shadow: 0 0 0 0 rgba(37,99,235,0.4); }
    70%  { transform: scale(1.2); opacity: 1;   box-shadow: 0 0 0 6px rgba(37,99,235,0); }
    100% { transform: scale(0.8); opacity: 0.5; box-shadow: 0 0 0 0 rgba(37,99,235,0); }
}
.custom-anchor-marker div { transition: all 0.2s ease; }
.custom-anchor-marker div:hover { transform: scale(1.2); background: #1d4ed8 !important; }
</style>
@endpush

