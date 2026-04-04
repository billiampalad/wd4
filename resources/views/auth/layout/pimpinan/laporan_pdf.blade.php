<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Global Kerjasama</title>
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 10pt; color: #000; line-height: 1.3; margin: 0; padding: 0; }
        
        .kop-surat { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 15px; }
        .kop-surat h2 { margin: 0; font-size: 14pt; text-transform: uppercase; }
        .kop-surat h3 { margin: 0; font-size: 12pt; text-transform: uppercase; }
        .kop-surat p { margin: 2px 0; font-size: 9pt; font-style: italic; }
        
        .title-area { text-align: center; margin-bottom: 15px; }
        .title-area h4 { margin: 0; font-size: 11pt; text-decoration: underline; text-transform: uppercase; }
        
        .item-container { margin-bottom: 25px; page-break-inside: avoid; }
        .section-header { background-color: #f3f4f6; padding: 5px; font-weight: bold; border: 1px solid #000; margin-top: 10px; text-transform: uppercase; font-size: 10pt; }
        
        .table-detail { width: 100%; border-collapse: collapse; margin-bottom: 5px; table-layout: fixed; }
        .table-detail td { border: 1px solid #000; padding: 5px; vertical-align: top; font-size: 9pt; word-wrap: break-word; }
        .label { font-weight: bold; width: 35%; background-color: #fafafa; }
        .value { width: 65%; }
        
        .footer-sign { margin-top: 30px; float: right; width: 200px; text-align: center; }
        .footer-sign p { margin: 0; font-size: 10pt; }
        .footer-sign .space { height: 60px; }
        
        @page { margin: 1.5cm 1cm; }
        .page-break { page-break-after: always; }
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="kop-surat">
        <h2>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h2>
        <h3>POLITEKNIK NEGERI MANADO</h3>
        <p>Jl. Kampus Bahu, Manado 95115, Sulawesi Utara</p>
        <p>Telepon: (0431) 861152, Fax: (0431) 861152, Website: www.polimdo.ac.id</p>
    </div>

    @php
        $mainTitle = "Rekapitulasi Kerja Sama Global";
        if($request->filled('kategori_mitra') && $request->kategori_mitra != 'all') {
            $mainTitle = "Rekapitulasi Kerja Sama " . ucfirst($request->kategori_mitra);
        }
    @endphp

    <div class="title-area">
        <h4>{{ $mainTitle }}</h4>
    </div>

    @forelse($data as $index => $item)
        <div class="item-container">
            <div style="font-weight: bold; font-size: 11pt; margin-bottom: 5px;">DATA KE-{{ $index + 1 }}: {{ strtoupper($item->nama_kegiatan) }}</div>
            
            <!-- INFORMASI UMUM -->
            <div class="section-header">I. INFORMASI UMUM</div>
            <table class="table-detail">
                <tr>
                    <td class="label">NAMA PROGRAM / KEGIATAN</td>
                    <td class="value">{{ $item->nama_kegiatan }}</td>
                </tr>
                <tr>
                    <td class="label">JENIS KERJA SAMA (Ruang Lingkup)</td>
                    <td class="value">{{ $item->jenisKerjasama->pluck('nama_kerjasama')->join(', ') }}</td>
                </tr>
                <tr>
                    <td class="label">NAMA MITRA DUDIKA</td>
                    <td class="value">{{ $item->mitras->pluck('nama_mitra')->join(', ') }}</td>
                </tr>
                <tr>
                    <td class="label">NEGARA</td>
                    <td class="value">{{ $item->mitras->pluck('negara')->unique()->join(', ') ?: 'Indonesia' }}</td>
                </tr>
                <tr>
                    <td class="label">UNIT PELAKSANA DI POLIMDO</td>
                    <td class="value">
                        @php
                        $pengusul = $item->jurusans->pluck('nama_jurusan')->merge($item->unitKerjas->pluck('nama_unit_pelaksana'))->join(', ');
                        @endphp
                        {{ $pengusul ?: 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">PERIODE PELAKSANAAN</td>
                    <td class="value">
                        {{ $item->periode_mulai ? $item->periode_mulai->format('d M Y') : '-' }} s/d 
                        {{ $item->periode_selesai ? $item->periode_selesai->format('d M Y') : 'Selesai' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">NOMOR DAN TANGGAL MOU/MOA</td>
                    <td class="value">
                        No: {{ $item->nomor_mou ?? '-' }} <br>
                        Tgl: {{ $item->tanggal_mou ? $item->tanggal_mou->format('d M Y') : '-' }}
                    </td>
                </tr>
            </table>

            <!-- TUJUAN DAN SASARAN -->
            <div class="section-header">II. TUJUAN DAN SASARAN</div>
            <table class="table-detail">
                <tr>
                    <td class="label">TUJUAN KERJASAMA</td>
                    <td class="value">{{ $item->tujuans->pluck('tujuan')->join('; ') ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="label">SASARAN YANG INGIN DICAPAI</td>
                    <td class="value">{{ $item->tujuans->pluck('sasaran')->join('; ') ?: '-' }}</td>
                </tr>
            </table>

            <!-- PELAKSANAAN KEGIATAN -->
            <div class="section-header">III. PELAKSANAAN KEGIATAN</div>
            @php $pel = $item->pelaksanaans->first(); @endphp
            <table class="table-detail">
                <tr>
                    <td class="label">DESKRIPSI SINGKAT KEGIATAN</td>
                    <td class="value">{{ $pel->deskripsi ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">CAKUPAN DAN SKALA KEGIATAN</td>
                    <td class="value">{{ $pel->cakupan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">JUMLAH PESERTA YANG TERLIBAT</td>
                    <td class="value">{{ $pel->jumlah_peserta ?? 0 }} Orang</td>
                </tr>
                <tr>
                    <td class="label">SUMBER DAYA YANG DIGUNAKAN</td>
                    <td class="value">{{ $pel->sumber_daya ?? '-' }}</td>
                </tr>
            </table>

            <!-- HASIL DAN CAPAIAN -->
            <div class="section-header">IV. HASIL DAN CAPAIAN</div>
            @php $h = $item->hasils->first(); @endphp
            <table class="table-detail">
                <tr>
                    <td class="label">OUTPUT (Hasil Langsung)</td>
                    <td class="value">{{ $h->hasil_langsung ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">OUTCOME (Dampak Jangka Menengah)</td>
                    <td class="value">{{ $h->dampak ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">MANFAAT BAGI MAHASISWA</td>
                    <td class="value">{{ $h->manfaat_mahasiswa ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">MANFAAT BAGI POLIMDO</td>
                    <td class="value">{{ $h->manfaat_polimdo ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">MANFAAT BAGI MITRA DUDIKA</td>
                    <td class="value">{{ $h->manfaat_mitra ?? '-' }}</td>
                </tr>
            </table>
            
            <!-- EVALUASI KINERJA -->
            <div class="section-header">V. EVALUASI KINERJA / ASPEK PENILAIAN</div>
            @php $e = $item->evaluasis->first(); @endphp
            <table class="table-detail">
                <tr>
                    <td class="label">KESESUAIAN DENGAN RENCANA</td>
                    <td class="value">{{ $e->sesuai_rencana ?? '-' }} / 5</td>
                </tr>
                <tr>
                    <td class="label">KUALITAS PELAKSANAAN</td>
                    <td class="value">{{ $e->kualitas ?? '-' }} / 5</td>
                </tr>
                <tr>
                    <td class="label">KETERLIBATAN MITRA DUDIKA</td>
                    <td class="value">{{ $e->keterlibatan ?? '-' }} / 5</td>
                </tr>
                <tr>
                    <td class="label">EFISIENSI SUMBER DAYA</td>
                    <td class="value">{{ $e->efisiensi ?? '-' }} / 5</td>
                </tr>
                <tr>
                    <td class="label">KEPUASAN PIHAK TERKAIT</td>
                    <td class="value">{{ $e->kepuasan ?? '-' }} / 5</td>
                </tr>
            </table>

            <!-- PERMASALAHAN DAN SOLUSI -->
            <div class="section-header">VI. PERMASALAHAN DAN SOLUSI</div>
            @php $m = $item->permasalahanSolusis->first(); @endphp
            <table class="table-detail">
                <tr>
                    <td class="label">KENDALA YANG DIHADAPI</td>
                    <td class="value">{{ $m->kendala ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">UPAYA MENGATASI KENDALA</td>
                    <td class="value">{{ $m->solusi ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">REKOMENDASI PERBAIKAN</td>
                    <td class="value">{{ $m->rekomendasi ?? '-' }}</td>
                </tr>
            </table>

            <!-- DOKUMENTASI & KESIMPULAN -->
            <div class="section-header">VII. DOKUMENTASI DAN KESIMPULAN</div>
            @php $k = $item->kesimpulans->first(); @endphp
            <table class="table-detail">
                <tr>
                    <td class="label">DOKUMEN PENDUKUNG (Link)</td>
                    <td class="value">
                        @foreach($item->dokumentasis as $dok)
                            @if($dok->link_drive)
                                - {{ $dok->keterangan ?: 'Link Drive' }}: <a href="{{ $dok->link_drive }}" target="_blank">{{ $dok->link_drive }}</a> <br>
                            @endif
                        @endforeach
                        {{ $item->dokumentasis->whereNotNull('link_drive')->isEmpty() ? '-' : '' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">KESIMPULAN & SARAN</td>
                    <td class="value">
                        <strong>Ringkasan:</strong> {{ $k->ringkasan ?? '-' }} <br>
                        <strong>Saran:</strong> {{ $k->saran ?? '-' }} <br>
                        <strong>Tindak Lanjut:</strong> {{ $k->tindak_lanjut ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">STATUS DATA</td>
                    <td class="value" style="font-weight: bold;">{{ $item->status_label }}</td>
                </tr>
            </table>
        </div>
        
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @empty
        <p class="text-center">Tidak ada data kerjasama yang ditemukan.</p>
    @endforelse

    <div class="footer-sign">
        <p>Manado, {{ date('d F Y') }}</p>
        <p>Mengetahui,</p>
        <p><strong>Pimpinan Politeknik Negeri Manado</strong></p>
        <div class="space"></div>
        <p>__________________________</p>
        <p>NIP. ............................</p>
    </div>

    <div class="clear"></div>
    <p style="font-size: 8pt; color: #666; margin-top: 20px;">Dicetak otomatis oleh Sistem Informasi Kerjasama pada {{ date('d/m/Y H:i') }}</p>
</body>
</html>
