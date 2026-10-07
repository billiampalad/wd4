@php
    $bentukList = $bentukKegiatans ?? collect();
    $totalBentuk = $bentukList->count();
    $totalCooperationsCount = $bentukList->sum('total_count');

    // Find the most active/frequent activity type
    $mostActive = $bentukList->sortByDesc('total_count')->first();
    $mostActiveName = $mostActive && $mostActive->total_count > 0 ? ($mostActive->nama_kerjasama ?: $mostActive->nama) : '-';
    $mostActiveCount = $mostActive ? $mostActive->total_count : 0;
@endphp

<link rel="stylesheet" href="{{ asset('css/auth/unit/institusi.css') }}" data-turbo-track="reload">

<main id="mainContent" class="sk-page" x-data="bentukKegiatanPage()">
    <section class="ud-topbar" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('unit.dashboard') }}">Beranda</a>
                <span>/</span>
                <span>Bentuk Kegiatan</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-university"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title">Bentuk Kegiatan</h2>
                    <p class="ud-subtitle">
                        Daftar bentuk kegiatan yang terlibat dalam pengelolaan kerjasama.
                    </p>
                </div>
            </div>
        </div>
        <div class="ud-topbar-actions" style="margin-top: 8px;">
            <button type="button" @click="openCreateModal()" class="dk-primary-btn" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 600; padding: 10px 18px; border-radius: 12px; cursor: pointer; background: linear-gradient(135deg, #059669, #10b981); color: #fff; border: none; box-shadow: 0 4px 12px rgba(5,150,105,0.25); transition: 0.2s;">
                <i class="fas fa-plus-circle"></i>
                <span>Tambah Bentuk Kegiatan</span>
            </button>
        </div>
    </section>

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="dk-alert dk-alert-success" style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; padding: 12px 18px; border-radius: 12px; font-size: 13.5px; font-weight: 500;">
        <i class="fas fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="dk-alert dk-alert-error" style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 12px 18px; border-radius: 12px; font-size: 13.5px; font-weight: 500;">
        <i class="fas fa-circle-exclamation"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if(isset($errors) && $errors->any())
    <div class="dk-alert dk-alert-error" style="margin-bottom: 20px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 12px 18px; border-radius: 12px; font-size: 13.5px;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <section class="dk-stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
        <div class="dk-stat-card dk-stat-total">
            <div class="dk-stat-icon"><i class="fas fa-layer-group"></i></div>
            <div class="dk-stat-content">
                <span class="dk-stat-label">Total Bentuk Kegiatan</span>
                <div class="dk-stat-value">{{ $totalBentuk }} <span>Jenis</span></div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-primary">
            <div class="dk-stat-icon"><i class="fas fa-file-contract"></i></div>
            <div class="dk-stat-content">
                <span class="dk-stat-label">Total Penggunaan</span>
                <div class="dk-stat-value">{{ $totalCooperationsCount }} <span>Kerjasama</span></div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-success">
            <div class="dk-stat-icon"><i class="fas fa-star"></i></div>
            <div class="dk-stat-content">
                <span class="dk-stat-label">Terbanyak Digunakan</span>
                <div class="dk-stat-value"
                    style="font-size: 15px; font-weight: 700; line-height: 1.2; margin-top: 4px;">
                    {{ Str::limit($mostActiveName, 30) }}
                    <span style="font-size: 12px; font-weight: 500; color: var(--ud-text-muted); display: block;">
                        ({{ $mostActiveCount }} Kerjasama)
                    </span>
                </div>
            </div>
        </div>
    </section>

    <div class="card dk-card">
        <div class="card-header um-header dk-card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
            <div class="dk-card-title">
                <span class="dk-title-icon"><i class="fas fa-list-ul"></i></span>
                <span>
                    <strong>Daftar Bentuk Kegiatan</strong>
                    <small>Referensi bentuk kegiatan kerjasama</small>
                </span>
            </div>
            <div class="um-header-actions" style="display: flex; align-items: center; gap: 12px;">
                <x-paginav-entries 
                    target=".dk-table tbody tr.um-row"
                    :perPage="10"
                    :options="[5, 10, 25, 50, 100]"
                />
            </div>
        </div>

        <div class="card-body dk-card-body">
            <div class="table-wrap um-table-wrap dk-table-wrap">
                <table class="um-table dk-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num" style="width: 60px;">#</th>
                            <th class="um-th dk-col-name" style="width: 280px;">Nama Bentuk Kegiatan</th>
                            <th class="um-th">Keterangan / Deskripsi</th>
                            <th class="um-th" style="text-align: center; width: 160px;">Jumlah Kerjasama</th>
                            <th class="um-th" style="text-align: center; width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bentukList as $bentuk)
                            <tr class="um-row dk-row">
                                <td class="um-td um-td-num" style="vertical-align: middle;">
                                    <span class="um-num dk-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="um-td dk-col-name" style="vertical-align: middle;">
                                    <div class="dk-entity" style="gap: 10px;">
                                        <div style="display: flex; flex-direction: column; gap: 4px;">
                                            <span class="dk-entity-text"
                                                style="font-weight: 700; font-size: 14px; color: var(--ud-text);">{{ $bentuk->nama_kerjasama ?: ($bentuk->nama ?: '-') }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="um-td" style="vertical-align: middle;">
                                    <span style="font-size: 13px; color: var(--ud-text-muted); line-height: 1.45; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $bentuk->keterangan ?: '-' }}
                                    </span>
                                </td>
                                <td class="um-td" style="vertical-align: middle; text-align: center;">
                                    <span class="dk-status dk-status-active"
                                        style="font-weight: 700; justify-content: center; width: 40px; margin: 0 auto; padding: 4px 0;">{{ $bentuk->total_count }}</span>
                                </td>
                                <td class="um-td" style="vertical-align: middle; text-align: center;">
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                        <button type="button" 
                                            @click="openEditModal({{ $bentuk->id }}, @js($bentuk->nama_kerjasama ?? ''), @js($bentuk->keterangan ?? ''))"
                                            class="dk-action-btn edit" 
                                            title="Edit Bentuk Kegiatan"
                                            style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border); background: var(--surface); color: #3b82f6; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s;">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if($bentuk->total_count == 0)
                                        <form action="{{ route('unit.referensi.bentuk-kegiatan.destroy', $bentuk->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bentuk kegiatan ini?')" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dk-action-btn delete" title="Hapus Bentuk Kegiatan"
                                                style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border); background: var(--surface); color: #ef4444; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s;">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                        @else
                                        <button type="button" class="dk-action-btn disabled" disabled title="Tidak dapat dihapus karena telah digunakan pada {{ $bentuk->total_count }} data kerjasama"
                                            style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border); background: var(--surface-sub); color: var(--text-sub); opacity: 0.5; cursor: not-allowed; display: inline-flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr data-empty>
                                <td colspan="5" class="um-empty">
                                    <div class="um-empty-state dk-empty-state">
                                        <div class="um-empty-icon dk-empty-icon"><i class="fas fa-book-open"></i></div>
                                        <p class="um-empty-title">Belum ada data bentuk kegiatan</p>
                                        <p class="um-empty-sub">Data bentuk kegiatan kerjasama akan tampil di sini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-paginav 
                id="bentukKegiatanTablePaginav"
                target=".dk-table tbody tr.um-row"
                :perPage="10"
                :showInfo="true"
                :showPerPage="false"
            />
        </div>
    </div>

    {{-- ═══ MODAL CREATE BENTUK KEGIATAN ═══ --}}
    <div x-show="showCreateModal" style="display: none; position: fixed; inset: 0; z-index: 9999; overflow-y: auto;" x-transition.opacity>
        <div style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);" @click="showCreateModal = false"></div>
        <div style="position: relative; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;">
            <div style="position: relative; width: 100%; max-width: 540px; background: var(--surface); border: 1px solid var(--border); border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden;" @click.stop>
                {{-- Header --}}
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--border); background: linear-gradient(135deg, rgba(5,150,105,0.06), rgba(16,185,129,0.04));">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, #059669, #10b981); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 16px; box-shadow: 0 4px 12px rgba(5,150,105,0.25);">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: var(--text);">Tambah Bentuk Kegiatan</h3>
                            <p style="margin: 2px 0 0; font-size: 12px; color: var(--text-sub);">Lengkapi nama dan keterangan bentuk kegiatan baru</p>
                        </div>
                    </div>
                    <button type="button" @click="showCreateModal = false" style="background: none; border: none; font-size: 18px; color: var(--text-sub); cursor: pointer; padding: 4px;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                {{-- Form --}}
                <form action="{{ route('unit.referensi.bentuk-kegiatan.store') }}" method="POST">
                    @csrf
                    <div style="padding: 24px; display: flex; flex-direction: column; gap: 18px;">
                        {{-- Nama Bentuk Kegiatan --}}
                        <div class="mc-group" x-data="{ nameVal: '', max: 255 }">
                            <div class="mc-label-row">
                                <label class="mc-label" style="margin-bottom: 0;">Nama Bentuk Kegiatan <span class="mc-req" style="color: #ef4444;">*</span></label>
                                <span class="mc-limit-badge" style="font-size: 11px; color: var(--text-sub);">Maks. 255</span>
                            </div>
                            <div class="mc-input-wrap" style="position: relative; margin-top: 6px;">
                                <input type="text" name="nama_kerjasama" x-model="nameVal" maxlength="255" required
                                    placeholder="Contoh: Magang Industri / PKL"
                                    class="mc-input"
                                    style="width: 100%; height: 42px; padding: 0 14px; border-radius: 10px; border: 1px solid var(--border); background: var(--surface-sub); color: var(--text);" />
                            </div>
                        </div>

                        {{-- Keterangan / Deskripsi --}}
                        <div class="mc-group" x-data="{ ketVal: '', max: 1000 }">
                            <div class="mc-label-row">
                                <label class="mc-label" style="margin-bottom: 0;">Keterangan <span style="font-weight: 400; font-size: 11px; color: var(--text-sub);">(Opsional)</span></label>
                                <span class="mc-limit-badge" style="font-size: 11px; color: var(--text-sub);">Maks. 1.000</span>
                            </div>
                            <div class="mc-input-wrap" style="position: relative; margin-top: 6px;">
                                <textarea name="keterangan" rows="3" x-model="ketVal" maxlength="1000"
                                    placeholder="Jelaskan deskripsi atau cakupan bentuk kegiatan ini..."
                                    class="mc-input"
                                    style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border); background: var(--surface-sub); color: var(--text); resize: vertical; min-height: 80px;"></textarea>
                            </div>
                            <div style="font-size: 11px; color: var(--text-sub); text-align: right; margin-top: 4px;">
                                <span x-text="ketVal.length"></span>/1000 karakter
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 10px; padding: 16px 24px; border-top: 1px solid var(--border); background: var(--surface-sub);">
                        <button type="button" @click="showCreateModal = false" class="dk-secondary-btn" style="padding: 9px 18px; border-radius: 10px; border: 1px solid var(--border); background: var(--surface); color: var(--text); font-weight: 600; cursor: pointer;">
                            Batal
                        </button>
                        <button type="submit" class="dk-primary-btn" style="padding: 9px 20px; border-radius: 10px; background: linear-gradient(135deg, #059669, #10b981); color: #fff; border: none; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(5,150,105,0.3);">
                            <i class="fas fa-save" style="margin-right: 6px;"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ═══ MODAL EDIT BENTUK KEGIATAN ═══ --}}
    <div x-show="showEditModal" style="display: none; position: fixed; inset: 0; z-index: 9999; overflow-y: auto;" x-transition.opacity>
        <div style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);" @click="showEditModal = false"></div>
        <div style="position: relative; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;">
            <div style="position: relative; width: 100%; max-width: 540px; background: var(--surface); border: 1px solid var(--border); border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden;" @click.stop>
                {{-- Header --}}
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--border); background: linear-gradient(135deg, rgba(59,130,246,0.06), rgba(37,99,235,0.04));">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, #2563eb, #3b82f6); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 16px; box-shadow: 0 4px 12px rgba(37,99,235,0.25);">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: var(--text);">Edit Bentuk Kegiatan</h3>
                            <p style="margin: 2px 0 0; font-size: 12px; color: var(--text-sub);">Perbarui data nama dan keterangan bentuk kegiatan</p>
                        </div>
                    </div>
                    <button type="button" @click="showEditModal = false" style="background: none; border: none; font-size: 18px; color: var(--text-sub); cursor: pointer; padding: 4px;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                {{-- Form --}}
                <form :action="editActionUrl" method="POST">
                    @csrf
                    @method('PUT')
                    <div style="padding: 24px; display: flex; flex-direction: column; gap: 18px;">
                        {{-- Nama Bentuk Kegiatan --}}
                        <div class="mc-group">
                            <div class="mc-label-row">
                                <label class="mc-label" style="margin-bottom: 0;">Nama Bentuk Kegiatan <span class="mc-req" style="color: #ef4444;">*</span></label>
                                <span class="mc-limit-badge" style="font-size: 11px; color: var(--text-sub);">Maks. 255</span>
                            </div>
                            <div class="mc-input-wrap" style="position: relative; margin-top: 6px;">
                                <input type="text" name="nama_kerjasama" x-model="editForm.nama_kerjasama" maxlength="255" required
                                    placeholder="Contoh: Magang Industri / PKL"
                                    class="mc-input"
                                    style="width: 100%; height: 42px; padding: 0 14px; border-radius: 10px; border: 1px solid var(--border); background: var(--surface-sub); color: var(--text);" />
                            </div>
                        </div>

                        {{-- Keterangan / Deskripsi --}}
                        <div class="mc-group">
                            <div class="mc-label-row">
                                <label class="mc-label" style="margin-bottom: 0;">Keterangan <span style="font-weight: 400; font-size: 11px; color: var(--text-sub);">(Opsional)</span></label>
                                <span class="mc-limit-badge" style="font-size: 11px; color: var(--text-sub);">Maks. 1.000</span>
                            </div>
                            <div class="mc-input-wrap" style="position: relative; margin-top: 6px;">
                                <textarea name="keterangan" rows="3" x-model="editForm.keterangan" maxlength="1000"
                                    placeholder="Jelaskan deskripsi atau cakupan bentuk kegiatan ini..."
                                    class="mc-input"
                                    style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border); background: var(--surface-sub); color: var(--text); resize: vertical; min-height: 80px;"></textarea>
                            </div>
                            <div style="font-size: 11px; color: var(--text-sub); text-align: right; margin-top: 4px;">
                                <span x-text="(editForm.keterangan || '').length"></span>/1000 karakter
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 10px; padding: 16px 24px; border-top: 1px solid var(--border); background: var(--surface-sub);">
                        <button type="button" @click="showEditModal = false" class="dk-secondary-btn" style="padding: 9px 18px; border-radius: 10px; border: 1px solid var(--border); background: var(--surface); color: var(--text); font-weight: 600; cursor: pointer;">
                            Batal
                        </button>
                        <button type="submit" class="dk-primary-btn" style="padding: 9px 20px; border-radius: 10px; background: linear-gradient(135deg, #2563eb, #3b82f6); color: #fff; border: none; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(37,99,235,0.3);">
                            <i class="fas fa-save" style="margin-right: 6px;"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
    function bentukKegiatanPage() {
        return {
            showCreateModal: false,
            showEditModal: false,
            editActionUrl: '',
            editForm: {
                id: null,
                nama_kerjasama: '',
                keterangan: ''
            },
            openCreateModal() {
                this.showCreateModal = true;
            },
            openEditModal(id, nama, keterangan) {
                this.editForm.id = id;
                this.editForm.nama_kerjasama = nama || '';
                this.editForm.keterangan = keterangan || '';
                this.editActionUrl = '{{ url("unit/referensi/bentuk-kegiatan") }}/' + id;
                this.showEditModal = true;
            }
        };
    }
</script>