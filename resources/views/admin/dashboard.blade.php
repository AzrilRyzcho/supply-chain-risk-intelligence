@extends('layouts.admin')

@section('title', 'Admin Panel - Ringkasan')
@section('page_title', 'Ringkasan Admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card card-custom p-4 bg-dark text-white">
                <h2>Selamat Datang, Administrator!</h2>
                <p class="lead">Gunakan panel ini untuk mengelola pengguna, dataset pelabuhan laut global, artikel analisis rantai pasok, dan kamus analisis sentimen berita.</p>
            </div>
        </div>
    </div>
    
    <!-- Admin Statistics Row Placeholders -->
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card card-custom p-4 bg-white">
                <h5 class="text-muted">Total Pengguna</h5>
                <h2 class="fw-bold mb-0">12</h2>
                <small class="text-success"><i class="bi bi-arrow-up"></i> +2 baru minggu ini</small>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card card-custom p-4 bg-white">
                <h5 class="text-muted">Dataset Pelabuhan</h5>
                <h2 class="fw-bold mb-0">850</h2>
                <small class="text-muted">Koordinat sebaran global</small>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card card-custom p-4 bg-white">
                <h5 class="text-muted">Artikel Terbit</h5>
                <h2 class="fw-bold mb-0">5</h2>
                <small class="text-muted">Tinjauan Geopolitik & Rantai Pasok</small>
            </div>
        </div>
    </div>
</div>
@endsection
