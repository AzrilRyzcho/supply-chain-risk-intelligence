@extends('layouts.app')

@section('title', 'Buat Rencana Impor - RiskIntel')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('user.shipments.index') }}" class="text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Shipment
        </a>
        <h2 class="fw-bold mt-2">Buat Rencana Impor (Import Shipment)</h2>
        <p class="text-muted">Masukkan detail pengiriman barang dari pelabuhan asal menuju pelabuhan tujuan.</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card card-custom p-4">
                <form action="{{ route('user.shipments.store') }}" method="POST">
                    @csrf

                    <!-- Nomor Shipment -->
                    <div class="mb-4">
                        <label for="shipment_number" class="form-label fw-semibold">Nomor Shipment / Referensi</label>
                        <input type="text" class="form-control @error('shipment_number') is-invalid @enderror" 
                               id="shipment_number" name="shipment_number" 
                               value="{{ old('shipment_number', 'SHP-' . date('Ymd') . '-' . rand(100, 999)) }}" 
                               required placeholder="Contoh: SHP-2026-0001">
                        @error('shipment_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">ID unik untuk mengidentifikasi rencana pengiriman kontainer ini.</small>
                    </div>

                    <div class="row">
                        <!-- Pelabuhan Asal -->
                        <div class="col-md-6 mb-4">
                            <label for="origin_port_id" class="form-label fw-semibold">Pelabuhan Asal (Origin Port)</label>
                            <select class="form-select @error('origin_port_id') is-invalid @enderror" 
                                    id="origin_port_id" name="origin_port_id" required>
                                <option value="" disabled {{ old('origin_port_id') ? '' : 'selected' }}>-- Pilih Pelabuhan Asal --</option>
                                @foreach($groupedPorts as $countryName => $ports)
                                    <optgroup label="{{ $countryName }}">
                                        @foreach($ports as $port)
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
                            <small class="text-muted">Pelabuhan pengirim di negara asal barang.</small>
                        </div>

                        <!-- Pelabuhan Tujuan -->
                        <div class="col-md-6 mb-4">
                            <label for="destination_port_id" class="form-label fw-semibold">Pelabuhan Tujuan (Destination Port)</label>
                            <select class="form-select @error('destination_port_id') is-invalid @enderror" 
                                    id="destination_port_id" name="destination_port_id" required>
                                <option value="" disabled {{ old('destination_port_id') ? '' : 'selected' }}>-- Pilih Pelabuhan Tujuan --</option>
                                @foreach($groupedPorts as $countryName => $ports)
                                    <optgroup label="{{ $countryName }}">
                                        @foreach($ports as $port)
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
                            <small class="text-muted">Pelabuhan penerima di negara tujuan pengiriman.</small>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Status Perjalanan -->
                        <div class="col-md-12 mb-4">
                            <label for="status" class="form-label fw-semibold">Status Perjalanan</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending (Persiapan)</option>
                                <option value="In Transit" {{ old('status') == 'In Transit' ? 'selected' : '' }}>In Transit (Dalam Pelayaran)</option>
                                <option value="Completed" {{ old('status') == 'Completed' ? 'selected' : '' }}>Completed (Tiba di Tujuan)</option>
                                <option value="Delayed" {{ old('status') == 'Delayed' ? 'selected' : '' }}>Delayed (Terhambat)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 border-top pt-4 text-end">
                        <a href="{{ route('user.shipments.index') }}" class="btn btn-light px-4 py-2 me-2" style="border-radius: 10px;">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 10px;">
                            <i class="bi bi-save me-2"></i>Simpan Rencana Impor
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="col-lg-4">
            <div class="card card-custom p-4 bg-light border-0">
                <h5 class="fw-bold mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Panduan Pengisian</h5>
                <ul class="list-unstyled text-muted small" style="line-height: 1.7;">
                    <li class="mb-2"><i class="bi bi-check2 text-success me-1"></i> Pastikan Pelabuhan Asal dan Pelabuhan Tujuan tidak sama.</li>
                    <li class="mb-2"><i class="bi bi-check2 text-success me-1"></i> Data pelabuhan yang tersedia disinkronkan secara real-time dari modul **Global Ports Tracking**.</li>
                    <li class="mb-2"><i class="bi bi-check2 text-success me-1"></i> Setelah disimpan, Anda dapat langsung mengklik tombol **"Lihat Route Journey"** untuk melihat visualisasi peta pelayaran interaktif.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
