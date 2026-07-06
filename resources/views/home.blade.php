<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Supply Chain Risk Intelligence Platform</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .hero-section {
            padding: 80px 20px;
            text-align: center;
        }
        .glass-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body>

    <div class="container my-auto">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 text-center hero-section">
                <div class="glass-card p-5">
                    <span class="badge bg-primary px-3 py-2 mb-3 fs-6">Versi Awal - Fondasi Aktif</span>
                    <h1 class="display-4 fw-bold text-dark mb-3">Global Supply Chain Risk Intelligence Platform</h1>
                    <p class="lead text-muted mb-4">Platform monitoring risiko rantai pasok global cerdas berbasis Multi-API dan analitik data terintegrasi.</p>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <!-- Redirect buttons for routing tests -->
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4 py-3 fw-bold"><i class="bi bi-box-arrow-in-right me-2"></i>Masuk Aplikasi</a>
                        <a href="{{ route('register') }}" class="btn btn-outline-dark btn-lg px-4 py-3"><i class="bi bi-person-plus me-2"></i>Daftar Akun</a>
                    </div>

                    <div class="row mt-5 text-start">
                        <div class="col-md-4 mb-3">
                            <h5 class="fw-bold text-dark"><i class="bi bi-lightning-charge text-info me-2"></i>Real-time API</h5>
                            <p class="text-muted small">Integrasi data cuaca global, kurs valas, dan indikator makro World Bank terpadu.</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h5 class="fw-bold text-dark"><i class="bi bi-shield-check text-success me-2"></i>Risk Engine</h5>
                            <p class="text-muted small">Penghitungan indeks risiko komposit menggunakan model pembobotan (*weighted*).</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h5 class="fw-bold text-dark"><i class="bi bi-chat-left-heart text-warning me-2"></i>Sentiment Analysis</h5>
                            <p class="text-muted small">Evaluasi sentimen artikel berita logistik berbasis leksikon di server PHP.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
