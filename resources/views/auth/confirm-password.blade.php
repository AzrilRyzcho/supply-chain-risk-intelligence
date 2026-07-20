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
        :root {
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
            --text-color: #1e293b;
            --text-muted: #64748b;
            --input-bg: #ffffff;
            --input-border: #cbd5e1;
            --input-text: #1e293b;
            --primary-btn-bg: #4f46e5;
            --primary-btn-hover: #4338ca;
            --link-color: #4f46e5;
            --link-hover: #3730a3;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }

        body.dark-theme {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --card-border: rgba(255, 255, 255, 0.08);
            --text-color: #ffffff;
            --text-muted: #94a3b8;
            --input-bg: #0f172a;
            --input-border: #334155;
            --input-text: #ffffff;
            --primary-btn-bg: #6366f1;
            --primary-btn-hover: #4f46e5;
            --link-color: #818cf8;
            --link-hover: #a5b4fc;
            --card-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .auth-card {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            color: var(--text-color);
            box-shadow: var(--card-shadow);
            transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .auth-card h3 {
            color: var(--text-color);
        }

        .auth-card p.text-secondary {
            color: var(--text-muted) !important;
        }

        .auth-card .form-label {
            color: var(--text-color);
            font-weight: 500;
        }

        .auth-card .form-control {
            background-color: var(--input-bg);
            border: 1px solid var(--input-border);
            color: var(--input-text);
            border-radius: 8px;
            padding: 10px 14px;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .auth-card .form-control::placeholder {
            color: var(--text-muted);
            opacity: 0.6;
        }

        .auth-card .form-control:focus {
            background-color: var(--input-bg);
            border-color: var(--primary-btn-bg);
            color: var(--input-text);
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25);
        }

        .auth-card .btn-primary {
            background-color: var(--primary-btn-bg);
            border-color: var(--primary-btn-bg);
            color: #ffffff;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .auth-card .btn-primary:hover, .auth-card .btn-primary:focus {
            background-color: var(--primary-btn-hover);
            border-color: var(--primary-btn-hover);
        }

        .auth-card a.text-info, .auth-card a.text-decoration-none {
            color: var(--link-color) !important;
            transition: color 0.2s ease;
        }

        .auth-card a.text-info:hover, .auth-card a.text-decoration-none:hover {
            color: var(--link-hover) !important;
            text-decoration: underline !important;
        }
    </style>
</head>
<body>
    <script>
        if (localStorage.getItem('dark_theme') === 'true') {
            document.body.classList.add('dark-theme');
        }
    </script>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card auth-card p-4 shadow-lg">
                    <h3 class="fw-bold text-center mb-2">Konfirmasi Kata Sandi</h3>
                    <p class="text-secondary small text-center mb-4">
                        Ini adalah area aman aplikasi. Konfirmasi kata sandi Anda terlebih dahulu sebelum melanjutkan.
                    </p>

                    <form action="{{ route('password.confirm') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Kata Sandi</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Masukkan kata sandi Anda" required autocomplete="current-password">
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
