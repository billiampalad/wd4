@php
    $submissionsList = $submissions ?? collect();
    $statsData = $stats ?? ['total' => 0, 'menunggu' => 0, 'selesai' => 0];
    $unitName = auth()->user()->profile?->unitKerja?->nama_unit_pelaksana ?? 'Unit Kerja';
@endphp

<link rel="stylesheet" href="{{ asset('css/auth/unit/institusi.css') }}" data-turbo-track="reload">
<link rel="stylesheet" href="{{ asset('css/kerjasama/repositori.css') }}" data-turbo-track="reload">

<main id="mainContent" class="dk-page">
    {{-- Header Section --}}
    <section class="ud-topbar">
        <div class="ud-hero-copy">
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <a href="{{ route('unit.dashboard') }}">Beranda</a>
                <span>/</span>
                <span>Pengajuan Perpanjangan</span>
            </div>
            <div class="ud-title-row">
                <span class="ud-title-icon"><i class="fas fa-clock-rotate-left"></i></span>
                <div class="ud-title-copy">
                    <h2 class="ud-title" id="pageTitle">Pengajuan Perpanjangan Kerja Sama</h2>
                    <p class="ud-subtitle" id="pageDesc">
                        Kelola dan selesaikan berkas perpanjangan kerja sama yang telah disetujui oleh Pimpinan untuk
                        <strong>{{ $unitName }}</strong>.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats Cards --}}
    <section class="dk-stats-grid pp-stats-grid" aria-label="Ringkasan pengajuan perpanjangan">
        <div class="dk-stat-card dk-stat-total">
            <div class="dk-stat-icon"><i class="fas fa-layer-group"></i></div>
            <div>
                <span class="dk-stat-label">Total Disetujui</span>
                <strong class="dk-stat-value">{{ number_format($statsData['total'], 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="dk-stat-card dk-stat-warning">
            <div class="dk-stat-icon"><i class="fas fa-hourglass-half"></i></div>
            <div>
                <span class="dk-stat-label">Menunggu / Draf</span>
                <strong class="dk-stat-value">{{ number_format($statsData['menunggu'], 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="dk-stat-card dk-stat-active">
            <div class="dk-stat-icon"><i class="fas fa-circle-check"></i></div>
            <div>
                <span class="dk-stat-label">Selesai & Aktif</span>
                <strong class="dk-stat-value">{{ number_format($statsData['selesai'], 0, ',', '.') }}</strong>
            </div>
        </div>
    </section>

    {{-- Table & Management Card (Identical structure & styling to dkerjasama.blade.php) --}}
    <div class="card um-card dk-card">
        <div class="card-header um-header dk-card-header">
            <div class="um-title dk-card-title">
                <span class="dk-title-icon"><i class="fas fa-folder-open"></i></span>
                <span>
                    <strong>Daftar Pengajuan Perpanjangan</strong>
                    <small id="dkerjasamaCount">{{ $submissionsList->count() }} data ditemukan</small>
                </span>
            </div>

            <div class="dk-card-tools">
                <form method="GET" action="{{ route('unit.pengajuan_perpanjangan') }}" id="filterFormPerpanjangan">
                    <div class="submission-filter-dropdown" x-data="{
                        open: false,
                        selectedValue: '{{ request('status_progres', '') }}',
                        selectedLabel: '{{ request('status_progres') == 'menunggu' ? 'Menunggu Draf' : (request('status_progres') == 'selesai' ? 'Selesai & Aktif' : 'Semua Status') }}',
                        select(val, label) {
                            this.selectedValue = val;
                            this.selectedLabel = label;
                            this.open = false;
                            this.$refs.statusInput.value = val;
                            this.$refs.statusInput.form.submit();
                        }
                    }" @click.outside="open = false">
                        <input type="hidden" name="status_progres" x-ref="statusInput"
                            value="{{ request('status_progres', '') }}">
                        <button type="button" class="submission-filter-trigger" @click="open = !open"
                            :aria-expanded="open.toString()" aria-haspopup="listbox" aria-label="Filter status progres">
                            <span class="submission-filter-icon"><i class="fas fa-filter"></i></span>
                            <span class="submission-filter-label" x-text="selectedLabel"></span>
                            <i class="fas fa-chevron-down submission-filter-chevron" :class="{ 'is-open': open }"></i>
                        </button>
                        <div class="submission-filter-menu" x-show="open" x-transition.origin.top.right x-cloak
                            role="listbox">
                            <button type="button" class="submission-filter-option"
                                :class="{ 'is-selected': selectedValue === '' }"
                                :aria-selected="(selectedValue === '').toString()" @click="select('', 'Semua Status')"
                                role="option">
                                <span>Semua Status</span>
                                <i class="fas fa-check" x-show="selectedValue === ''"></i>
                            </button>
                            <button type="button" class="submission-filter-option"
                                :class="{ 'is-selected': selectedValue === 'menunggu' }"
                                :aria-selected="(selectedValue === 'menunggu').toString()"
                                @click="select('menunggu', 'Menunggu Draf')" role="option">
                                <span>🟡 Menunggu Draf</span>
                                <i class="fas fa-check" x-show="selectedValue === 'menunggu'"></i>
                            </button>
                            <button type="button" class="submission-filter-option"
                                :class="{ 'is-selected': selectedValue === 'selesai' }"
                                :aria-selected="(selectedValue === 'selesai').toString()"
                                @click="select('selesai', 'Selesai & Aktif')" role="option">
                                <span>🟢 Selesai & Aktif</span>
                                <i class="fas fa-check" x-show="selectedValue === 'selesai'"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body dk-card-body">
            <div class="table-wrap um-table-wrap dk-table-wrap">
                <table class="um-table dk-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num">#</th>
                            <th class="um-th dk-th-title" style="width: 450px; min-width: 400px;">Judul & Mitra
                                Pengajuan</th>
                            <th class="um-th">Jenis & Dokumen</th>
                            <th class="um-th" style="white-space: nowrap;">Masa Berlaku Usulan</th>
                            <th class="um-th">Status</th>
                            <th class="um-th um-th-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($submissionsList as $index => $item)
                            @php
                                $coop = $item->cooperation;
                                $isAktif = strtolower($coop?->status ?? '') === 'aktif' || strtolower($coop?->status_dokumen ?? '') === 'aktif';
                            @endphp
                            <tr class="um-row dk-row" data-row-id="{{ $item->id }}">
                                <td class="um-td um-td-num" style="vertical-align: top; padding-top: 15px;">
                                    <span class="um-num dk-num">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="um-td dk-title-cell"
                                    style="width: 450px; min-width: 400px; vertical-align: top; padding-top: 15px;">
                                    <div class="dk-doc-cell" style="white-space: normal; word-break: break-word;">
                                        <span class="dk-doc-number">#{{ $item->kode_pengajuan }}</span>
                                        <span class="dk-doc-title"
                                            style="font-weight: 700; line-height: 1.5; display: block; overflow-wrap: break-word;">{{ $item->judul_pengajuan }}</span>
                                        <span class="dk-doc-kind"
                                            style="margin-top: 4px; display: inline-flex; align-items: center; gap: 6px;">
                                            <i class="fas fa-building" style="color: var(--accent, #4f46e5);"></i>
                                            {{ $item->nama_mitra }}
                                        </span>
                                    </div>
                                </td>
                                <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                    <div class="dk-doc-cell">
                                        <span class="dk-doc-kind"
                                            style="font-weight: 700;">{{ $item->jenis ?? 'MoU' }}</span>
                                        <span class="dk-doc-number"
                                            style="margin-top: 4px;">#{{ $coop?->doc_number ?? ($item->doc_number ?: '-') }}</span>
                                    </div>
                                </td>
                                <td class="um-td" style="white-space: nowrap; vertical-align: top; padding-top: 15px;">
                                    <div class="dk-date-range-compact">
                                        <span
                                            class="date-val">{{ $item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('d M Y') : '-' }}</span>
                                        <span class="date-sep">s/d</span>
                                        <span
                                            class="date-val">{{ $item->end_date ? \Carbon\Carbon::parse($item->end_date)->format('d M Y') : '-' }}</span>
                                    </div>
                                </td>
                                <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                    @if ($isAktif)
                                        <span class="dk-status dk-status-active">
                                            <i class="fas fa-circle-check"></i>
                                            Selesai & Aktif
                                        </span>
                                    @else
                                        <span class="dk-status dk-status-warning">
                                            <i class="fas fa-clock"></i>
                                            Menunggu Draf
                                        </span>
                                    @endif
                                </td>
                                <td class="um-td um-td-aksi" style="vertical-align: top; padding-top: 12px;">
                                    <div class="um-actions dk-actions-compact">
                                        @if ($isAktif)
                                            <a href="{{ route('unit.kerjasama.edit', $coop->id) }}" class="dk-action-btn edit"
                                                title="Edit Data"
                                                style="width: auto; padding: 6px 12px; height: auto; border-radius: 8px; font-weight: 700; gap: 6px; font-size: 12px; text-decoration: none; display: inline-flex; align-items: center;">
                                                <i class="fas fa-pen-to-square"></i>
                                                <span>Edit</span>
                                            </a>
                                        @else
                                            <a href="{{ route('unit.kerjasama.create', [
                                                    'perpanjangan_dari' => $item->old_cooperation?->id,
                                                    'pengajuan_mitra_id' => $item->id
                                                ]) }}" 
                                                class="dk-action-btn edit"
                                                title="Proses Perpanjangan"
                                                style="width: auto; padding: 6px 12px; height: auto; border-radius: 8px; font-weight: 700; gap: 6px; font-size: 12px; text-decoration: none; display: inline-flex; align-items: center;">
                                                <i class="fas fa-file-pen"></i>
                                                <span>Proses</span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr data-empty>
                                <td colspan="6" class="um-empty">
                                    <div class="um-empty-state dk-empty-state">
                                        <div class="um-empty-icon dk-empty-icon">
                                            <i class="fas fa-folder-open"></i>
                                        </div>
                                        <p class="um-empty-title">Belum ada pengajuan perpanjangan</p>
                                        <p class="um-empty-sub">Pengajuan perpanjangan yang disetujui Pimpinan akan otomatis
                                            muncul di sini.</p>
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