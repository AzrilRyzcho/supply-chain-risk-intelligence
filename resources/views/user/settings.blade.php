@extends('layouts.app')

@section('title', 'Settings - RiskIntel')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card card-custom p-4 bg-white mb-4 border border-light-subtle shadow-sm">
        <h4 class="fw-bold text-slate-800 mb-1"><i class="bi bi-gear me-2"></i>Account & Platform Settings</h4>
        <p class="text-muted small mb-0">Kelola preferensi akun pengguna, notifikasi peringatan dini cuaca, dan detail profil pribadi Anda.</p>
    </div>

    <div class="row">
        <!-- Profile Info Card -->
        <div class="col-lg-6 mb-4">
            <div class="card card-custom p-4 bg-white h-100 border border-light-subtle shadow-sm">
                <h5 class="fw-bold text-slate-800 mb-3"><i class="bi bi-person-circle me-1"></i>Informasi Profil</h5>
                <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Profil berhasil diperbarui (Simulasi)');">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nama Lengkap</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Alamat Email</label>
                        <input type="email" class="form-control" value="{{ auth()->user()->email }}" readonly>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">Peran Akses (Role)</label>
                        <input type="text" class="form-control" value="{{ ucfirst(auth()->user()->role) }}" readonly>
                    </div>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Simpan Perubahan</button>
                </form>
            </div>
        </div>

        <!-- Notification Preferences -->
        <div class="col-lg-6 mb-4">
            <div class="card card-custom p-4 bg-white h-100 border border-light-subtle shadow-sm">
                <h5 class="fw-bold text-slate-800 mb-3"><i class="bi bi-bell-fill me-1 text-warning"></i>Preferensi Peringatan Risiko</h5>
                <p class="text-muted small mb-4">Aktifkan notifikasi peringatan dini di dasbor untuk hambatan logistik atau cuaca buruk di wilayah watchlist Anda.</p>
                
                <div class="d-flex flex-column gap-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="notif-weather" checked>
                        <label class="form-check-label fw-bold text-slate-700" for="notif-weather">Notifikasi Cuaca Buruk & Badai</label>
                        <span class="text-muted d-block small">Kirim peringatan jika kecepatan angin melebihi 30 km/h atau curah hujan sangat lebat.</span>
                    </div>
                    
                    <div class="form-check form-switch border-top pt-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="notif-currency" checked>
                        <label class="form-check-label fw-bold text-slate-700" for="notif-currency">Pemberitahuan Depresiasi Valas</label>
                        <span class="text-muted d-block small">Notifikasi fluktuasi kurs mata uang watchlist yang melebihi batas 2.5% per hari.</span>
                    </div>

                    <div class="form-check form-switch border-top pt-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="notif-inflation" checked>
                        <label class="form-check-label fw-bold text-slate-700" for="notif-inflation">Peringatan Kenaikan Inflasi Makro</label>
                        <span class="text-muted d-block small">Notifikasi jika tingkat inflasi tahunan negara watchlist melampaui batas wajar 5.0%.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
