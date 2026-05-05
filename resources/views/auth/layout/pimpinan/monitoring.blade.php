@php
    $kerjasamaList = $dataKerjasama ?? collect();
    
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
                <a href="{{ route('pimpinan.dashboard') }}" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-home"></i>
                </a>
                <span class="sep">/</span>
                <span class="current">Monitoring Kerjasama</span>
                <span class="sep">/</span>
                <span class="current">Repositori Global</span>
            </div>

            <div class="dk-hero-main">
                <div class="dk-hero-icon">
                    <i class="fas fa-desktop"></i>
                </div>
                <div>
                    <span class="dk-eyebrow">Pusat Kendali Pimpinan</span>
                    <h2 id="pageTitle">Monitoring Kerjasama</h2>
                    <p id="pageDesc">
                        Pantau seluruh dokumen kerjasama, mitra, dan status masa berlaku secara real-time dari seluruh unit.
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

    <div class="card um-card dk-card">
        <div class="card-header um-header dk-card-header">
            <div class="um-title dk-card-title">
                <span class="dk-title-icon"><i class="fas fa-folder-open"></i></span>
                <span>
                    <strong>Daftar Kerjasama Global</strong>
                    <small>{{ $kerjasamaList->count() }} data ditemukan dari seluruh unit</small>
                </span>
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
                        @forelse($kerjasamaList as $kegiatan)
                        @php
                            $status = strtolower($kegiatan->status ?? '');
                            $isExpired = in_array($status, ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa'], true);
                            $isExtended = str_contains($status, 'perpanjangan');

                            $statusClass = match (true) {
                                $status === 'aktif' => 'dk-status-active',
                                $status === 'proses' => 'dk-status-info',
                                $isExtended => 'dk-status-warning',
                                $isExpired => 'dk-status-danger',
                                $status === 'tidak aktif' => 'dk-status-muted',
                                default => 'dk-status-neutral',
                            };
                            $statusIcon = match (true) {
                                $status === 'aktif' => 'fa-circle-check',
                                $status === 'proses' => 'fa-spinner fa-spin',
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

                            $pelaksanaName = '-';
                            $pelaksanaType = '-';
                            if ($kegiatan->tipe_pelaksana === 'jurusan') {
                                $pelaksanaName = $kegiatan->jurusan?->nama_jurusan ?? '-';
                                $pelaksanaType = 'Jurusan';
                            } elseif ($kegiatan->tipe_pelaksana === 'upa') {
                                $pelaksanaName = $kegiatan->upa?->nama_upa ?? '-';
                                $pelaksanaType = 'UPA';
                            } elseif ($kegiatan->tipe_pelaksana === 'pusat') {
                                $pelaksanaName = $kegiatan->pusat?->nama_pusat ?? '-';
                                $pelaksanaType = 'Pusat';
                            }
                        @endphp
                        <tr class="um-row">
                            <td class="um-td um-td-num">
                                <span class="um-num">{{ str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="um-td">
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span class="dk-table-title">{{ $kegiatan->title }}</span>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <span class="dk-table-meta">
                                            <i class="fas fa-fingerprint"></i>
                                            {{ $kegiatan->doc_number ?: 'Tanpa Nomor' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="um-td">
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-weight: 700; color: var(--text); font-size: 13px;">{{ $pelaksanaName }}</span>
                                    <span style="font-size: 11px; color: var(--text-sub);">{{ $pelaksanaType }}</span>
                                </div>
                            </td>
                            <td class="um-td">
                                <span class="dk-table-mitra">
                                    <i class="fas fa-building"></i>
                                    {{ $kegiatan->mitra?->nama_mitra ?? '-' }}
                                </span>
                            </td>
                            <td class="um-td">
                                <div style="display: flex; flex-direction: column; gap: 2px;">
                                    <span class="dk-table-date">{{ $kegiatan->start_date?->format('d M Y') ?? '-' }}</span>
                                    <span style="font-size: 10px; color: var(--text-sub);">s/d</span>
                                    <span class="dk-table-date">{{ $kegiatan->end_date?->format('d M Y') ?? 'Selesai' }}</span>
                                </div>
                            </td>
                            <td class="um-td">
                                <span class="dk-status-tag {{ $statusClass }}">
                                    <i class="fas {{ $statusIcon }}"></i>
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="um-td um-td-aksi">
                                <div class="um-actions">
                                    <a href="{{ route('pimpinan.monitoring.detail', $kegiatan->id) }}" class="um-btn-edit" title="Lihat Detail" style="background: rgba(79,70,229,0.1); color: #4f46e5;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 100px 20px;">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 16px;">
                                    <div style="width: 80px; height: 80px; border-radius: 24px; background: var(--surface2); color: var(--text-sub); display: flex; align-items: center; justify-content: center; font-size: 32px;">
                                        <i class="fas fa-folder-open"></i>
                                    </div>
                                    <div style="text-align: center;">
                                        <h3 style="margin: 0 0 8px 0; color: var(--text);">Belum Ada Data</h3>
                                        <p style="margin: 0; color: var(--text-sub); font-size: 14px;">Sistem belum menemukan data kerjasama dari unit manapun.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ══ PAGINATION ══ --}}
            <div class="dk-table-footer">
                <div class="dk-pagination-info">
                    Menampilkan <strong x-text="startRange"></strong> - <strong x-text="endRange"></strong> dari <strong x-text="totalRows"></strong> data
                </div>
                <div class="dk-pagination-nav">
                    <button class="dk-pagination-btn" :disabled="currentPage === 1" @click="currentPage--">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <template x-for="page in totalPages" :key="page">
                        <button class="dk-pagination-btn" :class="currentPage === page ? 'active' : ''" @click="currentPage = page" x-text="page"></button>
                    </template>
                    <button class="dk-pagination-btn" :disabled="currentPage === totalPages" @click="currentPage++">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>
