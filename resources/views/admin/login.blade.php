<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}">
    <title>Admin Login | Sistem Informasi Kerjasama Politeknik Negeri Manado</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/login.css') }}">
</head>

<body>

    <!-- LEFT PANEL -->
    <div class="left-panel">
        <div class="grid-bg"></div>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>

        <div class="left-content">
            <div class="brand">
                <div class="brand-icon">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" style="width: 40px; height: 40px;">
                </div>
                <span class="brand-name">AdminPortal</span>
            </div>

            <p class="hero-label">Sistem Manajemen Admin</p>
            <h1 class="hero-title">
                Kelola Sistem<br>dengan <span>Presisi</span><br>& Keamanan
            </h1>
            <p class="hero-desc">
                Platform administrasi terpusat untuk memantau, mengelola, dan mengontrol seluruh operasional sistem secara efisien dan terstruktur.
            </p>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right-panel">
        <div class="login-header">
            <div class="login-badge">
                <div class="badge-dot"></div>
                <span class="badge-text">Akses Terbatas</span>
            </div>
            <h2 class="login-title">Selamat Datang</h2>
            <p class="login-subtitle">Masuk ke panel administrator sistem</p>
        </div>

        <form method="POST" action="/admin/login">
            @csrf

            @if (session('success'))
                <div class="alert-success" role="status">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert-danger" role="alert">
                    {{ session('error') }}
                    @if (session('lockout_seconds'))
                        <span class="login-lockout">
                            Coba lagi dalam <strong>{{ session('lockout_seconds') }}</strong> detik.
                        </span>
                    @endif
                </div>
            @endif

            <div class="form-group">
                <label for="nik">NIK Administrator</label>
                <div class="input-wrap">
                    <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    <input type="text" id="nik" name="nik" placeholder="Masukkan NIK Anda" autocomplete="off" value="{{ old('nik') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <div class="input-wrap">
                    <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                    <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" required>
                    <button type="button" class="show-pass" onclick="togglePass()" title="Tampilkan kata sandi">
                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <div class="btn-content">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                        <polyline points="10 17 15 12 10 7" />
                        <line x1="15" y1="12" x2="3" y2="12" />
                    </svg>
                    Masuk ke Sistem
                </div>
            </button>
        </form>

        <div class="security-note">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
            </svg>
            <p>Koneksi aman dengan enkripsi end-to-end. Hanya personel berwenang yang diizinkan mengakses sistem ini.</p>
        </div>
    </div>

    <script src="{{ asset('js/admin/login.js') }}"></script>

</body>

</html>