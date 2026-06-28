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
    <!-- FontAwesome for Premium Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
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
                    Lengkapi permohonan perpanjangan kerjasama secara bertahap melalui sistem wizard form. Pilih nama mitra yang sudah terdaftar untuk memulai.
                </p>

                <div class="partner-hero-points">
                    <div class="partner-point">
                        <strong>1.</strong>
                        <span>Pilih nama instansi/mitra Anda yang terdaftar</span>
                    </div>
                    <div class="partner-point">
                        <strong>2.</strong>
                        <span>Lengkapi kontak PIC aktif terbaru</span>
                    </div>
                    <div class="partner-point">
                        <strong>3.</strong>
                        <span>Tulis rencana / tujuan perpanjangan kerja sama</span>
                    </div>
                    <div class="partner-point">
                        <strong>4.</strong>
                        <span>Tinjau rangkuman permohonan perpanjangan</span>
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
                                <span class="partner-step-label">Identitas Mitra</span>
                            </div>
                            <div class="partner-step-item" data-step-target="2">
                                <div class="partner-step-circle">2</div>
                                <span class="partner-step-label">Kontak PIC</span>
                            </div>
                            <div class="partner-step-item" data-step-target="3">
                                <div class="partner-step-circle">3</div>
                                <span class="partner-step-label">Rencana Baru</span>
                            </div>
                            <div class="partner-step-item" data-step-target="4">
                                <div class="partner-step-circle">4</div>
                                <span class="partner-step-label">Review</span>
                            </div>
                            <div class="partner-step-item" data-step-target="5">
                                <div class="partner-step-circle">5</div>
                                <span class="partner-step-label">Konfirmasi</span>
                            </div>
                        </div>
                        <div class="partner-wizard-meta">
                            <span class="partner-wizard-meta-label" id="wizardStepLabel">Langkah 1: Identitas Mitra</span>
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
                                <h2>Identitas Mitra Terdaftar</h2>
                            </div>
                            <p>Kolom dengan tanda <span class="partner-required">*</span> wajib diisi.</p>
                        </div>

                        <div class="partner-form-grid">
                            <div class="partner-form-section" style="border: none; padding: 0; margin: 0;">
                                <div class="partner-fields">
                                    <div class="partner-field partner-field-full">
                                        <label for="mitra_id">Cari &amp; Pilih Mitra <span class="partner-required">*</span></label>
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
                        </div>
                    </div>

                    <!-- ═══ STEP 2: Kontak PIC Terkini ═══ -->
                    <div class="form-step" data-step="2">
                        <div class="partner-form-head">
                            <div>
                                <span class="partner-kicker">Langkah 2 dari 5</span>
                                <h2>Kontak Person (PIC) Terkini</h2>
                            </div>
                            <p>Isi informasi PIC instansi Anda untuk keperluan korespondensi.</p>
                        </div>

                        <div class="partner-form-grid">
                            <div class="partner-form-section" style="border: none; padding: 0; margin: 0;">
                                <div class="partner-fields">
                                    <div class="partner-field">
                                        <label for="nama_pengaju">Nama Lengkap PIC <span class="partner-required">*</span></label>
                                        <input id="nama_pengaju" type="text" name="nama_pengaju" value="{{ old('nama_pengaju') }}"
                                            placeholder="Nama lengkap PIC" required>
                                        @error('nama_pengaju')
                                            <small class="partner-error">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="partner-field">
                                        <label for="jabatan_pengaju">Jabatan PIC</label>
                                        <input id="jabatan_pengaju" type="text" name="jabatan_pengaju"
                                            value="{{ old('jabatan_pengaju') }}" placeholder="Contoh: Staf Hubungan Industri">
                                        @error('jabatan_pengaju')
                                            <small class="partner-error">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="partner-field">
                                        <label for="email_pengaju">Email PIC <span class="partner-required">*</span></label>
                                        <input id="email_pengaju" type="email" name="email_pengaju"
                                            value="{{ old('email_pengaju') }}" placeholder="pic@perusahaan.com" required>
                                        @error('email_pengaju')
                                            <small class="partner-error">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="partner-field">
                                        <label for="telepon_pengaju">Telepon / WA PIC <span class="partner-required">*</span></label>
                                        <input id="telepon_pengaju" type="text" name="telepon_pengaju"
                                            value="{{ old('telepon_pengaju') }}" placeholder="Contoh: 08xxxxxxxxxx" required>
                                        @error('telepon_pengaju')
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
                                <h2>Rencana Perpanjangan Kerja Sama</h2>
                            </div>
                            <p>Rincian tema kegiatan dan rencana kolaborasi baru yang disesuaikan.</p>
                        </div>

                        <div class="partner-form-grid">
                            <div class="partner-form-section" style="border: none; padding: 0; margin: 0;">
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
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ═══ STEP 4: Review Data ═══ -->
                    <div class="form-step" data-step="4">
                        <div class="partner-form-head">
                            <div>
                                <span class="partner-kicker">Langkah 4 dari 5</span>
                                <h2>Review Hasil Pengisian</h2>
                            </div>
                            <p>Periksa rangkuman permohonan perpanjangan kerjasama Anda sebelum dikirim.</p>
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

                            <!-- Card 2: Kontak PIC -->
                            <div class="partner-review-card">
                                <div class="partner-review-card-title">
                                    <i class="fas fa-address-card"></i> Kontak PIC Terkini
                                </div>
                                <div class="partner-review-grid">
                                    <div class="partner-review-item">
                                        <span class="partner-review-label">Nama Lengkap PIC</span>
                                        <span class="partner-review-value" id="rev_nama_pengaju">-</span>
                                    </div>
                                    <div class="partner-review-item">
                                        <span class="partner-review-label">Jabatan PIC</span>
                                        <span class="partner-review-value" id="rev_jabatan_pengaju">-</span>
                                    </div>
                                    <div class="partner-review-item">
                                        <span class="partner-review-label">Email PIC</span>
                                        <span class="partner-review-value" id="rev_email_pengaju">-</span>
                                    </div>
                                    <div class="partner-review-item">
                                        <span class="partner-review-label">Telepon/WA PIC</span>
                                        <span class="partner-review-value" id="rev_telepon_pengaju">-</span>
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
                                        <span class="partner-review-label">Judul/Tema Perpanjangan</span>
                                        <span class="partner-review-value" id="rev_judul_pengajuan">-</span>
                                    </div>
                                    <div class="partner-review-item partner-review-value-full">
                                        <span class="partner-review-label">Tujuan &amp; Harapan</span>
                                        <span class="partner-review-value" id="rev_tujuan_pengajuan">-</span>
                                    </div>
                                    <div class="partner-review-item partner-review-value-full">
                                        <span class="partner-review-label">Ruang Lingkup Baru</span>
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
                                <h2>Konfirmasi &amp; Pengiriman</h2>
                            </div>
                            <p>Konfirmasi akhir persetujuan perpanjangan kerjasama resmi.</p>
                        </div>

                        <div class="partner-form-grid">
                            <div class="partner-form-section" style="border: none; padding: 0; margin: 0;">
                                <div class="partner-declaration">
                                    <input type="checkbox" id="declaration_agree" name="declaration_agree" required>
                                    <label for="declaration_agree">
                                        Saya menyatakan dengan sesungguhnya bahwa semua data permohonan perpanjangan kerjasama yang telah saya isikan adalah benar dan sesuai kesepakatan lembaga yang saya wakili.
                                    </label>
                                </div>

                                <div class="partner-fields">
                                    <div class="partner-field partner-field-full">
                                        <label for="pesan_tambahan">Catatan / Pesan Tambahan untuk Petugas Penilai (Opsional)</label>
                                        <textarea id="pesan_tambahan" name="pesan_tambahan" rows="4"
                                            placeholder="Tambahkan pesan khusus untuk tim admin jika terdapat dokumen rujukan kerjasama sebelumnya yang ingin disampaikan.">{{ old('pesan_tambahan') }}</textarea>
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
                            <i class="fas fa-paper-plane"></i> Kirim Permohonan Perpanjangan
                        </button>
                    </div>
                </form>
            </section>
        </main>
    </div>

    <!-- Wizard Javascript Logic -->
    <script>
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
            "Langkah 1: Identitas Mitra",
            "Langkah 2: Kontak PIC",
            "Langkah 3: Rencana Baru",
            "Langkah 4: Review Data",
            "Langkah 5: Konfirmasi"
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

            requiredFields.forEach(field => {
                const parent = field.closest('.partner-field') || field.parentElement;
                
                if (field.type === 'checkbox') {
                    if (!field.checked) {
                        isValid = false;
                        parent.classList.add('has-error');
                    } else {
                        parent.classList.remove('has-error');
                    }
                } else {
                    if (field.value.trim() === '') {
                        isValid = false;
                        if (parent.classList.contains('partner-field')) {
                            parent.classList.add('has-error');
                        }
                    } else {
                        if (parent.classList.contains('partner-field')) {
                            parent.classList.remove('has-error');
                        }
                    }
                }
            });

            if (!isValid) {
                const firstInvalid = activeStepEl.querySelector('.has-error input, .has-error select, .has-error textarea');
                if (firstInvalid) firstInvalid.focus();
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

            document.getElementById('rev_nama_pengaju').innerText = getVal('nama_pengaju');
            document.getElementById('rev_jabatan_pengaju').innerText = getVal('jabatan_pengaju');
            document.getElementById('rev_email_pengaju').innerText = getVal('email_pengaju');
            document.getElementById('rev_telepon_pengaju').innerText = getVal('telepon_pengaju');

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

        // Standard Submit check on Step 5
        form.addEventListener('submit', function(e) {
            const agreeCheck = document.getElementById('declaration_agree');
            if (agreeCheck && !agreeCheck.checked) {
                e.preventDefault();
                alert("Anda harus menyetujui pernyataan keabsahan data sebelum mengirim pengajuan.");
                agreeCheck.focus();
            }
        });
    </script>
</body>

</html>
