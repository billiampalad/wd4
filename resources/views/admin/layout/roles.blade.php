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
    <div id="createRoleModal" class="adm-modal-overlay" style="display: none;" onclick="AdminModal.handleOverlayClick(event, 'createRoleModal')">
        <div class="adm-modal-container adm-modal-md">
            <div class="adm-modal-header">
                <div class="adm-modal-title-wrap">
                    <div class="adm-modal-icon-badge adm-modal-icon-indigo">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h3 class="adm-modal-title">Tambah Role Baru</h3>
                        <p class="adm-modal-subtitle">Tambahkan role pengguna baru ke dalam sistem.</p>
                    </div>
                </div>
                <button type="button" class="adm-modal-close" onclick="AdminModal.close('createRoleModal')" title="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('roles.store') }}" method="POST" id="createRoleForm">
                @csrf
                <div class="adm-modal-body">
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="create_role_name">
                            <i class="fas fa-shield-alt"></i> Nama Role <span class="adm-required">*</span>
                        </label>
                        <input type="text" id="create_role_name" name="role_name" class="adm-form-input"
                            placeholder="Contoh: Admin, Pimpinan, dsb" required>
                    </div>
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="create_description">
                            <i class="fas fa-align-left"></i> Deskripsi
                        </label>
                        <textarea id="create_description" name="description" class="adm-form-input adm-form-textarea"
                            placeholder="Penjelasan wewenang atau hak akses role (opsional)" rows="3"></textarea>
                    </div>
                </div>
                <div class="adm-modal-footer">
                    <button type="button" class="adm-btn-cancel" onclick="AdminModal.close('createRoleModal')">
                        <i class="fas fa-arrow-left"></i> Batal
                    </button>
                    <button type="submit" class="adm-btn-submit">
                        <i class="fas fa-floppy-disk"></i> Simpan Role
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Role --}}
    <div id="editRoleModal" class="adm-modal-overlay" style="display: none;" onclick="AdminModal.handleOverlayClick(event, 'editRoleModal')">
        <div class="adm-modal-container adm-modal-md">
            <div class="adm-modal-header">
                <div class="adm-modal-title-wrap">
                    <div class="adm-modal-icon-badge adm-modal-icon-amber">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h3 class="adm-modal-title">Edit Role</h3>
                        <p class="adm-modal-subtitle">Ubah informasi role yang sudah terdaftar.</p>
                    </div>
                </div>
                <button type="button" class="adm-modal-close" onclick="AdminModal.close('editRoleModal')" title="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="" method="POST" id="editRoleForm">
                @csrf
                @method('PUT')
                <div class="adm-modal-body">
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="edit_role_name">
                            <i class="fas fa-shield-alt"></i> Nama Role <span class="adm-required">*</span>
                        </label>
                        <input type="text" id="edit_role_name" name="role_name" class="adm-form-input"
                            placeholder="Ubah nama role" required>
                    </div>
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="edit_description">
                            <i class="fas fa-align-left"></i> Deskripsi
                        </label>
                        <textarea id="edit_description" name="description" class="adm-form-input adm-form-textarea"
                            placeholder="Penjelasan wewenang atau hak akses role (opsional)" rows="3"></textarea>
                    </div>
                </div>
                <div class="adm-modal-footer">
                    <button type="button" class="adm-btn-cancel" onclick="AdminModal.close('editRoleModal')">
                        <i class="fas fa-arrow-left"></i> Batal
                    </button>
                    <button type="submit" class="adm-btn-submit">
                        <i class="fas fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateRoleModal() {
            AdminModal.open('createRoleModal', {
                focusSelector: '#create_role_name',
                resetForm: true
            });
        }

        function openEditRoleModal(id, roleName, description) {
            const form = document.getElementById('editRoleForm');
            if (form) {
                form.action = "{{ route('roles.update', ':id') }}".replace(':id', id);
            }
            const nameInput = document.getElementById('edit_role_name');
            const descInput = document.getElementById('edit_description');
            if (nameInput) nameInput.value = roleName;
            if (descInput) descInput.value = description;

            AdminModal.open('editRoleModal', {
                focusSelector: '#edit_role_name'
            });
        }
    </script>
@endsection