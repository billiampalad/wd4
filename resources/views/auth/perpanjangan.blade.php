<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}">
    <title>Ajukan Perpanjangan Kerja Sama | Sistem Informasi Kerjasama Politeknik Negeri Manado</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&family=DM+Serif+Display:ital@0;1&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" data-turbo-track="reload">
    <link rel="stylesheet" href="{{ asset('css/auth/public-submission.css') }}" data-turbo-track="reload">
</head>

<body class="partner-submission-body">
    <div class="partner-submission-shell">
        <header class="partner-submission-nav">
            <a href="{{ url('/') }}" class="partner-brand">
                <img src="{{ asset('img/logo.png') }}" alt="Logo Polimdo" width="36" height="36">
                <span>POLIMDO &amp; DUDIKA</span>
            </a>
            <div class="partner-nav-actions">
                <a href="{{ url('/') }}" class="partner-nav-link">Kembali ke Landing Page</a>
                <a href="{{ route('login') }}" class="partner-nav-button">Login Pengelola</a>
            </div>
        </header>

        <main class="partner-submission-main">
            <section class="partner-submission-hero">
                <span class="partner-kicker">Formulir Perpanjangan Publik</span>
                <h1>Perpanjang masa kerja sama mitra Politeknik Negeri Manado.</h1>
                <p>
                    Pilih nama instansi/mitra Anda yang sudah terdaftar di sistem, lengkapi informasi PIC aktif saat ini, serta ringkasan rencana perpanjangan kerja sama.
                </p>

                <div class="partner-hero-points">
                    <div class="partner-point">
                        <strong>1.</strong>
                        <span>Pilih nama instansi Anda yang sudah terdaftar di sistem</span>
                    </div>
                    <div class="partner-point">
                        <strong>2.</strong>
                        <span>Lengkapi kontak PIC aktif terbaru</span>
                    </div>
                    <div class="partner-point">
                        <strong>3.</strong>
                        <span>Tulis rencana / tujuan perpanjangan kerja sama</span>
                    </div>
                </div>
            </section>

            <section class="partner-form-card">
                @if (session('success'))
                    <div class="partner-alert partner-alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="partner-alert partner-alert-error">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="partner-alert partner-alert-error">
                        Mohon periksa kembali formulir. Masih ada data yang perlu diperbaiki.
                    </div>
                @endif

                <div class="partner-form-head">
                    <div>
                        <span class="partner-kicker">Data Perpanjangan</span>
                        <h2>Lengkapi data permohonan</h2>
                    </div>
                    <p>Kolom bertanda <span class="partner-required">*</span> wajib diisi.</p>
                </div>

                <form action="{{ route('pengajuan.perpanjangan.store') }}" method="POST" class="partner-form-grid">
                    @csrf

                    <div class="partner-form-section">
                        <div class="partner-section-head">
                            <h3>Identitas Mitra Terdaftar</h3>
                            <p>Silakan cari dan pilih nama lembaga/perusahaan Anda.</p>
                        </div>

                        <div class="partner-fields">
                            <div class="partner-field partner-field-full">
                                <label for="mitra_id">Nama Mitra <span class="partner-required">*</span></label>
                                <select id="mitra_id" name="mitra_id" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--partner-border); background: #fff; font-family: inherit; font-size: 0.9rem; color: var(--partner-text); cursor: pointer; outline: none; transition: border-color 0.2s ease;">
                                    <option value="">-- Pilih Instansi / Perusahaan Anda --</option>
                                    @foreach ($mitras as $mitra)
                                        <option value="{{ $mitra->id }}"
                                            {{ (string) old('mitra_id') === (string) $mitra->id ? 'selected' : '' }}>
                                            {{ $mitra->nama_mitra }} ({{ ucfirst($mitra->kategori) }} &middot; {{ $mitra->negara ?: 'Indonesia' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('mitra_id')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="partner-form-section">
                        <div class="partner-section-head">
                            <h3>Kontak PIC Terkini</h3>
                            <p>Orang yang dapat dihubungi oleh tim kampus untuk tindak lanjut.</p>
                        </div>

                        <div class="partner-fields">
                            <div class="partner-field">
                                <label for="nama_pengaju">Nama Pengaju <span class="partner-required">*</span></label>
                                <input id="nama_pengaju" type="text" name="nama_pengaju" value="{{ old('nama_pengaju') }}"
                                    placeholder="Nama lengkap PIC" required>
                                @error('nama_pengaju')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="partner-field">
                                <label for="jabatan_pengaju">Jabatan Pengaju</label>
                                <input id="jabatan_pengaju" type="text" name="jabatan_pengaju"
                                    value="{{ old('jabatan_pengaju') }}" placeholder="Contoh: Staf Hubungan Industri">
                                @error('jabatan_pengaju')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="partner-field">
                                <label for="email_pengaju">Email Pengaju <span class="partner-required">*</span></label>
                                <input id="email_pengaju" type="email" name="email_pengaju"
                                    value="{{ old('email_pengaju') }}" placeholder="pic@perusahaan.com" required>
                                @error('email_pengaju')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="partner-field">
                                <label for="telepon_pengaju">Telepon / WA Pengaju <span class="partner-required">*</span></label>
                                <input id="telepon_pengaju" type="text" name="telepon_pengaju"
                                    value="{{ old('telepon_pengaju') }}" placeholder="Contoh: 08xxxxxxxxxx" required>
                                @error('telepon_pengaju')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="partner-form-section">
                        <div class="partner-section-head">
                            <h3>Rencana Perpanjangan Kerja Sama</h3>
                            <p>Rincian judul kegiatan dan tujuan perpanjangan kerja sama.</p>
                        </div>

                        <div class="partner-fields">
                            <div class="partner-field partner-field-full">
                                <label for="judul_pengajuan">Judul / Tema Perpanjangan <span class="partner-required">*</span></label>
                                <input id="judul_pengajuan" type="text" name="judul_pengajuan"
                                    value="{{ old('judul_pengajuan') }}"
                                    placeholder="Contoh: Perpanjangan MoU kegiatan magang mahasiswa dan penyelarasan kurikulum" required>
                                @error('judul_pengajuan')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="partner-field partner-field-full">
                                <label for="tujuan_pengajuan">Tujuan &amp; Harapan Perpanjangan <span class="partner-required">*</span></label>
                                <textarea id="tujuan_pengajuan" name="tujuan_pengajuan" rows="4"
                                    placeholder="Deskripsikan secara ringkas tujuan memperpanjang hubungan kerja sama ini" required>{{ old('tujuan_pengajuan') }}</textarea>
                                @error('tujuan_pengajuan')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="partner-field partner-field-full">
                                <label for="ruang_lingkup">Ruang Lingkup Kegiatan Baru (Bila Ada)</label>
                                <textarea id="ruang_lingkup" name="ruang_lingkup" rows="4"
                                    placeholder="Sebutkan cakupan kerja sama (misal: beasiswa, riset bersama, sertifikasi kompetensi)">{{ old('ruang_lingkup') }}</textarea>
                                @error('ruang_lingkup')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="partner-field partner-field-full">
                                <label for="pesan_tambahan">Catatan / Pesan Tambahan</label>
                                <textarea id="pesan_tambahan" name="pesan_tambahan" rows="3"
                                    placeholder="Pesan tambahan untuk tim admin penelaah kemitraan">{{ old('pesan_tambahan') }}</textarea>
                                @error('pesan_tambahan')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="partner-form-actions">
                        <a href="{{ url('/') }}" class="partner-secondary-button">Batal</a>
                        <button type="submit" class="partner-primary-button">Kirim Permohonan Perpanjangan</button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>

</html>
