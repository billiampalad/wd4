@php
    $summaryCards = [
        [
            'label' => 'Total Kerjasama Unit',
            'value' => $totalKerjasama ?? 0,
            'hint' => 'Dokumen yang melibatkan unit ini',
            'icon' => 'fa-layer-group',
            'tone' => 'blue',
        ],
        [
            'label' => 'Menunggu Validasi',
            'value' => $menungguValidasi ?? 0,
            'hint' => 'Menunggu evaluasi Pimpinan',
            'icon' => 'fa-hourglass-half',
            'tone' => 'amber',
        ],
        [
            'label' => 'Dokumen Kadaluarsa',
            'value' => $dokumenKadaluarsa ?? 0,
            'hint' => 'Butuh tindak lanjut arsip/perpanjangan',
            'icon' => 'fa-triangle-exclamation',
            'tone' => 'red',
        ],
        [
            'label' => 'Laporan Belum Diunggah',
            'value' => $laporanBelumDiunggah ?? 0,
            'hint' => 'Belum memiliki link Drive',
            'icon' => 'fa-cloud-arrow-up',
            'tone' => 'slate',
        ],
    ];
@endphp

<link rel="stylesheet" href="{{ asset('css/auth/dashboard.css') }}" data-turbo-track="reload">

<main id="mainContent" class="unitdash">
    <section class="ud-topbar">
        <div>
            <div class="ud-breadcrumb">
                <i class="fas fa-home"></i>
                <span>/</span>
                <span>Dashboard Unit Kerja</span>
            </div>
            <h2 class="ud-title">Operasional Kerjasama</h2>
            <p class="ud-subtitle">
                {{ $unitName ?? auth()->user()->profile?->unitKerja?->nama_unit_pelaksana ?? 'Unit Kerja' }}
                <span style="color:#94a3b8;">/</span>
                {{ now()->format('d M Y') }}
            </p>
        </div>
        <div class="ud-live-chip">
            <span class="ud-dot"></span>
            <span>Operational Control</span>
        </div>
    </section>

    <section class="ud-summary">
        @foreach($summaryCards as $card)
            <article class="ud-card ud-tone-{{ $card['tone'] }}">
                <div class="ud-card-top">
                    <div class="ud-icon"><i class="fas {{ $card['icon'] }}"></i></div>
                </div>
                <div class="ud-metric-value">{{ number_format($card['value']) }}</div>
                <div class="ud-metric-label">{{ $card['label'] }}</div>
                <div class="ud-metric-hint">{{ $card['hint'] }}</div>
            </article>
        @endforeach
    </section>

    <section class="ud-bento">
        <article class="ud-panel">
            <div class="ud-panel-head">
                <div>
                    <h3 class="ud-panel-title">Project Progress IA</h3>
                    <p class="ud-panel-desc">Realisasi target dan tracking kontrak implementasi.</p>
                </div>
                <span class="ud-type-badge"><i class="fas fa-file-invoice"></i> IA</span>
            </div>

            <div class="ud-progress-layout">
                <div class="ud-progress-ring">
                    <div class="ud-ring" style="--progress: {{ $realisasiTargetPercent ?? 0 }};">
                        <span>{{ $realisasiTargetPercent ?? 0 }}%</span>
                    </div>
                </div>

                <div class="ud-track-list">
                    <div class="ud-track-item">
                        <div>
                            <div class="ud-track-label">Tujuan IA</div>
                            <div class="ud-small">Target yang tercatat</div>
                        </div>
                        <div class="ud-track-value">{{ number_format($tujuanCount ?? 0) }}</div>
                    </div>
                    <div class="ud-track-item">
                        <div>
                            <div class="ud-track-label">Volume Luaran</div>
                            <div class="ud-small">Luaran yang sudah diisi</div>
                        </div>
                        <div class="ud-track-value">{{ number_format($volumeCount ?? 0) }}</div>
                    </div>
                    <div class="ud-track-item">
                        <div>
                            <div class="ud-track-label">Financial Tracking</div>
                            <div class="ud-small">Total nilai kontrak unit</div>
                        </div>
                        <div class="ud-track-value">Rp {{ number_format($totalNilaiKontrak ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </article>

        <article class="ud-panel">
            <div class="ud-panel-head">
                <div>
                    <h3 class="ud-panel-title">Upcoming Deadlines</h3>
                    <p class="ud-panel-desc">Dokumen dengan masa berlaku tersisa maksimal 30 hari.</p>
                </div>
                <span class="ud-status-badge is-pending"><i class="fas fa-clock"></i> 30 hari</span>
            </div>

            <div class="ud-deadlines">
                @forelse($upcomingDeadlines ?? [] as $deadline)
                    @php
                        $daysLeft = now()->startOfDay()->diffInDays($deadline->end_date->copy()->startOfDay());
                    @endphp
                    <div class="ud-deadline-item">
                        <div class="ud-daybox">{{ $daysLeft }}</div>
                        <div style="min-width:0;">
                            <div class="ud-deadline-title">{{ $deadline->title ?? '-' }}</div>
                            <div class="ud-deadline-meta">
                                {{ $deadline->mitra?->nama_mitra ?? 'Mitra belum diisi' }} - berakhir {{ $deadline->end_date?->format('d M Y') }}
                            </div>
                        </div>
                        <a class="ud-link-btn" href="{{ route('unit.kerjasama.show', $deadline->id) }}" title="Detail">
                            <i class="fas fa-arrow-up-right-from-square"></i>
                        </a>
                    </div>
                @empty
                    <div class="ud-empty">Tidak ada deadline kritis dalam 30 hari.</div>
                @endforelse
            </div>
        </article>
    </section>

    <section class="ud-panel ud-table-panel">
        <div class="ud-table-head">
            <div>
                <h3 class="ud-panel-title">Data Teknis Kerjasama</h3>
                <p class="ud-panel-desc">Filtered view, quick edit link dokumen, dan status operasional.</p>
            </div>
            <div class="ud-tabs" aria-label="Filter tipe dokumen">
                @foreach(['Semua', 'MoU', 'MoA', 'IA'] as $filter)
                    <button type="button" class="ud-tab {{ $loop->first ? 'is-active' : '' }}" data-filter-tab="{{ $filter === 'Semua' ? 'all' : $filter }}">
                        {{ $filter }}
                        <span>({{ $jenisCounts[$filter] ?? 0 }})</span>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="ud-table-wrap">
            <table class="ud-table">
                <thead>
                    <tr>
                        <th>Dokumen</th>
                        <th>Tipe</th>
                        <th>Mitra</th>
                        <th>Status</th>
                        <th>Deadline</th>
                        <th>Link Dokumen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kerjasamaTable ?? [] as $item)
                        @php
                            $jenisLower = strtolower($item->jenis ?? '');
                            $jenisShort = str_contains($jenisLower, 'mou') ? 'MoU' : (str_contains($jenisLower, 'moa') ? 'MoA' : (str_contains($jenisLower, 'ia') ? 'IA' : '-'));
                            $statusRaw = strtolower($item->status ?? '');
                            $isExpired = in_array($statusRaw, ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa'], true)
                                || ($item->end_date && now()->startOfDay()->greaterThan($item->end_date->copy()->startOfDay()));
                            $isPending = ($item->status_dokumen ?? '') === 'Menunggu Evaluasi';
                            $statusClass = $isExpired ? 'is-expired' : ($isPending ? 'is-pending' : '');
                            $statusLabel = $isExpired ? 'Kadaluarsa' : ($item->status_dokumen ?? ucfirst($item->status ?? 'Draft'));
                            $deadlineLabel = $item->end_date ? $item->end_date->format('d M Y') : '-';
                            $pjInternal = $item->pjInternal?->nama ?? '-';
                        @endphp
                        <tr data-kerjasama-row data-doc-type="{{ $jenisShort }}">
                            <td>
                                <div class="ud-doc-title">{{ $item->title ?? '-' }}</div>
                                <div class="ud-small">No. {{ $item->doc_number ?: ($item->pks_number ?: '-') }}</div>
                            </td>
                            <td>
                                <span class="ud-type-badge">{{ $jenisShort }}</span>
                            </td>
                            <td>
                                <span class="ud-mitra">
                                    <i class="fas fa-building"></i>
                                    {{ $item->mitra?->nama_mitra ?? '-' }}
                                    <span class="ud-tooltip">PJ Internal: {{ $pjInternal }}</span>
                                </span>
                            </td>
                            <td>
                                <span class="ud-status-badge {{ $statusClass }}">
                                    <i class="fas {{ $isExpired ? 'fa-triangle-exclamation' : ($isPending ? 'fa-clock' : 'fa-circle-check') }}"></i>
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ $deadlineLabel }}</strong>
                                <div class="ud-small">{{ $item->end_date ? 'Masa berlaku dokumen' : 'Belum ada tanggal' }}</div>
                            </td>
                            <td>
                                <div class="ud-link-editor" data-link-editor>
                                    <input class="ud-link-input" type="text" value="{{ $item->document_link }}" placeholder="Paste link Drive..." data-document-link-input>
                                    <button class="ud-save-btn" type="button" data-save-document-link data-update-url="{{ route('unit.kerjasama.document-link.update', $item->id) }}" title="Simpan link">
                                        <i class="fas fa-floppy-disk"></i>
                                    </button>
                                </div>
                                <span class="ud-save-state" data-save-state>{{ $item->document_link ? 'Link tersimpan' : 'Belum ada link' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="ud-empty">Belum ada data kerjasama untuk ditampilkan.</div>
                            </td>
                        </tr>
                    @endforelse
                    <tr id="unitDashNoResult" style="display:none;">
                        <td colspan="6">
                            <div class="ud-empty">Tidak ada dokumen pada filter ini.</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</main>

<script src="{{ asset('js/auth/dashboard.js') }}" data-turbo-track="reload"></script>
