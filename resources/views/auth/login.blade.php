<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
</head>

<body>

    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="dots"></div>

    <div class="card-wrapper">

        <!-- Floating status pill -->
        <div class="float-pill">
            <div class="pill-dot"></div>
            <span class="pill-text">Sistem aktif & aman</span>
        </div>

        <div class="top-bar"></div>
        <div class="card">

            <div class="card-header">
                <div class="avatar-wrap">
                    <img src="{{ asset('img/logo.png') }}" alt="Profile" width="50" height="50" style="border-radius: 50%; object-fit: cover;">
                </div>
                <h1 class="card-title">Masuk ke Akun</h1>
                <p class="card-subtitle">Masukkan NIP dan kata sandi Anda<br>untuk melanjutkan</p>
            </div>

            <form method="POST" action="/login">
                @csrf

                <div class="form-group">
                    <label>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="5" width="20" height="14" rx="2" />
                            <path d="M2 10h20" />
                        </svg>
                        Nomor Induk Pendidikan
                    </label>
                    <div class="input-wrap">
                        <svg class="input-prefix" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <input type="text" name="nik" placeholder="Masukkan NIP Anda" autocomplete="off" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        Kata Sandi
                    </label>
                    <div class="input-wrap">
                        <svg class="input-prefix" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <input type="password" id="pass" name="password" placeholder="Masukkan kata sandi" required>
                        <button type="button" class="eye-btn" onclick="togglePass()" title="Tampilkan">
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <div class="btn-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                            <polyline points="10 17 15 12 10 7" />
                            <line x1="15" y1="12" x2="3" y2="12" />
                        </svg>
                        Masuk Sekarang
                    </div>
                </button>

                <!-- <div class="divider">
                    <div class="divider-line"></div>
                    <span class="divider-text">Butuh bantuan?</span>
                    <div class="divider-line"></div>
                </div>

                <p class="help-text">
                    Belum punya akun? <a href="#">Hubungi administrator</a>
                </p> -->

            </form>
        </div>
    </div>

    <script src="{{ asset('js/auth/login.js') }}"></script>

</body>

</html>