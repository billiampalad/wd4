<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}">
    <title>Login | Sistem Informasi Kerjasama Politeknik Negeri Manado</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body>

    <div class="login-background" aria-hidden="true">
        <div class="background-orb orb-top"></div>
        <div class="background-orb orb-bottom"></div>
        <div class="background-grid"></div>
        <div class="background-bar bar-1"></div>
        <div class="background-bar bar-2"></div>
        <div class="background-bar bar-3"></div>
        <div class="background-bar bar-4"></div>
        <div class="background-bar bar-5"></div>
        <div class="background-bar bar-6"></div>
        <div class="background-bar bar-7"></div>
    </div>

    <div class="card-wrapper">

        <!-- Floating status pill -->
        {{-- <div class="float-pill">
            <div class="pill-dot"></div>
            <span class="pill-text">Sistem aktif & aman</span>
        </div> --}}

        <div class="top-bar"></div>
        <div class="card">

            <div class="card-header">
                <div class="avatar-wrap">
                    <img src="{{ asset('img/logo.png') }}" alt="Profile" width="50" height="50"
                        style="border-radius: 50%; object-fit: cover;">
                </div>
                <h1 class="card-title">Masuk ke Akun</h1>
                <p class="card-subtitle">Masukkan NIP dan kata sandi Anda untuk melanjutkan</p>
            </div>

            <form method="POST" action="/login">
                @csrf

                @if (session('status') || session('success'))
                    <div class="auth-alert auth-alert-success" role="status">
                        {{ session('status') ?? session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="auth-alert auth-alert-danger" role="alert">
                        {{ session('error') }}
                        @if (session('lockout_seconds'))
                            <span class="auth-lockout">
                                Coba lagi dalam <strong>{{ session('lockout_seconds') }}</strong> detik.
                            </span>
                        @endif
                    </div>
                @endif

                <div class="form-group" x-data="{ showInfo: false }">
                    <label>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                            stroke-linejoin="round">
                            <rect x="2" y="5" width="20" height="14" rx="2" />
                            <path d="M2 10h20" />
                        </svg>
                        Email/Nomor Induk Pendidikan
                        {{-- Interactive Info Icon & Popover Tooltip --}}
                        <div style="position: relative; display: inline-flex; align-items: center;">
                            <button type="button"
                                @mouseenter="showInfo = true"
                                @mouseleave="showInfo = false"
                                @click="showInfo = !showInfo"
                                aria-label="Informasi Email atau NIP"
                                style="background: rgba(79, 70, 229, 0.08); border: 1px solid rgba(79, 70, 229, 0.18); width: 22px; height: 22px; border-radius: 50%; color: var(--accent, #4f46e5); display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; font-size: 11px;"
                                onmouseover="this.style.background='var(--accent, #4f46e5)'; this.style.color='#ffffff';"
                                onmouseout="this.style.background='rgba(79, 70, 229, 0.08)'; this.style.color='var(--accent, #4f46e5)';">
                                <i class="fas fa-info"></i>
                            </button>

                            {{-- Floating Premium Tooltip Card --}}
                            <div x-show="showInfo"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                style="position: absolute; right: 0; bottom: calc(100% + 8px); width: 270px; background: #ffffff; border: 1px solid var(--border, #e2e8f0); border-radius: 12px; padding: 12px 14px; box-shadow: 0 14px 30px -4px rgba(0,0,0,0.15); z-index: 70; pointer-events: none;"
                                x-cloak>
                                <div style="display: flex; gap: 10px; align-items: flex-start;">
                                    <div style="width: 26px; height: 26px; border-radius: 8px; background: rgba(79, 70, 229, 0.12); color: var(--accent, #4f46e5); display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 12px;">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    <div style="display: flex; flex-direction: column; gap: 2px;">
                                        <span style="font-size: 11px; font-weight: 700; color: var(--text); letter-spacing: 0.2px;">Petunjuk Pengisian</span>
                                        <p style="font-size: 11px; color: var(--muted); margin: 0; line-height: 1.45; font-weight: 400;">
                                            Gunakan NIP untuk akun internal kampus. Mitra menggunakan email yang telah terdaftar.
                                        </p>
                                    </div>
                                </div>
                                {{-- Arrow pointer --}}
                                <div style="position: absolute; bottom: -5px; right: 7px; width: 8px; height: 8px; background: #ffffff; border-right: 1px solid var(--border, #e2e8f0); border-bottom: 1px solid var(--border, #e2e8f0); transform: rotate(45deg);"></div>
                            </div>
                        </div>
                    </label>
                    <div class="input-wrap">
                        <svg class="input-prefix" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <input type="text" name="nik" placeholder="Masukkan Email atau NIP Anda" autocomplete="off"
                            value="{{ old('nik') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                            stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        Kata Sandi
                    </label>
                    <div class="input-wrap">
                        <svg class="input-prefix" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <input type="password" id="pass" name="password" placeholder="Masukkan kata sandi"
                            required>
                        <button type="button" class="eye-btn" onclick="togglePass()" title="Tampilkan">
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <div class="btn-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                            <polyline points="10 17 15 12 10 7" />
                            <line x1="15" y1="12" x2="3" y2="12" />
                        </svg>
                        Masuk Sekarang
                    </div>
                </button>

                <div class="forgot-password-wrap">
                    <a href="{{ route('password.request') }}" class="forgot-password-link">
                        <span class="forgot-password-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="7.5" cy="15.5" r="5.5" />
                                <path d="m21 2-9.6 9.6" />
                                <path d="m15.5 7.5 2 2L21 6" />
                            </svg>
                        </span>
                        <span>Lupa kata sandi?</span>
                    </a>
                </div>

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
