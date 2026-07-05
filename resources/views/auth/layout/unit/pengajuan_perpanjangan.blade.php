@php
    $submissionsList = $submissions ?? collect();
    $statsData = $stats ?? ['total' => 0, 'menunggu' => 0, 'selesai' => 0];
    $unitName = auth()->user()->profile?->unitKerja?->nama_unit_pelaksana ?? 'Unit Kerja';
@endphp

<link rel="stylesheet" href="{{ asset('css/auth/unit/institusi.css') }}" data-turbo-track="reload">
<link rel="stylesheet" href="{{ asset('css/kerjasama/repositori.css') }}" data-turbo-track="reload">

<main id="mainContent" class="dk-page">
    {{-- Header Section (Standard Unit Topbar Style) --}}
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

    {{-- Stats Cards Section --}}
    <section class="dk-stats-grid" aria-label="Ringkasan pengajuan perpanjangan">
        <div class="dk-stat-card dk-stat-total">
            <div class="dk-stat-icon"><i class="fas fa-layer-group"></i></div>
            <div>
                <span class="dk-stat-label">Total Disetujui Pimpinan</span>
                <strong class="dk-stat-value">{{ number_format($statsData['total'], 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="dk-stat-card dk-stat-warning">
            <div class="dk-stat-icon" style="background: rgba(245, 158, 11, 0.15); color: #d97706;"><i class="fas fa-hourglass-half"></i></div>
            <div>
                <span class="dk-stat-label">Menunggu / Draf Berkas</span>
                <strong class="dk-stat-value" style="color: #d97706;">{{ number_format($statsData['menunggu'], 0, ',', '.') }}</strong>
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

    {{-- Table & Management Card --}}
    <section class="dk-card">
        <div class="dk-card-header" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
            <div class="dk-card-title">
                <i class="fas fa-file-signature" style="color:var(--brand-primary); font-size:18px;"></i>
                <div>
                    <h3 style="font-size:16px; font-weight:700; color:var(--text);">Daftar Pengajuan Perpanjangan</h3>
                    <p style="font-size:12px; color:var(--text-sub);">Pilih pengajuan untuk melengkapi nomor dokumen & berkas perpanjangan baru</p>
                </div>
            </div>

            {{-- Filter Status Dropdown (Tanpa Search Input) --}}
            <div class="dk-filter-group" style="display:flex; align-items:center; gap:12px;">
                <form method="GET" action="{{ route('unit.pengajuan_perpanjangan') }}" id="filterFormPerpanjangan" style="display:flex; align-items:center; gap:8px;">
                    <label for="statusProgresFilter" style="font-size:13px; font-weight:600; color:var(--text-sub);">Filter Status:</label>
                    <select name="status_progres" id="statusProgresFilter" class="dk-select" onchange="this.form.submit()" style="padding:8px 14px; border-radius:10px; border:1px solid var(--border); font-size:13px; font-weight:600; background:var(--surface); color:var(--text); cursor:pointer;">
                        <option value="" {{ request('status_progres') == '' ? 'selected' : '' }}>Semua Status Progres</option>
                        <option value="menunggu" {{ request('status_progres') == 'menunggu' ? 'selected' : '' }}>🟡 Menunggu Draf / Berkas</option>
                        <option value="selesai" {{ request('status_progres') == 'selesai' ? 'selected' : '' }}>🟢 Selesai & Aktif</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="dk-table-wrapper" style="overflow-x:auto; margin-top:16px;">
            <table class="dk-table" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:2px solid var(--border); text-align:left;">
                        <th style="padding:14px; font-size:12px; font-weight:700; text-transform:uppercase; color:var(--text-sub);">No</th>
                        <th style="padding:14px; font-size:12px; font-weight:700; text-transform:uppercase; color:var(--text-sub);">Pengajuan & Mitra</th>
                        <th style="padding:14px; font-size:12px; font-weight:700; text-transform:uppercase; color:var(--text-sub);">Jenis & Dok. Lama</th>
                        <th style="padding:14px; font-size:12px; font-weight:700; text-transform:uppercase; color:var(--text-sub);">Usulan Periode Baru</th>
                        <th style="padding:14px; font-size:12px; font-weight:700; text-transform:uppercase; color:var(--text-sub);">Status Progres</th>
                        <th style="padding:14px; font-size:12px; font-weight:700; text-transform:uppercase; color:var(--text-sub); text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($submissionsList as $index => $item)
                        @php
                            $coop = $item->cooperation;
                            $isAktif = strtolower($coop?->status ?? '') === 'aktif' || strtolower($coop?->status_dokumen ?? '') === 'aktif';
                        @endphp
                        <tr style="border-bottom:1px solid var(--border); transition:background 0.2s;">
                            <td style="padding:14px; font-size:13px; font-weight:600; color:var(--text-sub);">{{ $index + 1 }}</td>
                            <td style="padding:14px;">
                                <div style="display:flex; flex-direction:column; gap:4px;">
                                    <span style="font-size:14px; font-weight:700; color:var(--text);">{{ $item->nama_mitra }}</span>
                                    <small style="font-size:12px; color:var(--text-sub); font-family:var(--font-mono, monospace);">{{ $item->kode_pengajuan }}</small>
                                    <span style="font-size:13px; color:var(--text); font-weight:500;">{{ $item->judul_pengajuan }}</span>
                                </div>
                            </td>
                            <td style="padding:14px;">
                                <div style="display:flex; flex-direction:column; gap:4px;">
                                    <span class="dk-badge dk-badge-indigo" style="align-self:flex-start; font-size:11px; padding:3px 8px; border-radius:6px; font-weight:700;">
                                        {{ $item->jenis ?? 'MoU' }}
                                    </span>
                                    <span style="font-size:12px; color:var(--text-sub);">
                                        <i class="fas fa-hashtag" style="font-size:10px;"></i> {{ $item->doc_number ?: '-' }}
                                    </span>
                                </div>
                            </td>
                            <td style="padding:14px;">
                                <div style="display:flex; flex-direction:column; gap:2px; font-size:12px; color:var(--text);">
                                    <span><i class="far fa-calendar-alt" style="color:var(--brand-primary); margin-right:4px;"></i> {{ $item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('d M Y') : '-' }}</span>
                                    <span><i class="far fa-calendar-check" style="color:#10b981; margin-right:4px;"></i> {{ $item->end_date ? \Carbon\Carbon::parse($item->end_date)->format('d M Y') : '-' }}</span>
                                </div>
                            </td>
                            <td style="padding:14px;">
                                @if ($isAktif)
                                    <span style="display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border-radius:20px; font-size:12px; font-weight:700; background:rgba(16,185,129,0.12); color:#047857; border:1px solid rgba(16,185,129,0.2);">
                                        <i class="fas fa-check-circle"></i> Selesai & Aktif
                                    </span>
                                @else
                                    <span style="display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border-radius:20px; font-size:12px; font-weight:700; background:rgba(245,158,11,0.12); color:#b45309; border:1px solid rgba(245,158,11,0.2);">
                                        <i class="fas fa-hourglass-half"></i> Menunggu Draf / Berkas
                                    </span>
                                @endif
                            </td>
                            <td style="padding:14px; text-align:center;">
                                <button type="button" class="btn-proses-perpanjangan" 
                                    data-id="{{ $item->id }}"
                                    data-mitra="{{ $item->nama_mitra }}"
                                    data-kode="{{ $item->kode_pengajuan }}"
                                    data-judul="{{ $item->judul_pengajuan }}"
                                    data-jenis="{{ $item->jenis }}"
                                    data-docnumber="{{ $coop->doc_number ?? $item->doc_number }}"
                                    data-startdate="{{ $coop->start_date ? \Carbon\Carbon::parse($coop->start_date)->format('Y-m-d') : ($item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('Y-m-d') : '') }}"
                                    data-enddate="{{ $coop->end_date ? \Carbon\Carbon::parse($coop->end_date)->format('Y-m-d') : ($item->end_date ? \Carbon\Carbon::parse($item->end_date)->format('Y-m-d') : '') }}"
                                    data-tujuan="{{ $item->tujuan_pengajuan }}"
                                    data-filesurat="{{ $item->file_surat ? asset('storage/' . $item->file_surat) : '' }}"
                                    style="padding:8px 16px; border-radius:10px; font-size:13px; font-weight:700; border:none; background:linear-gradient(135deg, #2563eb, #1d4ed8); color:#fff; cursor:pointer; box-shadow:0 2px 8px rgba(37,99,235,0.25); display:inline-flex; align-items:center; gap:6px; transition:transform 0.15s ease, box-shadow 0.15s ease;">
                                    <i class="fas fa-edit"></i>
                                    <span>{{ $isAktif ? 'Edit Data' : 'Proses Draf' }}</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:40px; text-align:center; color:var(--text-sub);">
                                <div style="display:flex; flex-direction:column; align-items:center; gap:12px;">
                                    <i class="fas fa-folder-open" style="font-size:36px; opacity:0.4;"></i>
                                    <p style="font-size:14px; font-weight:600;">Belum ada pengajuan perpanjangan yang disetujui Pimpinan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>

{{-- MODAL PROSES PERPANJANGAN --}}
<div id="modalProsesPerpanjangan" class="modal-backdrop" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(15, 23, 42, 0.65); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:16px;">
    <div class="modal-content" style="background:var(--surface); border:1px solid var(--border); border-radius:20px; max-width:680px; width:100%; max-height:90vh; overflow-y:auto; box-shadow:0 20px 40px rgba(0,0,0,0.3); animation:modalSlideUp 0.25s ease;">
        <div class="modal-header" style="padding:20px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:40px; height:40px; border-radius:12px; background:rgba(37,99,235,0.1); color:#2563eb; display:flex; align-items:center; justify-content:center; font-size:18px;">
                    <i class="fas fa-file-pen"></i>
                </div>
                <div>
                    <h3 style="font-size:17px; font-weight:700; color:var(--text); margin:0;">Proses Berkas Perpanjangan</h3>
                    <p style="font-size:12px; color:var(--text-sub); margin:0;" id="modalSubtitleMitra">Lengkapi nomor dokumen baru & sahkan perpanjangan</p>
                </div>
            </div>
            <button type="button" onclick="closeProsesModal()" style="background:none; border:none; color:var(--text-sub); font-size:18px; cursor:pointer; width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <form id="formProsesPerpanjangan" method="POST" action="" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="status_aksi" id="inputStatusAksi" value="aktif">

            <div class="modal-body" style="padding:24px; display:grid; gap:20px;">
                {{-- Ringkasan Pengajuan --}}
                <div style="padding:16px; border-radius:14px; background:var(--surface2, rgba(241,245,249,0.5)); border:1px solid var(--border); display:grid; gap:8px;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span id="modalKodeSubmission" style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:var(--brand-primary);"></span>
                        <a id="modalFileSuratLink" href="#" target="_blank" style="display:none; font-size:12px; color:#2563eb; font-weight:600; text-decoration:none;">
                            <i class="fas fa-file-pdf"></i> Lihat Surat Permohonan
                        </a>
                    </div>
                    <h4 id="modalJudulPengajuan" style="font-size:14px; font-weight:700; color:var(--text); margin:0;"></h4>
                    <p id="modalTujuanPengajuan" style="font-size:12px; color:var(--text-sub); margin:0; line-height:1.5;"></p>
                </div>

                {{-- Form Inputs --}}
                <div style="display:grid; gap:16px;">
                    <div>
                        <label style="display:block; font-size:13px; font-weight:700; color:var(--text); margin-bottom:6px;">
                            Nomor Dokumen Perpanjangan Baru <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="text" name="doc_number" id="inputDocNumber" required placeholder="Contoh: 045/PKS/POLIMDO/2026" style="width:100%; padding:10px 14px; border-radius:10px; border:1px solid var(--border); background:var(--surface); color:var(--text); font-size:13px; font-weight:600;">
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div>
                            <label style="display:block; font-size:13px; font-weight:700; color:var(--text); margin-bottom:6px;">
                                Tanggal Mulai Periode Baru <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="date" name="start_date" id="inputStartDate" required style="width:100%; padding:10px 14px; border-radius:10px; border:1px solid var(--border); background:var(--surface); color:var(--text); font-size:13px; font-weight:600;">
                        </div>

                        <div>
                            <label style="display:block; font-size:13px; font-weight:700; color:var(--text); margin-bottom:6px;">
                                Tanggal Selesai Periode Baru <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="date" name="end_date" id="inputEndDate" required style="width:100%; padding:10px 14px; border-radius:10px; border:1px solid var(--border); background:var(--surface); color:var(--text); font-size:13px; font-weight:600;">
                        </div>
                    </div>

                    <div>
                        <label style="display:block; font-size:13px; font-weight:700; color:var(--text); margin-bottom:6px;">
                            Unggah Dokumen Kerjasama Baru (PKS / MoU) <small style="color:var(--text-sub); font-weight:400;">(.pdf, maks 10MB)</small>
                        </label>
                        <input type="file" name="file_dokumen" accept=".pdf,.doc,.docx" style="width:100%; padding:8px 12px; border-radius:10px; border:1px solid var(--border); background:var(--surface); color:var(--text); font-size:12px;">
                    </div>

                    <div>
                        <label style="display:block; font-size:13px; font-weight:700; color:var(--text); margin-bottom:6px;">
                            Catatan / Pesan Tambahan <small style="color:var(--text-sub); font-weight:400;">(Opsional)</small>
                        </label>
                        <textarea name="pesan_tambahan" rows="2" placeholder="Catatan mengenai pelengkapan berkas perpanjangan..." style="width:100%; padding:10px 14px; border-radius:10px; border:1px solid var(--border); background:var(--surface); color:var(--text); font-size:13px; font-family:inherit;"></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="padding:16px 24px; border-top:1px solid var(--border); display:flex; align-items:center; justify-content:flex-end; gap:12px; background:var(--surface2, rgba(248,250,252,0.5)); border-bottom-left-radius:20px; border-bottom-right-radius:20px;">
                <button type="button" onclick="closeProsesModal()" style="padding:10px 18px; border-radius:10px; font-size:13px; font-weight:600; border:1px solid var(--border); background:var(--surface); color:var(--text); cursor:pointer;">
                    Batal
                </button>
                <button type="button" onclick="submitPerpanjanganForm('draf')" style="padding:10px 18px; border-radius:10px; font-size:13px; font-weight:600; border:1px solid #cbd5e1; background:#f1f5f9; color:#334155; cursor:pointer;">
                    <i class="fas fa-save" style="margin-right:4px;"></i> Simpan Draf
                </button>
                <button type="button" onclick="submitPerpanjanganForm('aktif')" style="padding:10px 20px; border-radius:10px; font-size:13px; font-weight:700; border:none; background:linear-gradient(135deg, #10b981, #059669); color:#fff; cursor:pointer; box-shadow:0 2px 10px rgba(16,185,129,0.3);">
                    <i class="fas fa-check-circle" style="margin-right:4px;"></i> Sahkan & Aktifkan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes modalSlideUp {
    from { opacity: 0; transform: translateY(20px) scale(0.98); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
</style>
