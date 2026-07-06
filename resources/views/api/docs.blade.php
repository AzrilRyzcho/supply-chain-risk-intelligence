<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REST API Documentation - RiskIntel</title>
    <!-- Google Fonts & Bootstrap -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .sidebar {
            background-color: #ffffff;
            border-right: 1px solid #e2e8f0;
            min-height: 100vh;
            position: sticky;
            top: 0;
        }
        .endpoint-badge {
            font-weight: bold;
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 4px;
        }
        .badge-get {
            background-color: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
        }
        pre {
            background-color: #0f172a;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            font-size: 0.85em;
            max-height: 300px;
            overflow-y: auto;
        }
        .card-custom {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05);
            background-color: #ffffff;
            margin-bottom: 25px;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar p-4 d-none d-md-block">
            <h5 class="fw-bold text-slate-800 mb-4"><i class="bi bi-shield-lock-fill text-danger me-2"></i>RiskIntel API</h5>
            <ul class="nav flex-column gap-2">
                <li class="nav-item">
                    <a href="#intro" class="nav-link text-dark fw-bold"><i class="bi bi-info-circle me-2"></i>Pengantar</a>
                </li>
                <li class="nav-item">
                    <a href="#countries" class="nav-link text-dark"><span class="endpoint-badge badge-get me-2">GET</span>/countries</a>
                </li>
                <li class="nav-item">
                    <a href="#risk" class="nav-link text-dark"><span class="endpoint-badge badge-get me-2">GET</span>/risk</a>
                </li>
                <li class="nav-item">
                    <a href="#news" class="nav-link text-dark"><span class="endpoint-badge badge-get me-2">GET</span>/news</a>
                </li>
                <li class="nav-item">
                    <a href="#currency" class="nav-link text-dark"><span class="endpoint-badge badge-get me-2">GET</span>/currency</a>
                </li>
                <li class="nav-item">
                    <a href="#ports" class="nav-link text-dark"><span class="endpoint-badge badge-get me-2">GET</span>/ports</a>
                </li>
            </ul>
        </div>

        <!-- Content Area -->
        <div class="col-md-9 col-lg-10 p-5">
            <div id="intro" class="card card-custom p-4 mb-4">
                <h2 class="fw-bold text-slate-800 mb-2">Dokumentasi REST API Internal</h2>
                <p class="text-muted">Selamat datang di panduan teknis REST API internal platform **RiskIntel**. Seluruh endpoint di bawah ini mengembalikan data terstruktur dalam format JSON menggunakan standar **Laravel API Resource**.</p>
                <div class="alert alert-info border-0 mb-0">
                    <i class="bi bi-info-circle-fill me-2"></i><strong>Base URL:</strong> <code>/api</code>
                </div>
            </div>

            <!-- GET /countries -->
            <div id="countries" class="card card-custom p-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="endpoint-badge badge-get fs-6">GET</span>
                    <h4 class="fw-bold mb-0">/countries</h4>
                </div>
                <p class="text-muted">Mengambil daftar negara mitra dagang strategis yang terdaftar di sistem.</p>
                
                <h5>Parameter Query (Optional):</h5>
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Parameter</th>
                            <th>Tipe</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>search</code></td>
                            <td>String (max: 100)</td>
                            <td>Mencari negara berdasarkan nama atau kode ISO negara.</td>
                        </tr>
                        <tr>
                            <td><code>region</code></td>
                            <td>String (max: 100)</td>
                            <td>Memfilter negara berdasarkan wilayah (contoh: <code>Europe</code>, <code>Asia</code>).</td>
                        </tr>
                    </tbody>
                </table>

                <h5 class="mt-3">Contoh Respons (JSON):</h5>
                <pre><code>{
  "data": [
    {
      "id": 1,
      "name": "Jerman",
      "code": "DE",
      "currency_code": "EUR",
      "region": "Europe",
      "latitude": 52.52,
      "longitude": 13.405,
      "created_at": "2026-07-06T00:00:00.000000Z",
      "updated_at": "2026-07-06T00:00:00.000000Z"
    }
  ]
}</code></pre>
            </div>

            <!-- GET /risk -->
            <div id="risk" class="card card-custom p-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="endpoint-badge badge-get fs-6">GET</span>
                    <h4 class="fw-bold mb-0">/risk</h4>
                </div>
                <p class="text-muted">Mengambil kalkulasi indeks risiko komposit terbaru dari setiap negara mitra.</p>
                
                <h5>Parameter Query (Optional):</h5>
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Parameter</th>
                            <th>Tipe</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>search</code></td>
                            <td>String (max: 100)</td>
                            <td>Mencari data risiko berdasarkan nama atau kode negara.</td>
                        </tr>
                    </tbody>
                </table>

                <h5 class="mt-3">Contoh Respons (JSON):</h5>
                <pre><code>{
  "data": [
    {
      "id": 12,
      "country_id": 1,
      "country_name": "Jerman",
      "country_code": "DE",
      "weather_score": 10,
      "inflation_score": 20,
      "currency_score": 30,
      "sentiment_score": 40,
      "total_score": 25,
      "calculated_at": "2026-07-06T03:14:48.000000Z",
      "created_at": "2026-07-06T03:14:48.000000Z"
    }
  ]
}</code></pre>
            </div>

            <!-- GET /news -->
            <div id="news" class="card card-custom p-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="endpoint-badge badge-get fs-6">GET</span>
                    <h4 class="fw-bold mb-0">/news</h4>
                </div>
                <p class="text-muted">Mengambil cache berita logistik rantai pasok global beserta skor analisis sentimen.</p>
                
                <h5>Parameter Query (Optional):</h5>
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Parameter</th>
                            <th>Tipe</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>search</code></td>
                            <td>String (max: 100)</td>
                            <td>Mencari berita berdasarkan kata kunci judul atau nama sumber berita.</td>
                        </tr>
                        <tr>
                            <td><code>sentiment</code></td>
                            <td>String</td>
                            <td>Memfilter berdasarkan klasifikasi sentimen: <code>positive</code>, <code>neutral</code>, atau <code>negative</code>.</td>
                        </tr>
                    </tbody>
                </table>

                <h5 class="mt-3">Contoh Respons (JSON):</h5>
                <pre><code>{
  "data": [
    {
      "id": 5,
      "country_id": 2,
      "country_name": "Indonesia",
      "title": "Maritime logistics efficiency boosts in Jakarta Port",
      "source": "Bloomberg",
      "url": "https://bloomberg.com/jakarta-logistics",
      "sentiment": "positive",
      "positive_score": 0.82,
      "negative_score": 0.05,
      "published_at": "2026-07-06T00:00:00.000000Z",
      "created_at": "2026-07-06T02:00:00.000000Z"
    }
  ]
}</code></pre>
            </div>

            <!-- GET /currency -->
            <div id="currency" class="card card-custom p-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="endpoint-badge badge-get fs-6">GET</span>
                    <h4 class="fw-bold mb-0">/currency</h4>
                </div>
                <p class="text-muted">Mengambil daftar nilai tukar valas terbaru dari seluruh negara mitra terhadap mata uang USD.</p>
                
                <h5>Parameter Query (Optional):</h5>
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Parameter</th>
                            <th>Tipe</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>search</code></td>
                            <td>String (max: 10)</td>
                            <td>Mencari mata uang berdasarkan kode valas (contoh: <code>EUR</code>, <code>IDR</code>).</td>
                        </tr>
                    </tbody>
                </table>

                <h5 class="mt-3">Contoh Respons (JSON):</h5>
                <pre><code>{
  "data": [
    {
      "id": 1,
      "code": "EUR",
      "rate_to_usd": 0.92,
      "fetched_at": "2026-07-06T03:00:00.000000Z",
      "created_at": "2026-07-06T03:00:00.000000Z"
    }
  ]
}</code></pre>
            </div>

            <!-- GET /ports -->
            <div id="ports" class="card card-custom p-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="endpoint-badge badge-get fs-6">GET</span>
                    <h4 class="fw-bold mb-0">/ports</h4>
                </div>
                <p class="text-muted">Mengambil daftar lokasi sebaran pelabuhan laut utama dunia berserta koordinat geografisnya.</p>
                
                <h5>Parameter Query (Optional):</h5>
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Parameter</th>
                            <th>Tipe</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>search</code></td>
                            <td>String (max: 100)</td>
                            <td>Mencari pelabuhan berdasarkan nama atau kode pelabuhan.</td>
                        </tr>
                        <tr>
                            <td><code>country_id</code></td>
                            <td>Integer</td>
                            <td>Memfilter pelabuhan berdasarkan ID negara pemilik.</td>
                        </tr>
                    </tbody>
                </table>

                <h5 class="mt-3">Contoh Respons (JSON):</h5>
                <pre><code>{
  "data": [
    {
      "id": 1,
      "name": "Port of Hamburg",
      "code": "DEHAM",
      "country_id": 1,
      "country_name": "Jerman",
      "latitude": 53.5,
      "longitude": 9.9,
      "created_at": "2026-07-06T00:00:00.000000Z"
    }
  ]
}</code></pre>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
