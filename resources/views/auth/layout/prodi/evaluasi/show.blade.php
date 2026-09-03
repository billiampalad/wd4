@extends('auth.prodi')

@section('content')
    @php
        $docNumber = $cooperation->doc_number ?: ($cooperation->pks_number ?: '-');
        $judul = $cooperation->judul ?: ($cooperation->title ?: 'Kerja Sama Industri');
        $mName = $cooperation->mitra?->nama_mitra ?? '-';
        $statusVal = strtolower($cooperation->status ?? 'aktif');

        $statusColor = match($statusVal) {
            'aktif' => '#10b981',
            'selesai' => '#3b82f6',
            'kadaluarsa', 'tidak aktif' => '#ef4444',
            default => '#f59e0b'
        };

        $statusBg = match($statusVal) {
            'aktif' => 'rgba(16, 185, 129, 0.1)',
            'selesai' => 'rgba(59, 130, 246, 0.1)',
            'kadaluarsa', 'tidak aktif' => 'rgba(239, 68, 68, 0.1)',
            default => 'rgba(245, 158, 11, 0.1)'
        };

        $hasEvaluasi = $evaluasi !== null;
        $evaluasiScore = $evaluasi?->kualitas ? ($evaluasi->kualitas * 20) : null;
        $totalMhsTerkait = $penempatans->count();
    @endphp

    <main id="mainContent" class="dk-page">
        {{-- ── HERO TOPBAR & BREADCRUMB ── --}}
        <section class="ud-topbar">
            <div class="ud-hero-copy">
                <div class="ud-breadcrumb">
                    <i class="fas fa-home"></i>
                    <span>/</span>
                    <a href="{{ route('prodi.dashboard') }}">Beranda</a>
                    <span>/</span>
                    <a href="{{ route('prodi.evaluasi') }}">Evaluasi &amp; Laporan</a>
                    <span>/</span>
                    <span>Rincian Evaluasi</span>
                </div>
                <div class="ud-title-row">
                    <span class="ud-title-icon"><i class="fas fa-chart-pie"></i></span>
                    <div class="ud-title-copy">
                        <h2 class="ud-title" id="pageTitle">Rincian Evaluasi Pelaksanaan Kerja Sama</h2>
                        <p class="ud-subtitle" id="pageDesc">
                            Monitoring capaian luaran, penilaian evaluasi mitra industri, performa mahasiswa, dan ringkasan tindak lanjut untuk
                            <strong>{{ $prodiName }}</strong>.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        @if(session('success'))
            <div class="dk-alert dk-alert-success" style="margin-bottom: 20px;">
                <i class="fas fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="card um-card dk-card" style="width: 100%; max-width: 100%; box-sizing: border-box; border-radius: 16px; overflow: visible;">
            {{-- ── CARD HEADER ── --}}
            <div class="card-header um-header dk-card-header" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div class="um-title dk-card-title">
                    <span class="dk-title-icon"><i class="fas fa-file-contract"></i></span>
                    <div>
                        <strong style="font-size: 15px;">{{ $judul }}</strong>
                        <small style="font-family: monospace; font-size: 12px; color: var(--text-sub);">
                            No. Dokumen: {{ $docNumber }} • Mitra: {{ $mName }}
                        </small>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <button type="button" onclick="window.print()" class="rfc-btn"
                        style="padding: 8px 16px; border-radius: 10px; font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; background: var(--surface2); color: var(--text); border: 1px solid var(--border); cursor: pointer;">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </button>
                    <a href="{{ route('prodi.evaluasi') }}" class="rfc-btn"
                        style="padding: 8px 16px; border-radius: 10px; font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; background: var(--surface2); color: var(--text); border: 1px solid var(--border);">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="card-body dk-card-body" style="padding: 0; width: 100%; box-sizing: border-box; overflow: visible;">
                {{-- ═══ TWO-COLUMN LAYOUT (Identik dengan mamag/show.blade.php) ═══ --}}
                <div style="display: grid; grid-template-columns: 320px minmax(0, 1fr); gap: 24px; padding: 24px; width: 100%; max-width: 100%; box-sizing: border-box;">

                    {{-- ══ LEFT COLUMN: Masa Berlaku & Skor (Sticky) ══ --}}
                    <div style="position: sticky; top: 24px; align-self: start; min-width: 0; max-width: 100%; box-sizing: border-box;">
                        <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 16px; overflow: visible; width: 100%; box-sizing: border-box;">
                            <div x-data="{ showStatusCard: true }">
                                {{-- Card Header --}}
                                <div @click="showStatusCard = !showStatusCard"
                                    style="display: flex; align-items: center; gap: 10px; padding: 14px 18px; cursor: pointer; user-select: none; border-bottom: 1px solid var(--border); background: linear-gradient(135deg, rgba(79,70,229,0.06), rgba(124,58,237,0.04)); border-radius: 16px 16px 0 0; transition: background 0.2s;">
                                    <div
                                        style="width: 34px; height: 34px; border-radius: 9px; background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; box-shadow: 0 3px 8px rgba(79,70,229,0.25);">
                                        <i class="fas fa-shield-halved"></i>
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <h4 style="margin: 0; font-size: 13px; font-weight: 700; color: var(--text); letter-spacing: -0.01em;">
                                            Status &amp; Indikator
                                        </h4>
                                    </div>
                                    <i class="fas fa-chevron-down"
                                        style="font-size: 10px; color: var(--text-sub); transition: transform 0.3s ease; flex-shrink: 0;"
                                        :style="showStatusCard ? 'transform: rotate(180deg)' : ''"></i>
                                </div>

                                {{-- Card Body --}}
                                <div x-show="showStatusCard" x-collapse.duration.300ms style="padding: 18px; width: 100%; box-sizing: border-box;">

                                    {{-- Status Pelaksanaan --}}
                                    <div style="margin-bottom: 16px;">
                                        <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">
                                            Status Pelaksanaan
                                        </label>
                                        <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; background: {{ $statusBg }}; color: {{ $statusColor }}; border: 1px solid {{ $statusColor }}30;">
                                            <div style="width: 8px; height: 8px; border-radius: 50%; background: {{ $statusColor }};"></div>
                                            <span>{{ ucfirst($cooperation->status ?? 'Aktif') }}</span>
                                        </div>
                                    </div>

                                    {{-- Jenis & Tingkat Dokumen --}}
                                    <div style="margin-bottom: 16px;">
                                        <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">
                                            Klasifikasi Dokumen
                                        </label>
                                        <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                            <span style="padding: 4px 10px; border-radius: 6px; background: rgba(79,70,229,0.08); color: #4f46e5; font-size: 11px; font-weight: 700;">
                                                {{ $cooperation->jenis ?? 'PKS' }}
                                            </span>
                                            <span style="padding: 4px 10px; border-radius: 6px; background: var(--surface2); color: var(--text); border: 1px solid var(--border); font-size: 11px; font-weight: 600;">
                                                Tingkat {{ $cooperation->tingkat ?? 'Institusi' }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Periode Mulai --}}
                                    <div style="margin-bottom: 14px;">
                                        <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                                            Tanggal Mulai
                                        </label>
                                        <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: var(--text);">
                                            <i class="fas fa-calendar-plus" style="color: #059669; font-size: 14px;"></i>
                                            <span>{{ $cooperation->start_date ? \Carbon\Carbon::parse($cooperation->start_date)->translatedFormat('d F Y') : '-' }}</span>
                                        </div>
                                    </div>

                                    {{-- Periode Selesai --}}
                                    <div style="margin-bottom: 16px;">
                                        <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                                            Tanggal Selesai
                                        </label>
                                        <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: var(--text);">
                                            <i class="fas fa-calendar-check" style="color: #4f46e5; font-size: 14px;"></i>
                                            <span>{{ $cooperation->end_date ? \Carbon\Carbon::parse($cooperation->end_date)->translatedFormat('d F Y') : 'Selesai' }}</span>
                                        </div>
                                    </div>

                                    {{-- Skor Evaluasi Mitra --}}
                                    <div style="margin-bottom: 16px;">
                                        <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">
                                            Skor Evaluasi Kemitraan
                                        </label>
                                        @if($evaluasiScore)
                                            <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 10px; background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.25); font-size: 13px; font-weight: 800; color: #d97706;">
                                                <i class="fas fa-star"></i>
                                                <span>{{ $evaluasiScore }} / 100</span>
                                            </div>
                                        @else
                                            <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 8px; background: var(--surface2); border: 1px solid var(--border); font-size: 12px; font-weight: 600; color: var(--text-sub);">
                                                <i class="fas fa-clock"></i>
                                                <span>{{ $hasEvaluasi ? 'Tervalidasi' : 'Belum Ada Penilaian' }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Partisipasi Mahasiswa Prodi --}}
                                    <div style="margin-bottom: 16px;">
                                        <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">
                                            Partisipasi Mahasiswa Prodi
                                        </label>
                                        <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 8px; background: rgba(16,185,129,0.08); color: #059669; font-size: 12px; font-weight: 700;">
                                            <i class="fas fa-user-graduate"></i>
                                            <span>{{ $totalMhsTerkait }} Mahasiswa Ditempatkan</span>
                                        </div>
                                    </div>

                                    {{-- Divider --}}
                                    <div style="height: 1px; background: linear-gradient(90deg, transparent, var(--border), transparent); margin: 18px 0;"></div>

                                    {{-- Panduan Singkat --}}
                                    <div style="background: rgba(79,70,229,0.04); border: 1px dashed rgba(79,70,229,0.25); border-radius: 12px; padding: 14px; width: 100%; box-sizing: border-box;">
                                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                                            <i class="fas fa-circle-info" style="color: #4f46e5; margin-top: 2px; font-size: 14px; flex-shrink: 0;"></i>
                                            <div style="font-size: 12px; color: var(--text-sub); line-height: 1.5;">
                                                Evaluasi ini digunakan sebagai eviden capaian IKU-6 serta instrumen pelaporan kepuasan mitra industri.
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ══ RIGHT COLUMN: Konten Rincian Utama ══ --}}
                    <div style="min-width: 0; width: 100%; max-width: 100%; box-sizing: border-box;">
                        <div class="mc-body" style="padding: 0; width: 100%; max-width: 100%; box-sizing: border-box;">

                            {{-- ── SEKSI 1: DATA DOKUMEN & MITRA INDUSTRI ── --}}
                            <div style="margin-bottom: 24px; width: 100%; box-sizing: border-box;">
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                                    <div style="width: 4px; height: 18px; border-radius: 2px; background: linear-gradient(180deg, #4f46e5, #818cf8); flex-shrink: 0;"></div>
                                    <span style="font-weight: 700; font-size: 14px; color: var(--text); letter-spacing: 0.01em;">
                                        Informasi Dokumen &amp; Mitra Industri (DUDIKA)
                                    </span>
                                </div>

                                <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; width: 100%; box-sizing: border-box;">
                                    {{-- 1. Kartu Dokumen Kerja Sama --}}
                                    <div style="background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; padding: 18px; min-width: 0; width: 100%; box-sizing: border-box;">
                                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                                            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(79,70,229,0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                                                <i class="fas fa-file-contract"></i>
                                            </div>
                                            <div style="min-width: 0;">
                                                <small style="font-size: 11px; color: var(--text-sub); display: block;">Dokumen Kerja Sama</small>
                                                <strong style="font-size: 13px; color: var(--text); display: block; font-family: monospace;">{{ $docNumber }}</strong>
                                            </div>
                                        </div>
                                        <div style="font-size: 12px; color: var(--text-sub); line-height: 1.6;">
                                            <div><strong>Judul:</strong> {{ $judul }}</div>
                                            <div><strong>Jenis:</strong> {{ $cooperation->jenis ?? 'PKS' }} • {{ $cooperation->tingkat ?? 'Institusi' }}</div>
                                            @if($cooperation->ruang_lingkup)
                                                <div style="margin-top: 4px;"><strong>Ruang Lingkup:</strong> {{ $cooperation->ruang_lingkup }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- 2. Kartu Mitra Industri --}}
                                    <div style="background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; padding: 18px; min-width: 0; width: 100%; box-sizing: border-box;">
                                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                                            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(5,150,105,0.1); color: #059669; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                                                <i class="fas fa-building"></i>
                                            </div>
                                            <div style="min-width: 0;">
                                                <small style="font-size: 11px; color: var(--text-sub); display: block;">Mitra Industri (DUDIKA)</small>
                                                <strong style="font-size: 14px; color: var(--text); display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $mName }}</strong>
                                            </div>
                                        </div>
                                        <div style="font-size: 12px; color: var(--text-sub); line-height: 1.6;">
                                            <div><strong>Alamat / Wilayah:</strong> {{ $cooperation->mitra?->kota ?? 'Indonesia' }}</div>
                                            <div><strong>Kategori:</strong> {{ $cooperation->mitra?->kategori ?? 'Industri / Swasta' }}</div>
                                            @if($cooperation->mitra?->kontak)
                                                <div><strong>Kontak:</strong> {{ $cooperation->mitra->kontak }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ── SEKSI 2: REALISASI LUARAN & EVALUASI MITRA ── --}}
                            <div style="background: var(--surface2); border: 1px solid var(--border); border-radius: 16px; padding: 22px; margin-bottom: 24px; width: 100%; box-sizing: border-box;">
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 18px;">
                                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(79,70,229,0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                                        <i class="fas fa-clipboard-check"></i>
                                    </div>
                                    <div style="min-width: 0;">
                                        <h4 style="margin: 0; font-size: 14px; font-weight: 800; color: var(--text);">
                                            Capaian Luaran &amp; Hasil Evaluasi Kemitraan
                                        </h4>
                                        <small style="color: var(--text-sub); font-size: 12px;">Monitoring realisasi kegiatan, pencapaian target IKU, dan feedback dari mitra industri.</small>
                                    </div>
                                </div>

                                <div style="display: flex; flex-direction: column; gap: 16px; width: 100%; box-sizing: border-box;">
                                    {{-- Ringkasan Realisasi --}}
                                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 16px; width: 100%; box-sizing: border-box;">
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                            <i class="fas fa-file-lines" style="color: #4f46e5; font-size: 14px;"></i>
                                            <strong style="font-size: 13px; color: var(--text);">Ringkasan Realisasi &amp; Luaran Kegiatan:</strong>
                                        </div>
                                        <p style="margin: 0; font-size: 13px; color: var(--text); line-height: 1.6;">
                                            {{ $evaluasi?->ringkasan ?? 'Program kerja sama telah terlaksana secara aktif, mendukung integrasi kurikulum industri dan penyerapan kompetensi mahasiswa sesuai target IKU program studi.' }}
                                        </p>
                                    </div>

                                    {{-- Rekomendasi & Saran Tindak Lanjut --}}
                                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 16px; width: 100%; box-sizing: border-box;">
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                            <i class="fas fa-lightbulb" style="color: #d97706; font-size: 14px;"></i>
                                            <strong style="font-size: 13px; color: var(--text);">Rekomendasi / Saran Tindak Lanjut:</strong>
                                        </div>
                                        <p style="margin: 0; font-size: 13px; color: var(--text-sub); font-style: italic; line-height: 1.6;">
                                            "{{ $evaluasi?->saran ?? 'Kerja sama industri direkomendasikan untuk dilanjutkan dan diperluas kuota penempatan magang mahasiswa pada periode perikatan berikutnya.' }}"
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- ── SEKSI 3: MAHASISWA PRODI YANG DITEMPATKAN DI MITRA INI ── --}}
                            <div style="background: var(--surface2); border: 1px solid var(--border); border-radius: 16px; padding: 22px; margin-bottom: 24px; width: 100%; box-sizing: border-box;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(16,185,129,0.1); color: #059669; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                                            <i class="fas fa-users-viewfinder"></i>
                                        </div>
                                        <div>
                                            <h4 style="margin: 0; font-size: 14px; font-weight: 800; color: var(--text);">
                                                Mahasiswa Program Studi Terkait di Mitra Ini
                                            </h4>
                                            <small style="color: var(--text-sub); font-size: 12px;">Daftar peserta magang/kegiatan kerja sama dari prodi {{ $prodiName }}.</small>
                                        </div>
                                    </div>
                                    <span style="font-size: 12px; font-weight: 700; color: #059669; padding: 3px 10px; border-radius: 6px; background: rgba(16,185,129,0.08);">
                                        {{ $penempatans->count() }} Mahasiswa
                                    </span>
                                </div>

                                @if($penempatans->isNotEmpty())
                                    <div style="overflow-x: auto; background: var(--surface); border: 1px solid var(--border); border-radius: 12px;">
                                        <table class="um-table" style="width: 100%; font-size: 12px;">
                                            <thead>
                                                <tr>
                                                    <th class="um-th" style="width: 40px; text-align: center;">#</th>
                                                    <th class="um-th">Nama Mahasiswa</th>
                                                    <th class="um-th">NIM</th>
                                                    <th class="um-th">Kegiatan</th>
                                                    <th class="um-th" style="text-align: center;">Nilai Mitra</th>
                                                    <th class="um-th" style="text-align: center;">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($penempatans as $pIndex => $p)
                                                    <tr class="um-row">
                                                        <td class="um-td" style="text-align: center;">{{ $pIndex + 1 }}</td>
                                                        <td class="um-td">
                                                            <strong>{{ $p->mahasiswa?->nama ?? '-' }}</strong>
                                                        </td>
                                                        <td class="um-td" style="font-family: monospace;">{{ $p->mahasiswa?->nim ?? '-' }}</td>
                                                        <td class="um-td">{{ $p->kegiatan?->nama_kegiatan ?? 'Praktik Kerja Industri / Magang' }}</td>
                                                        <td class="um-td" style="text-align: center;">
                                                            @if($p->nilai_mitra)
                                                                <span style="font-weight: 800; color: #059669;">
                                                                    <i class="fas fa-star" style="color: #f59e0b; font-size: 11px;"></i> {{ $p->nilai_mitra }}
                                                                </span>
                                                            @else
                                                                <span style="color: var(--text-sub);">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="um-td" style="text-align: center;">
                                                            <span style="padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; background: {{ $p->status === 'Aktif' ? 'rgba(16,185,129,0.1)' : 'rgba(59,130,246,0.1)' }}; color: {{ $p->status === 'Aktif' ? '#10b981' : '#3b82f6' }};">
                                                                {{ $p->status ?? 'Aktif' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div style="padding: 24px; text-align: center; background: var(--surface); border: 1px dashed var(--border); border-radius: 12px;">
                                        <i class="fas fa-user-slash" style="font-size: 24px; color: var(--text-sub); margin-bottom: 8px; display: block;"></i>
                                        <p style="margin: 0; font-size: 13px; color: var(--text-sub);">Belum ada penempatan mahasiswa khusus dari program studi ini pada mitra terkait.</p>
                                    </div>
                                @endif
                            </div>

                            {{-- ── FOOTER ACTIONS ── --}}
                            <div style="display: flex; justify-content: flex-end; align-items: center; gap: 12px; padding-top: 16px; border-top: 1px solid var(--border); width: 100%; box-sizing: border-box;">
                                <a href="{{ route('prodi.evaluasi') }}" class="rfc-btn"
                                    style="background: var(--surface2); color: var(--text); border: 1.5px solid var(--border); padding: 10px 20px; border-radius: 10px; font-weight: 600; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-arrow-left"></i> Kembali ke Rekapitulasi Evaluasi
                                </a>
                                <button type="button" onclick="window.print()" class="rfc-btn rfc-btn-primary"
                                    style="padding: 10px 24px; border-radius: 10px; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; border: none;">
                                    <i class="fas fa-print"></i> Cetak Dokumen Rincian
                                </button>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>
@endsection
