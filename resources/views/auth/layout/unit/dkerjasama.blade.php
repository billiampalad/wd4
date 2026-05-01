@php
    $kerjasamaList = $kerjasamaUnit ?? collect();
    if (! $kerjasamaList instanceof \Illuminate\Support\Collection) {
        $kerjasamaList = collect($kerjasamaList);
    }

    $unitName = auth()->user()->profile?->unitKerja?->nama_unit_pelaksana ?? 'Unit Kerja';
    $totalKerjasama = $kerjasamaList->count();
    $aktifCount = $kerjasamaList->filter(fn ($item) => strtolower($item->status ?? '') === 'aktif')->count();
    $perpanjanganCount = $kerjasamaList->filter(fn ($item) => str_contains(strtolower($item->status ?? ''), 'perpanjangan'))->count();
    $expiredCount = $kerjasamaList->filter(function ($item) {
        $status = strtolower($item->status ?? '');
        return in_array($status, ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa'], true);
    })->count();
@endphp

<!-- Main Content -->
<main id="mainContent" class="dk-page">
    <section class="dk-hero">
        <div class="dk-hero-content">
            <div class="breadcrumb dk-breadcrumb">
                <a href="{{ route('unit.dashboard') }}" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-home"></i>
                </a>
                <span class="sep">/</span>
                <a href="{{ route('unit.dkerjasama') }}" style="text-decoration: none; color: inherit;">
                    <span class="current" id="breadcrumbCurrent">Data Kerjasama</span>
                </a>
                <span class="sep">/</span>
                <a href="{{ route('unit.dkerjasama') }}" style="text-decoration: none; color: inherit;">
                    <span class="current">Repositori</span>
                </a>
            </div>

            <div class="dk-hero-main">
                <div class="dk-hero-icon">
                    <i class="fas fa-handshake-angle"></i>
                </div>
                <div>
                    <span class="dk-eyebrow">Repositori Unit</span>
                    <h2 id="pageTitle">Data Kerjasama</h2>
                    <p id="pageDesc">
                        Pantau dokumen, mitra, masa berlaku, dan status kerjasama untuk
                        <strong>{{ $unitName }}</strong>.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="dk-stats-grid" aria-label="Ringkasan data kerjasama">
        <div class="dk-stat-card dk-stat-total">
            <div class="dk-stat-icon"><i class="fas fa-layer-group"></i></div>
            <div>
                <span class="dk-stat-label">Total Dokumen</span>
                <strong>{{ number_format($totalKerjasama) }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-active">
            <div class="dk-stat-icon"><i class="fas fa-circle-check"></i></div>
            <div>
                <span class="dk-stat-label">Aktif</span>
                <strong>{{ number_format($aktifCount) }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-warning">
            <div class="dk-stat-icon"><i class="fas fa-clock-rotate-left"></i></div>
            <div>
                <span class="dk-stat-label">Perpanjangan</span>
                <strong>{{ number_format($perpanjanganCount) }}</strong>
            </div>
        </div>
        <div class="dk-stat-card dk-stat-danger">
            <div class="dk-stat-icon"><i class="fas fa-calendar-xmark"></i></div>
            <div>
                <span class="dk-stat-label">Kadaluarsa</span>
                <strong>{{ number_format($expiredCount) }}</strong>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="dk-alert dk-alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="dk-alert dk-alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="card um-card dk-card">
        <div class="card-header um-header dk-card-header">
            <div class="um-title dk-card-title">
                <span class="dk-title-icon"><i class="fas fa-folder-open"></i></span>
                <span>
                    <strong>Daftar Kerjasama</strong>
                    <small>{{ $kerjasamaList->count() }} data ditemukan</small>
                </span>
            </div>

            <div class="dk-card-tools">
                <a href="{{ route('unit.kerjasama.create') }}" class="dk-primary-btn">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Data</span>
                </a>
            </div>
        </div>

        <div class="card-body dk-card-body" x-data="{ 
            currentPage: 1, 
            perPage: 10,
            totalRows: {{ $kerjasamaList->count() }},
            get totalPages() { return Math.ceil(this.totalRows / this.perPage); },
            get startRange() { return (this.currentPage - 1) * this.perPage + 1; },
            get endRange() { return Math.min(this.currentPage * this.perPage, this.totalRows); }
        }">
            <div class="table-wrap um-table-wrap dk-table-wrap">
                <table class="um-table dk-table">
                    <thead>
                        <tr>
                            <th class="um-th um-th-num">#</th>
                            <th class="um-th dk-th-title" style="width: 450px; min-width: 400px;">Judul Kerjasama</th>
                            <th class="um-th">Unit Pelaksana</th>
                            <th class="um-th">Mitra</th>
                            <th class="um-th" style="white-space: nowrap;">Masa Berlaku</th>
                            <th class="um-th">Status</th>
                            <th class="um-th um-th-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kerjasamaList as $index => $kegiatan)
                            @php
                                $status = strtolower($kegiatan->status ?? '');
                                $isExpired = in_array($status, ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa'], true);
                                $isExtended = str_contains($status, 'perpanjangan');
                                $statusValue = $isExtended ? 'dalam perpanjangan' : ($isExpired ? 'kadarluarsa' : $status);
                                
                                $statusClass = match (true) {
                                    $status === 'aktif' => 'dk-status-active',
                                    $isExtended => 'dk-status-warning',
                                    $isExpired => 'dk-status-danger',
                                    $status === 'tidak aktif' => 'dk-status-muted',
                                    default => 'dk-status-neutral',
                                };
                                $statusIcon = match (true) {
                                    $status === 'aktif' => 'fa-circle-check',
                                    $isExtended => 'fa-clock',
                                    $isExpired => 'fa-circle-xmark',
                                    $status === 'tidak aktif' => 'fa-circle-minus',
                                    default => 'fa-circle-info',
                                };
                                $statusLabel = match (true) {
                                    $status === 'aktif' => 'Aktif',
                                    $isExtended => 'Perpanjangan',
                                    $isExpired => 'Kadarluarsa',
                                    $status === 'tidak aktif' => 'Tidak Aktif',
                                    $status !== '' => ucwords($status),
                                    default => 'Belum Diatur',
                                };

                                $pelaksanaIcon = 'fa-building';
                                $pelaksanaClass = 'dk-entity-indigo';
                                $pelaksanaName = '-';
                                if ($kegiatan->tipe_pelaksana === 'jurusan') {
                                    $pelaksanaIcon = 'fa-microchip';
                                    $pelaksanaClass = 'dk-entity-indigo';
                                    $pelaksanaName = $kegiatan->jurusan?->nama_jurusan ?? '-';
                                } elseif ($kegiatan->tipe_pelaksana === 'upa') {
                                    $pelaksanaIcon = 'fa-building-columns';
                                    $pelaksanaClass = 'dk-entity-cyan';
                                    $pelaksanaName = $kegiatan->upa?->nama_upa ?? '-';
                                } elseif ($kegiatan->tipe_pelaksana === 'pusat') {
                                    $pelaksanaIcon = 'fa-landmark';
                                    $pelaksanaClass = 'dk-entity-violet';
                                    $pelaksanaName = $kegiatan->pusat?->nama_pusat ?? '-';
                                }

                                $mulai = $kegiatan->start_date?->format('d M Y');
                                $selesai = $kegiatan->end_date?->format('d M Y');
                                $docNumber = $kegiatan->doc_number ?? '';
                                $title = $kegiatan->title ?? '';
                                $mitraName = $kegiatan->mitra?->nama_mitra ?? '';
                            @endphp
                            <tr class="um-row dk-row">
                                <td class="um-td um-td-num" style="vertical-align: top; padding-top: 15px;">
                                    <span class="um-num dk-num">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="um-td dk-title-cell" style="width: 450px; min-width: 400px; vertical-align: top; padding-top: 15px;">
                                    <div class="dk-doc-cell" style="white-space: normal; word-break: break-word;">
                                        <span class="dk-doc-number">#{{ $docNumber ?: '-' }}</span>
                                        <span class="dk-doc-title" style="font-weight: 700; line-height: 1.5; display: block; overflow-wrap: break-word;">{{ $title ?: '-' }}</span>
                                        <span class="dk-doc-kind">{{ $kegiatan->jenis ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                    <div class="dk-entity" style="align-items: flex-start;">
                                        <span class="dk-entity-icon {{ $pelaksanaClass }}" style="flex-shrink: 0;">
                                            <i class="fas {{ $pelaksanaIcon }}"></i>
                                        </span>
                                        <span class="dk-entity-text" style="padding-top: 4px;">{{ $pelaksanaName }}</span>
                                    </div>
                                </td>
                                <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                    <div class="dk-entity" style="align-items: flex-start;">
                                        <span class="dk-entity-icon dk-entity-emerald" style="flex-shrink: 0;">
                                            <i class="fas fa-building"></i>
                                        </span>
                                        <span class="dk-entity-text" style="padding-top: 4px;">{{ $mitraName ?: '-' }}</span>
                                    </div>
                                </td>
                                <td class="um-td" style="white-space: nowrap; vertical-align: top; padding-top: 15px;">
                                    <div class="dk-date-range-compact">
                                        <span class="date-val">{{ $mulai ?? '-' }}</span>
                                        <span class="date-sep">s/d</span>
                                        <span class="date-val">{{ $selesai ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="um-td" style="vertical-align: top; padding-top: 15px;">
                                    <span class="dk-status {{ $statusClass }}">
                                        <i class="fas {{ $statusIcon }}"></i>
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="um-td um-td-aksi" style="vertical-align: top; padding-top: 12px;">
                                    <div class="um-actions dk-actions-compact">
                                        <a href="{{ route('unit.kerjasama.show', $kegiatan->id) }}" class="dk-action-btn view" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('unit.kerjasama.edit', $kegiatan->id) }}" class="dk-action-btn edit" title="Edit">
                                            <i class="fas fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('unit.kerjasama.destroy', $kegiatan->id) }}" method="POST"
                                            class="dk-delete-form" style="display: inline;"
                                            onsubmit="return confirm('Yakin ingin menghapus data kerjasama ini?')">
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
                                <td colspan="7" class="um-empty">
                                    <div class="um-empty-state dk-empty-state">
                                        <div class="um-empty-icon dk-empty-icon">
                                            <i class="fas fa-folder-open"></i>
                                        </div>
                                        <p class="um-empty-title">Belum ada data kerjasama</p>
                                        <p class="um-empty-sub">Tambahkan dokumen pertama agar repositori unit mulai terisi.</p>
                                        <a href="{{ route('unit.kerjasama.create') }}" class="dk-empty-btn">
                                            <i class="fas fa-plus"></i>
                                            Tambah Data
                                        </a>
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
