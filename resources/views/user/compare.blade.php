@extends('layouts.app')

@section('title', 'Perbandingan Negara - RiskIntel')
@section('page_title', 'Perbandingan Negara Mitra')

@section('content')
<div class="container-fluid">
    <div class="card card-custom p-4 bg-white mb-4">
        <h5 class="fw-bold mb-3">Pilih Dua Negara untuk Dibandingkan</h5>
        <div class="row align-items-center">
            <div class="col-md-4">
                <label class="form-label text-muted">Negara Pertama</label>
                <select class="form-select" id="country-1">
                    <option value="DE">Jerman</option>
                    <option value="ID">Indonesia</option>
                </select>
            </div>
            <div class="col-md-1 text-center">
                <span class="fw-bold text-muted">VS</span>
            </div>
            <div class="col-md-4">
                <label class="form-label text-muted">Negara Kedua</label>
                <select class="form-select" id="country-2">
                    <option value="AU">Australia</option>
                    <option value="CN">China</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label d-block">&nbsp;</label>
                <button class="btn btn-outline-primary w-100" id="btn-compare">Bandingkan Kinerja</button>
            </div>
        </div>
    </div>

    <!-- Comparison Placeholders -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card card-custom p-4 bg-white">
                <h5 class="fw-bold" id="c1-title">Negara A</h5>
                <hr>
                <p class="text-muted">Pilih negara dan mulai perbandingan.</p>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card card-custom p-4 bg-white">
                <h5 class="fw-bold" id="c2-title">Negara B</h5>
                <hr>
                <p class="text-muted">Pilih negara dan mulai perbandingan.</p>
            </div>
        </div>
    </div>
</div>
@endsection
