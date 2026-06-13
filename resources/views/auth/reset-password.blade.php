<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Kata Sandi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
</head>

<body>
    <div class="background-grid"></div>
    <div class="background-orb orb-1"></div>
    <div class="background-orb orb-2"></div>
    <div class="background-bars" aria-hidden="true">
        @for ($bar = 1; $bar <= 7; $bar++)
            <div class="background-bar bar-{{ $bar }}"></div>
        @endfor
    </div>

    <div class="card-wrapper">
        <div class="top-bar"></div>
        <div class="card auth-reset-card">
            <div class="card-header">
                <div class="avatar-wrap auth-icon-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                </div>
                <h1 class="card-title">Buat Kata Sandi Baru</h1>
                <p class="card-subtitle">Gunakan minimal 8 karakter dan pastikan kedua isian kata sandi sama.</p>
            </div>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label for="email">Email Akun</label>
                    <div class="input-wrap">
                        <svg class="input-prefix" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="16" x="2" y="4" rx="2" />
                            <path d="m22 7-10 5L2 7" />
                        </svg>
                        <input type="email" id="email" name="email" value="{{ old('email', $email) }}"
                            autocomplete="email" required>
                    </div>
                    @error('email')
                        <span class="auth-field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Kata Sandi Baru</label>
                    <div class="input-wrap">
                        <svg class="input-prefix" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <input type="password" id="password" name="password" placeholder="Minimal 8 karakter"
                            autocomplete="new-password" required>
                    </div>
                    @error('password')
                        <span class="auth-field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                    <div class="input-wrap">
                        <svg class="input-prefix" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 12 2 2 4-4" />
                            <circle cx="12" cy="12" r="9" />
                        </svg>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            placeholder="Ulangi kata sandi baru" autocomplete="new-password" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <span class="btn-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M20 6 9 17l-5-5" />
                        </svg>
                        Simpan Kata Sandi Baru
                    </span>
                </button>
            </form>
        </div>
    </div>
</body>

</html>
