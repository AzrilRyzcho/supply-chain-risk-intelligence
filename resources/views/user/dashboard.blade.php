@extends('layouts.app')

@section('title', 'Dasbor Utama - RiskIntel')
@section('page_title', 'Dasbor Utama')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card card-custom p-4 bg-primary text-white" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;">
                <h2>Global Supply Chain Risk Intelligence Platform</h2>
                <p class="lead">Pantau cuaca, nilai tukar mata uang, kemacetan pelabuhan, inflasi makroekonomi, dan sentimen berita geopolitik global secara terintegrasi.</p>
            </div>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card card-custom p-3 bg-white">
                <span class="text-muted">Risiko Tertinggi</span>
                <h4 class="fw-bold mt-1 text-danger">China (High)</h4>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 78%"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-custom p-3 bg-white">
                <span class="text-muted">Kurs Terfluktuatif</span>
                <h4 class="fw-bold mt-1 text-warning">EUR/USD (-1.2%)</h4>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 60%"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-custom p-3 bg-white">
                <span class="text-muted">Cuaca Ekstrem Terpantau</span>
                <h4 class="fw-bold mt-1 text-info">3 Wilayah</h4>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width: 40%"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-custom p-3 bg-white">
                <span class="text-muted">Pelabuhan Aktif Dipantau</span>
                <h4 class="fw-bold mt-1 text-success">15 Pelabuhan</h4>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 90%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
