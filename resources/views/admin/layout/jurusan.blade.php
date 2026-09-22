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
                    <span>Jurusan</span>
                </div>
                <div class="ud-title-row">
                    <span class="ud-title-icon"><i class="fas fa-graduation-cap"></i></span>
                    <div class="ud-title-copy">
                        <h2 class="ud-title" id="pageTitle">Jurusan</h2>
                        <p class="ud-subtitle" id="pageDesc">
                            Tambah, edit, dan hapus data Jurusan.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <div class="card um-card">
            <div class="card-header um-header">
                <div class="um-header-left" style="display: flex; align-items: center; gap: 18px; flex-wrap: wrap;">
                    <div class="card-title"><i class="fas fa-microchip"></i> Daftar Jurusan</div>
                    <x-paginav-entries target=".um-table tbody tr.um-row" :perPage="10" :options="[5, 10, 25, 50]" />
                </div>
                <div class="um-header-actions">
                    <button type="button" class="um-btn-add" onclick="openCreateJurusanModal()">
                        <i class="fas fa-plus"></i> Tambah Jurusan
                    </button>
                </div>
            </div>

            <div class="table-wrap um-table-wrap">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num">#</th>
                            <th class="um-th">Kode</th>
                            <th class="um-th">Nama Jurusan</th>
                            <th class="um-th">Dibuat</th>
                            <th class="um-th">Diperbarui</th>
                            <th class="um-th um-th-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jurusans as $i => $jurusan)
                            <tr class="um-row">
                                <td class="um-td um-td-num">
                                    <span class="um-num">{{ $i + 1 }}</span>
                                </td>
                                <td class="um-td">
                                    <span class="um-meta"
                                        style="font-family: monospace;">{{ $jurusan->kode_jurusan ?? '-' }}</span>
                                </td>
                                <td class="um-td">
                                    <span class="um-name">{{ $jurusan->nama_jurusan ?? '-' }}</span>
                                </td>
                                <td class="um-td">
                                    <div class="um-date">
                                        <i class="fas fa-calendar-plus um-date-icon"></i>
                                        {{ $jurusan->created_at?->format('d-m-Y H:i') ?? '-' }}
                                    </div>
                                </td>
                                <td class="um-td">
                                    <div class="um-date">
                                        <i class="fas fa-calendar-check um-date-icon"></i>
                                        {{ $jurusan->updated_at?->format('d-m-Y H:i') ?? '-' }}
                                    </div>
                                </td>
                                <td class="um-td um-td-aksi">
                                    <div class="actions um-actions">
                                        <button type="button" class="btn-action edit um-btn-edit" title="Edit"
                                            onclick="openEditJurusanModal({{ $jurusan->id }}, '{{ addslashes($jurusan->kode_jurusan ?? '') }}', '{{ addslashes($jurusan->nama_jurusan) }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form id="form-delete-jurusan-{{ $jurusan->id }}"
                                            action="{{ route('jurusan.destroy', $jurusan->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn-action delete um-btn-delete" title="Hapus" onclick="CustomAlert.confirm({
                                                    title: 'Hapus Jurusan',
                                                    message: 'Menghapus jurusan <span class=\'custom-alert-highlight\'>{{ addslashes($jurusan->nama_jurusan) }}</span> tidak dapat dikembalikan.',
                                                    type: 'danger',
                                                    confirmText: 'Hapus',
                                                    confirmColor: 'danger',
                                                    onConfirm: () => document.getElementById('form-delete-jurusan-{{ $jurusan->id }}').submit()
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
                                            <i class="fas fa-microchip"></i>
                                        </div>
                                        <p class="um-empty-title">Belum ada data Jurusan</p>
                                        <p class="um-empty-sub">Klik tombol <strong>Tambah Jurusan</strong> untuk memulai.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        <tr id="jurusanSearchEmptyRow" style="display: none;">
                            <td colspan="6" class="um-empty">
                                <div class="um-empty-state">
                                    <div class="um-empty-icon" style="color: var(--accent, #4f46e5); opacity: 0.7;">
                                        <i class="fas fa-search-minus"></i>
                                    </div>
                                    <p class="um-empty-title">Data Tidak Ditemukan</p>
                                    <p class="um-empty-sub">Tidak ada jurusan yang cocok dengan kata kunci "<span
                                            id="jurusanSearchQueryText"
                                            style="font-weight: 600; color: var(--accent, #4f46e5);"></span>".</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <x-paginav id="jurusanTablePaginav" target=".um-table tbody tr.um-row" :perPage="10" :showInfo="true"
                :showPerPage="false" />
        </div>

        {{-- Modal Tambah Jurusan --}}
        <div id="createJurusanModal" class="adm-modal-overlay" style="display: none;"
            onclick="AdminModal.handleOverlayClick(event, 'createJurusanModal')">
            <div class="adm-modal-container adm-modal-md">
                <div class="adm-modal-header">
                    <div class="adm-modal-title-wrap">
                        <div class="adm-modal-icon-badge adm-modal-icon-indigo">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div>
                            <h3 class="adm-modal-title">Tambah Jurusan</h3>
                            <p class="adm-modal-subtitle">Isi formulir untuk menambahkan data jurusan baru.</p>
                        </div>
                    </div>
                    <button type="button" class="adm-modal-close" onclick="AdminModal.close('createJurusanModal')"
                        title="Tutup">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form action="{{ route('jurusan.store') }}" method="POST" id="createJurusanForm">
                    @csrf
                    <div class="adm-modal-body">
                        <div class="adm-form-group">
                            <label class="adm-form-label" for="create_kode_jurusan">
                                <i class="fas fa-barcode"></i> Kode Jurusan
                            </label>
                            <input type="text" id="create_kode_jurusan" name="kode_jurusan" class="adm-form-input"
                                placeholder="Contoh: JUR01 (opsional)" maxlength="20">
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-form-label" for="create_nama_jurusan">
                                <i class="fas fa-microchip"></i> Nama Jurusan <span class="adm-required">*</span>
                            </label>
                            <input type="text" id="create_nama_jurusan" name="nama_jurusan" class="adm-form-input"
                                placeholder="Contoh: Teknik Elektro" required maxlength="150">
                        </div>
                    </div>
                    <div class="adm-modal-footer">
                        <button type="button" class="adm-btn-cancel" onclick="AdminModal.close('createJurusanModal')">
                            <i class="fas fa-arrow-left"></i> Batal
                        </button>
                        <button type="submit" class="adm-btn-submit">
                            <i class="fas fa-floppy-disk"></i> Simpan Jurusan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Edit Jurusan --}}
        <div id="editJurusanModal" class="adm-modal-overlay" style="display: none;"
            onclick="AdminModal.handleOverlayClick(event, 'editJurusanModal')">
            <div class="adm-modal-container adm-modal-md">
                <div class="adm-modal-header">
                    <div class="adm-modal-title-wrap">
                        <div class="adm-modal-icon-badge adm-modal-icon-emerald">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <h3 class="adm-modal-title">Edit Jurusan</h3>
                            <p class="adm-modal-subtitle">Ubah data jurusan yang sudah ada.</p>
                        </div>
                    </div>
                    <button type="button" class="adm-modal-close" onclick="AdminModal.close('editJurusanModal')"
                        title="Tutup">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form action="" method="POST" id="editJurusanForm">
                    @csrf
                    @method('PUT')
                    <div class="adm-modal-body">
                        <div class="adm-form-group">
                            <label class="adm-form-label" for="edit_kode_jurusan">
                                <i class="fas fa-barcode"></i> Kode Jurusan
                            </label>
                            <input type="text" id="edit_kode_jurusan" name="kode_jurusan" class="adm-form-input"
                                placeholder="Contoh: JUR01 (opsional)" maxlength="20">
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-form-label" for="edit_nama_jurusan">
                                <i class="fas fa-microchip"></i> Nama Jurusan <span class="adm-required">*</span>
                            </label>
                            <input type="text" id="edit_nama_jurusan" name="nama_jurusan" class="adm-form-input"
                                placeholder="Ubah nama jurusan" required maxlength="150">
                        </div>
                    </div>
                    <div class="adm-modal-footer">
                        <button type="button" class="adm-btn-cancel" onclick="AdminModal.close('editJurusanModal')">
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
        function openCreateJurusanModal() {
            AdminModal.open('createJurusanModal', {
                focusSelector: '#create_kode_jurusan',
                resetForm: true
            });
        }

        function openEditJurusanModal(id, kodeJurusan, namaJurusan) {
            const form = document.getElementById('editJurusanForm');
            if (form) {
                form.action = "{{ route('jurusan.update', ':id') }}".replace(':id', id);
            }
            const kodeInput = document.getElementById('edit_kode_jurusan');
            const namaInput = document.getElementById('edit_nama_jurusan');
            if (kodeInput) kodeInput.value = kodeJurusan || '';
            if (namaInput) namaInput.value = namaJurusan || '';

            AdminModal.open('editJurusanModal', {
                focusSelector: '#edit_nama_jurusan'
            });
        }
    </script>
@endsection