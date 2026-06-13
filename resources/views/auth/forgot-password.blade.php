<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi</title>
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
        <div class="card">
            <div class="card-header">
                <div class="avatar-wrap auth-icon-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="7.5" cy="15.5" r="5.5" />
                        <path d="m21 2-9.6 9.6" />
                        <path d="m15.5 7.5 2 2L21 6" />
                    </svg>
                </div>
                <h1 class="card-title">Lupa Kata Sandi?</h1>
                <p class="card-subtitle">Masukkan email akun Anda. Kami akan mengirimkan tautan untuk membuat kata
                    sandi baru.</p>
            </div>

            @if (session('status'))
                <div class="auth-alert auth-alert-success" role="status">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="form-group">
                    <label for="email">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                            stroke-linejoin="round">
                            <rect width="20" height="16" x="2" y="4" rx="2" />
                            <path d="m22 7-10 5L2 7" />
                        </svg>
                        Email Terdaftar
                    </label>
                    <div class="input-wrap">
                        <svg class="input-prefix" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="16" x="2" y="4" rx="2" />
                            <path d="m22 7-10 5L2 7" />
                        </svg>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            placeholder="nama@institusi.ac.id" autocomplete="email" required autofocus>
                    </div>
                    @error('email')
                        <span class="auth-field-error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">
                    <span class="btn-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="m22 2-7 20-4-9-9-4Z" />
                            <path d="M22 2 11 13" />
                        </svg>
                        Kirim Tautan Pemulihan
                    </span>
                </button>

                <div class="auth-back-wrap">
                    <a href="{{ route('login') }}" class="auth-back-link">
                        <span aria-hidden="true">&larr;</span>
                        Kembali ke halaman masuk
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
