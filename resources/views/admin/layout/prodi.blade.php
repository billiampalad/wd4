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
                <span>Program Studi</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-graduation-cap"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Program Studi</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Tambah, edit, dan hapus data Program Studi.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="card um-card">
        <div class="card-header um-header">
            <div class="um-header-left" style="display: flex; align-items: center; gap: 18px; flex-wrap: wrap;">
                <div class="card-title"><i class="fas fa-graduation-cap"></i> Daftar Program Studi</div>
                <x-paginav-entries target=".um-table tbody tr.um-row" :perPage="10" :options="[5, 10, 25, 50]" />
            </div>
            <div class="um-header-actions">
                <x-search id="prodiSearchInput" placeholder="Cari program studi..." target=".um-table tbody tr.um-row"
                    emptyTarget="#prodiSearchEmptyRow" querySpan="#prodiSearchQueryText" />
                <button type="button" class="um-btn-add" onclick="openCreateProdiModal()">
                    <i class="fas fa-plus"></i> Tambah Prodi
                </button>
            </div>
        </div>

        <div class="table-wrap um-table-wrap">
            <table class="um-table">
                <thead>
                    <tr>
                        <th class="um-th um-th-num">#</th>
                        <th class="um-th">Kode</th>
                        <th class="um-th">Nama Prodi</th>
                        <th class="um-th">Jurusan</th>
                        <th class="um-th">Jenjang</th>
                        <th class="um-th">Dibuat</th>
                        <th class="um-th um-th-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prodis as $i => $prodi)
                        <tr class="um-row">
                            <td class="um-td um-td-num">
                                <span class="um-num">{{ $i + 1 }}</span>
                            </td>
                            <td class="um-td">
                                <span class="um-meta" style="font-family: monospace;">{{ $prodi->kode_prodi ?? '-' }}</span>
                            </td>
                            <td class="um-td">
                                <span class="um-name">{{ $prodi->nama_prodi }}</span>
                            </td>
                            <td class="um-td">
                                <span class="um-meta">{{ $prodi->jurusan->nama_jurusan ?? '-' }}</span>
                            </td>
                            <td class="um-td">
                                <span class="tag tag-blue" style="font-size: 11px;">{{ $prodi->jenjang }}</span>
                            </td>
                            <td class="um-td">
                                <div class="um-date">
                                    <i class="fas fa-calendar-plus um-date-icon"></i>
                                    {{ $prodi->created_at?->format('d-m-Y H:i') ?? '-' }}
                                </div>
                            </td>
                            <td class="um-td um-td-aksi">
                                <div class="actions um-actions">
                                    <button type="button" class="btn-action edit um-btn-edit" title="Edit"
                                        onclick="openEditProdiModal({{ $prodi->id }}, '{{ $prodi->jurusan_id }}', '{{ addslashes($prodi->kode_prodi ?? '') }}', '{{ addslashes($prodi->nama_prodi) }}', '{{ addslashes($prodi->jenjang) }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form id="form-delete-prodi-{{ $prodi->id }}" action="{{ route('prodi.destroy', $prodi->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-action delete um-btn-delete" title="Hapus" onclick="CustomAlert.confirm({
                                            title: 'Hapus Program Studi',
                                            message: 'Menghapus program studi <span class=\'custom-alert-highlight\'>{{ addslashes($prodi->nama_prodi) }}</span> tidak dapat dikembalikan.',
                                            type: 'danger',
                                            confirmText: 'Hapus',
                                            confirmColor: 'danger',
                                            onConfirm: () => document.getElementById('form-delete-prodi-{{ $prodi->id }}').submit()
                                        })">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="um-empty">
                                <div class="um-empty-state">
                                    <div class="um-empty-icon">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <p class="um-empty-title">Belum ada data Program Studi</p>
                                    <p class="um-empty-sub">Klik tombol <strong>Tambah Prodi</strong> untuk memulai.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    <tr id="prodiSearchEmptyRow" style="display: none;">
                        <td colspan="7" class="um-empty">
                            <div class="um-empty-state">
                                <div class="um-empty-icon" style="color: var(--accent, #4f46e5); opacity: 0.7;">
                                    <i class="fas fa-search-minus"></i>
                                </div>
                                <p class="um-empty-title">Data Tidak Ditemukan</p>
                                <p class="um-empty-sub">Tidak ada program studi yang cocok dengan kata kunci "<span
                                        id="prodiSearchQueryText"
                                        style="font-weight: 600; color: var(--accent, #4f46e5);"></span>".</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <x-paginav id="prodiTablePaginav" target=".um-table tbody tr.um-row" :perPage="10" :showInfo="true"
            :showPerPage="false" />
    </div>

    {{-- Modal Tambah Prodi --}}
    <div id="createProdiModal" class="adm-modal-overlay" style="display: none;" onclick="AdminModal.handleOverlayClick(event, 'createProdiModal')">
        <div class="adm-modal-container adm-modal-md">
            <div class="adm-modal-header">
                <div class="adm-modal-title-wrap">
                    <div class="adm-modal-icon-badge adm-modal-icon-indigo">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h3 class="adm-modal-title">Tambah Program Studi</h3>
                        <p class="adm-modal-subtitle">Isi formulir untuk menambahkan program studi baru.</p>
                    </div>
                </div>
                <button type="button" class="adm-modal-close" onclick="AdminModal.close('createProdiModal')" title="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('prodi.store') }}" method="POST" id="createProdiForm">
                @csrf
                <div class="adm-modal-body">
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="create_jurusan_id">
                            <i class="fas fa-microchip"></i> Jurusan <span class="adm-required">*</span>
                        </label>
                        <select id="create_jurusan_id" name="jurusan_id" class="adm-form-input" required>
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach($jurusans as $jurusan)
                                <option value="{{ $jurusan->id }}">{{ $jurusan->nama_jurusan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="create_nama_prodi">
                            <i class="fas fa-graduation-cap"></i> Nama Program Studi <span class="adm-required">*</span>
                        </label>
                        <input type="text" id="create_nama_prodi" name="nama_prodi" class="adm-form-input"
                            placeholder="Contoh: Teknik Informatika" required maxlength="150">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        <div class="adm-form-group">
                            <label class="adm-form-label" for="create_kode_prodi">
                                <i class="fas fa-barcode"></i> Kode Prodi
                            </label>
                            <input type="text" id="create_kode_prodi" name="kode_prodi" class="adm-form-input"
                                placeholder="Contoh: TI01 (opsional)" maxlength="20">
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-form-label" for="create_jenjang">
                                <i class="fas fa-layer-group"></i> Jenjang <span class="adm-required">*</span>
                            </label>
                            <select id="create_jenjang" name="jenjang" class="adm-form-input" required>
                                <option value="">-- Pilih --</option>
                                <option value="D3">D3</option>
                                <option value="D4" selected>D4</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="adm-modal-footer">
                    <button type="button" class="adm-btn-cancel" onclick="AdminModal.close('createProdiModal')">
                        <i class="fas fa-arrow-left"></i> Batal
                    </button>
                    <button type="submit" class="adm-btn-submit">
                        <i class="fas fa-floppy-disk"></i> Simpan Prodi
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Prodi --}}
    <div id="editProdiModal" class="adm-modal-overlay" style="display: none;" onclick="AdminModal.handleOverlayClick(event, 'editProdiModal')">
        <div class="adm-modal-container adm-modal-md">
            <div class="adm-modal-header">
                <div class="adm-modal-title-wrap">
                    <div class="adm-modal-icon-badge adm-modal-icon-emerald">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h3 class="adm-modal-title">Edit Program Studi</h3>
                        <p class="adm-modal-subtitle">Ubah data program studi yang sudah ada.</p>
                    </div>
                </div>
                <button type="button" class="adm-modal-close" onclick="AdminModal.close('editProdiModal')" title="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="" method="POST" id="editProdiForm">
                @csrf
                @method('PUT')
                <div class="adm-modal-body">
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="edit_jurusan_id">
                            <i class="fas fa-microchip"></i> Jurusan <span class="adm-required">*</span>
                        </label>
                        <select id="edit_jurusan_id" name="jurusan_id" class="adm-form-input" required>
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach($jurusans as $jurusan)
                                <option value="{{ $jurusan->id }}">{{ $jurusan->nama_jurusan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="edit_nama_prodi">
                            <i class="fas fa-graduation-cap"></i> Nama Program Studi <span class="adm-required">*</span>
                        </label>
                        <input type="text" id="edit_nama_prodi" name="nama_prodi" class="adm-form-input"
                            placeholder="Ubah nama program studi" required maxlength="150">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        <div class="adm-form-group">
                            <label class="adm-form-label" for="edit_kode_prodi">
                                <i class="fas fa-barcode"></i> Kode Prodi
                            </label>
                            <input type="text" id="edit_kode_prodi" name="kode_prodi" class="adm-form-input"
                                placeholder="Contoh: TI01 (opsional)" maxlength="20">
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-form-label" for="edit_jenjang">
                                <i class="fas fa-layer-group"></i> Jenjang <span class="adm-required">*</span>
                            </label>
                            <select id="edit_jenjang" name="jenjang" class="adm-form-input" required>
                                <option value="">-- Pilih --</option>
                                <option value="D3">D3</option>
                                <option value="D4">D4</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="adm-modal-footer">
                    <button type="button" class="adm-btn-cancel" onclick="AdminModal.close('editProdiModal')">
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
    function openCreateProdiModal() {
        AdminModal.open('createProdiModal', {
            focusSelector: '#create_jurusan_id',
            resetForm: true
        });
    }

    function openEditProdiModal(id, jurusanId, kodeProdi, namaProdi, jenjang) {
        const form = document.getElementById('editProdiForm');
        if (form) {
            form.action = "{{ route('prodi.update', ':id') }}".replace(':id', id);
        }
        const jurusanSelect = document.getElementById('edit_jurusan_id');
        const kodeInput = document.getElementById('edit_kode_prodi');
        const namaInput = document.getElementById('edit_nama_prodi');
        const jenjangSelect = document.getElementById('edit_jenjang');

        if (jurusanSelect) jurusanSelect.value = jurusanId || '';
        if (kodeInput) kodeInput.value = kodeProdi || '';
        if (namaInput) namaInput.value = namaProdi || '';
        if (jenjangSelect) jenjangSelect.value = jenjang || '';

        AdminModal.open('editProdiModal', {
            focusSelector: '#edit_nama_prodi'
        });
    }
</script>
@endsection
