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
                <span>Jenis Kerjasama</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-tags"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Jenis Kerjasama</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Tambah, edit, dan hapus data jenis kerjasama.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="card um-card">
        <div class="card-header um-header">
            <div class="um-header-left" style="display: flex; align-items: center; gap: 18px; flex-wrap: wrap;">
                <div class="card-title"><i class="fas fa-tags"></i> Daftar Jenis Kerjasama</div>
                <x-paginav-entries target=".um-table tbody tr.um-row" :perPage="10" :options="[5, 10, 25, 50]" />
            </div>
            <div class="um-header-actions">
                <x-search id="jkerjasamaSearchInput" placeholder="Cari jenis kerjasama..." target=".um-table tbody tr.um-row"
                    emptyTarget="#jkerjasamaSearchEmptyRow" querySpan="#jkerjasamaSearchQueryText" />
                <button type="button" class="um-btn-add" onclick="openCreateJKerjasamaModal()">
                    <i class="fas fa-plus"></i> Tambah Jenis
                </button>
            </div>
        </div>

        <div class="table-wrap um-table-wrap">
            <table class="um-table">
                <thead>
                    <tr>
                        <th class="um-th um-th-num">#</th>
                        <th class="um-th">Nama Jenis</th>
                        <th class="um-th">Dibuat</th>
                        <th class="um-th">Diperbarui</th>
                        <th class="um-th um-th-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jenisKerjasamas as $i => $jkerjasama)
                        <tr class="um-row">
                            <td class="um-td um-td-num">
                                <span class="um-num">{{ $i + 1 }}</span>
                            </td>
                            <td class="um-td">
                                <span class="um-name">{{ $jkerjasama->nama_kerjasama ?? $jkerjasama->nama ?? '-' }}</span>
                            </td>
                            <td class="um-td">
                                <div class="um-date">
                                    <i class="fas fa-calendar-plus um-date-icon"></i>
                                    {{ $jkerjasama->created_at?->format('d-m-Y H:i') ?? '-' }}
                                </div>
                            </td>
                            <td class="um-td">
                                <div class="um-date">
                                    <i class="fas fa-calendar-check um-date-icon"></i>
                                    {{ $jkerjasama->updated_at?->format('d-m-Y H:i') ?? '-' }}
                                </div>
                            </td>
                            <td class="um-td um-td-aksi">
                                <div class="actions um-actions">
                                    <button type="button" class="btn-action edit um-btn-edit" title="Edit"
                                        onclick="openEditJKerjasamaModal({{ $jkerjasama->id }}, '{{ addslashes($jkerjasama->nama_kerjasama ?? $jkerjasama->nama) }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form id="form-delete-jkerjasama-{{ $jkerjasama->id }}" action="{{ route('jkerjasama.destroy', $jkerjasama->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-action delete um-btn-delete" title="Hapus" onclick="CustomAlert.confirm({
                                            title: 'Hapus Jenis Kerjasama',
                                            message: 'Menghapus jenis kerjasama <span class=\'custom-alert-highlight\'>{{ addslashes($jkerjasama->nama_kerjasama ?? $jkerjasama->nama) }}</span> tidak dapat dikembalikan.',
                                            type: 'danger',
                                            confirmText: 'Hapus',
                                            confirmColor: 'danger',
                                            onConfirm: () => document.getElementById('form-delete-jkerjasama-{{ $jkerjasama->id }}').submit()
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
                                        <i class="fas fa-tags"></i>
                                    </div>
                                    <p class="um-empty-title">Belum ada data jenis kerjasama</p>
                                    <p class="um-empty-sub">Klik tombol <strong>Tambah Jenis</strong> untuk memulai.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    <tr id="jkerjasamaSearchEmptyRow" style="display: none;">
                        <td colspan="5" class="um-empty">
                            <div class="um-empty-state">
                                <div class="um-empty-icon" style="color: var(--accent, #4f46e5); opacity: 0.7;">
                                    <i class="fas fa-search-minus"></i>
                                </div>
                                <p class="um-empty-title">Data Tidak Ditemukan</p>
                                <p class="um-empty-sub">Tidak ada jenis kerjasama yang cocok dengan kata kunci "<span
                                        id="jkerjasamaSearchQueryText"
                                        style="font-weight: 600; color: var(--accent, #4f46e5);"></span>".</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <x-paginav id="jkerjasamaTablePaginav" target=".um-table tbody tr.um-row" :perPage="10" :showInfo="true"
            :showPerPage="false" />
    </div>

    {{-- Modal Tambah Jenis Kerjasama --}}
    <div id="createJKerjasamaModal" class="adm-modal-overlay" style="display: none;" onclick="AdminModal.handleOverlayClick(event, 'createJKerjasamaModal')">
        <div class="adm-modal-container adm-modal-sm">
            <div class="adm-modal-header">
                <div class="adm-modal-title-wrap">
                    <div class="adm-modal-icon-badge adm-modal-icon-indigo">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div>
                        <h3 class="adm-modal-title">Tambah Jenis Kerjasama</h3>
                        <p class="adm-modal-subtitle">Isi formulir untuk menambahkan jenis kerjasama baru.</p>
                    </div>
                </div>
                <button type="button" class="adm-modal-close" onclick="AdminModal.close('createJKerjasamaModal')" title="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('jkerjasama.store') }}" method="POST" id="createJKerjasamaForm">
                @csrf
                <div class="adm-modal-body">
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="create_nama_kerjasama">
                            <i class="fas fa-tag"></i> Nama Jenis Kerjasama <span class="adm-required">*</span>
                        </label>
                        <input type="text" id="create_nama_kerjasama" name="nama_kerjasama" class="adm-form-input"
                            placeholder="Contoh: Magang, Penelitian, dsb" required maxlength="255">
                    </div>
                </div>
                <div class="adm-modal-footer">
                    <button type="button" class="adm-btn-cancel" onclick="AdminModal.close('createJKerjasamaModal')">
                        <i class="fas fa-arrow-left"></i> Batal
                    </button>
                    <button type="submit" class="adm-btn-submit">
                        <i class="fas fa-floppy-disk"></i> Simpan Jenis
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Jenis Kerjasama --}}
    <div id="editJKerjasamaModal" class="adm-modal-overlay" style="display: none;" onclick="AdminModal.handleOverlayClick(event, 'editJKerjasamaModal')">
        <div class="adm-modal-container adm-modal-sm">
            <div class="adm-modal-header">
                <div class="adm-modal-title-wrap">
                    <div class="adm-modal-icon-badge adm-modal-icon-emerald">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h3 class="adm-modal-title">Edit Jenis Kerjasama</h3>
                        <p class="adm-modal-subtitle">Ubah nama jenis kerjasama yang sudah ada.</p>
                    </div>
                </div>
                <button type="button" class="adm-modal-close" onclick="AdminModal.close('editJKerjasamaModal')" title="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="" method="POST" id="editJKerjasamaForm">
                @csrf
                @method('PUT')
                <div class="adm-modal-body">
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="edit_nama_kerjasama">
                            <i class="fas fa-tag"></i> Nama Jenis Kerjasama <span class="adm-required">*</span>
                        </label>
                        <input type="text" id="edit_nama_kerjasama" name="nama_kerjasama" class="adm-form-input"
                            placeholder="Ubah nama jenis kerjasama" required maxlength="255">
                    </div>
                </div>
                <div class="adm-modal-footer">
                    <button type="button" class="adm-btn-cancel" onclick="AdminModal.close('editJKerjasamaModal')">
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
    function openCreateJKerjasamaModal() {
        AdminModal.open('createJKerjasamaModal', {
            focusSelector: '#create_nama_kerjasama',
            resetForm: true
        });
    }

    function openEditJKerjasamaModal(id, namaKerjasama) {
        const form = document.getElementById('editJKerjasamaForm');
        if (form) {
            form.action = "{{ route('jkerjasama.update', ':id') }}".replace(':id', id);
        }
        const nameInput = document.getElementById('edit_nama_kerjasama');
        if (nameInput) nameInput.value = namaKerjasama;

        AdminModal.open('editJKerjasamaModal', {
            focusSelector: '#edit_nama_kerjasama'
        });
    }
</script>
@endsection
