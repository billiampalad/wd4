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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" data-turbo-track="reload">
    <link rel="stylesheet" href="{{ asset('css/auth/public-submission.css') }}" data-turbo-track="reload">
    <!-- FontAwesome for Premium Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Theme Sync Script (Prevents FOUC) -->
    <script>
        (function() {
            const saved = localStorage.getItem('welcome-theme');
            if (saved) {
                document.documentElement.setAttribute('data-theme', saved);
            } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
</head>

<body class="partner-submission-body">
    <div class="partner-submission-shell">
        <header class="partner-submission-nav">
            <a href="{{ url('/') }}" class="partner-brand">
                <img src="{{ asset('img/logo.png') }}" alt="Logo Polimdo" width="36" height="36">
                <span>POLIMDO &amp; DUDIKA</span>
            </a>
            <div class="partner-nav-actions">
                <a href="{{ url('/') }}" class="partner-nav-link">
                    <i class="fas fa-arrow-left" style="margin-right:4px;font-size:0.75rem;"></i> Kembali
                </a>
                <button type="button" class="theme-toggle" data-theme-toggle aria-pressed="false"
                    aria-label="Ubah ke mode gelap">
                    <span class="theme-toggle-orb" aria-hidden="true">
                        <svg class="theme-icon theme-icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3a7 7 0 0 0 9.79 9.79Z" />
                        </svg>
                        <svg class="theme-icon theme-icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="12" cy="12" r="4" />
                            <path d="M12 2v2" />
                            <path d="M12 20v2" />
                            <path d="m4.93 4.93 1.41 1.41" />
                            <path d="m17.66 17.66 1.41 1.41" />
                            <path d="M2 12h2" />
                            <path d="M20 12h2" />
                            <path d="m6.34 17.66-1.41 1.41" />
                            <path d="m19.07 4.93-1.41 1.41" />
                        </svg>
                    </span>
                    <span class="theme-toggle-text" data-theme-toggle-label>Mode Gelap</span>
                </button>
                <a href="{{ route('login') }}" class="partner-nav-button">Login Pengelola</a>
            </div>
        </header>

        <main class="partner-submission-main">
            <section class="partner-submission-hero">
                <span class="partner-kicker">Perpanjangan Kerja Sama</span>
                <h1>Perpanjang kerja sama aktif dengan proses yang lebih jelas.</h1>
                <p>
                    Pilih mitra terdaftar, perbarui kontak penghubung, lalu jelaskan rencana lanjutan kerja sama dalam beberapa langkah sederhana.
                </p>

                <div class="partner-hero-points">
                    <div class="partner-point">
                        <strong>1.</strong>
                        <span>Pilih mitra terdaftar</span>
                    </div>
                    <div class="partner-point">
                        <strong>2.</strong>
                        <span>Perbarui kontak aktif</span>
                    </div>
                    <div class="partner-point">
                        <strong>3.</strong>
                        <span>Jelaskan tujuan perpanjangan</span>
                    </div>
                    <div class="partner-point">
                        <strong>4.</strong>
                        <span>Periksa ringkasan sebelum dikirim</span>
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
                        Mohon periksa kembali formulir. Masih ada data yang perlu diperbaiki di beberapa langkah.
                    </div>
                @endif

                <!-- Stepper Progress Tracker -->
                <div class="partner-wizard-container">
                    <div class="partner-stepper-wrap">
                        <div class="partner-stepper-track">
                            <div class="partner-stepper-track-fill" id="wizardProgressFill"></div>
                        </div>
                        <div class="partner-stepper-steps">
                            <div class="partner-step-item is-active" data-step-target="1">
                                <div class="partner-step-circle">1</div>
                                <span class="partner-step-label">Mitra</span>
                            </div>
                            <div class="partner-step-item" data-step-target="2">
                                <div class="partner-step-circle">2</div>
                                <span class="partner-step-label">Kontak</span>
                            </div>
                            <div class="partner-step-item" data-step-target="3">
                                <div class="partner-step-circle">3</div>
                                <span class="partner-step-label">Rencana</span>
                            </div>
                            <div class="partner-step-item" data-step-target="4">
                                <div class="partner-step-circle">4</div>
                                <span class="partner-step-label">Tinjau</span>
                            </div>
                            <div class="partner-step-item" data-step-target="5">
                                <div class="partner-step-circle">5</div>
                                <span class="partner-step-label">Kirim</span>
                            </div>
                        </div>
                        <div class="partner-wizard-meta">
                            <span class="partner-wizard-meta-label" id="wizardStepLabel">Langkah 1: Mitra terdaftar</span>
                            <span class="partner-wizard-meta-percentage" id="wizardPercentage">0%</span>
                        </div>
                    </div>
                </div>

                <!-- Form Wizard -->
                <form action="{{ route('pengajuan.perpanjangan.store') }}" method="POST" id="wizardForm">
                    @csrf

                    <!-- ═══ STEP 1: Identitas Mitra Terdaftar ═══ -->
                    <div class="form-step active" data-step="1">
                        <div class="partner-form-head">
                            <div>
                                <span class="partner-kicker">Langkah 1 dari 5</span>
                                <h2>Mitra Terdaftar</h2>
                            </div>
                            <p>Kolom bertanda <span class="partner-required">*</span> wajib diisi.</p>
                        </div>

                        <div class="partner-form-grid">
                            <div class="partner-form-section is-flat">
                                <div class="partner-fields">
                                    <div class="partner-field partner-field-full">
                                        <label for="mitra_id">Mitra yang Mengajukan Perpanjangan <span class="partner-required">*</span></label>
                                        <div class="partner-alpine-select" x-data="partnerSelect('Pilih mitra terdaftar')" x-init="init($refs.native)" @click.outside="close()">
                                            <select x-ref="native" id="mitra_id" name="mitra_id" class="partner-native-select" required>
                                                <option value="">-- Pilih mitra terdaftar --</option>
                                                @foreach ($mitras as $mitra)
                                                    <option value="{{ $mitra->id }}"
                                                        {{ (string) old('mitra_id') === (string) $mitra->id ? 'selected' : '' }}>
                                                        {{ $mitra->nama_mitra }} ({{ ucfirst($mitra->kategori) }} &middot; {{ $mitra->negara ?: 'Indonesia' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="partner-select-trigger" :class="{ 'is-open': open, 'is-placeholder': !value }" @click="toggle(); $nextTick(() => $refs.search && $refs.search.focus())" :aria-expanded="open.toString()" aria-haspopup="listbox">
                                                <span class="partner-select-value" x-text="selectedLabel || placeholder"></span>
                                                <span class="partner-select-icon"><i class="fas fa-chevron-down"></i></span>
                                            </button>
                                            <div class="partner-select-panel" x-show="open" x-transition.origin.top style="display: none;" role="listbox">
                                                <div class="partner-select-search" x-show="options.length > 6">
                                                    <i class="fas fa-magnifying-glass"></i>
                                                    <input x-ref="search" type="text" x-model="query" placeholder="Cari nama mitra..." @keydown.stop>
                                                </div>
                                                <template x-for="option in filteredOptions()" :key="`${option.value}-${option.label}`">
                                                    <button type="button" class="partner-select-option" :class="{ 'is-selected': option.value === value, 'is-placeholder': option.placeholder }" @click="choose(option)" role="option" :aria-selected="(option.value === value).toString()">
                                                        <span x-text="option.label"></span>
                                                        <i class="fas fa-check" x-show="option.value === value"></i>
                                                    </button>
                                                </template>
                                                <div class="partner-select-empty" x-show="filteredOptions().length === 0">Data tidak ditemukan</div>
                                            </div>
                                        </div>
                                        @error('mitra_id')
                                            <small class="partner-error">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ═══ STEP 2: Kontak Terkini ═══ -->
                    <div class="form-step" data-step="2">
                        <div class="partner-form-head">
                            <div>
                                <span class="partner-kicker">Langkah 2 dari 5</span>
                                <h2>Kontak Terbaru</h2>
                            </div>
                            <p>Masukkan kontak aktif untuk kebutuhan klarifikasi selama peninjauan.</p>
                        </div>

                        <div class="partner-form-grid">
                            <div class="partner-form-section is-flat">
                                <div class="partner-fields">
                                    <div class="partner-field">
                                        <label for="nama_penandatangan">Nama Penandatangan <span class="partner-required">*</span></label>
                                        <input id="nama_penandatangan" type="text" name="nama_penandatangan" value="{{ old('nama_penandatangan') }}"
                                            placeholder="Nama lengkap penandatangan" required>
                                        @error('nama_penandatangan')
                                            <small class="partner-error">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="partner-field">
                                        <label for="jabatan_penandatangan">Jabatan Penandatangan <span class="partner-required">*</span></label>
                                        <input id="jabatan_penandatangan" type="text" name="jabatan_penandatangan"
                                            value="{{ old('jabatan_penandatangan') }}" placeholder="Contoh: Direktur / Kepala Bagian" required>
                                        @error('jabatan_penandatangan')
                                            <small class="partner-error">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="partner-field">
                                        <label for="nama_penanggung_jawab">Nama Penanggung Jawab</label>
                                        <input id="nama_penanggung_jawab" type="text" name="nama_penanggung_jawab"
                                            value="{{ old('nama_penanggung_jawab') }}" placeholder="Nama lengkap penanggung jawab">
                                        @error('nama_penanggung_jawab')
                                            <small class="partner-error">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="partner-field">
                                        <label for="jabatan_penanggung_jawab">Jabatan Penanggung Jawab</label>
                                        <input id="jabatan_penanggung_jawab" type="text" name="jabatan_penanggung_jawab"
                                            value="{{ old('jabatan_penanggung_jawab') }}" placeholder="Contoh: Koordinator Kerja Sama">
                                        @error('jabatan_penanggung_jawab')
                                            <small class="partner-error">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="partner-field">
                                        <label for="email">Email <span class="partner-required">*</span></label>
                                        <input id="email" type="email" name="email"
                                            value="{{ old('email') }}" placeholder="pic@perusahaan.com" required>
                                        @error('email')
                                            <small class="partner-error">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="partner-field">
                                        <label for="telepon">WhatsApp / Telepon <span class="partner-required">*</span></label>
                                        <input id="telepon" type="text" name="telepon"
                                            value="{{ old('telepon') }}" placeholder="Contoh: 08xxxxxxxxxx" required>
                                        @error('telepon')
                                            <small class="partner-error">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ═══ STEP 3: Rencana Perpanjangan Kerja Sama ═══ -->
                    <div class="form-step" data-step="3">
                        <div class="partner-form-head">
                            <div>
                                <span class="partner-kicker">Langkah 3 dari 5</span>
                                <h2>Rencana Lanjutan</h2>
                            </div>
                            <p>Tuliskan tujuan, manfaat, dan pembaruan ruang lingkup untuk periode kerja sama berikutnya.</p>
                        </div>

                        <div class="partner-form-grid">
                            <div class="partner-form-section is-flat">
                                <div class="partner-fields">
                                    <div class="partner-field partner-field-full">
                                        <label for="judul_pengajuan">Judul Rencana Perpanjangan <span class="partner-required">*</span></label>
                                        <input id="judul_pengajuan" type="text" name="judul_pengajuan"
                                            value="{{ old('judul_pengajuan') }}"
                                            placeholder="Contoh: Lanjutan Program Magang Mahasiswa dan Penyelarasan Kurikulum" required>
                                        @error('judul_pengajuan')
                                            <small class="partner-error">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="partner-field partner-field-full">
                                        <label for="tujuan_pengajuan">Tujuan Perpanjangan <span class="partner-required">*</span></label>
                                        <textarea id="tujuan_pengajuan" name="tujuan_pengajuan" rows="4"
                                            placeholder="Jelaskan alasan perpanjangan, manfaat, dan target kegiatan berikutnya" required>{{ old('tujuan_pengajuan') }}</textarea>
                                        @error('tujuan_pengajuan')
                                            <small class="partner-error">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="partner-field partner-field-full">
                                        <label for="ruang_lingkup">Ruang Lingkup Lanjutan <span class="partner-required">*</span></label>
                                        <div class="partner-alpine-select" x-data="partnerSelect('Pilih ruang lingkup kegiatan')" x-init="init($refs.native)" @click.outside="close()">
                                            <select x-ref="native" id="ruang_lingkup" name="ruang_lingkup" class="partner-native-select" required>
                                                <option value="">Pilih ruang lingkup kegiatan</option>
                                                @foreach ($jenisKerjasamas as $jenis)
                                                    <option value="{{ $jenis->nama_kerjasama }}"
                                                        {{ old('ruang_lingkup') === $jenis->nama_kerjasama ? 'selected' : '' }}>
                                                        {{ $jenis->nama_kerjasama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="partner-select-trigger" :class="{ 'is-open': open, 'is-placeholder': !value }" @click="toggle(); $nextTick(() => $refs.search && $refs.search.focus())" :aria-expanded="open.toString()" aria-haspopup="listbox">
                                                <span class="partner-select-value" x-text="selectedLabel || placeholder"></span>
                                                <span class="partner-select-icon"><i class="fas fa-chevron-down"></i></span>
                                            </button>
                                            <div class="partner-select-panel" x-show="open" x-transition.origin.top style="display: none;" role="listbox">
                                                <div class="partner-select-search" x-show="options.length > 6">
                                                    <i class="fas fa-magnifying-glass"></i>
                                                    <input x-ref="search" type="text" x-model="query" placeholder="Cari ruang lingkup..." @keydown.stop>
                                                </div>
                                                <template x-for="option in filteredOptions()" :key="`${option.value}-${option.label}`">
                                                    <button type="button" class="partner-select-option" :class="{ 'is-selected': option.value === value, 'is-placeholder': option.placeholder }" @click="choose(option)" role="option" :aria-selected="(option.value === value).toString()">
                                                        <span x-text="option.label"></span>
                                                        <i class="fas fa-check" x-show="option.value === value"></i>
                                                    </button>
                                                </template>
                                                <div class="partner-select-empty" x-show="filteredOptions().length === 0">Data tidak ditemukan</div>
                                            </div>
                                        </div>
                                        @error('ruang_lingkup')
                                            <small class="partner-error">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ═══ STEP 4: Tinjau Data ═══ -->
                    <div class="form-step" data-step="4">
                        <div class="partner-form-head">
                            <div>
                                <span class="partner-kicker">Langkah 4 dari 5</span>
                                <h2>Tinjau Perpanjangan</h2>
                            </div>
                            <p>Periksa ringkasan mitra, kontak, dan rencana lanjutan sebelum konfirmasi.</p>
                        </div>

                        <div class="partner-review-container">
                            <!-- Card 1: Profil Mitra -->
                            <div class="partner-review-card">
                                <div class="partner-review-card-title">
                                    <i class="fas fa-building"></i> Identitas Mitra
                                </div>
                                <div class="partner-review-grid">
                                    <div class="partner-review-item partner-review-value-full">
                                        <span class="partner-review-label">Mitra Terdaftar</span>
                                        <span class="partner-review-value" id="rev_mitra_id">-</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 2: Kontak -->
                            <div class="partner-review-card">
                                <div class="partner-review-card-title">
                                    <i class="fas fa-address-card"></i> Kontak Terkini
                                </div>
                                <div class="partner-review-grid">
                                    <div class="partner-review-item">
                                        <span class="partner-review-label">Nama Penandatangan</span>
                                        <span class="partner-review-value" id="rev_nama_penandatangan">-</span>
                                    </div>
                                    <div class="partner-review-item">
                                        <span class="partner-review-label">Jabatan Penandatangan</span>
                                        <span class="partner-review-value" id="rev_jabatan_penandatangan">-</span>
                                    </div>
                                    <div class="partner-review-item">
                                        <span class="partner-review-label">Nama Penanggung Jawab</span>
                                        <span class="partner-review-value" id="rev_nama_penanggung_jawab">-</span>
                                    </div>
                                    <div class="partner-review-item">
                                        <span class="partner-review-label">Jabatan Penanggung Jawab</span>
                                        <span class="partner-review-value" id="rev_jabatan_penanggung_jawab">-</span>
                                    </div>
                                    <div class="partner-review-item">
                                        <span class="partner-review-label">Email</span>
                                        <span class="partner-review-value" id="rev_email">-</span>
                                    </div>
                                    <div class="partner-review-item">
                                        <span class="partner-review-label">WhatsApp / Telepon</span>
                                        <span class="partner-review-value" id="rev_telepon">-</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 3: Rencana Kerja Sama -->
                            <div class="partner-review-card">
                                <div class="partner-review-card-title">
                                    <i class="fas fa-handshake"></i> Rencana Perpanjangan
                                </div>
                                <div class="partner-review-grid">
                                    <div class="partner-review-item partner-review-value-full">
                                        <span class="partner-review-label">Judul Perpanjangan</span>
                                        <span class="partner-review-value" id="rev_judul_pengajuan">-</span>
                                    </div>
                                    <div class="partner-review-item partner-review-value-full">
                                        <span class="partner-review-label">Tujuan Lanjutan</span>
                                        <span class="partner-review-value" id="rev_tujuan_pengajuan">-</span>
                                    </div>
                                    <div class="partner-review-item partner-review-value-full">
                                        <span class="partner-review-label">Ruang Lingkup</span>
                                        <span class="partner-review-value" id="rev_ruang_lingkup">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ═══ STEP 5: Konfirmasi & Kirim ═══ -->
                    <div class="form-step" data-step="5">
                        <div class="partner-form-head">
                            <div>
                                <span class="partner-kicker">Langkah 5 dari 5</span>
                                <h2>Konfirmasi Akhir</h2>
                            </div>
                            <p>Setujui pernyataan data sebelum permohonan dikirim untuk ditinjau.</p>
                        </div>

                        <div class="partner-form-grid">
                            <div class="partner-form-section is-flat">
                                <div class="partner-declaration">
                                    <input type="checkbox" id="declaration_agree" name="declaration_agree" required>
                                    <label for="declaration_agree">
                                        Saya menyatakan bahwa seluruh data perpanjangan yang diisi benar, mutakhir, dan dapat dipertanggungjawabkan oleh lembaga yang saya wakili.
                                    </label>
                                </div>

                                <div class="partner-fields">
                                    <div class="partner-field partner-field-full">
                                        <label for="pesan_tambahan">Catatan Tambahan (Opsional)</label>
                                        <textarea id="pesan_tambahan" name="pesan_tambahan" rows="4"
                                            placeholder="Tambahkan catatan singkat terkait kerja sama sebelumnya bila diperlukan.">{{ old('pesan_tambahan') }}</textarea>
                                        @error('pesan_tambahan')
                                            <small class="partner-error">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Wizard Action Navigations -->
                    <div class="partner-form-actions">
                        <button type="button" class="partner-secondary-button" id="prevBtn" onclick="navigateStep(-1)" style="display: none;">
                            <i class="fas fa-arrow-left"></i> Sebelumnya
                        </button>
                        <span></span> <!-- Spacer helper -->
                        <button type="button" class="partner-primary-button" id="nextBtn" onclick="navigateStep(1)">
                            Selanjutnya <i class="fas fa-arrow-right"></i>
                        </button>
                        <button type="submit" class="partner-primary-button" id="submitBtn" style="display: none;">
                            <i class="fas fa-paper-plane"></i> Kirim Data
                        </button>
                    </div>
                </form>
            </section>
        </main>
    </div>

    <!-- Wizard Javascript Logic -->
    <script>
        function partnerSelect(defaultPlaceholder = 'Pilih data') {
            return {
                open: false,
                query: '',
                value: '',
                selectedLabel: '',
                placeholder: defaultPlaceholder,
                options: [],
                native: null,
                init(nativeSelect) {
                    this.native = nativeSelect;
                    this.options = Array.from(nativeSelect.options).map(option => ({
                        value: option.value,
                        label: option.text.trim(),
                        disabled: option.disabled,
                        placeholder: option.value === ''
                    }));
                    this.syncFromNative();
                    nativeSelect.addEventListener('change', () => this.syncFromNative());
                },
                syncFromNative() {
                    this.value = this.native ? this.native.value : '';
                    const selected = this.options.find(option => option.value === this.value);
                    if (selected?.placeholder) {
                        this.placeholder = selected.label || this.placeholder;
                        this.selectedLabel = '';
                        return;
                    }
                    this.selectedLabel = selected?.label || '';
                },
                filteredOptions() {
                    const keyword = this.query.trim().toLowerCase();
                    return this.options.filter(option => !keyword || option.label.toLowerCase().includes(keyword));
                },
                choose(option) {
                    if (option.disabled || !this.native) return;
                    this.native.value = option.value;
                    this.native.dispatchEvent(new Event('change', { bubbles: true }));
                    this.syncFromNative();
                    this.close();
                },
                toggle() {
                    this.open = !this.open;
                    if (!this.open) this.query = '';
                },
                close() {
                    this.open = false;
                    this.query = '';
                }
            };
        }

        let currentStep = 1;
        const totalSteps = 5;

        // Elements
        const form = document.getElementById('wizardForm');
        const steps = document.querySelectorAll('.form-step');
        const stepperItems = document.querySelectorAll('.partner-step-item');
        const progressFill = document.getElementById('wizardProgressFill');
        const percentageText = document.getElementById('wizardPercentage');
        const stepLabelText = document.getElementById('wizardStepLabel');

        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');

        const stepLabels = [
            "Langkah 1: Mitra terdaftar",
            "Langkah 2: Kontak aktif",
            "Langkah 3: Rencana lanjut",
            "Langkah 4: Tinjau data",
            "Langkah 5: Kirim data"
        ];

        // Realtime Input Cleanup on Error Class
        document.querySelectorAll('.partner-field input, .partner-field select, .partner-field textarea').forEach(el => {
            el.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    this.closest('.partner-field').classList.remove('has-error');
                }
            });
            el.addEventListener('change', function() {
                if (this.value !== '') {
                    this.closest('.partner-field').classList.remove('has-error');
                }
            });
        });

        function navigateStep(direction) {
            if (direction === 1 && !validateCurrentStep()) {
                return; // block if active step invalid
            }

            // Sync review fields if moving to Step 4
            if (currentStep === 3 && direction === 1) {
                syncReviewData();
            }

            // Update step count
            currentStep += direction;

            // Constrain
            if (currentStep < 1) currentStep = 1;
            if (currentStep > totalSteps) currentStep = totalSteps;

            updateWizardUI();
        }

        function validateCurrentStep() {
            const activeStepEl = document.querySelector(`.form-step[data-step="${currentStep}"]`);
            if (!activeStepEl) return true;

            const requiredFields = activeStepEl.querySelectorAll('[required]');
            let isValid = true;
            let firstInvalid = null;

            requiredFields.forEach(field => {
                const parent = field.closest('.partner-field') || field.closest('.partner-declaration') || field.parentElement;
                const invalid = field.type === 'checkbox' ? !field.checked : !field.checkValidity();

                parent.classList.toggle('has-error', invalid);

                if (invalid) {
                    isValid = false;
                    firstInvalid = firstInvalid || field;
                }
            });

            if (!isValid && firstInvalid) {
                const focusTarget = firstInvalid.classList.contains('partner-native-select')
                    ? firstInvalid.closest('.partner-alpine-select')?.querySelector('.partner-select-trigger') || firstInvalid
                    : firstInvalid;

                focusTarget.focus({ preventScroll: true });
                focusTarget.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            return isValid;
        }
        function syncReviewData() {
            // Helper to clean values
            const getVal = (id) => {
                const el = document.getElementById(id);
                return el ? el.value.trim() || '-' : '-';
            };

            const getSelectText = (id) => {
                const el = document.getElementById(id);
                if (el && el.selectedIndex >= 0) {
                    return el.options[el.selectedIndex].text || '-';
                }
                return '-';
            };

            // Mapping inputs to labels
            document.getElementById('rev_mitra_id').innerText = getSelectText('mitra_id');

            document.getElementById('rev_nama_penandatangan').innerText = getVal('nama_penandatangan');
            document.getElementById('rev_jabatan_penandatangan').innerText = getVal('jabatan_penandatangan');
            document.getElementById('rev_nama_penanggung_jawab').innerText = getVal('nama_penanggung_jawab');
            document.getElementById('rev_jabatan_penanggung_jawab').innerText = getVal('jabatan_penanggung_jawab');
            document.getElementById('rev_email').innerText = getVal('email');
            document.getElementById('rev_telepon').innerText = getVal('telepon');

            document.getElementById('rev_judul_pengajuan').innerText = getVal('judul_pengajuan');
            document.getElementById('rev_tujuan_pengajuan').innerText = getVal('tujuan_pengajuan');
            document.getElementById('rev_ruang_lingkup').innerText = getVal('ruang_lingkup');
        }

        function updateWizardUI() {
            // Update step views visibility
            steps.forEach(step => {
                const stepNum = parseInt(step.getAttribute('data-step'));
                if (stepNum === currentStep) {
                    step.classList.add('active');
                } else {
                    step.classList.remove('active');
                }
            });

            // Update stepper circle classes
            stepperItems.forEach(item => {
                const targetNum = parseInt(item.getAttribute('data-step-target'));
                if (targetNum === currentStep) {
                    item.classList.add('is-active');
                    item.classList.remove('is-completed');
                } else if (targetNum < currentStep) {
                    item.classList.add('is-completed');
                    item.classList.remove('is-active');
                    item.querySelector('.partner-step-circle').innerHTML = '<i class="fas fa-check"></i>';
                } else {
                    item.classList.remove('is-active', 'is-completed');
                    item.querySelector('.partner-step-circle').innerText = targetNum;
                }
            });

            // Track percentage and fill width
            const fillWidth = ((currentStep - 1) / (totalSteps - 1)) * 100;
            const percentage = Math.round(fillWidth);
            
            progressFill.style.width = `${fillWidth}%`;
            percentageText.innerText = `${percentage}%`;
            stepLabelText.innerText = stepLabels[currentStep - 1];

            // Action buttons configuration
            if (currentStep === 1) {
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'inline-flex';
                submitBtn.style.display = 'none';
            } else if (currentStep === totalSteps) {
                prevBtn.style.display = 'inline-flex';
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'inline-flex';
            } else {
                prevBtn.style.display = 'inline-flex';
                nextBtn.style.display = 'inline-flex';
                submitBtn.style.display = 'none';
            }
        }

        // Initialize progress width and UI states
        updateWizardUI();

        // Submit validation on Step 5
        form.addEventListener('submit', function(e) {
            if (currentStep !== totalSteps || !validateCurrentStep()) {
                e.preventDefault();
                return;
            }
        });

        // ── Theme Toggle Logic ──
        (function() {
            const themeToggle = document.querySelector('[data-theme-toggle]');
            const themeLabel = themeToggle ? themeToggle.querySelector('[data-theme-toggle-label]') : null;
            const storageKey = 'welcome-theme';

            function applyTheme(theme) {
                const isDark = theme === 'dark';
                document.documentElement.dataset.theme = isDark ? 'dark' : 'light';

                if (themeToggle) {
                    themeToggle.setAttribute('aria-pressed', String(isDark));
                    themeToggle.setAttribute('aria-label', isDark ? 'Ubah ke mode terang' : 'Ubah ke mode gelap');
                }
                if (themeLabel) {
                    themeLabel.textContent = isDark ? 'Mode Terang' : 'Mode Gelap';
                }
            }

            function getInitialTheme() {
                const dt = document.documentElement.dataset.theme;
                if (dt === 'dark' || dt === 'light') return dt;
                try {
                    const saved = localStorage.getItem(storageKey);
                    if (saved === 'dark' || saved === 'light') return saved;
                } catch (e) {}
                return 'light';
            }

            applyTheme(getInitialTheme());

            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
                    try { localStorage.setItem(storageKey, nextTheme); } catch (e) {}
                    applyTheme(nextTheme);
                });
            }
        })();
    </script>
</body>

</html>
