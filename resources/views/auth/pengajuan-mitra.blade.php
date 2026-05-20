<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Kerja Sama Mitra - Sistem Informasi Kerjasama</title>
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
                <span class="partner-kicker">Formulir Pengajuan Publik</span>
                <h1>Ajukan kerja sama mitra ke Politeknik Negeri Manado.</h1>
                <p>
                    Lengkapi profil mitra dan rencana kolaborasi. Setelah dikirim, pengajuan akan masuk ke antrean
                    Pimpinan untuk divalidasi dan disetujui sebelum dicatat sebagai mitra resmi.
                </p>

                <div class="partner-hero-points">
                    <div class="partner-point">
                        <strong>1.</strong>
                        <span>Isi data mitra dan PIC pengajuan</span>
                    </div>
                    <div class="partner-point">
                        <strong>2.</strong>
                        <span>Tim Pimpinan meninjau kelayakan kerja sama</span>
                    </div>
                    <div class="partner-point">
                        <strong>3.</strong>
                        <span>Data yang disetujui masuk ke master mitra sistem</span>
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
                        <span class="partner-kicker">Data Pengajuan</span>
                        <h2>Lengkapi informasi mitra</h2>
                    </div>
                    <p>Kolom bertanda <span class="partner-required">*</span> wajib diisi.</p>
                </div>

                <form action="{{ route('pengajuan.kerjasama.store') }}" method="POST" class="partner-form-grid">
                    @csrf

                    <div class="partner-form-section">
                        <div class="partner-section-head">
                            <h3>Informasi Mitra</h3>
                            <p>Profil lembaga/perusahaan yang akan mengajukan kerja sama.</p>
                        </div>

                        <div class="partner-fields">
                            <div class="partner-field partner-field-full">
                                <label for="nama_mitra">Nama Mitra <span class="partner-required">*</span></label>
                                <input id="nama_mitra" type="text" name="nama_mitra" value="{{ old('nama_mitra') }}"
                                    placeholder="Contoh: PT Inovasi Sulawesi Utara">
                                @error('nama_mitra')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="partner-field">
                                <label for="id_klasifikasi">Klasifikasi Mitra</label>
                                <select id="id_klasifikasi" name="id_klasifikasi">
                                    <option value="">Pilih klasifikasi</option>
                                    @foreach ($klasifikasis as $klasifikasi)
                                        <option value="{{ $klasifikasi->id }}"
                                            {{ (string) old('id_klasifikasi') === (string) $klasifikasi->id ? 'selected' : '' }}>
                                            {{ $klasifikasi->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_klasifikasi')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="partner-field">
                                <label for="kategori">Kategori Mitra <span class="partner-required">*</span></label>
                                <select id="kategori" name="kategori">
                                    <option value="nasional" {{ old('kategori', 'nasional') === 'nasional' ? 'selected' : '' }}>Nasional</option>
                                    <option value="internasional" {{ old('kategori') === 'internasional' ? 'selected' : '' }}>Internasional</option>
                                </select>
                                @error('kategori')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="partner-field">
                                <label for="negara">Negara</label>
                                <input id="negara" type="text" name="negara" value="{{ old('negara') }}"
                                    placeholder="Contoh: Indonesia">
                                @error('negara')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="partner-field">
                                <label for="telp">Telepon Mitra <span class="partner-required">*</span></label>
                                <input id="telp" type="text" name="telp" value="{{ old('telp') }}"
                                    placeholder="Contoh: 0431-xxxxxx / 08xxxxxxxxxx">
                                @error('telp')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="partner-field partner-field-full">
                                <label for="website">Website</label>
                                <input id="website" type="text" name="website" value="{{ old('website') }}"
                                    placeholder="https://contohmitra.com">
                                @error('website')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="partner-field partner-field-full">
                                <label for="alamat">Alamat Mitra <span class="partner-required">*</span></label>
                                <textarea id="alamat" name="alamat" rows="3" placeholder="Alamat lengkap mitra">{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="partner-form-section">
                        <div class="partner-section-head">
                            <h3>Kontak Pengaju</h3>
                            <p>Orang yang dapat dihubungi untuk tindak lanjut awal.</p>
                        </div>

                        <div class="partner-fields">
                            <div class="partner-field">
                                <label for="nama_pengaju">Nama Pengaju <span class="partner-required">*</span></label>
                                <input id="nama_pengaju" type="text" name="nama_pengaju" value="{{ old('nama_pengaju') }}"
                                    placeholder="Nama lengkap PIC">
                                @error('nama_pengaju')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="partner-field">
                                <label for="jabatan_pengaju">Jabatan Pengaju</label>
                                <input id="jabatan_pengaju" type="text" name="jabatan_pengaju"
                                    value="{{ old('jabatan_pengaju') }}" placeholder="Contoh: Manager Partnership">
                                @error('jabatan_pengaju')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="partner-field">
                                <label for="email_pengaju">Email Pengaju <span class="partner-required">*</span></label>
                                <input id="email_pengaju" type="email" name="email_pengaju"
                                    value="{{ old('email_pengaju') }}" placeholder="nama@perusahaan.com">
                                @error('email_pengaju')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="partner-field">
                                <label for="telepon_pengaju">Telepon Pengaju <span class="partner-required">*</span></label>
                                <input id="telepon_pengaju" type="text" name="telepon_pengaju"
                                    value="{{ old('telepon_pengaju') }}" placeholder="08xxxxxxxxxx">
                                @error('telepon_pengaju')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="partner-form-section">
                        <div class="partner-section-head">
                            <h3>Rencana Kerja Sama</h3>
                            <p>Berikan ringkasan tujuan dan ruang lingkup yang diusulkan.</p>
                        </div>

                        <div class="partner-fields">
                            <div class="partner-field partner-field-full">
                                <label for="judul_pengajuan">Judul / Tema Kerja Sama <span class="partner-required">*</span></label>
                                <input id="judul_pengajuan" type="text" name="judul_pengajuan"
                                    value="{{ old('judul_pengajuan') }}"
                                    placeholder="Contoh: Kolaborasi magang industri dan pengembangan kurikulum">
                                @error('judul_pengajuan')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="partner-field partner-field-full">
                                <label for="tujuan_pengajuan">Tujuan Pengajuan <span class="partner-required">*</span></label>
                                <textarea id="tujuan_pengajuan" name="tujuan_pengajuan" rows="4"
                                    placeholder="Tuliskan tujuan utama dari kerja sama ini">{{ old('tujuan_pengajuan') }}</textarea>
                                @error('tujuan_pengajuan')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="partner-field partner-field-full">
                                <label for="ruang_lingkup">Ruang Lingkup Kerja Sama</label>
                                <textarea id="ruang_lingkup" name="ruang_lingkup" rows="4"
                                    placeholder="Contoh: magang, riset terapan, rekrutmen alumni, kuliah tamu, sertifikasi">{{ old('ruang_lingkup') }}</textarea>
                                @error('ruang_lingkup')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="partner-field partner-field-full">
                                <label for="pesan_tambahan">Catatan Tambahan</label>
                                <textarea id="pesan_tambahan" name="pesan_tambahan" rows="3"
                                    placeholder="Tambahkan informasi penting lain bila diperlukan">{{ old('pesan_tambahan') }}</textarea>
                                @error('pesan_tambahan')
                                    <small class="partner-error">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="partner-form-actions">
                        <a href="{{ url('/') }}" class="partner-secondary-button">Kembali</a>
                        <button type="submit" class="partner-primary-button">Kirim Pengajuan</button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>

</html>
