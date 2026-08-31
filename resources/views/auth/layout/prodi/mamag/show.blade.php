@extends('auth.prodi')

@section('content')
    @php
        $pembimbingInternal = $penempatan->pembimbings?->first(fn($p) => strtolower($p->tipe ?? '') === 'internal') 
            ?? $penempatan->pembimbings?->where('tipe', 'Internal')->first();
        $pembimbingEksternal = $penempatan->pembimbings?->first(fn($p) => strtolower($p->tipe ?? '') === 'eksternal') 
            ?? $penempatan->pembimbings?->where('tipe', 'Eksternal')->first();

        $statusColor = match($penempatan->status) {
            'Aktif' => '#10b981',
            'Selesai' => '#3b82f6',
            'Dibatalkan' => '#ef4444',
            default => '#6b7280'
        };

        $statusBg = match($penempatan->status) {
            'Aktif' => 'rgba(16, 185, 129, 0.1)',
            'Selesai' => 'rgba(59, 130, 246, 0.1)',
            'Dibatalkan' => 'rgba(239, 68, 68, 0.1)',
            default => 'rgba(107, 114, 128, 0.1)'
        };
    @endphp

    <main id="mainContent" class="dk-page">
        <section class="ud-topbar">
            <div class="ud-hero-copy">
                <div class="ud-breadcrumb">
                    <i class="fas fa-home"></i>
                    <span>/</span>
                    <a href="{{ route('prodi.dashboard') }}">Beranda</a>
                    <span>/</span>
                    <a href="{{ route('prodi.penempatan.index') }}">Mahasiswa &amp; Magang</a>
                    <span>/</span>
                    <span>Rincian Penempatan</span>
                </div>
                <div class="ud-title-row">
                    <span class="ud-title-icon"><i class="fas fa-id-card-clip"></i></span>
                    <div class="ud-title-copy">
                        <h2 class="ud-title" id="pageTitle">Rincian Penempatan Mahasiswa</h2>
                        <p class="ud-subtitle" id="pageDesc">
                            Informasi lengkap penempatan mahasiswa, status pelaksanaan, dan penetapan pembimbing industri.
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
            <div class="card-header um-header dk-card-header" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div class="um-title dk-card-title">
                    <span class="dk-title-icon"><i class="fas fa-user-graduate"></i></span>
                    <div>
                        <strong>{{ $penempatan->mahasiswa?->nama ?? 'Mahasiswa' }}</strong>
                        <small>NIM: {{ $penempatan->mahasiswa?->nim ?? '-' }} @if($penempatan->mahasiswa?->prodi) • {{ $penempatan->mahasiswa->prodi->nama_prodi }} @endif</small>
                    </div>
                </div>
                <div>
                    <a href="{{ route('prodi.penempatan.edit', $penempatan->id) }}" class="rfc-btn rfc-btn-primary"
                        style="padding: 8px 18px; border-radius: 10px; font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                        <i class="fas fa-pen-to-square"></i> Edit Data
                    </a>
                </div>
            </div>

            <div class="card-body dk-card-body" style="padding: 0; width: 100%; box-sizing: border-box; overflow: visible;">
                {{-- ═══ TWO-COLUMN TOP LAYOUT (Identik dengan create & edit) ═══ --}}
                <div style="display: grid; grid-template-columns: 320px minmax(0, 1fr); gap: 24px; padding: 24px; width: 100%; max-width: 100%; box-sizing: border-box;">

                    {{-- ══ LEFT COLUMN: Masa Berlaku & Status (Sticky) ══ --}}
                    <div style="position: sticky; top: 24px; align-self: start; min-width: 0; max-width: 100%; box-sizing: border-box;">
                        <div
                            style="background: var(--surface); border: 1px solid var(--border); border-radius: 16px; overflow: visible; width: 100%; box-sizing: border-box;">
                            <div x-data="{ showMasaBerlaku: true }">
                                {{-- Card Header --}}
                                <div @click="showMasaBerlaku = !showMasaBerlaku"
                                    style="display: flex; align-items: center; gap: 10px; padding: 14px 18px; cursor: pointer; user-select: none; border-bottom: 1px solid var(--border); background: linear-gradient(135deg, rgba(16,185,129,0.06), rgba(5,150,105,0.04)); border-radius: 16px 16px 0 0; transition: background 0.2s;">
                                    <div
                                        style="width: 34px; height: 34px; border-radius: 9px; background: linear-gradient(135deg, #059669, #10b981); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; box-shadow: 0 3px 8px rgba(5,150,105,0.25);">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <h4
                                            style="margin: 0; font-size: 13px; font-weight: 700; color: var(--text); letter-spacing: -0.01em;">
                                            Masa Berlaku
                                        </h4>
                                    </div>
                                    <i class="fas fa-chevron-down"
                                        style="font-size: 10px; color: var(--text-sub); transition: transform 0.3s ease; flex-shrink: 0;"
                                        :style="showMasaBerlaku ? 'transform: rotate(180deg)' : ''"></i>
                                </div>

                                {{-- Card Body --}}
                                <div x-show="showMasaBerlaku" x-collapse.duration.300ms style="padding: 18px; width: 100%; box-sizing: border-box;">

                                    {{-- Status Kegiatan --}}
                                    <div style="margin-bottom: 16px;">
                                        <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">
                                            Status Pelaksanaan
                                        </label>
                                        <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; background: {{ $statusBg }}; color: {{ $statusColor }}; border: 1px solid {{ $statusColor }}30;">
                                            <div style="width: 8px; height: 8px; border-radius: 50%; background: {{ $statusColor }};"></div>
                                            <span>{{ $penempatan->status ?? 'Aktif' }}</span>
                                        </div>
                                    </div>

                                    {{-- Periode Mulai --}}
                                    <div style="margin-bottom: 14px;">
                                        <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                                            Tanggal Mulai
                                        </label>
                                        <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: var(--text);">
                                            <i class="fas fa-calendar-plus" style="color: #059669; font-size: 14px;"></i>
                                            <span>{{ $penempatan->periode_mulai ? \Carbon\Carbon::parse($penempatan->periode_mulai)->translatedFormat('d F Y') : '-' }}</span>
                                        </div>
                                    </div>

                                    {{-- Periode Selesai --}}
                                    <div style="margin-bottom: 16px;">
                                        <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                                            Tanggal Selesai
                                        </label>
                                        <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: var(--text);">
                                            <i class="fas fa-calendar-check" style="color: #4f46e5; font-size: 14px;"></i>
                                            <span>{{ $penempatan->periode_selesai ? \Carbon\Carbon::parse($penempatan->periode_selesai)->translatedFormat('d F Y') : 'Sedang Berjalan' }}</span>
                                        </div>
                                    </div>

                                    {{-- Nilai Mitra --}}
                                    <div style="margin-bottom: 16px;">
                                        <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                                            Nilai Evaluasi Mitra
                                        </label>
                                        <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 8px; background: var(--surface2); border: 1px solid var(--border); font-size: 13px; font-weight: 800; color: var(--text);">
                                            <i class="fas fa-star" style="color: #f59e0b;"></i>
                                            <span>{{ $penempatan->nilai_mitra ? $penempatan->nilai_mitra . ' / 100' : 'Belum Dinilai' }}</span>
                                        </div>
                                    </div>

                                    {{-- Divider --}}
                                    <div
                                        style="height: 1px; background: linear-gradient(90deg, transparent, var(--border), transparent); margin: 18px 0;">
                                    </div>

                                    {{-- Panduan Singkat --}}
                                    <div
                                        style="background: rgba(79,70,229,0.04); border: 1px dashed rgba(79,70,229,0.25); border-radius: 12px; padding: 14px; width: 100%; box-sizing: border-box;">
                                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                                            <i class="fas fa-circle-info"
                                                style="color: #4f46e5; margin-top: 2px; font-size: 14px; flex-shrink: 0;"></i>
                                            <div style="font-size: 12px; color: var(--text-sub); line-height: 1.5;">
                                                Data penempatan ini tercatat resmi di pangkalan data kerja sama program studi Politeknik Negeri Manado.
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

                            {{-- ── SEKSI 1: DATA PENEMPATAN & MITRA INDUSTRI ── --}}
                            <div style="margin-bottom: 24px; width: 100%; box-sizing: border-box;">
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                                    <div
                                        style="width: 4px; height: 18px; border-radius: 2px; background: linear-gradient(180deg, #4f46e5, #818cf8); flex-shrink: 0;">
                                    </div>
                                    <span
                                        style="font-weight: 700; font-size: 14px; color: var(--text); letter-spacing: 0.01em;">
                                        Data Penempatan &amp; Mitra Industri
                                    </span>
                                </div>

                                <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; width: 100%; box-sizing: border-box;">
                                    {{-- 1. Kartu Mahasiswa --}}
                                    <div style="background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; padding: 18px; min-width: 0; width: 100%; box-sizing: border-box;">
                                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                                            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(79,70,229,0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                                                <i class="fas fa-user-graduate"></i>
                                            </div>
                                            <div style="min-width: 0;">
                                                <small style="font-size: 11px; color: var(--text-sub); display: block;">Mahasiswa Terdaftar</small>
                                                <strong style="font-size: 14px; color: var(--text); display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $penempatan->mahasiswa?->nama ?? '-' }}</strong>
                                            </div>
                                        </div>
                                        <div style="font-size: 12px; color: var(--text-sub); line-height: 1.6;">
                                            <div><strong>NIM:</strong> {{ $penempatan->mahasiswa?->nim ?? '-' }}</div>
                                            <div><strong>Program Studi:</strong> {{ $penempatan->mahasiswa?->prodi?->nama_prodi ?? '-' }}</div>
                                        </div>
                                    </div>

                                    {{-- 2. Kartu Kegiatan Kerja Sama --}}
                                    <div style="background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; padding: 18px; min-width: 0; width: 100%; box-sizing: border-box;">
                                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                                            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(5,150,105,0.1); color: #059669; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                                                <i class="fas fa-file-contract"></i>
                                            </div>
                                            <div style="min-width: 0;">
                                                <small style="font-size: 11px; color: var(--text-sub); display: block;">Kegiatan Kerja Sama</small>
                                                <strong style="font-size: 14px; color: var(--text); display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $penempatan->kegiatan?->nama_kegiatan ?? '-' }}</strong>
                                            </div>
                                        </div>
                                        <div style="font-size: 12px; color: var(--text-sub); line-height: 1.6;">
                                            <div><strong>Jenis:</strong> {{ $penempatan->kegiatan?->jenis ?? 'Kerja Sama Industri' }}</div>
                                            <div><strong>Nomor Dokumen:</strong> {{ $penempatan->kegiatan?->nomor_dokumen ?? '-' }}</div>
                                        </div>
                                    </div>

                                    {{-- 3. Kartu Mitra Industri (Full Width) --}}
                                    <div style="grid-column: 1 / -1; background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; padding: 18px; min-width: 0; width: 100%; box-sizing: border-box;">
                                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(217,119,6,0.1); color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                                                <i class="fas fa-building"></i>
                                            </div>
                                            <div style="min-width: 0;">
                                                <small style="font-size: 11px; color: var(--text-sub); display: block;">Mitra Industri Penempatan (DUDIKA)</small>
                                                <strong style="font-size: 14px; color: var(--text); display: block;">{{ $penempatan->mitra?->nama_mitra ?? '-' }}</strong>
                                            </div>
                                        </div>
                                        <div style="font-size: 12px; color: var(--text-sub); line-height: 1.6;">
                                            <div><strong>Alamat / Wilayah:</strong> {{ $penempatan->mitra?->alamat ?? 'Indonesia' }}</div>
                                            @if($penempatan->mitra?->kontak)
                                                <div><strong>Kontak Mitra:</strong> {{ $penempatan->mitra->kontak }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ── SEKSI 2: PENETAPAN PEMBIMBING ── --}}
                            <div
                                style="background: var(--surface2); border: 1px solid var(--border); border-radius: 16px; padding: 22px; margin-bottom: 24px; width: 100%; box-sizing: border-box;">
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 18px;">
                                    <div
                                        style="width: 32px; height: 32px; border-radius: 8px; background: rgba(79,70,229,0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                                        <i class="fas fa-users-rectangle"></i>
                                    </div>
                                    <div style="min-width: 0;">
                                        <h4 style="margin: 0; font-size: 14px; font-weight: 800; color: var(--text);">
                                            Penetapan Dosen &amp; Pembimbing Industri
                                        </h4>
                                        <small style="color: var(--text-sub); font-size: 12px;">Dosen pembimbing internal Polimdo dan pembimbing lapangan instansi mitra.</small>
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; width: 100%; box-sizing: border-box;">
                                    {{-- Pembimbing Internal (Dosen) --}}
                                    <div
                                        style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 16px; min-width: 0; width: 100%; box-sizing: border-box;">
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                                            <i class="fas fa-chalkboard-user"
                                                style="color: #4f46e5; font-size: 13px; flex-shrink: 0;"></i>
                                            <span
                                                style="font-weight: 700; font-size: 13px; color: var(--text);">Pembimbing
                                                Internal (Dosen)</span>
                                        </div>

                                        <div style="margin-bottom: 10px;">
                                            <small style="font-size: 11px; color: var(--text-sub); display: block;">Nama Dosen Pembimbing</small>
                                            <strong style="font-size: 13px; color: var(--text);">{{ $pembimbingInternal->nama_pembimbing ?? 'Belum Ditetapkan' }}</strong>
                                        </div>

                                        <div>
                                            <small style="font-size: 11px; color: var(--text-sub); display: block;">Kontak / WhatsApp</small>
                                            @if($pembimbingInternal?->kontak)
                                                <div style="display: flex; align-items: center; gap: 6px; margin-top: 2px;">
                                                    <i class="fab fa-whatsapp" style="color: #10b981; font-size: 13px;"></i>
                                                    <span style="font-size: 13px; font-weight: 600; color: var(--text);">{{ $pembimbingInternal->kontak }}</span>
                                                </div>
                                            @else
                                                <span style="font-size: 12px; color: var(--text-sub);">-</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Pembimbing Eksternal (Mitra) --}}
                                    <div
                                        style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 16px; min-width: 0; width: 100%; box-sizing: border-box;">
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                                            <i class="fas fa-building-user"
                                                style="color: #059669; font-size: 13px; flex-shrink: 0;"></i>
                                            <span
                                                style="font-weight: 700; font-size: 13px; color: var(--text);">Pembimbing
                                                Eksternal (Mitra)</span>
                                        </div>

                                        <div style="margin-bottom: 10px;">
                                            <small style="font-size: 11px; color: var(--text-sub); display: block;">Nama Pembimbing Mitra</small>
                                            <strong style="font-size: 13px; color: var(--text);">{{ $pembimbingEksternal->nama_pembimbing ?? 'Belum Ditetapkan' }}</strong>
                                        </div>

                                        <div>
                                            <small style="font-size: 11px; color: var(--text-sub); display: block;">Kontak / Email Pembimbing Mitra</small>
                                            @if($pembimbingEksternal?->kontak)
                                                <div style="display: flex; align-items: center; gap: 6px; margin-top: 2px;">
                                                    <i class="fas fa-envelope" style="color: #6366f1; font-size: 12px;"></i>
                                                    <span style="font-size: 13px; font-weight: 600; color: var(--text);">{{ $pembimbingEksternal->kontak }}</span>
                                                </div>
                                            @else
                                                <span style="font-size: 12px; color: var(--text-sub);">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ── SEKSI 3: CATATAN EVALUASI MITRA (JIKA ADA) ── --}}
                            @if($penempatan->catatan_mitra)
                                <div style="margin-bottom: 24px; padding: 18px 20px; background: rgba(79,70,229,0.04); border: 1px solid rgba(79,70,229,0.2); border-radius: 14px; width: 100%; box-sizing: border-box;">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                        <i class="fas fa-comment-dots" style="color: #4f46e5; font-size: 14px;"></i>
                                        <strong style="color: #4f46e5; font-size: 13px;">Catatan Penilaian dari Mitra Industri:</strong>
                                    </div>
                                    <p style="margin: 0; font-size: 13px; color: var(--text); font-style: italic; line-height: 1.5;">
                                        "{{ $penempatan->catatan_mitra }}"
                                    </p>
                                </div>
                            @endif

                            {{-- ── FOOTER ACTIONS ── --}}
                            <div
                                style="display: flex; justify-content: flex-end; align-items: center; gap: 12px; padding-top: 16px; border-top: 1px solid var(--border); width: 100%; box-sizing: border-box;">
                                <a href="{{ route('prodi.penempatan.index') }}" class="rfc-btn"
                                    style="background: var(--surface2); color: var(--text); border: 1.5px solid var(--border); padding: 10px 20px; border-radius: 10px; font-weight: 600; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                                </a>
                                <a href="{{ route('prodi.penempatan.edit', $penempatan->id) }}" class="rfc-btn rfc-btn-primary"
                                    style="padding: 10px 24px; border-radius: 10px; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none;">
                                    <i class="fas fa-pen-to-square"></i> Edit Penempatan
                                </a>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>
@endsection
