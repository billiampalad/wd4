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
    <div id="createJKerjasamaModal" class="jkerjasama-modal-overlay" style="display: none;" onclick="handleJKerjasamaModalOverlayClick(event, 'createJKerjasamaModal')">
        <div class="jkerjasama-modal-container">
            <div class="jkerjasama-modal-header">
                <div class="jkerjasama-modal-title-wrap">
                    <div class="jkerjasama-modal-icon-badge jkerjasama-modal-icon-add">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div>
                        <h3 class="jkerjasama-modal-title">Tambah Jenis Kerjasama</h3>
                        <p class="jkerjasama-modal-subtitle">Isi formulir untuk menambahkan jenis kerjasama baru.</p>
                    </div>
                </div>
                <button type="button" class="jkerjasama-modal-close" onclick="closeJKerjasamaModal('createJKerjasamaModal')" title="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('jkerjasama.store') }}" method="POST" id="createJKerjasamaForm">
                @csrf
                <div class="jkerjasama-modal-body">
                    <div class="jkerjasama-form-group">
                        <label class="jkerjasama-form-label" for="create_nama_kerjasama">
                            <i class="fas fa-tag"></i> Nama Jenis Kerjasama <span class="jkerjasama-required">*</span>
                        </label>
                        <input type="text" id="create_nama_kerjasama" name="nama_kerjasama" class="jkerjasama-form-input"
                            placeholder="Contoh: Magang, Penelitian, dsb" required maxlength="255">
                    </div>
                </div>
                <div class="jkerjasama-modal-footer">
                    <button type="button" class="jkerjasama-btn-cancel" onclick="closeJKerjasamaModal('createJKerjasamaModal')">
                        <i class="fas fa-arrow-left"></i> Batal
                    </button>
                    <button type="submit" class="jkerjasama-btn-submit">
                        <i class="fas fa-floppy-disk"></i> Simpan Jenis
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Jenis Kerjasama --}}
    <div id="editJKerjasamaModal" class="jkerjasama-modal-overlay" style="display: none;" onclick="handleJKerjasamaModalOverlayClick(event, 'editJKerjasamaModal')">
        <div class="jkerjasama-modal-container">
            <div class="jkerjasama-modal-header">
                <div class="jkerjasama-modal-title-wrap">
                    <div class="jkerjasama-modal-icon-badge jkerjasama-modal-icon-edit">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h3 class="jkerjasama-modal-title">Edit Jenis Kerjasama</h3>
                        <p class="jkerjasama-modal-subtitle">Ubah nama jenis kerjasama yang sudah ada.</p>
                    </div>
                </div>
                <button type="button" class="jkerjasama-modal-close" onclick="closeJKerjasamaModal('editJKerjasamaModal')" title="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="" method="POST" id="editJKerjasamaForm">
                @csrf
                @method('PUT')
                <div class="jkerjasama-modal-body">
                    <div class="jkerjasama-form-group">
                        <label class="jkerjasama-form-label" for="edit_nama_kerjasama">
                            <i class="fas fa-tag"></i> Nama Jenis Kerjasama <span class="jkerjasama-required">*</span>
                        </label>
                        <input type="text" id="edit_nama_kerjasama" name="nama_kerjasama" class="jkerjasama-form-input"
                            placeholder="Ubah nama jenis kerjasama" required maxlength="255">
                    </div>
                </div>
                <div class="jkerjasama-modal-footer">
                    <button type="button" class="jkerjasama-btn-cancel" onclick="closeJKerjasamaModal('editJKerjasamaModal')">
                        <i class="fas fa-arrow-left"></i> Batal
                    </button>
                    <button type="submit" class="jkerjasama-btn-submit">
                        <i class="fas fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<style>
    /* ── JKerjasama Modal Overlay & Container ── */
    .jkerjasama-modal-overlay {
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

    .jkerjasama-modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .jkerjasama-modal-container {
        background: var(--surface, #ffffff);
        border: 1px solid var(--border, #e2e8f0);
        border-radius: 16px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transform: scale(0.95) translateY(10px);
        transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .jkerjasama-modal-overlay.active .jkerjasama-modal-container {
        transform: scale(1) translateY(0);
    }

    /* ── Modal Header ── */
    .jkerjasama-modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 20px 24px 16px;
        border-bottom: 1px solid var(--border, #e2e8f0);
        background: var(--surface2, #f8fafc);
    }

    .jkerjasama-modal-title-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .jkerjasama-modal-icon-badge {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .jkerjasama-modal-icon-add {
        background: rgba(99, 102, 241, 0.12);
        color: #6366f1;
        border: 1px solid rgba(99, 102, 241, 0.25);
    }

    .jkerjasama-modal-icon-edit {
        background: rgba(16, 185, 129, 0.12);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.25);
    }

    .jkerjasama-modal-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text, #0f172a);
        margin: 0;
        line-height: 1.3;
    }

    .jkerjasama-modal-subtitle {
        font-size: 0.8rem;
        color: var(--text-sub, #64748b);
        margin: 3px 0 0 0;
    }

    .jkerjasama-modal-close {
        background: transparent;
        border: none;
        color: var(--text-sub, #94a3b8);
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.95rem;
        transition: background 0.15s, color 0.15s;
        flex-shrink: 0;
    }

    .jkerjasama-modal-close:hover {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger, #ef4444);
    }

    /* ── Modal Body ── */
    .jkerjasama-modal-body {
        padding: 22px 24px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .jkerjasama-form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .jkerjasama-form-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--text, #334155);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .jkerjasama-form-label i {
        color: var(--accent, #6366f1);
        font-size: 0.8rem;
    }

    .jkerjasama-required {
        color: var(--danger, #ef4444);
    }

    .jkerjasama-form-input {
        width: 100%;
        padding: 10px 14px;
        background: var(--surface2, #f8fafc);
        border: 1.5px solid var(--border, #e2e8f0);
        border-radius: 10px;
        font-size: 0.88rem;
        color: var(--text, #0f172a);
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        font-family: inherit;
        box-sizing: border-box;
    }

    .jkerjasama-form-input:focus {
        background: var(--surface, #ffffff);
        border-color: var(--accent, #6366f1);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }

    /* ── Modal Footer ── */
    .jkerjasama-modal-footer {
        padding: 14px 24px 20px;
        border-top: 1px solid var(--border, #e2e8f0);
        background: var(--surface2, #f8fafc);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
    }

    .jkerjasama-btn-cancel {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 18px;
        border-radius: 9px;
        font-size: 0.84rem;
        font-weight: 600;
        color: var(--text-sub, #64748b);
        background: var(--surface, #ffffff);
        border: 1px solid var(--border, #cbd5e1);
        cursor: pointer;
        transition: all 0.15s ease;
        font-family: inherit;
    }

    .jkerjasama-btn-cancel:hover {
        background: var(--surface2, #f1f5f9);
        color: var(--text, #0f172a);
    }

    .jkerjasama-btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 20px;
        border-radius: 9px;
        font-size: 0.84rem;
        font-weight: 600;
        color: #ffffff;
        background: var(--accent, #4f46e5);
        border: none;
        cursor: pointer;
        box-shadow: 0 3px 10px rgba(79, 70, 229, 0.3);
        transition: all 0.15s ease;
        font-family: inherit;
    }

    .jkerjasama-btn-submit:hover {
        filter: brightness(1.1);
        transform: translateY(-1px);
        box-shadow: 0 5px 14px rgba(79, 70, 229, 0.4);
    }

    [data-theme="dark"] .jkerjasama-modal-container {
        background: var(--surface, #181c27);
        border-color: var(--border, #2a2f45);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.05);
    }

    [data-theme="dark"] .jkerjasama-modal-header,
    [data-theme="dark"] .jkerjasama-modal-footer {
        background: var(--surface2, #1e2333);
        border-color: var(--border, #2a2f45);
    }

    [data-theme="dark"] .jkerjasama-form-input {
        background: var(--surface2, #1e2333);
        border-color: var(--border, #2a2f45);
        color: var(--text, #e8eaf6);
    }

    [data-theme="dark"] .jkerjasama-form-input:focus {
        background: var(--surface, #181c27);
        border-color: var(--accent, #6366f1);
    }

    [data-theme="dark"] .jkerjasama-btn-cancel {
        background: var(--surface2, #1e2333);
        border-color: var(--border, #2a2f45);
        color: var(--text-sub, #8b92a8);
    }

    [data-theme="dark"] .jkerjasama-btn-cancel:hover {
        background: rgba(255, 255, 255, 0.08);
        color: var(--text, #e8eaf6);
    }
</style>

<script>
    function openCreateJKerjasamaModal() {
        const modal = document.getElementById('createJKerjasamaModal');
        const form = document.getElementById('createJKerjasamaForm');
        if (form) form.reset();
        if (modal) {
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.add('active');
                document.getElementById('create_nama_kerjasama')?.focus();
            }, 10);
            document.body.style.overflow = 'hidden';
        }
    }

    function openEditJKerjasamaModal(id, namaKerjasama) {
        const modal = document.getElementById('editJKerjasamaModal');
        const form = document.getElementById('editJKerjasamaForm');
        if (form) {
            form.action = "{{ route('jkerjasama.update', ':id') }}".replace(':id', id);
        }
        const nameInput = document.getElementById('edit_nama_kerjasama');
        if (nameInput) nameInput.value = namaKerjasama;

        if (modal) {
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.add('active');
                nameInput?.focus();
            }, 10);
            document.body.style.overflow = 'hidden';
        }
    }

    function closeJKerjasamaModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }, 250);
        }
    }

    function handleJKerjasamaModalOverlayClick(event, modalId) {
        if (event.target === document.getElementById(modalId)) {
            closeJKerjasamaModal(modalId);
        }
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeJKerjasamaModal('createJKerjasamaModal');
            closeJKerjasamaModal('editJKerjasamaModal');
        }
    });
</script>
@endsection
