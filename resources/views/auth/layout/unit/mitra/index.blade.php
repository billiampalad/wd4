<link rel="stylesheet" href="{{ asset('css/auth/unit/institusi.css') }}" data-turbo-track="reload">

<!-- Main Content -->
<main id="mainContent" class="dk-page" data-mitra-index>
    {{-- ═══ HERO SECTION ═══ --}}
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('unit.dashboard') }}">Beranda</a>
                <span>/</span>
                <a href="{{ route('unit.dkerjasama') }}">Repositori</a>
                <span>/</span>
                <span>Daftar Mitra</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-handshake"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Daftar Mitra Kerjasama</h2>
                    <p class="ud-subtitle" id="pageDesc">Kelola data instansi dan organisasi mitra unit pelaksana.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="dk-stats-grid">
        <div class="dk-stat-card dk-stat-total">
            <div class="dk-stat-icon"><i class="fas fa-building"></i></div>
            <div>
                <span class="dk-stat-label">Total Mitra</span>
                <div>{{ ($mitras ?? collect())->count() }} Instansi</div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-active">
            <div class="dk-stat-icon"><i class="fas fa-globe-asia"></i></div>
            <div>
                <span class="dk-stat-label">Nasional</span>
                <div>{{ ($mitras ?? collect())->where('kategori', 'nasional')->count() }} Mitra</div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-warning">
            <div class="dk-stat-icon"><i class="fas fa-earth-americas"></i></div>
            <div>
                <span class="dk-stat-label">Internasional</span>
                <div>{{ ($mitras ?? collect())->where('kategori', 'internasional')->count() }} Mitra</div>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-danger">
            <div class="dk-stat-icon"><i class="fas fa-tags"></i></div>
            <div>
                <span class="dk-stat-label">Klasifikasi</span>
                <div>{{ ($mitras ?? collect())->pluck('id_klasifikasi')->unique()->count() }} Kategori</div>
            </div>
        </div>
    </section>

    <div class="card um-card dk-card">
        <div class="card-header um-header dk-card-header">
            <div class="um-title dk-card-title">
                <span class="dk-title-icon"><i class="fas fa-list-ul"></i></span>
                <span>
                    <strong>Tabel Data Mitra</strong>
                    <small>Daftar seluruh mitra yang bekerjasama dengan unit</small>
                </span>
            </div>

            <div class="dk-card-tools">
                <button onclick="openMitraModal()" class="dk-primary-btn">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Mitra</span>
                </button>
            </div>
        </div>

        <div class="card-body dk-card-body" style="padding: 0;" x-data="{ 
            currentPage: 1, 
            perPage: 10,
            totalRows: {{ ($mitras ?? collect())->count() }},
            get totalPages() { return Math.ceil(this.totalRows / this.perPage); },
            get startRange() { return (this.currentPage - 1) * this.perPage + 1; },
            get endRange() { return Math.min(this.currentPage * this.perPage, this.totalRows); }
        }">
            <div class="table-wrap um-table-wrap dk-table-wrap">
                <table class="um-table dk-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num">No</th>
                            <th class="um-th">Nama Mitra</th>
                            <th class="um-th">Klasifikasi</th>
                            <th class="um-th">Negara</th>
                            <th class="um-th um-th-num">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($mitras ?? collect()) as $index => $mitra)
                            <tr class="um-row dk-row" x-show="Math.ceil(({{ $index }} + 1) / perPage) === currentPage" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2">
                                <td class="um-td um-td-num">
                                    <span class="um-num dk-num">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="um-td">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--surface2); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 14px; border: 1px solid var(--border);">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <span class="um-name" style="font-weight: 700; color: var(--text);">{{ $mitra->nama_mitra }}</span>
                                    </div>
                                </td>
                                <td class="um-td">
                                    <span class="tag tag-purple" style="font-size: 11px;">
                                        {{ $mitra->klasifikasi->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="um-td">
                                    <div class="dk-entity">
                                        <span class="dk-entity-icon dk-entity-emerald" style="width: 24px; height: 24px; font-size: 10px;">
                                            <i class="fas fa-globe-asia"></i>
                                        </span>
                                        <span class="dk-entity-text" style="font-size: 13px;">{{ $mitra->negara ?? 'Indonesia' }}</span>
                                    </div>
                                </td>
                                <td class="um-td um-td-aksi">
                                    <div class="um-actions dk-actions-compact">
                                        <a href="{{ route('unit.mitra.show', $mitra->id) }}" class="dk-action-btn view" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="javascript:void(0)" onclick="openMitraEditModal('{{ $mitra->id }}', '{{ addslashes($mitra->nama_mitra) }}', '{{ $mitra->id_klasifikasi }}', '{{ $mitra->kategori }}', '{{ addslashes($mitra->negara) }}', '{{ addslashes($mitra->alamat) }}', '{{ addslashes($mitra->telp) }}', '{{ addslashes($mitra->website) }}')" class="dk-action-btn edit" title="Edit">
                                            <i class="fas fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('unit.mitra.destroy', $mitra->id) }}" method="POST" style="display: inline-flex;"
                                            data-mitra-delete-form data-confirm-message="Apakah Anda yakin ingin menghapus mitra ini?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dk-action-btn delete" title="Hapus">
                                                <i class="fas fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr data-empty>
                                <td colspan="5" class="um-empty">
                                    <div class="um-empty-state dk-empty-state">
                                        <div class="um-empty-icon dk-empty-icon">
                                            <i class="fas fa-handshake-slash"></i>
                                        </div>
                                        <p class="um-empty-title">Belum ada mitra terdaftar</p>
                                        <p class="um-empty-sub">Mulai kelola data mitra kerjasama unit Anda.</p>
                                        <button onclick="openMitraModal()" class="dk-empty-btn">
                                            <i class="fas fa-plus"></i>
                                            Tambah Mitra
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

@include('auth.layout.unit.mitra._modal_create')
@include('auth.layout.unit.mitra._modal_edit')
