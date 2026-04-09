<main id="mainContent">
    <div class="page-header">
        <div class="breadcrumb">
            <a href="{{ route('pimpinan.monitoring') }}" style="color: var(--text-sub); text-decoration: none;">Monitoring Data</a>
            <span class="sep">/</span>
            <span class="current">Detail Kegiatan</span>
        </div>
        <h2>Detail Monitoring Kerjasama</h2>
        <p>Informasi lengkap mengenai kegiatan kerjasama yang telah divalidasi/dievaluasi.</p>
    </div>

    <div class="detail-container" style="display: grid; gap: 20px;">
        <!-- Card 1: Informasi Umum -->
        <div class="card um-card">
            <div class="card-header um-header">
                <div class="card-title">
                    <i class="fas fa-info-circle" style="color: var(--accent);"></i>
                    <span>I. INFORMASI UMUM</span>
                </div>
                <span class="tag {{ $kegiatan->status_class }}">{{ $kegiatan->status_label }}</span>
            </div>
            <div class="card-body">
                <table class="um-table-detail" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 30%; padding: 12px; font-weight: 600; color: var(--text-sub); border-bottom: 1px solid #eee;">NAMA PROGRAM / KEGIATAN</td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">{{ $kegiatan->nama_kegiatan }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; font-weight: 600; color: var(--text-sub); border-bottom: 1px solid #eee;">JENIS KERJA SAMA</td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">{{ $kegiatan->jenisKerjasama->pluck('nama_kerjasama')->join(', ') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; font-weight: 600; color: var(--text-sub); border-bottom: 1px solid #eee;">NAMA MITRA DUDIKA</td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">{{ $kegiatan->mitras->pluck('nama_mitra')->join(', ') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; font-weight: 600; color: var(--text-sub); border-bottom: 1px solid #eee;">NEGARA</td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">{{ $kegiatan->mitras->pluck('negara')->unique()->join(', ') ?: 'Indonesia' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; font-weight: 600; color: var(--text-sub); border-bottom: 1px solid #eee;">UNIT PELAKSANA</td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">
                            @php
                                $pengusul = $kegiatan->jurusans->pluck('nama_jurusan')->merge($kegiatan->unitKerjas->pluck('nama_unit_pelaksana'))->join(', ');
                            @endphp
                            {{ $pengusul ?: 'N/A' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; font-weight: 600; color: var(--text-sub); border-bottom: 1px solid #eee;">PERIODE</td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">
                            {{ $kegiatan->periode_mulai ? $kegiatan->periode_mulai->format('d M Y') : '-' }} s/d 
                            {{ $kegiatan->periode_selesai ? $kegiatan->periode_selesai->format('d M Y') : 'Selesai' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; font-weight: 600; color: var(--text-sub);">NOMOR & TANGGAL MOU</td>
                        <td style="padding: 12px;">
                            {{ $kegiatan->nomor_mou ?? '-' }} 
                            @if($kegiatan->tanggal_mou) ({{ $kegiatan->tanggal_mou->format('d M Y') }}) @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Card 2: Tujuan & Pelaksanaan -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="card um-card">
                <div class="card-header um-header">
                    <div class="card-title"><i class="fas fa-bullseye" style="color: var(--accent);"></i> II. TUJUAN & SASARAN</div>
                </div>
                <div class="card-body" style="padding: 15px;">
                    <h4 style="margin-bottom: 8px; color: var(--text-sub); font-size: 13px;">TUJUAN:</h4>
                    <p style="margin-bottom: 15px;">{{ $kegiatan->tujuans->pluck('tujuan')->join('; ') ?: '-' }}</p>
                    <h4 style="margin-bottom: 8px; color: var(--text-sub); font-size: 13px;">SASARAN:</h4>
                    <p>{{ $kegiatan->tujuans->pluck('sasaran')->join('; ') ?: '-' }}</p>
                </div>
            </div>
            <div class="card um-card">
                <div class="card-header um-header">
                    <div class="card-title"><i class="fas fa-tasks" style="color: var(--accent);"></i> III. PELAKSANAAN</div>
                </div>
                <div class="card-body" style="padding: 15px;">
                    @php $pel = $kegiatan->pelaksanaans->first(); @endphp
                    <div style="display: grid; gap: 10px;">
                        <div>
                            <span style="font-size: 12px; color: var(--text-sub);">Deskripsi:</span>
                            <p>{{ $pel->deskripsi ?? '-' }}</p>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <div>
                                <span style="font-size: 12px; color: var(--text-sub);">Cakupan:</span>
                                <p>{{ $pel->cakupan ?? '-' }}</p>
                            </div>
                            <div>
                                <span style="font-size: 12px; color: var(--text-sub);">Peserta:</span>
                                <p>{{ $pel->jumlah_peserta ?? 0 }} Orang</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Hasil & Evaluasi -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="card um-card">
                <div class="card-header um-header">
                    <div class="card-title"><i class="fas fa-chart-line" style="color: var(--accent);"></i> IV. HASIL & CAPAIAN</div>
                </div>
                <div class="card-body" style="padding: 15px;">
                    @php $h = $kegiatan->hasils->first(); @endphp
                    <div style="display: grid; gap: 10px;">
                        <div><span style="font-size: 12px; color: var(--text-sub);">Output:</span> <p>{{ $h->hasil_langsung ?? '-' }}</p></div>
                        <div><span style="font-size: 12px; color: var(--text-sub);">Outcome:</span> <p>{{ $h->dampak ?? '-' }}</p></div>
                        <div><span style="font-size: 12px; color: var(--text-sub);">Manfaat Mahasiswa:</span> <p>{{ $h->manfaat_mahasiswa ?? '-' }}</p></div>
                    </div>
                </div>
            </div>
            <div class="card um-card">
                <div class="card-header um-header">
                    <div class="card-title"><i class="fas fa-star" style="color: var(--accent);"></i> V. EVALUASI KINERJA</div>
                </div>
                <div class="card-body" style="padding: 15px;">
                    @php $e = $kegiatan->evaluasis->first(); @endphp
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div style="background: #f9fafb; padding: 8px; border-radius: 6px; text-align: center;">
                            <div style="font-size: 11px; color: var(--text-sub);">Kesesuaian</div>
                            <div style="font-weight: 700; color: var(--accent);">{{ $e->sesuai_rencana ?? '-' }}/5</div>
                        </div>
                        <div style="background: #f9fafb; padding: 8px; border-radius: 6px; text-align: center;">
                            <div style="font-size: 11px; color: var(--text-sub);">Kualitas</div>
                            <div style="font-weight: 700; color: var(--accent);">{{ $e->kualitas ?? '-' }}/5</div>
                        </div>
                        <div style="background: #f9fafb; padding: 8px; border-radius: 6px; text-align: center;">
                            <div style="font-size: 11px; color: var(--text-sub);">Keterlibatan</div>
                            <div style="font-weight: 700; color: var(--accent);">{{ $e->keterlibatan ?? '-' }}/5</div>
                        </div>
                        <div style="background: #f9fafb; padding: 8px; border-radius: 6px; text-align: center;">
                            <div style="font-size: 11px; color: var(--text-sub);">Kepuasan</div>
                            <div style="font-weight: 700; color: var(--accent);">{{ $e->kepuasan ?? '-' }}/5</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Dokumentasi & Kesimpulan -->
        <div class="card um-card">
            <div class="card-header um-header">
                <div class="card-title"><i class="fas fa-file-alt" style="color: var(--accent);"></i> VI. DOKUMENTASI & KESIMPULAN</div>
            </div>
            <div class="card-body" style="padding: 15px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <h4 style="margin-bottom: 10px; font-size: 13px; color: var(--text-sub);">DOKUMEN PENDUKUNG:</h4>
                        <div style="display: grid; gap: 8px;">
                            @foreach($kegiatan->dokumentasis as $dok)
                                @if($dok->link_drive)
                                    <a href="{{ $dok->link_drive }}" target="_blank" style="display: flex; align-items: center; gap: 8px; padding: 8px; background: #eff6ff; border-radius: 6px; color: #1e40af; text-decoration: none; font-size: 13px;">
                                        <i class="fab fa-google-drive"></i>
                                        <span>{{ $dok->keterangan ?: 'Buka Link Drive' }}</span>
                                    </a>
                                @endif
                            @endforeach
                            @if($kegiatan->dokumentasis->whereNotNull('link_drive')->isEmpty())
                                <p style="color: var(--text-sub); font-style: italic;">Tidak ada dokumen pendukung.</p>
                            @endif
                        </div>
                    </div>
                    <div>
                        @php $k = $kegiatan->kesimpulans->first(); @endphp
                        <h4 style="margin-bottom: 10px; font-size: 13px; color: var(--text-sub);">KESIMPULAN & SARAN:</h4>
                        <p><strong>Ringkasan:</strong> {{ $k->ringkasan ?? '-' }}</p>
                        <p><strong>Saran:</strong> {{ $k->saran ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 10px; display: flex; gap: 10px;">
            <a href="{{ route('pimpinan.monitoring') }}" class="rfc-btn" style="text-decoration: none; display: flex; align-items: center; gap: 8px; background: #6b7280; color: white;">
                <i class="fas fa-arrow-left"></i> Kembali ke Monitoring
            </a>
            <a href="{{ route('pimpinan.laporan.pdf', ['pengusul' => 'all', 'status' => 'all']) }}" target="_blank" class="rfc-btn rfc-btn-danger" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-file-pdf"></i> Cetak Laporan
            </a>
        </div>
    </div>
</main>
