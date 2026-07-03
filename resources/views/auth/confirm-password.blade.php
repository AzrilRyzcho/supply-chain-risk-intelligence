<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Kata Sandi - RiskIntel</title>
    
    <!-- Google Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
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
        .auth-card {
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
                <div class="card auth-card p-4 shadow-lg">
                    <h3 class="fw-bold text-center text-white mb-2">Konfirmasi Kata Sandi</h3>
                    <p class="text-secondary small text-center mb-4">
                        Ini adalah area aman aplikasi. Konfirmasi kata sandi Anda terlebih dahulu sebelum melanjutkan.
                    </p>

                    <form action="{{ route('password.confirm') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="password" class="form-label text-light">Kata Sandi</label>
                            <input type="password" name="password" class="form-control bg-dark border-secondary text-white @error('password') is-invalid @enderror" id="password" placeholder="Masukkan kata sandi Anda" required autocomplete="current-password">
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold mt-2">
                            Konfirmasi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
