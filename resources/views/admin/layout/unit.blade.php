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
                <span>Humas</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-sitemap"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Humas</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Tambah, edit, dan hapus data humas.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="card um-card">
        <div class="card-header um-header">
            <div class="um-header-left" style="display: flex; align-items: center; gap: 18px; flex-wrap: wrap;">
                <div class="card-title"><i class="fas fa-sitemap"></i> Daftar Humas</div>
                <x-paginav-entries target=".um-table tbody tr.um-row" :perPage="10" :options="[5, 10, 25, 50]" />
            </div>
            <div class="um-header-actions">
                <x-search id="unitSearchInput" placeholder="Cari data humas..." target=".um-table tbody tr.um-row"
                    emptyTarget="#unitSearchEmptyRow" querySpan="#unitSearchQueryText" />
                <button type="button" class="um-btn-add" onclick="openCreateUnitModal()">
                    <i class="fas fa-plus"></i> Tambah Humas
                </button>
            </div>
        </div>

        <div class="table-wrap um-table-wrap">
            <table class="um-table">
                <thead>
                    <tr>
                        <th class="um-th um-th-num">#</th>
                        <th class="um-th">Nama Unit</th>
                        <th class="um-th">Dibuat</th>
                        <th class="um-th">Diperbarui</th>
                        <th class="um-th um-th-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($unit_kerjas as $i => $upelaksana)
                        <tr class="um-row">
                            <td class="um-td um-td-num">
                                <span class="um-num">{{ $i + 1 }}</span>
                            </td>
                            <td class="um-td">
                                <span class="um-name">{{ $upelaksana->nama_unit_pelaksana ?? '-' }}</span>
                            </td>
                            <td class="um-td">
                                <div class="um-date">
                                    <i class="fas fa-calendar-plus um-date-icon"></i>
                                    {{ $upelaksana->created_at?->format('d-m-Y H:i') ?? '-' }}
                                </div>
                            </td>
                            <td class="um-td">
                                <div class="um-date">
                                    <i class="fas fa-calendar-check um-date-icon"></i>
                                    {{ $upelaksana->updated_at?->format('d-m-Y H:i') ?? '-' }}
                                </div>
                            </td>
                            <td class="um-td um-td-aksi">
                                <div class="actions um-actions">
                                    <button type="button" class="btn-action edit um-btn-edit" title="Edit"
                                        onclick="openEditUnitModal({{ $upelaksana->id }}, '{{ addslashes($upelaksana->nama_unit_pelaksana) }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form id="form-delete-upelaksana-{{ $upelaksana->id }}" action="{{ route('upelaksana.destroy', $upelaksana->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-action delete um-btn-delete" title="Hapus" onclick="CustomAlert.confirm({
                                            title: 'Hapus Unit Humas',
                                            message: 'Menghapus unit pelaksana humas <span class=\'custom-alert-highlight\'>{{ addslashes($upelaksana->nama_unit_pelaksana) }}</span> tidak dapat dikembalikan.',
                                            type: 'danger',
                                            confirmText: 'Hapus',
                                            confirmColor: 'danger',
                                            onConfirm: () => document.getElementById('form-delete-upelaksana-{{ $upelaksana->id }}').submit()
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
                                        <i class="fas fa-sitemap"></i>
                                    </div>
                                    <p class="um-empty-title">Belum ada data unit pelaksana</p>
                                    <p class="um-empty-sub">Klik tombol <strong>Tambah Humas</strong> untuk memulai.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    <tr id="unitSearchEmptyRow" style="display: none;">
                        <td colspan="5" class="um-empty">
                            <div class="um-empty-state">
                                <div class="um-empty-icon" style="color: var(--accent, #4f46e5); opacity: 0.7;">
                                    <i class="fas fa-search-minus"></i>
                                </div>
                                <p class="um-empty-title">Data Tidak Ditemukan</p>
                                <p class="um-empty-sub">Tidak ada data yang cocok dengan kata kunci "<span
                                        id="unitSearchQueryText"
                                        style="font-weight: 600; color: var(--accent, #4f46e5);"></span>".</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <x-paginav id="unitTablePaginav" target=".um-table tbody tr.um-row" :perPage="10" :showInfo="true"
            :showPerPage="false" />
    </div>

    {{-- Modal Tambah Humas / Unit Pelaksana --}}
    <div id="createUnitModal" class="adm-modal-overlay" style="display: none;" onclick="AdminModal.handleOverlayClick(event, 'createUnitModal')">
        <div class="adm-modal-container adm-modal-sm">
            <div class="adm-modal-header">
                <div class="adm-modal-title-wrap">
                    <div class="adm-modal-icon-badge adm-modal-icon-indigo">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <div>
                        <h3 class="adm-modal-title">Tambah Humas</h3>
                        <p class="adm-modal-subtitle">Isi formulir untuk menambahkan unit pelaksana humas baru.</p>
                    </div>
                </div>
                <button type="button" class="adm-modal-close" onclick="AdminModal.close('createUnitModal')" title="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('upelaksana.store') }}" method="POST" id="createUnitForm">
                @csrf
                <div class="adm-modal-body">
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="create_nama_unit_pelaksana">
                            <i class="fas fa-building"></i> Nama Unit Pelaksana Humas <span class="adm-required">*</span>
                        </label>
                        <input type="text" id="create_nama_unit_pelaksana" name="nama_unit_pelaksana" class="adm-form-input"
                            placeholder="Contoh: Humas dan Kerjasama, dsb" required maxlength="255">
                    </div>
                </div>
                <div class="adm-modal-footer">
                    <button type="button" class="adm-btn-cancel" onclick="AdminModal.close('createUnitModal')">
                        <i class="fas fa-arrow-left"></i> Batal
                    </button>
                    <button type="submit" class="adm-btn-submit">
                        <i class="fas fa-floppy-disk"></i> Simpan Unit
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Humas / Unit Pelaksana --}}
    <div id="editUnitModal" class="adm-modal-overlay" style="display: none;" onclick="AdminModal.handleOverlayClick(event, 'editUnitModal')">
        <div class="adm-modal-container adm-modal-sm">
            <div class="adm-modal-header">
                <div class="adm-modal-title-wrap">
                    <div class="adm-modal-icon-badge adm-modal-icon-emerald">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h3 class="adm-modal-title">Edit Humas</h3>
                        <p class="adm-modal-subtitle">Ubah nama unit pelaksana humas yang sudah ada.</p>
                    </div>
                </div>
                <button type="button" class="adm-modal-close" onclick="AdminModal.close('editUnitModal')" title="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="" method="POST" id="editUnitForm">
                @csrf
                @method('PUT')
                <div class="adm-modal-body">
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="edit_nama_unit_pelaksana">
                            <i class="fas fa-building"></i> Nama Unit Pelaksana Humas <span class="adm-required">*</span>
                        </label>
                        <input type="text" id="edit_nama_unit_pelaksana" name="nama_unit_pelaksana" class="adm-form-input"
                            placeholder="Ubah nama unit pelaksana humas" required maxlength="255">
                    </div>
                </div>
                <div class="adm-modal-footer">
                    <button type="button" class="adm-btn-cancel" onclick="AdminModal.close('editUnitModal')">
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
    function openCreateUnitModal() {
        AdminModal.open('createUnitModal', {
            focusSelector: '#create_nama_unit_pelaksana',
            resetForm: true
        });
    }

    function openEditUnitModal(id, namaUnit) {
        const form = document.getElementById('editUnitForm');
        if (form) {
            form.action = "{{ route('upelaksana.update', ':id') }}".replace(':id', id);
        }
        const nameInput = document.getElementById('edit_nama_unit_pelaksana');
        if (nameInput) nameInput.value = namaUnit;

        AdminModal.open('editUnitModal', {
            focusSelector: '#edit_nama_unit_pelaksana'
        });
    }
</script>
@endsection