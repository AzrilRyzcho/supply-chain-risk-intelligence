@extends('layouts.app')

@section('title', 'Comparison - RiskIntel')

@section('content')
<div class="container-fluid py-4">
    <!-- Comparison Form Header -->
    <div class="card card-custom p-4 bg-white mb-4 border border-light-subtle shadow-sm">
        <h5 class="fw-bold text-slate-800 mb-3"><i class="bi bi-columns-gap text-primary me-2"></i>Bandingkan Dua Negara Mitra Dagang</h5>
        <form action="{{ route('user.compare') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-muted small fw-bold">Negara Pertama</label>
                <select name="country1" class="form-select bg-light border-secondary-subtle">
                    <option value="">-- Pilih Negara A --</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->code }}" {{ $code1 == $c->code ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 text-center align-self-center py-2">
                <span class="fw-bold text-muted fs-4">VS</span>
            </div>
            <div class="col-md-4">
                <label class="form-label text-muted small fw-bold">Negara Kedua</label>
                <select name="country2" class="form-select bg-light border-secondary-subtle">
                    <option value="">-- Pilih Negara B --</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->code }}" {{ $code2 == $c->code ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Bandingkan Kinerja</button>
            </div>
        </form>
    </div>

    @if($country1 || $country2)
        <div class="row">
            <!-- Side-by-side comparison cards -->
            <div class="col-md-6 mb-4">
                <div class="card card-custom p-4 bg-white h-100 border border-light-subtle shadow-sm">
                    @if($country1)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="fw-bold text-slate-800 mb-0">{{ $country1->name }}</h4>
                            <span class="badge bg-primary px-3 py-2">Negara A</span>
                        </div>
                        <hr>
                        <table class="table table-borderless align-middle my-3">
                            <tr>
                                <td class="text-muted w-50">Wilayah</td>
                                <td class="fw-bold text-slate-800">{{ $country1->region }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kode Kurs</td>
                                <td class="fw-bold text-slate-800">{{ $country1->currency_code }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Nilai Kurs per 1 USD</td>
                                <td class="fw-bold text-slate-800">
                                    @php 
                                        $curr1 = \App\Models\Currency::where('code', $country1->currency_code)->first();
                                    @endphp
                                    {{ $curr1 ? number_format($curr1->rate_to_usd, 2) . ' ' . $curr1->code : 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Koordinat</td>
                                <td class="fw-bold text-slate-800">{{ $country1->latitude }}, {{ $country1->longitude }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Suhu Cuaca</td>
                                <td class="fw-bold text-slate-800">{{ $country1->weather->temperature ?? 'N/A' }}°C</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Risiko Badai</td>
                                <td class="fw-bold text-danger">{{ $country1->weather->storm_risk ?? 'N/A' }}%</td>
                            </tr>
                            <tr>
                                <td class="text-muted">PDB / GDP (Terbaru)</td>
                                <td class="fw-bold text-slate-800">
                                    @php $latestGdp1 = $country1->gdps->sortByDesc('year')->first(); @endphp
                                    {{ $latestGdp1 ? '$' . number_format($latestGdp1->value, 1) . ' Miliar (' . $latestGdp1->year . ')' : 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Laju Inflasi (Terbaru)</td>
                                <td class="fw-bold text-slate-800">
                                    @php $latestInf1 = $country1->inflations->sortByDesc('year')->first(); @endphp
                                    {{ $latestInf1 ? number_format($latestInf1->rate, 2) . '% (' . $latestInf1->year . ')' : 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Ekspor (Terbaru)</td>
                                <td class="fw-bold text-slate-800">
                                    @php $latestExp1 = $country1->exports->sortByDesc('year')->first(); @endphp
                                    {{ $latestExp1 ? '$' . number_format($latestExp1->value, 1) . ' Miliar (' . $latestExp1->year . ')' : 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Impor (Terbaru)</td>
                                <td class="fw-bold text-slate-800">
                                    @php $latestImp1 = $country1->imports->sortByDesc('year')->first(); @endphp
                                    {{ $latestImp1 ? '$' . number_format($latestImp1->value, 1) . ' Miliar (' . $latestImp1->year . ')' : 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Indeks Risiko</td>
                                <td>
                                    @php $score1 = $country1->riskScores->sortByDesc('calculated_at')->first()->total_score ?? null; @endphp
                                    @if($score1 !== null)
                                        <span class="badge {{ $score1 >= 50 ? 'bg-danger' : ($score1 >= 25 ? 'bg-warning text-dark' : 'bg-success') }} fs-6">
                                            {{ $score1 }}%
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    @else
                        <div class="text-center py-5 text-muted">
                            <h5>Negara A belum dipilih</h5>
                            <p class="small">Silakan pilih negara pertama pada form di atas.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card card-custom p-4 bg-white h-100 border border-light-subtle shadow-sm">
                    @if($country2)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="fw-bold text-slate-800 mb-0">{{ $country2->name }}</h4>
                            <span class="badge bg-secondary px-3 py-2">Negara B</span>
                        </div>
                        <hr>
                        <table class="table table-borderless align-middle my-3">
                            <tr>
                                <td class="text-muted w-50">Wilayah</td>
                                <td class="fw-bold text-slate-800">{{ $country2->region }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kode Kurs</td>
                                <td class="fw-bold text-slate-800">{{ $country2->currency_code }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Nilai Kurs per 1 USD</td>
                                <td class="fw-bold text-slate-800">
                                    @php 
                                        $curr2 = \App\Models\Currency::where('code', $country2->currency_code)->first();
                                    @endphp
                                    {{ $curr2 ? number_format($curr2->rate_to_usd, 2) . ' ' . $curr2->code : 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Koordinat</td>
                                <td class="fw-bold text-slate-800">{{ $country2->latitude }}, {{ $country2->longitude }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Suhu Cuaca</td>
                                <td class="fw-bold text-slate-800">{{ $country2->weather->temperature ?? 'N/A' }}°C</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Risiko Badai</td>
                                <td class="fw-bold text-danger">{{ $country2->weather->storm_risk ?? 'N/A' }}%</td>
                            </tr>
                            <tr>
                                <td class="text-muted">PDB / GDP (Terbaru)</td>
                                <td class="fw-bold text-slate-800">
                                    @php $latestGdp2 = $country2->gdps->sortByDesc('year')->first(); @endphp
                                    {{ $latestGdp2 ? '$' . number_format($latestGdp2->value, 1) . ' Miliar (' . $latestGdp2->year . ')' : 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Laju Inflasi (Terbaru)</td>
                                <td class="fw-bold text-slate-800">
                                    @php $latestInf2 = $country2->inflations->sortByDesc('year')->first(); @endphp
                                    {{ $latestInf2 ? number_format($latestInf2->rate, 2) . '% (' . $latestInf2->year . ')' : 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Ekspor (Terbaru)</td>
                                <td class="fw-bold text-slate-800">
                                    @php $latestExp2 = $country2->exports->sortByDesc('year')->first(); @endphp
                                    {{ $latestExp2 ? '$' . number_format($latestExp2->value, 1) . ' Miliar (' . $latestExp2->year . ')' : 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Impor (Terbaru)</td>
                                <td class="fw-bold text-slate-800">
                                    @php $latestImp2 = $country2->imports->sortByDesc('year')->first(); @endphp
                                    {{ $latestImp2 ? '$' . number_format($latestImp2->value, 1) . ' Miliar (' . $latestImp2->year . ')' : 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Indeks Risiko</td>
                                <td>
                                    @php $score2 = $country2->riskScores->sortByDesc('calculated_at')->first()->total_score ?? null; @endphp
                                    @if($score2 !== null)
                                        <span class="badge {{ $score2 >= 50 ? 'bg-danger' : ($score2 >= 25 ? 'bg-warning text-dark' : 'bg-success') }} fs-6">
                                            {{ $score2 }}%
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    @else
                        <div class="text-center py-5 text-muted">
                            <h5>Negara B belum dipilih</h5>
                            <p class="small">Silakan pilih negara kedua pada form di atas.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if($country1 && $country2)
            <!-- Comparison Charts Row -->
            <div class="row mt-4">
                <!-- Chart 1: Risk Profile Comparison -->
                <div class="col-md-6 mb-4">
                    <div class="card card-custom p-4 bg-white border border-light-subtle shadow-sm h-100">
                        <h5 class="fw-bold text-slate-800 mb-3">
                            <i class="bi bi-shield-exclamation text-primary me-2"></i>Komparasi Profil Risiko Komposit
                        </h5>
                        <div style="height: 320px; position: relative;">
                            <canvas id="riskCompareChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Chart 2: Economic & Climate Metrics -->
                <div class="col-md-6 mb-4">
                    <div class="card card-custom p-4 bg-white border border-light-subtle shadow-sm h-100">
                        <h5 class="fw-bold text-slate-800 mb-3">
                            <i class="bi bi-activity text-primary me-2"></i>Komparasi Indikator Makro & Cuaca
                        </h5>
                        <div style="height: 320px; position: relative;">
                            <canvas id="metricsCompareChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="card card-custom p-5 bg-white text-center border border-light-subtle shadow-sm">
            <div class="text-slate-300 mb-3" style="font-size: 5rem;">
                <i class="bi bi-columns-gap text-secondary"></i>
            </div>
            <h4 class="fw-bold text-slate-800">Mulai Membandingkan Kinerja Rantai Pasok</h4>
            <p class="text-muted col-md-6 mx-auto">
                Silakan pilih dua negara mitra dagang di atas untuk membandingkan statistik geopolitik makro, risiko cuaca pelabuhan, inflasi domestik, dan total level kerentanan secara berdampingan.
            </p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@if($country1 && $country2)
<script>
    // Pass dynamic backend data to global JS scope
    window.compareData = {
        country1: {
            name: "{{ $country1->name }}",
            score: {{ $score1 ?? 0 }},
            weather: {{ $country1->riskScores->sortByDesc('calculated_at')->first()->weather_score ?? 0 }},
            inflation: {{ $country1->riskScores->sortByDesc('calculated_at')->first()->inflation_score ?? 0 }},
            currency: {{ $country1->riskScores->sortByDesc('calculated_at')->first()->currency_score ?? 0 }},
            sentiment: {{ $country1->riskScores->sortByDesc('calculated_at')->first()->sentiment_score ?? 0 }},
            gdp: {{ $latestGdp1->value ?? 0 }},
            inflationRate: {{ $latestInf1->rate ?? 0 }},
            temp: {{ $country1->weather->temperature ?? 0 }},
            wind: {{ $country1->weather->wind_speed ?? 0 }},
            storm: {{ $country1->weather->storm_risk ?? 0 }}
        },
        country2: {
            name: "{{ $country2->name }}",
            score: {{ $score2 ?? 0 }},
            weather: {{ $country2->riskScores->sortByDesc('calculated_at')->first()->weather_score ?? 0 }},
            inflation: {{ $country2->riskScores->sortByDesc('calculated_at')->first()->inflation_score ?? 0 }},
            currency: {{ $country2->riskScores->sortByDesc('calculated_at')->first()->currency_score ?? 0 }},
            sentiment: {{ $country2->riskScores->sortByDesc('calculated_at')->first()->sentiment_score ?? 0 }},
            gdp: {{ $latestGdp2->value ?? 0 }},
            inflationRate: {{ $latestInf2->rate ?? 0 }},
            temp: {{ $country2->weather->temperature ?? 0 }},
            wind: {{ $country2->weather->wind_speed ?? 0 }},
            storm: {{ $country2->weather->storm_risk ?? 0 }}
        }
    };
</script>
<script src="{{ asset('js/compare.js') }}"></script>
@endif
@endpush
