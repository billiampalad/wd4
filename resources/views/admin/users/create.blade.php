@extends('admin.dashboard')

@section('content')
<main class="main-content">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-users"></i>
            <span class="sep">/</span>
            <a href="{{ route('users.index') }}" style="color: inherit; text-decoration: none;">User Management</a>
            <span class="sep">/</span>
            <span class="current">Tambah Pengguna</span>
        </div>
        <h2 id="pageTitle">Tambah Pengguna Baru</h2>
        <p id="pageDesc">Isi formulir untuk menambahkan pengguna baru ke sistem.</p>
    </div>

    <div class="uc-layout">

        {{-- ── Kiri: Preview Card ─────────────────────────── --}}
        <div class="uc-preview-col">
            <div class="card uc-preview-card">
                <div class="uc-preview-top">
                    <div class="uc-avatar-ring">
                        <div class="uc-avatar-lg" id="previewAvatar">??</div>
                    </div>
                    <div class="uc-preview-name" id="previewName">Nama Pengguna</div>
                    <div class="uc-preview-nik" id="previewNik">NIK: —</div>
                    <div class="uc-preview-badge" id="previewRole">Role Belum Dipilih</div>
                </div>
                <div class="uc-preview-divider"></div>
                <div class="uc-preview-info">
                    <div class="uc-info-row">
                        <span class="uc-info-icon" style="background:rgba(79,70,229,.1);color:#4f46e5;"><i class="fas fa-briefcase"></i></span>
                        <div>
                            <div class="uc-info-label">Jabatan</div>
                            <div class="uc-info-val" id="previewJabatan">—</div>
                        </div>
                    </div>
                    <div class="uc-info-row">
                        <span class="uc-info-icon" style="background:rgba(14,165,233,.1);color:#0ea5e9;"><i class="fas fa-graduation-cap"></i></span>
                        <div>
                            <div class="uc-info-label">Jurusan</div>
                            <div class="uc-info-val" id="previewJurusan">—</div>
                        </div>
                    </div>
                    <div class="uc-info-row">
                        <span class="uc-info-icon" style="background:rgba(16,185,129,.1);color:#10b981;"><i class="fas fa-building"></i></span>
                        <div>
                            <div class="uc-info-label">Unit Kerja</div>
                            <div class="uc-info-val" id="previewUnit">—</div>
                        </div>
                    </div>
                </div>
                <div class="uc-preview-note">
                    <i class="fas fa-eye"></i> Pratinjau diperbarui otomatis
                </div>
            </div>

            {{-- Step guide --}}
            <div class="card uc-steps-card">
                <div class="uc-steps-title"><i class="fas fa-list-check"></i> Panduan Pengisian</div>
                <div class="uc-step uc-step-done" id="step1">
                    <div class="uc-step-dot">1</div>
                    <div class="uc-step-text">Isi identitas (Nama & NIK)</div>
                </div>
                <div class="uc-step" id="step2">
                    <div class="uc-step-dot">2</div>
                    <div class="uc-step-text">Pilih Role pengguna</div>
                </div>
                <div class="uc-step" id="step3">
                    <div class="uc-step-dot">3</div>
                    <div class="uc-step-text">Buat password yang kuat</div>
                </div>
                <div class="uc-step" id="step4">
                    <div class="uc-step-dot">4</div>
                    <div class="uc-step-text">Lengkapi data profil</div>
                </div>
            </div>
        </div>

        {{-- ── Kanan: Form ────────────────────────────────── --}}
        <div class="uc-form-col">
            <div class="card uc-form-card">
                <div class="card-header uc-form-header">
                    <div class="card-title"><i class="fas fa-plus-circle"></i> Formulir Pengguna Baru</div>
                </div>

                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="card-body uc-body">

                        {{-- Section: Identitas --}}
                        <div class="uc-section-label">
                            <span class="uc-section-num">01</span>
                            <span>Identitas Pengguna</span>
                        </div>

                        <div class="uc-grid-2">
                            <div class="uc-form-group">
                                <label class="uc-label" for="name">
                                    <i class="fas fa-user uc-label-icon"></i>
                                    Nama Lengkap
                                    <span class="uc-required">*</span>
                                </label>
                                <input
                                    type="text" id="name" name="name"
                                    class="uc-input @error('name') uc-input-error @enderror"
                                    placeholder="Masukkan nama lengkap"
                                    value="{{ old('name') }}"
                                    oninput="updatePreview()"
                                    required
                                />
                                @error('name')
                                <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="uc-form-group">
                                <label class="uc-label" for="nik">
                                    <i class="fas fa-id-card uc-label-icon"></i>
                                    NIK
                                    <span class="uc-required">*</span>
                                </label>
                                <input
                                    type="text" id="nik" name="nik"
                                    class="uc-input uc-input-mono @error('nik') uc-input-error @enderror"
                                    placeholder="Nomor Induk Kependudukan"
                                    value="{{ old('nik') }}"
                                    oninput="updatePreview()"
                                    required
                                />
                                @error('nik')
                                <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Section: Akun --}}
                        <div class="uc-section-label" style="margin-top:8px;">
                            <span class="uc-section-num">02</span>
                            <span>Akun & Akses</span>
                        </div>

                        <div class="uc-grid-2">
                            <div class="uc-form-group">
                                <label class="uc-label" for="role_id">
                                    <i class="fas fa-shield-halved uc-label-icon"></i>
                                    Role
                                    <span class="uc-required">*</span>
                                </label>
                                <select
                                    id="role_id" name="role_id"
                                    class="uc-input uc-select @error('role_id') uc-input-error @enderror"
                                    onchange="updatePreview()"
                                    required
                                >
                                    <option value="" disabled selected>— Pilih Role —</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->role_name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="uc-form-group">
                                <label class="uc-label" for="password">
                                    <i class="fas fa-lock uc-label-icon"></i>
                                    Password
                                    <span class="uc-required">*</span>
                                </label>
                                <div class="uc-pass-wrap">
                                    <input
                                        type="password" id="password" name="password"
                                        class="uc-input uc-input-pass @error('password') uc-input-error @enderror"
                                        placeholder="Min. 8 karakter"
                                        oninput="checkStrength(this.value)"
                                        required
                                    />
                                    <button type="button" class="uc-pass-toggle" onclick="togglePass()">
                                        <i class="fas fa-eye" id="passEye"></i>
                                    </button>
                                </div>
                                <div class="uc-strength-bar">
                                    <div class="uc-strength-fill" id="strengthFill"></div>
                                </div>
                                <span class="uc-strength-label" id="strengthLabel"></span>
                                @error('password')
                                <span class="uc-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Section: Profil --}}
                        <div class="uc-section-label" style="margin-top:8px;">
                            <span class="uc-section-num">03</span>
                            <span>Data Profil</span>
                        </div>

                        <div class="uc-grid-3">
                            <div class="uc-form-group">
                                <label class="uc-label" for="jabatan">
                                    <i class="fas fa-briefcase uc-label-icon"></i>
                                    Jabatan
                                </label>
                                <input
                                    type="text" id="jabatan" name="jabatan"
                                    class="uc-input"
                                    placeholder="Contoh: Direktur"
                                    value="{{ old('jabatan') }}"
                                    oninput="updatePreview()"
                                />
                            </div>

                            <div class="uc-form-group">
                                <label class="uc-label" for="jurusan_id">
                                    <i class="fas fa-graduation-cap uc-label-icon"></i>
                                    Nama Jurusan
                                </label>
                                <select id="jurusan_id" name="jurusan_id" class="uc-input">
                                    <option value="">-- Pilih Jurusan --</option>
                                    @foreach($jurusans as $jurusan)
                                        <option value="{{ $jurusan->id }}" {{ old('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                                            {{ $jurusan->nama_jurusan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="uc-form-group">
                                <label class="uc-label" for="unit_kerja_id">
                                    <i class="fas fa-building uc-label-icon"></i>
                                    Nama Unit
                                </label>
                                <select id="unit_kerja_id" name="unit_kerja_id" class="uc-input">
                                    <option value="">-- Pilih Unit Kerja --</option>
                                    @foreach($unitKerjas as $unit)
                                        <option value="{{ $unit->id }}" {{ old('unit_kerja_id') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->nama_unit_pelaksana }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="card-footer uc-footer">
                        <a href="{{ route('users.index') }}" class="uc-btn-cancel">
                            <i class="fas fa-arrow-left"></i> Batal
                        </a>
                        <button type="reset" class="uc-btn-reset" onclick="resetPreview()">
                            <i class="fas fa-rotate-left"></i> Reset
                        </button>
                        <button type="submit" class="uc-btn-submit">
                            <i class="fas fa-floppy-disk"></i> Simpan Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</main>
@endsection