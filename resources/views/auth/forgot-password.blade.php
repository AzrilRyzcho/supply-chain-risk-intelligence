<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - RiskIntel</title>
    
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
                    <h3 class="fw-bold text-center text-white mb-2">Lupa Kata Sandi</h3>
                    <p class="text-secondary small text-center mb-4">
                        Masukkan alamat email Anda untuk menerima tautan guna mengatur ulang kata sandi baru.
                    </p>

                    <!-- Session Status Alert -->
                    @if (session('status'))
                        <div class="alert alert-success border-0 small mb-4 text-dark" style="background-color: #a7f3d0;" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="{{ route('password.email') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email" class="form-label text-light">Alamat Email</label>
                            <input type="email" name="email" class="form-control bg-dark border-secondary text-white @error('email') is-invalid @enderror" id="email" placeholder="contoh: nama@domain.com" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold mt-2">
                            Kirim Tautan Atur Ulang
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}" class="text-info text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Kembali ke Halaman Masuk</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
