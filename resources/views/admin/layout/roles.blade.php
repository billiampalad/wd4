@extends('admin.dashboard')

@section('content')
    <main class="main-content admin-dashboard">
        <section class="ud-topbar">
            <div class="ud-hero-copy">
                <div class="ud-breadcrumb">
                    <i class="fas fa-home"></i>
                    <span>/</span>
                    <a href="{{ route('admin.dashboard') }}" class="ud-breadcrumb-link">Beranda</a>
                    <span>/</span>
                    <span>Roles</span>
                </div>
                <div class="ud-title-row">
                    <span class="ud-title-icon"><i class="fas fa-shield-halved"></i></span>
                    <div class="ud-title-copy">
                        <h2 class="ud-title" id="pageTitle">Role Management</h2>
                        <p class="ud-subtitle" id="pageDesc">
                            Tambah, edit, dan hapus data role pengguna.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <div class="card um-card">
            <div class="card-header um-header">
                <div class="um-header-left" style="display: flex; align-items: center; gap: 18px; flex-wrap: wrap;">
                    <div class="card-title"><i class="fas fa-shield-alt"></i> Daftar Role</div>
                    <x-paginav-entries target=".um-table tbody tr.um-row" :perPage="10" :options="[5, 10, 25, 50]" />
                </div>
                <div class="um-header-actions">
                    <x-search id="roleSearchInput" placeholder="Cari data role..." target=".um-table tbody tr.um-row"
                        emptyTarget="#roleSearchEmptyRow" querySpan="#roleSearchQueryText" />
                    <button type="button" class="um-btn-add" onclick="openCreateRoleModal()">
                        <i class="fas fa-plus"></i> Tambah Role
                    </button>
                </div>
            </div>
            <div class="table-wrap um-table-wrap">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num">#</th>
                            <th class="um-th">Nama Role</th>
                            <th class="um-th">Deskripsi</th>
                            <th class="um-th">Dibuat</th>
                            <th class="um-th">Diperbarui</th>
                            <th class="um-th um-th-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $i => $role)
                            <tr class="um-row">
                                <td class="um-td um-td-num">
                                    <span class="um-num">{{ $i + 1 }}</span>
                                </td>
                                <td class="um-td">
                                    <span class="um-name">{{ $role->role_name ?? '-' }}</span>
                                </td>
                                <td class="um-td">
                                    <span class="um-meta">{{ $role->description ?? '-' }}</span>
                                </td>
                                <td class="um-td">
                                    <div class="um-date">
                                        <i class="fas fa-calendar-plus um-date-icon"></i>
                                        {{ $role->created_at?->format('d-m-Y H:i') ?? '-' }}
                                    </div>
                                </td>
                                <td class="um-td">
                                    <div class="um-date">
                                        <i class="fas fa-calendar-check um-date-icon"></i>
                                        {{ $role->updated_at?->format('d-m-Y H:i') ?? '-' }}
                                    </div>
                                </td>
                                <td class="um-td um-td-aksi">
                                    <div class="actions um-actions">
                                        <button type="button" class="btn-action edit um-btn-edit" title="Edit"
                                            onclick="openEditRoleModal({{ $role->id }}, '{{ addslashes($role->role_name) }}', '{{ addslashes($role->description ?? '') }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form id="form-delete-role-{{ $role->id }}"
                                             action="{{ route('roles.destroy', $role->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn-action delete um-btn-delete" title="Hapus" onclick="CustomAlert.confirm({
                                                title: 'Hapus Role',
                                                message: 'Menghapus role pengguna <span class=\'custom-alert-highlight\'>{{ addslashes($role->role_name) }}</span> tidak dapat dikembalikan.',
                                                type: 'danger',
                                                confirmText: 'Hapus',
                                                confirmColor: 'danger',
                                                onConfirm: () => document.getElementById('form-delete-role-{{ $role->id }}').submit()
                                            })">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="um-empty">
                                    <div class="um-empty-state">
                                        <div class="um-empty-icon">
                                            <i class="fas fa-shield-alt"></i>
                                        </div>
                                        <p class="um-empty-title">Belum ada data role</p>
                                        <p class="um-empty-sub">Klik tombol <strong>Tambah Role</strong> untuk memulai.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        <tr id="roleSearchEmptyRow" style="display: none;">
                            <td colspan="6" class="um-empty">
                                <div class="um-empty-state">
                                    <div class="um-empty-icon" style="color: var(--accent, #4f46e5); opacity: 0.7;">
                                        <i class="fas fa-search-minus"></i>
                                    </div>
                                    <p class="um-empty-title">Data Tidak Ditemukan</p>
                                    <p class="um-empty-sub">Tidak ada role yang cocok dengan kata kunci "<span
                                            id="roleSearchQueryText"
                                            style="font-weight: 600; color: var(--accent, #4f46e5);"></span>".</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <x-paginav id="roleTablePaginav" target=".um-table tbody tr.um-row" :perPage="10" :showInfo="true"
                :showPerPage="false" />
        </div>
    </main>

    {{-- Modal Tambah Role --}}
    <div id="createRoleModal" class="role-modal-overlay" style="display: none;" onclick="handleModalOverlayClick(event, 'createRoleModal')">
        <div class="role-modal-container">
            <div class="role-modal-header">
                <div class="role-modal-title-wrap">
                    <div class="role-modal-icon-badge role-modal-icon-add">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h3 class="role-modal-title">Tambah Role Baru</h3>
                        <p class="role-modal-subtitle">Tambahkan role pengguna baru ke dalam sistem.</p>
                    </div>
                </div>
                <button type="button" class="role-modal-close" onclick="closeRoleModal('createRoleModal')" title="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('roles.store') }}" method="POST" id="createRoleForm">
                @csrf
                <div class="role-modal-body">
                    <div class="role-form-group">
                        <label class="role-form-label" for="create_role_name">
                            <i class="fas fa-shield-alt"></i> Nama Role <span class="role-required">*</span>
                        </label>
                        <input type="text" id="create_role_name" name="role_name" class="role-form-input"
                            placeholder="Contoh: Admin, Pimpinan, dsb" required>
                    </div>
                    <div class="role-form-group">
                        <label class="role-form-label" for="create_description">
                            <i class="fas fa-align-left"></i> Deskripsi
                        </label>
                        <textarea id="create_description" name="description" class="role-form-input role-form-textarea"
                            placeholder="Penjelasan wewenang atau hak akses role (opsional)" rows="3"></textarea>
                    </div>
                </div>
                <div class="role-modal-footer">
                    <button type="button" class="role-btn-cancel" onclick="closeRoleModal('createRoleModal')">
                        <i class="fas fa-arrow-left"></i> Batal
                    </button>
                    <button type="submit" class="role-btn-submit">
                        <i class="fas fa-floppy-disk"></i> Simpan Role
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Role --}}
    <div id="editRoleModal" class="role-modal-overlay" style="display: none;" onclick="handleModalOverlayClick(event, 'editRoleModal')">
        <div class="role-modal-container">
            <div class="role-modal-header">
                <div class="role-modal-title-wrap">
                    <div class="role-modal-icon-badge role-modal-icon-edit">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h3 class="role-modal-title">Edit Role</h3>
                        <p class="role-modal-subtitle">Ubah informasi role yang sudah terdaftar.</p>
                    </div>
                </div>
                <button type="button" class="role-modal-close" onclick="closeRoleModal('editRoleModal')" title="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="" method="POST" id="editRoleForm">
                @csrf
                @method('PUT')
                <div class="role-modal-body">
                    <div class="role-form-group">
                        <label class="role-form-label" for="edit_role_name">
                            <i class="fas fa-shield-alt"></i> Nama Role <span class="role-required">*</span>
                        </label>
                        <input type="text" id="edit_role_name" name="role_name" class="role-form-input"
                            placeholder="Ubah nama role" required>
                    </div>
                    <div class="role-form-group">
                        <label class="role-form-label" for="edit_description">
                            <i class="fas fa-align-left"></i> Deskripsi
                        </label>
                        <textarea id="edit_description" name="description" class="role-form-input role-form-textarea"
                            placeholder="Penjelasan wewenang atau hak akses role (opsional)" rows="3"></textarea>
                    </div>
                </div>
                <div class="role-modal-footer">
                    <button type="button" class="role-btn-cancel" onclick="closeRoleModal('editRoleModal')">
                        <i class="fas fa-arrow-left"></i> Batal
                    </button>
                    <button type="submit" class="role-btn-submit">
                        <i class="fas fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* ── Role Modal Overlay & Container ── */
        .role-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .role-modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .role-modal-container {
            background: var(--card-bg, #ffffff);
            border: 1px solid var(--border, #e2e8f0);
            border-radius: 16px;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transform: scale(0.95) translateY(10px);
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .role-modal-overlay.active .role-modal-container {
            transform: scale(1) translateY(0);
        }

        /* ── Modal Header ── */
        .role-modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 22px 24px 18px;
            border-bottom: 1px solid var(--border, #e2e8f0);
            background: var(--bg-hover, #f8fafc);
        }

        .role-modal-title-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .role-modal-icon-badge {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .role-modal-icon-add {
            background: rgba(99, 102, 241, 0.12);
            color: #6366f1;
            border: 1px solid rgba(99, 102, 241, 0.25);
        }

        .role-modal-icon-edit {
            background: rgba(245, 158, 11, 0.12);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.25);
        }

        .role-modal-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-main, #0f172a);
            margin: 0 0 2px 0;
        }

        .role-modal-subtitle {
            font-size: 0.85rem;
            color: var(--text-sub, #64748b);
            margin: 0;
        }

        .role-modal-close {
            background: transparent;
            border: none;
            color: var(--text-sub, #94a3b8);
            font-size: 1.15rem;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .role-modal-close:hover {
            background: rgba(0, 0, 0, 0.06);
            color: #ef4444;
            transform: rotate(90deg);
        }

        /* ── Modal Body & Form ── */
        .role-modal-body {
            padding: 22px 24px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .role-form-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .role-form-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-main, #1e293b);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .role-form-label i {
            color: var(--accent, #6366f1);
            font-size: 0.85rem;
        }

        .role-required {
            color: #ef4444;
            font-weight: 700;
        }

        .role-form-input {
            width: 100%;
            padding: 10px 14px;
            font-size: 0.92rem;
            border: 1.5px solid var(--border, #cbd5e1);
            border-radius: 10px;
            background: var(--input-bg, #ffffff);
            color: var(--text-main, #0f172a);
            font-family: inherit;
            outline: none;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .role-form-input:focus {
            border-color: var(--accent, #6366f1);
            box-shadow: 0 0 0 3.5px rgba(99, 102, 241, 0.18);
        }

        .role-form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        /* ── Modal Footer ── */
        .role-modal-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            padding: 16px 24px;
            border-top: 1px solid var(--border, #e2e8f0);
            background: var(--bg-hover, #f8fafc);
        }

        .role-btn-cancel {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-sub, #64748b);
            background: transparent;
            border: 1.5px solid var(--border, #cbd5e1);
            border-radius: 9px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .role-btn-cancel:hover {
            background: rgba(0, 0, 0, 0.04);
            color: var(--text-main, #0f172a);
            border-color: #94a3b8;
        }

        .role-btn-submit {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 20px;
            font-size: 0.875rem;
            font-weight: 600;
            color: #ffffff;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border: none;
            border-radius: 9px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.28);
            transition: all 0.2s ease;
        }

        .role-btn-submit:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.38);
        }

        .role-btn-submit:active {
            transform: translateY(0);
        }

        /* ── Dark Mode Tweaks ── */
        [data-theme="dark"] .role-modal-container {
            background: #1e293b;
            border-color: #334155;
        }

        [data-theme="dark"] .role-modal-header,
        [data-theme="dark"] .role-modal-footer {
            background: #0f172a;
            border-color: #334155;
        }

        [data-theme="dark"] .role-modal-title {
            color: #f8fafc;
        }

        [data-theme="dark"] .role-modal-subtitle {
            color: #94a3b8;
        }

        [data-theme="dark"] .role-form-label {
            color: #e2e8f0;
        }

        [data-theme="dark"] .role-form-input {
            background: #0f172a;
            border-color: #334155;
            color: #f8fafc;
        }

        [data-theme="dark"] .role-form-input:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 3.5px rgba(129, 140, 248, 0.22);
        }

        [data-theme="dark"] .role-btn-cancel {
            color: #94a3b8;
            border-color: #334155;
        }

        [data-theme="dark"] .role-btn-cancel:hover {
            background: #334155;
            color: #f8fafc;
        }

        [data-theme="dark"] .role-modal-close:hover {
            background: rgba(255, 255, 255, 0.08);
        }
    </style>

    <script>
        function openCreateRoleModal() {
            const modal = document.getElementById('createRoleModal');
            const form = document.getElementById('createRoleForm');
            if (form) form.reset();
            if (modal) {
                modal.style.display = 'flex';
                setTimeout(() => {
                    modal.classList.add('active');
                    document.getElementById('create_role_name')?.focus();
                }, 10);
                document.body.style.overflow = 'hidden';
            }
        }

        function openEditRoleModal(id, roleName, description) {
            const modal = document.getElementById('editRoleModal');
            const form = document.getElementById('editRoleForm');
            if (form) {
                form.action = "{{ route('roles.update', ':id') }}".replace(':id', id);
            }
            const nameInput = document.getElementById('edit_role_name');
            const descInput = document.getElementById('edit_description');
            if (nameInput) nameInput.value = roleName;
            if (descInput) descInput.value = description;

            if (modal) {
                modal.style.display = 'flex';
                setTimeout(() => {
                    modal.classList.add('active');
                    nameInput?.focus();
                }, 10);
                document.body.style.overflow = 'hidden';
            }
        }

        function closeRoleModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('active');
                setTimeout(() => {
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                }, 250);
            }
        }

        function handleModalOverlayClick(event, modalId) {
            if (event.target === document.getElementById(modalId)) {
                closeRoleModal(modalId);
            }
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeRoleModal('createRoleModal');
                closeRoleModal('editRoleModal');
            }
        });
    </script>
@endsection