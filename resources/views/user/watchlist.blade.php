@extends('layouts.app')

@section('title', 'Daftar Favorit - RiskIntel')
@section('page_title', 'Daftar Favorit Saya')

@section('content')
<div class="container-fluid">
    <div class="card card-custom p-4 bg-white mb-4">
        <h5 class="fw-bold mb-3">Negara yang Anda Pantau</h5>
        <p class="text-muted">Tambahkan negara strategis dari dasbor negara ke daftar pantauan ini untuk mendapatkan informasi cepat mengenai status risiko harian.</p>
        
        <!-- Table Watchlist Placeholder -->
        <div class="table-responsive mt-3">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Negara</th>
                        <th>Mata Uang</th>
                        <th>Cuaca Ibu Kota</th>
                        <th>Skor Risiko</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada negara di daftar favorit Anda.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
