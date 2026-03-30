@extends('admin.dashboard')

@section('content')
<main class="main-content">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-users"></i>
            <span class="sep">/</span>
            <a href="{{ route('users.index') }}" style="color: inherit; text-decoration: none;">User Management</a>
            <span class="sep">/</span>
            <span class="current">Edit Pengguna</span>
        </div>
        <h2 id="pageTitle">Edit Pengguna</h2>
        <p id="pageDesc">Ubah data pengguna yang sudah ada di sistem.</p>
    </div>

    <div class="ue-layout">

        {{-- ── Kiri: Info Panel ───────────────────────────── --}}
        <div class="ue-side-col">

            {{-- Profile Card --}}
            <div class="card ue-profile-card">
                <div class="ue-profile-top">
                    <div class="ue-avatar-ring" id="previewRing">
                        <div class="ue-avatar-lg" id="previewAvatar">
                            {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(strstr($user->name, ' ') ?: $user->name, 1, 1)) }}
                        </div>
                    </div>
                    <div class="ue-profile-name" id="previewName">{{ $user->name }}</div>
                    <div class="ue-profile-nik" id="previewNik">NIK: {{ $user->nik }}</div>
                    <div class="ue-profile-badge" id="previewRole">
                        {{ $user->role?->role_name ?? 'Tidak Ada Role' }}
                    </div>
                    <div class="ue-profile-since">
                        <i class="fas fa-clock"></i>
                        Dibuat {{ $user->created_at?->diffForHumans() ?? '-' }}
                    </div>
                </div>

                <div class="ue-profile-divider"></div>

                <div class="ue-profile-info">
                    <div class="ue-info-row">
                        <span class="ue-info-icon" style="background:rgba(79,70,229,.1);color:#4f46e5;">
                            <i class="fas fa-briefcase"></i>
                        </span>
                        <div>
                            <div class="ue-info-label">Jabatan</div>
                            <div class="ue-info-val" id="previewJabatan">{{ $user->profile?->jabatan ?: '—' }}</div>
                        </div>
                    </div>
                    <div class="ue-info-row">
                        <span class="ue-info-icon" style="background:rgba(14,165,233,.1);color:#0ea5e9;">
                            <i class="fas fa-graduation-cap"></i>
                        </span>
                        <div>
                            <div class="ue-info-label">Jurusan</div>
                            <div class="ue-info-val" id="previewJurusan">{{ $user->profile?->nama_jurusan ?: '—' }}</div>
                        </div>
                    </div>
                    <div class="ue-info-row">
                        <span class="ue-info-icon" style="background:rgba(16,185,129,.1);color:#10b981;">
                            <i class="fas fa-building"></i>
                        </span>
                        <div>
                            <div class="ue-info-label">Unit Kerja</div>
                            <div class="ue-info-val" id="previewUnit">{{ $user->profile?->nama_unit ?: '—' }}</div>
                        </div>
                    </div>
                </div>

                <div class="ue-preview-note">
                    <i class="fas fa-pen-to-square"></i> Pratinjau diperbarui otomatis
                </div>
            </div>

            {{-- Changelog box --}}
            <div class="card ue-change-card">
                <div class="ue-change-title"><i class="fas fa-triangle-exclamation"></i> Perhatian</div>
                <ul class="ue-change-list">
                    <li><i class="fas fa-check"></i> Kosongkan password jika tidak ingin menggantinya</li>
                    <li><i class="fas fa-check"></i> NIK harus unik di seluruh sistem</li>
                    <li><i class="fas fa-check"></i> Perubahan role berlaku segera setelah disimpan</li>
                </ul>
            </div>

        </div>

        {{-- ── Kanan: Form ────────────────────────────────── --}}
        <div class="ue-form-col">
            <div class="card ue-form-card">
                <div class="card-header ue-form-header">
                    <div class="card-title"><i class="fas fa-pen-to-square"></i> Formulir Edit Pengguna</div>
                    {{-- Edit badge --}}
                    <span class="ue-edit-badge">
                        <i class="fas fa-circle" style="font-size:7px;"></i> Mode Edit
                    </span>
                </div>

                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body ue-body">

                        {{-- Section 01: Identitas --}}
                        <div class="ue-section-label">
                            <span class="ue-section-num">01</span>
                            <span>Identitas Pengguna</span>
                        </div>

                        <div class="ue-grid-2">
                            <div class="ue-form-group">
                                <label class="ue-label" for="name">
                                    <i class="fas fa-user ue-label-icon"></i>
                                    Nama Lengkap
                                    <span class="ue-required">*</span>
                                </label>
                                <input
                                    type="text" id="name" name="name"
                                    class="ue-input @error('name') ue-input-error @enderror"
                                    value="{{ old('name', $user->name) }}"
                                    placeholder="Nama lengkap pengguna"
                                    oninput="updatePreview()"
                                    required />
                                @error('name')
                                <span class="ue-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="ue-form-group">
                                <label class="ue-label" for="nik">
                                    <i class="fas fa-id-card ue-label-icon"></i>
                                    NIK
                                    <span class="ue-required">*</span>
                                </label>
                                <input
                                    type="text" id="nik" name="nik"
                                    class="ue-input ue-input-mono @error('nik') ue-input-error @enderror"
                                    value="{{ old('nik', $user->nik) }}"
                                    placeholder="Nomor Induk Kependudukan"
                                    oninput="updatePreview()"
                                    required />
                                @error('nik')
                                <span class="ue-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Section 02: Akun --}}
                        <div class="ue-section-label">
                            <span class="ue-section-num">02</span>
                            <span>Akun &amp; Akses</span>
                        </div>

                        <div class="ue-grid-2">
                            <div class="ue-form-group">
                                <label class="ue-label" for="role_id">
                                    <i class="fas fa-shield-halved ue-label-icon"></i>
                                    Role
                                    <span class="ue-required">*</span>
                                </label>
                                <select
                                    id="role_id" name="role_id"
                                    class="ue-input ue-select @error('role_id') ue-input-error @enderror"
                                    onchange="updatePreview()"
                                    required>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->role_name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                <span class="ue-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="ue-form-group">
                                <label class="ue-label" for="password">
                                    <i class="fas fa-lock ue-label-icon"></i>
                                    Password Baru
                                    <span class="ue-optional">(opsional)</span>
                                </label>
                                <div class="ue-pass-wrap">
                                    <input
                                        type="password" id="password" name="password"
                                        class="ue-input ue-input-pass @error('password') ue-input-error @enderror"
                                        placeholder="Kosongkan jika tidak diubah"
                                        oninput="checkStrength(this.value)" />
                                    <button type="button" class="ue-pass-toggle" onclick="togglePass()">
                                        <i class="fas fa-eye" id="passEye"></i>
                                    </button>
                                </div>
                                <div class="ue-strength-bar">
                                    <div class="ue-strength-fill" id="strengthFill"></div>
                                </div>
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-top:3px;">
                                    <span class="ue-strength-label" id="strengthLabel"></span>
                                    <small class="ue-pass-hint">Kosongkan jika tidak ingin mengubah password.</small>
                                </div>
                                @error('password')
                                <span class="ue-error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Section 03: Profil --}}
                        <div class="ue-section-label">
                            <span class="ue-section-num">03</span>
                            <span>Data Profil</span>
                        </div>

                        <div class="ue-grid-3">
                            <div class="ue-form-group">
                                <label class="ue-label" for="jabatan">
                                    <i class="fas fa-briefcase ue-label-icon"></i>
                                    Jabatan
                                </label>
                                <input
                                    type="text" id="jabatan" name="jabatan"
                                    class="ue-input"
                                    value="{{ old('jabatan', $user->profile?->jabatan) }}"
                                    placeholder="Contoh: Direktur"
                                    oninput="updatePreview()" />
                            </div>

                            <div class="ue-form-group">
                                <label class="ue-label" for="jurusan_id">
                                    <i class="fas fa-graduation-cap ue-label-icon"></i>
                                    Nama Jurusan
                                </label>
                                <select id="jurusan_id" name="jurusan_id" class="ue-input">
                                    <option value="">-- Pilih Jurusan --</option>
                                    @foreach($jurusans as $jurusan)
                                        <option value="{{ $jurusan->id }}" {{ old('jurusan_id', $user->profile?->jurusan_id) == $jurusan->id ? 'selected' : '' }}>
                                            {{ $jurusan->nama_jurusan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="ue-form-group">
                                <label class="ue-label" for="unit_kerja_id">
                                    <i class="fas fa-building ue-label-icon"></i>
                                    Nama Unit
                                </label>
                                <select id="unit_kerja_id" name="unit_kerja_id" class="ue-input">
                                    <option value="">-- Pilih Unit Kerja --</option>
                                    @foreach($unitKerjas as $unit)
                                        <option value="{{ $unit->id }}" {{ old('unit_kerja_id', $user->profile?->unit_kerja_id) == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->nama_unit_pelaksana }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="card-footer ue-footer">
                        <a href="{{ route('users.index') }}" class="ue-btn-cancel">
                            <i class="fas fa-arrow-left"></i> Batal
                        </a>
                        <button type="reset" class="ue-btn-reset" onclick="restorePreview()">
                            <i class="fas fa-rotate-left"></i> Kembalikan
                        </button>
                        <button type="submit" class="ue-btn-submit">
                            <i class="fas fa-floppy-disk"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</main>
@endsection