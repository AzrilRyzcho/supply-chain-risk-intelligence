<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - RiskIntel</title>
    
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
                    <h3 class="fw-bold text-center text-white mb-2">Masuk Aplikasi</h3>
                    <p class="text-secondary text-center small mb-4">Akses platform monitoring risiko rantai pasok global.</p>

                    <!-- Session Status Alert -->
                    @if (session('status'))
                        <div class="alert alert-success border-0 small mb-4" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email" class="form-label text-light">Alamat Email</label>
                            <input type="email" name="email" class="form-control bg-dark border-secondary text-white @error('email') is-invalid @enderror" id="email" placeholder="contoh: user@example.com" value="{{ old('email') }}" required autofocus autocomplete="username">
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <label for="password" class="form-label text-light mb-0">Kata Sandi</label>
                                @if (Route::has('password.request'))
                                    <a class="text-info text-decoration-none small" href="{{ route('password.request') }}">
                                        Lupa Kata Sandi?
                                    </a>
                                @endif
                            </div>
                            <input type="password" name="password" class="form-control bg-dark border-secondary text-white @error('password') is-invalid @enderror" id="password" placeholder="Masukkan kata sandi Anda" required autocomplete="current-password">
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label text-light small" for="remember">
                                Ingat Saya
                            </label>
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
