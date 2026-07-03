<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - RiskIntel</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #1e293b;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            color: #fff;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card login-card p-4 shadow-lg">
                    <h3 class="fw-bold text-center text-white mb-3">Masuk Aplikasi</h3>
                    <p class="text-secondary text-center small mb-4">Akses platform monitoring risiko rantai pasok global.</p>

                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        

                        <div class="mb-3">
                            <label for="email" class="form-label text-light">Alamat Email</label>
                            <input type="email" name="email" class="form-control bg-dark border-secondary text-white" id="email" placeholder="contoh: admin@example.com" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label text-light">Kata Sandi</label>
                            <input type="password" name="password" class="form-control bg-dark border-secondary text-white" id="password" placeholder="Masukkan kata sandi Anda" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold mt-2">Masuk</button>
                    </form>

                    <div class="text-center mt-4">
                        <span class="text-secondary small">Belum punya akun? <a href="{{ route('register') }}" class="text-info text-decoration-none">Daftar</a></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
