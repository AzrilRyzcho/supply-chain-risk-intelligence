@extends('layouts.app')

@section('title', 'Detail Pengiriman - ' . $shipment->shipment_number)

@section('content')
<div class="container-fluid">
    <!-- Header/Back Navigation -->
    <div class="mb-4">
        <a href="{{ route('user.shipments.index') }}" class="text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Shipment
        </a>
        <div class="d-flex justify-content-between align-items-center mt-2">
            <div>
                <h2 class="fw-bold mb-0">Shipment {{ $shipment->shipment_number }}</h2>
                <p class="text-muted mb-0">Rencana Impor Barang Strategis</p>
            </div>
            <!-- Main Button: Lihat Route Journey -->
            <a href="{{ route('user.shipments.route', $shipment) }}" class="btn btn-primary px-4 py-2 card-custom" style="border-radius: 12px; font-weight: 500;">
                <i class="bi bi-geo-alt-fill me-2 text-warning"></i>Lihat Route Journey
            </a>
        </div>
    </div>

    <!-- Shipment Details Section -->
    <div class="row">
        <!-- Main Info -->
        <div class="col-lg-8">
            <div class="card card-custom p-4 mb-4">
                <h4 class="fw-bold mb-4 border-bottom pb-2">Informasi Rencana Pengiriman</h4>
                
                <div class="row g-4">
                    <!-- Status -->
                    <div class="col-md-6">
                        <div class="text-muted small">Status Perjalanan</div>
                        <div class="mt-1">
                            @if($shipment->status === 'Pending')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 fs-6" style="border-radius: 8px;">Pending (Persiapan)</span>
                            @elseif($shipment->status === 'In Transit')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fs-6" style="border-radius: 8px;">In Transit (Dalam Pelayaran)</span>
                            @elseif($shipment->status === 'Completed')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6" style="border-radius: 8px;">Completed (Tiba di Tujuan)</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 fs-6" style="border-radius: 8px;">Delayed (Terhambat)</span>
                            @endif
                        </div>
                    </div>

                    <!-- Transport Mode -->
                    <div class="col-md-6">
                        <div class="text-muted small">Moda Transportasi</div>
                        <div class="mt-1 fw-bold fs-6">
                            @if($shipment->transport_mode === 'Sea Freight')
                                <i class="bi bi-anchor text-primary me-2"></i> Laut (Sea Freight)
                            @elseif($shipment->transport_mode === 'Air Freight')
                                <i class="bi bi-airplane text-info me-2"></i> Udara (Air Freight)
                            @else
                                <i class="bi bi-truck text-secondary me-2"></i> Darat (Land Transport)
                            @endif
                        </div>
                    </div>

                    <!-- Origin Country & Port -->
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-4 border">
                            <div class="text-muted small mb-2"><i class="bi bi-box-arrow-up-right text-success me-1"></i> Asal (Origin)</div>
                            <div class="fw-bold fs-5">{{ $shipment->originPort->name }}</div>
                            <div class="text-secondary small mt-1">
                                <i class="bi bi-globe me-1"></i> {{ $shipment->originPort->country->name }} ({{ $shipment->originPort->country->code }})
                            </div>
                            <div class="text-secondary small mt-1">
                                <i class="bi bi-compass me-1"></i> Koordinat: [{{ $shipment->originPort->latitude }}, {{ $shipment->originPort->longitude }}]
                            </div>
                        </div>
                    </div>

                    <!-- Destination Country & Port -->
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-4 border">
                            <div class="text-muted small mb-2"><i class="bi bi-box-arrow-in-down text-danger me-1"></i> Tujuan (Destination)</div>
                            <div class="fw-bold fs-5">{{ $shipment->destinationPort->name }}</div>
                            <div class="text-secondary small mt-1">
                                <i class="bi bi-globe me-1"></i> {{ $shipment->destinationPort->country->name }} ({{ $shipment->destinationPort->country->code }})
                            </div>
                            <div class="text-secondary small mt-1">
                                <i class="bi bi-compass me-1"></i> Koordinat: [{{ $shipment->destinationPort->latitude }}, {{ $shipment->destinationPort->longitude }}]
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Integration Links -->
        <div class="col-lg-4">
            <div class="card card-custom p-4 mb-4 bg-light border-0">
                <h5 class="fw-bold mb-3"><i class="bi bi-diagram-3-fill text-primary me-2"></i>Modul Terintegrasi</h5>
                <p class="text-muted small">Rencana pengiriman ini terhubung dengan sistem analisis risiko dinamis RiskIntel:</p>
                <div class="list-group list-group-flush rounded-3 border">
                    <!-- Weather -->
                    <a href="{{ route('user.weather') }}?code={{ $shipment->destinationPort->country->code }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                        <div>
                            <i class="bi bi-cloud-sun text-primary me-2"></i> Pantau Cuaca Tujuan
                        </div>
                        <span class="badge bg-light text-dark border">Lihat</span>
                    </a>
                    <!-- Risk Score -->
                    <a href="{{ route('user.risk') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                        <div>
                            <i class="bi bi-shield-check text-success me-2"></i> Indeks Risiko Negara
                        </div>
                        <span class="badge bg-light text-dark border">Lihat</span>
                    </a>
                    <!-- News -->
                    <a href="{{ route('user.news') }}?q={{ $shipment->destinationPort->country->name }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                        <div>
                            <i class="bi bi-newspaper text-warning me-2"></i> Berita Logistik Terkait
                        </div>
                        <span class="badge bg-light text-dark border">Lihat</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
