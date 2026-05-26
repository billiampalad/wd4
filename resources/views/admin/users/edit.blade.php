@extends('admin.dashboard')

@section('content')
@php
    $roleLabels = [
        'pimpinan' => 'Pimpinan',
        'jurusan' => 'Jurusan',
        'unit_kerja' => 'Humas',
        'upa' => 'Upa',
        'pusat' => 'Pusat',
        'admin' => 'Admin',
    ];
@endphp
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
                        {{ $roleLabels[$user->role?->role_name] ?? $user->role?->role_name ?? 'Tidak Ada Role' }}
                    </div>
                    <div class="ue-profile-since">
                        <i class="fas fa-clock"></i>
                        Dibuat {{ $user->created_at?->diffForHumans() ?? '-' }}
                    </div>
                </div>

                <div class="ue-profile-divider"></div>

                <div class="ue-profile-info">
                    <div class="ue-info-row" data-preview-field="jabatan">
                        <span class="ue-info-icon" style="background:rgba(79,70,229,.1);color:#4f46e5;">
                            <i class="fas fa-briefcase"></i>
                        </span>
                        <div>
                            <div class="ue-info-label">Jabatan</div>
                            <div class="ue-info-val" id="previewJabatan">{{ $user->profile?->jabatan ?: '—' }}</div>
                        </div>
                    </div>
                    <div class="ue-info-row" data-preview-field="jurusan">
                        <span class="ue-info-icon" style="background:rgba(14,165,233,.1);color:#0ea5e9;">
                            <i class="fas fa-graduation-cap"></i>
                        </span>
                        <div>
                            <div class="ue-info-label">Jurusan</div>
                            <div class="ue-info-val" id="previewJurusan">{{ $user->profile?->jurusan?->nama_jurusan ?: '—' }}</div>
                        </div>
                    </div>
                    <div class="ue-info-row" data-preview-field="unit">
                        <span class="ue-info-icon" style="background:rgba(16,185,129,.1);color:#10b981;">
                            <i class="fas fa-building"></i>
                        </span>
                        <div>
                            <div class="ue-info-label">Unit Kerja</div>
                            <div class="ue-info-val" id="previewUnit">{{ $user->profile?->unitKerja?->nama_unit_pelaksana ?: '—' }}</div>
                        </div>
                    </div>
                    <div class="ue-info-row" data-preview-field="upa">
                        <span class="ue-info-icon" style="background:rgba(6,182,212,.1);color:#0891b2;">
                            <i class="fas fa-building-columns"></i>
                        </span>
                        <div>
                            <div class="ue-info-label">UPA</div>
                            <div class="ue-info-val" id="previewUpa">{{ $user->profile?->upa?->nama_upa ?: '—' }}</div>
                        </div>
                    </div>
                    <div class="ue-info-row" data-preview-field="pusat">
                        <span class="ue-info-icon" style="background:rgba(168,85,247,.1);color:#9333ea;">
                            <i class="fas fa-landmark"></i>
                        </span>
                        <div>
                            <div class="ue-info-label">Pusat</div>
                            <div class="ue-info-val" id="previewPusat">{{ $user->profile?->pusat?->nama_pusat ?: '—' }}</div>
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
                                <div
                                    class="uc-alpine-select"
                                    x-data="adminUserSelect({
                                        placeholder: '-- Pilih Role --',
                                        selectedValue: @js((string) old('role_id', $user->role_id)),
                                        items: @js($roles->map(fn ($role) => [
                                            'value' => (string) $role->id,
                                            'label' => $roleLabels[$role->role_name] ?? $role->role_name,
                                            'meta' => $role->role_name,
                                        ])->values())
                                    })"
                                    x-init="init()"
                                    :class="{ 'is-open': open }"
                                    @click.outside="open = false"
                                >
                                    <select
                                        id="role_id" name="role_id"
                                        class="uc-native-select @error('role_id') ue-input-error @enderror"
                                        x-model="selectedValue"
                                        @change="syncFromNative(); updateProfileFields(); updatePreview()"
                                        required
                                        tabindex="-1"
                                        aria-hidden="true"
                                    >
                                        @foreach($roles as $role)
                                        <option
                                            value="{{ $role->id }}"
                                            data-role-name="{{ $role->role_name }}"
                                            {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}
                                        >
                                            {{ $roleLabels[$role->role_name] ?? $role->role_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <button
                                        type="button"
                                        class="uc-select-trigger @error('role_id') uc-select-trigger-error @enderror"
                                        :class="{ 'is-open': open, 'is-empty': !selectedValue, 'is-disabled': disabled }"
                                        @click="toggle()"
                                        :disabled="disabled"
                                    >
                                        <span class="uc-select-text" x-text="selectedLabel || placeholder"></span>
                                        <i class="fas fa-chevron-down uc-select-chevron"></i>
                                    </button>
                                    <div class="uc-select-menu" x-show="open" x-transition x-cloak>
                                        <template x-for="item in items" :key="item.value">
                                            <button
                                                type="button"
                                                class="uc-select-option"
                                                :class="{ 'is-selected': selectedValue === item.value }"
                                                @click="choose(item)"
                                            >
                                                <span x-text="item.label"></span>
                                                <i class="fas fa-check" x-show="selectedValue === item.value"></i>
                                            </button>
                                        </template>
                                    </div>
                                </div>
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
                        <div class="uc-profile-pointer" id="profileRolePointer" aria-live="polite">
                            Pilih role terlebih dahulu untuk melihat form profil yang dapat digunakan.
                        </div>

                        <div class="ue-grid-3" id="profileFields">
                            <div class="ue-form-group" data-profile-field="jabatan">
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

                            <div class="ue-form-group" data-profile-field="jurusan">
                                <label class="ue-label" for="jurusan_id">
                                    <i class="fas fa-graduation-cap ue-label-icon"></i>
                                    Nama Jurusan
                                </label>
                                <div
                                    class="uc-alpine-select"
                                    x-data="adminUserSelect({
                                        placeholder: '-- Pilih Jurusan --',
                                        selectedValue: @js((string) old('jurusan_id', $user->profile?->jurusan_id)),
                                        items: @js($jurusans->map(fn ($jurusan) => [
                                            'value' => (string) $jurusan->id,
                                            'label' => $jurusan->nama_jurusan,
                                        ])->values())
                                    })"
                                    x-init="init()"
                                    :class="{ 'is-open': open }"
                                    @click.outside="open = false"
                                >
                                    <select
                                        id="jurusan_id" name="jurusan_id"
                                        class="uc-native-select"
                                        x-model="selectedValue"
                                        @change="syncFromNative(); updatePreview()"
                                        tabindex="-1"
                                        aria-hidden="true"
                                    >
                                        <option value="">-- Pilih Jurusan --</option>
                                        @foreach($jurusans as $jurusan)
                                            <option value="{{ $jurusan->id }}" {{ old('jurusan_id', $user->profile?->jurusan_id) == $jurusan->id ? 'selected' : '' }}>
                                                {{ $jurusan->nama_jurusan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button
                                        type="button"
                                        class="uc-select-trigger"
                                        :class="{ 'is-open': open, 'is-empty': !selectedValue, 'is-disabled': disabled }"
                                        @click="toggle()"
                                        :disabled="disabled"
                                    >
                                        <span class="uc-select-text" x-text="selectedLabel || placeholder"></span>
                                        <i class="fas fa-chevron-down uc-select-chevron"></i>
                                    </button>
                                    <div class="uc-select-menu" x-show="open" x-transition x-cloak>
                                        <template x-for="item in items" :key="item.value">
                                            <button
                                                type="button"
                                                class="uc-select-option"
                                                :class="{ 'is-selected': selectedValue === item.value }"
                                                @click="choose(item)"
                                            >
                                                <span x-text="item.label"></span>
                                                <i class="fas fa-check" x-show="selectedValue === item.value"></i>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="ue-form-group" data-profile-field="unit">
                                <label class="ue-label" for="unit_kerja_id">
                                    <i class="fas fa-building ue-label-icon"></i>
                                    Nama Unit
                                </label>
                                <div
                                    class="uc-alpine-select"
                                    x-data="adminUserSelect({
                                        placeholder: '-- Pilih Unit Kerja --',
                                        selectedValue: @js((string) old('unit_kerja_id', $user->profile?->unit_kerja_id)),
                                        items: @js($unitKerjas->map(fn ($unit) => [
                                            'value' => (string) $unit->id,
                                            'label' => $unit->nama_unit_pelaksana,
                                        ])->values())
                                    })"
                                    x-init="init()"
                                    :class="{ 'is-open': open }"
                                    @click.outside="open = false"
                                >
                                    <select
                                        id="unit_kerja_id" name="unit_kerja_id"
                                        class="uc-native-select"
                                        x-model="selectedValue"
                                        @change="syncFromNative(); updatePreview()"
                                        tabindex="-1"
                                        aria-hidden="true"
                                    >
                                        <option value="">-- Pilih Unit Kerja --</option>
                                        @foreach($unitKerjas as $unit)
                                            <option value="{{ $unit->id }}" {{ old('unit_kerja_id', $user->profile?->unit_kerja_id) == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->nama_unit_pelaksana }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button
                                        type="button"
                                        class="uc-select-trigger"
                                        :class="{ 'is-open': open, 'is-empty': !selectedValue, 'is-disabled': disabled }"
                                        @click="toggle()"
                                        :disabled="disabled"
                                    >
                                        <span class="uc-select-text" x-text="selectedLabel || placeholder"></span>
                                        <i class="fas fa-chevron-down uc-select-chevron"></i>
                                    </button>
                                    <div class="uc-select-menu" x-show="open" x-transition x-cloak>
                                        <template x-for="item in items" :key="item.value">
                                            <button
                                                type="button"
                                                class="uc-select-option"
                                                :class="{ 'is-selected': selectedValue === item.value }"
                                                @click="choose(item)"
                                            >
                                                <span x-text="item.label"></span>
                                                <i class="fas fa-check" x-show="selectedValue === item.value"></i>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="ue-form-group" data-profile-field="upa">
                                <label class="ue-label" for="upa_id">
                                    <i class="fas fa-building-columns ue-label-icon"></i>
                                    Nama Upa
                                </label>
                                <div
                                    class="uc-alpine-select"
                                    x-data="adminUserSelect({
                                        placeholder: '-- Pilih Upa --',
                                        selectedValue: @js((string) old('upa_id', $user->profile?->upa_id)),
                                        items: @js($upas->map(fn ($upa) => [
                                            'value' => (string) $upa->id,
                                            'label' => $upa->nama_upa,
                                        ])->values())
                                    })"
                                    x-init="init()"
                                    :class="{ 'is-open': open }"
                                    @click.outside="open = false"
                                >
                                    <select
                                        id="upa_id" name="upa_id"
                                        class="uc-native-select"
                                        x-model="selectedValue"
                                        @change="syncFromNative(); updatePreview()"
                                        tabindex="-1"
                                        aria-hidden="true"
                                    >
                                        <option value="">-- Pilih Upa --</option>
                                        @foreach($upas as $upa)
                                            <option value="{{ $upa->id }}" {{ old('upa_id', $user->profile?->upa_id) == $upa->id ? 'selected' : '' }}>
                                                {{ $upa->nama_upa }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button
                                        type="button"
                                        class="uc-select-trigger"
                                        :class="{ 'is-open': open, 'is-empty': !selectedValue, 'is-disabled': disabled }"
                                        @click="toggle()"
                                        :disabled="disabled"
                                    >
                                        <span class="uc-select-text" x-text="selectedLabel || placeholder"></span>
                                        <i class="fas fa-chevron-down uc-select-chevron"></i>
                                    </button>
                                    <div class="uc-select-menu" x-show="open" x-transition x-cloak>
                                        <template x-for="item in items" :key="item.value">
                                            <button
                                                type="button"
                                                class="uc-select-option"
                                                :class="{ 'is-selected': selectedValue === item.value }"
                                                @click="choose(item)"
                                            >
                                                <span x-text="item.label"></span>
                                                <i class="fas fa-check" x-show="selectedValue === item.value"></i>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="ue-form-group" data-profile-field="pusat">
                                <label class="ue-label" for="pusat_id">
                                    <i class="fas fa-landmark ue-label-icon"></i>
                                    Nama Pusat
                                </label>
                                <div
                                    class="uc-alpine-select"
                                    x-data="adminUserSelect({
                                        placeholder: '-- Pilih Pusat --',
                                        selectedValue: @js((string) old('pusat_id', $user->profile?->pusat_id)),
                                        items: @js($pusats->map(fn ($pusat) => [
                                            'value' => (string) $pusat->id,
                                            'label' => $pusat->nama_pusat,
                                        ])->values())
                                    })"
                                    x-init="init()"
                                    :class="{ 'is-open': open }"
                                    @click.outside="open = false"
                                >
                                    <select
                                        id="pusat_id" name="pusat_id"
                                        class="uc-native-select"
                                        x-model="selectedValue"
                                        @change="syncFromNative(); updatePreview()"
                                        tabindex="-1"
                                        aria-hidden="true"
                                    >
                                        <option value="">-- Pilih Pusat --</option>
                                        @foreach($pusats as $pusat)
                                            <option value="{{ $pusat->id }}" {{ old('pusat_id', $user->profile?->pusat_id) == $pusat->id ? 'selected' : '' }}>
                                                {{ $pusat->nama_pusat }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button
                                        type="button"
                                        class="uc-select-trigger"
                                        :class="{ 'is-open': open, 'is-empty': !selectedValue, 'is-disabled': disabled }"
                                        @click="toggle()"
                                        :disabled="disabled"
                                    >
                                        <span class="uc-select-text" x-text="selectedLabel || placeholder"></span>
                                        <i class="fas fa-chevron-down uc-select-chevron"></i>
                                    </button>
                                    <div class="uc-select-menu" x-show="open" x-transition x-cloak>
                                        <template x-for="item in items" :key="item.value">
                                            <button
                                                type="button"
                                                class="uc-select-option"
                                                :class="{ 'is-selected': selectedValue === item.value }"
                                                @click="choose(item)"
                                            >
                                                <span x-text="item.label"></span>
                                                <i class="fas fa-check" x-show="selectedValue === item.value"></i>
                                            </button>
                                        </template>
                                    </div>
                                </div>
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
