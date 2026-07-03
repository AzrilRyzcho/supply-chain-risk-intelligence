@extends('layouts.app')

@section('title', 'Dasbor Negara - RiskIntel')
@section('page_title', 'Dasbor Analisis Negara')

@section('content')
<div class="container-fluid">
    <div class="card card-custom p-4 bg-white mb-4">
        <h5 class="fw-bold mb-3">Pilih Negara Mitra Dagang</h5>
        <div class="row">
            <div class="col-md-4">
                <select class="form-select" id="country-select">
                    <option value="">-- Pilih Negara --</option>
                    <option value="DE">Jerman</option>
                    <option value="CN">China</option>
                    <option value="ID">Indonesia</option>
                    <option value="AU">Australia</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" id="btn-load-country">Tampilkan</button>
            </div>
        </div>
    </div>

    <!-- Placeholder info negara -->
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card card-custom p-4 bg-white">
                <h5 class="fw-bold">Indikator Makro & Cuaca</h5>
                <p class="text-muted">Pilih negara terlebih dahulu untuk memuat data World Bank, REST Countries, dan Open-Meteo secara real-time.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card card-custom p-4 bg-white">
                <h5 class="fw-bold">Skor Risiko</h5>
                <div class="text-center py-5">
                    <h1 class="display-3 fw-bold text-muted">-</h1>
                    <span class="badge bg-secondary px-3 py-2">Belum Ditentukan</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
