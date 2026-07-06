document.addEventListener("DOMContentLoaded", function () {
    const data = window.compareData || {};
    if (!data.country1 || !data.country2) return;

    // --- 1. RISK COMPARE CHART (Radar) ---
    const ctxRisk = document.getElementById('riskCompareChart').getContext('2d');
    new Chart(ctxRisk, {
        type: 'radar',
        data: {
            labels: ['Total Risiko', 'Cuaca', 'Inflasi', 'Kurs', 'Sentimen'],
            datasets: [
                {
                    label: data.country1.name,
                    data: [
                        data.country1.score,
                        data.country1.weather,
                        data.country1.inflation,
                        data.country1.currency,
                        data.country1.sentiment
                    ],
                    backgroundColor: 'rgba(37, 99, 235, 0.2)',
                    borderColor: '#2563eb',
                    borderWidth: 2,
                    pointBackgroundColor: '#2563eb'
                },
                {
                    label: data.country2.name,
                    data: [
                        data.country2.score,
                        data.country2.weather,
                        data.country2.inflation,
                        data.country2.currency,
                        data.country2.sentiment
                    ],
                    backgroundColor: 'rgba(239, 68, 68, 0.2)',
                    borderColor: '#ef4444',
                    borderWidth: 2,
                    pointBackgroundColor: '#ef4444'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                r: {
                    angleLines: { display: true },
                    suggestedMin: 0,
                    suggestedMax: 100
                }
            }
        }
    });

    // --- 2. METRICS COMPARE CHART (Grouped Bar) ---
    const ctxMetrics = document.getElementById('metricsCompareChart').getContext('2d');
    new Chart(ctxMetrics, {
        type: 'bar',
        data: {
            labels: ['GDP (Miliar $)', 'Inflasi (%)', 'Suhu (°C)', 'Angin (m/s)', 'Risiko Badai (%)'],
            datasets: [
                {
                    label: data.country1.name,
                    data: [
                        data.country1.gdp,
                        data.country1.inflationRate,
                        data.country1.temp,
                        data.country1.wind,
                        data.country1.storm
                    ],
                    backgroundColor: '#2563eb',
                    borderRadius: 4
                },
                {
                    label: data.country2.name,
                    data: [
                        data.country2.gdp,
                        data.country2.inflationRate,
                        data.country2.temp,
                        data.country2.wind,
                        data.country2.storm
                    ],
                    backgroundColor: '#ef4444',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: {
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                },
                x: { grid: { display: false } }
            }
        }
    });
});
