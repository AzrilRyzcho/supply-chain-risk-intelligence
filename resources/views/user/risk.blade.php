@extends('layouts.app')

@section('title', 'Risk Analysis - RiskIntel')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card card-custom p-4 bg-white mb-4 border border-light-subtle shadow-sm">
        <h4 class="fw-bold text-slate-800 mb-1"><i class="bi bi-shield-check me-2"></i>Composite Risk Analysis</h4>
        <p class="text-muted small mb-0">Riwayat kalkulasi indeks risiko rantai pasok berdasarkan integrasi data makroekonomi, logistik pelabuhan, dan cuaca ekstrem.</p>
    </div>

    <!-- Risk Scores History Table -->
    <div class="card card-custom p-4 bg-white border border-light-subtle shadow-sm">
        <h5 class="fw-bold text-slate-800 mb-3">Log Historis Perhitungan Risiko</h5>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Negara</th>
                        <th class="text-center">Cuaca</th>
                        <th class="text-center">Inflasi</th>
                        <th class="text-center">Kurs</th>
                        <th class="text-center">Sentimen</th>
                        <th class="text-center">Total Indeks Risiko</th>
                        <th class="text-center">Waktu Kalkulasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riskScores as $risk)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-slate-800">{{ $risk->country->name }}</span>
                                    <span class="badge bg-secondary ms-2">{{ $risk->country->code }}</span>
                                </div>
                            </td>
                            <td class="text-center">{{ $risk->weather_score }}%</td>
                            <td class="text-center">{{ $risk->inflation_score }}%</td>
                            <td class="text-center">{{ $risk->currency_score }}%</td>
                            <td class="text-center">{{ $risk->sentiment_score }}%</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="fw-bold me-2 {{ $risk->total_score >= 50 ? 'text-danger' : ($risk->total_score >= 25 ? 'text-warning' : 'text-success') }}">
                                        {{ $risk->total_score }}%
                                    </span>
                                    <div class="progress w-100" style="height: 6px; min-width: 60px;">
                                        <div class="progress-bar {{ $risk->total_score >= 50 ? 'bg-danger' : ($risk->total_score >= 25 ? 'bg-warning' : 'bg-success') }}" 
                                             role="progressbar" 
                                             style="width: {{ $risk->total_score }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center text-muted small">
                                {{ \Carbon\Carbon::parse($risk->calculated_at)->format('d M Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada riwayat kalkulasi indeks risiko.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
