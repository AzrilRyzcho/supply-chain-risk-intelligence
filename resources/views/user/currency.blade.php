@extends('layouts.app')

@section('title', 'Currency - RiskIntel')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card card-custom p-4 bg-white mb-4 border border-light-subtle shadow-sm">
        <h4 class="fw-bold text-slate-800 mb-1"><i class="bi bi-currency-exchange me-2"></i>Currency Exchange Rates</h4>
        <p class="text-muted small mb-0">Analisis perbandingan kekuatan nilai tukar valuta asing terhadap USD untuk mengukur stabilitas biaya material impor dan beban inflasi logistik.</p>
    </div>

    <div class="row">
        <!-- Exchange Rates List -->
        <div class="col-lg-6 mb-4">
            <div class="card card-custom p-4 bg-white h-100 border border-light-subtle shadow-sm">
                <h5 class="fw-bold text-slate-800 mb-3">Nilai Tukar Saat Ini (Terhadap 1 USD)</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Valuta Asing</th>
                                <th class="text-end">Nilai Tukar per USD</th>
                                <th class="text-center">Diperbarui Pada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($currencies as $curr)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold me-2" 
                                                 style="width: 36px; height: 36px;">
                                                {{ $curr->code }}
                                            </div>
                                            <span class="fw-bold text-slate-800">{{ $curr->code }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold text-slate-700">
                                        {{ number_format($curr->rate_to_usd, 4) }}
                                    </td>
                                    <td class="text-center text-muted small">
                                        {{ \Carbon\Carbon::parse($curr->fetched_at)->format('d M Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada data nilai tukar mata uang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Currency Converter & Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card card-custom p-4 bg-white h-100 border border-light-subtle shadow-sm">
                <h5 class="fw-bold text-slate-800 mb-3">Simulasi Konverter Kurs USD</h5>
                <div class="row g-3 bg-light rounded p-3 mb-4">
                    <div class="col-md-5">
                        <label class="form-label text-muted small">Nominal USD</label>
                        <input type="number" id="usd-amount" class="form-control" value="100" min="1" oninput="convertCurrency()">
                    </div>
                    <div class="col-md-2 text-center align-self-end py-2">
                        <i class="bi bi-arrow-left-right fs-4 text-secondary"></i>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-muted small">Target Mata Uang</label>
                        <select id="target-currency" class="form-select" onchange="convertCurrency()">
                            @foreach($currencies as $curr)
                                <option value="{{ $curr->rate_to_usd }}" data-code="{{ $curr->code }}">
                                    {{ $curr->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 mt-3 text-center">
                        <h4 class="fw-bold text-slate-800 mb-0" id="conversion-result">-</h4>
                    </div>
                </div>

                <!-- Currency Strength Visualizer Chart.js -->
                <h6 class="fw-bold text-slate-800 mb-3">Indeks Kekuatan Kurs (Rasio per USD)</h6>
                <div style="height: 180px;">
                    <canvas id="currencyStrengthChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function convertCurrency() {
        const amount = parseFloat(document.getElementById('usd-amount').value) || 0;
        const select = document.getElementById('target-currency');
        const rate = parseFloat(select.value) || 0;
        const code = select.options[select.selectedIndex].getAttribute('data-code');

        const result = amount * rate;
        document.getElementById('conversion-result').innerText = `${amount.toLocaleString()} USD = ${result.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})} ${code}`;
    }

    document.addEventListener("DOMContentLoaded", function () {
        convertCurrency();

        // Currency Strength Chart
        const currencies = {!! json_encode($currencies) !!};
        const labels = currencies.map(c => c.code);
        const data = currencies.map(c => c.rate_to_usd);

        // Filters out extreme values like IDR to scale properly in chart
        const labelsFiltered = labels.filter(l => l !== 'IDR');
        const dataFiltered = data.filter((d, i) => labels[i] !== 'IDR');

        const ctx = document.getElementById('currencyStrengthChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labelsFiltered,
                datasets: [{
                    label: 'Nilai terhadap 1 USD',
                    data: dataFiltered,
                    backgroundColor: '#10b981',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endpush
