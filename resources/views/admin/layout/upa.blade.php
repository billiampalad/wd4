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
                <span>UPA</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-building-columns"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">UPA</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Tambah, edit, dan hapus data UPA.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="card um-card">
        <div class="card-header um-header">
            <div class="um-header-left" style="display: flex; align-items: center; gap: 18px; flex-wrap: wrap;">
                <div class="card-title"><i class="fas fa-building-columns"></i> Daftar UPA</div>
                <x-paginav-entries target=".um-table tbody tr.um-row" :perPage="10" :options="[5, 10, 25, 50]" />
            </div>
            <div class="um-header-actions">
                <button type="button" class="um-btn-add" onclick="openCreateUpaModal()">
                    <i class="fas fa-plus"></i> Tambah UPA
                </button>
            </div>
        </div>

        <div class="table-wrap um-table-wrap">
            <table class="um-table">
                <thead>
                    <tr>
                        <th class="um-th um-th-num">#</th>
                        <th class="um-th">Nama UPA</th>
                        <th class="um-th">Dibuat</th>
                        <th class="um-th">Diperbarui</th>
                        <th class="um-th um-th-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($upas as $i => $upa)
                        <tr class="um-row">
                            <td class="um-td um-td-num">
                                <span class="um-num">{{ $i + 1 }}</span>
                            </td>
                            <td class="um-td">
                                <span class="um-name">{{ $upa->nama_upa ?? '-' }}</span>
                            </td>
                            <td class="um-td">
                                <div class="um-date">
                                    <i class="fas fa-calendar-plus um-date-icon"></i>
                                    {{ $upa->created_at?->format('d-m-Y H:i') ?? '-' }}
                                </div>
                            </td>
                            <td class="um-td">
                                <div class="um-date">
                                    <i class="fas fa-calendar-check um-date-icon"></i>
                                    {{ $upa->updated_at?->format('d-m-Y H:i') ?? '-' }}
                                </div>
                            </td>
                            <td class="um-td um-td-aksi">
                                <div class="actions um-actions">
                                    <button type="button" class="btn-action edit um-btn-edit" title="Edit"
                                        onclick="openEditUpaModal({{ $upa->id }}, '{{ addslashes($upa->nama_upa) }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form id="form-delete-upa-{{ $upa->id }}" action="{{ route('upa.destroy', $upa->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-action delete um-btn-delete" title="Hapus" onclick="CustomAlert.confirm({
                                            title: 'Hapus UPA',
                                            message: 'Menghapus UPA <span class=\'custom-alert-highlight\'>{{ addslashes($upa->nama_upa) }}</span> tidak dapat dikembalikan.',
                                            type: 'danger',
                                            confirmText: 'Hapus',
                                            confirmColor: 'danger',
                                            onConfirm: () => document.getElementById('form-delete-upa-{{ $upa->id }}').submit()
                                        })">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="um-empty">
                                <div class="um-empty-state">
                                    <div class="um-empty-icon">
                                        <i class="fas fa-building-columns"></i>
                                    </div>
                                    <p class="um-empty-title">Belum ada data UPA</p>
                                    <p class="um-empty-sub">Klik tombol <strong>Tambah UPA</strong> untuk memulai.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    <tr id="upaSearchEmptyRow" style="display: none;">
                        <td colspan="5" class="um-empty">
                            <div class="um-empty-state">
                                <div class="um-empty-icon" style="color: var(--accent, #4f46e5); opacity: 0.7;">
                                    <i class="fas fa-search-minus"></i>
                                </div>
                                <p class="um-empty-title">Data Tidak Ditemukan</p>
                                <p class="um-empty-sub">Tidak ada UPA yang cocok dengan kata kunci "<span
                                        id="upaSearchQueryText"
                                        style="font-weight: 600; color: var(--accent, #4f46e5);"></span>".</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <x-paginav id="upaTablePaginav" target=".um-table tbody tr.um-row" :perPage="10" :showInfo="true"
            :showPerPage="false" />
    </div>

    {{-- Modal Tambah UPA --}}
    <div id="createUpaModal" class="adm-modal-overlay" style="display: none;" onclick="AdminModal.handleOverlayClick(event, 'createUpaModal')">
        <div class="adm-modal-container adm-modal-sm">
            <div class="adm-modal-header">
                <div class="adm-modal-title-wrap">
                    <div class="adm-modal-icon-badge adm-modal-icon-indigo">
                        <i class="fas fa-building-columns"></i>
                    </div>
                    <div>
                        <h3 class="adm-modal-title">Tambah UPA</h3>
                        <p class="adm-modal-subtitle">Isi formulir untuk menambahkan data UPA baru.</p>
                    </div>
                </div>
                <button type="button" class="adm-modal-close" onclick="AdminModal.close('createUpaModal')" title="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('upa.store') }}" method="POST" id="createUpaForm">
                @csrf
                <div class="adm-modal-body">
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="create_nama_upa">
                            <i class="fas fa-building-columns"></i> Nama UPA <span class="adm-required">*</span>
                        </label>
                        <input type="text" id="create_nama_upa" name="nama_upa" class="adm-form-input"
                            placeholder="Contoh: UPA Perpustakaan" required maxlength="150">
                    </div>
                </div>
                <div class="adm-modal-footer">
                    <button type="button" class="adm-btn-cancel" onclick="AdminModal.close('createUpaModal')">
                        <i class="fas fa-arrow-left"></i> Batal
                    </button>
                    <button type="submit" class="adm-btn-submit">
                        <i class="fas fa-floppy-disk"></i> Simpan UPA
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit UPA --}}
    <div id="editUpaModal" class="adm-modal-overlay" style="display: none;" onclick="AdminModal.handleOverlayClick(event, 'editUpaModal')">
        <div class="adm-modal-container adm-modal-sm">
            <div class="adm-modal-header">
                <div class="adm-modal-title-wrap">
                    <div class="adm-modal-icon-badge adm-modal-icon-emerald">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h3 class="adm-modal-title">Edit UPA</h3>
                        <p class="adm-modal-subtitle">Ubah data UPA yang sudah ada.</p>
                    </div>
                </div>
                <button type="button" class="adm-modal-close" onclick="AdminModal.close('editUpaModal')" title="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="" method="POST" id="editUpaForm">
                @csrf
                @method('PUT')
                <div class="adm-modal-body">
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="edit_nama_upa">
                            <i class="fas fa-building-columns"></i> Nama UPA <span class="adm-required">*</span>
                        </label>
                        <input type="text" id="edit_nama_upa" name="nama_upa" class="adm-form-input"
                            placeholder="Ubah nama UPA" required maxlength="150">
                    </div>
                </div>
                <div class="adm-modal-footer">
                    <button type="button" class="adm-btn-cancel" onclick="AdminModal.close('editUpaModal')">
                        <i class="fas fa-arrow-left"></i> Batal
                    </button>
                    <button type="submit" class="adm-btn-submit">
                        <i class="fas fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    function openCreateUpaModal() {
        AdminModal.open('createUpaModal', {
            focusSelector: '#create_nama_upa',
            resetForm: true
        });
    }

    function openEditUpaModal(id, namaUpa) {
        const form = document.getElementById('editUpaForm');
        if (form) {
            form.action = "{{ route('upa.update', ':id') }}".replace(':id', id);
        }
        const namaInput = document.getElementById('edit_nama_upa');
        if (namaInput) {
            namaInput.value = namaUpa || '';
        }

        AdminModal.open('editUpaModal', {
            focusSelector: '#edit_nama_upa'
        });
    }
</script>
@endsection
