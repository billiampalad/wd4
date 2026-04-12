<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <table>
        <thead>
            <tr>
                <th colspan="34" style="font-size: 16pt; font-weight: bold; text-align: center;">LAPORAN DATA KERJASAMA GLOBAL (RAW DATA)</th>
            </tr>
            <tr>
                <th colspan="34" style="font-style: italic; text-align: center;">Dicetak pada: {{ date('d/m/Y H:i') }}</th>
            </tr>
            <tr>
                <!-- INFORMASI UMUM -->
                <th style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000;">No</th>
                <th style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000;">NAMA PROGRAM / KEGIATAN</th>
                <th style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000;">JENIS KERJA SAMA (Ruang Lingkup)</th>
                <th style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000;">NAMA MITRA DUDIKA</th>
                <th style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000;">NEGARA</th>
                <th style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000;">UNIT PELAKSANA DI POLIMDO</th>
                <th style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000;">PERIODE MULAI</th>
                <th style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000;">PERIODE SELESAI</th>
                <th style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000;">NOMOR MOU/MOA</th>
                <th style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000;">TANGGAL MOU/MOA</th>
                
                <!-- TUJUAN DAN SASARAN -->
                <th style="background-color: #fef3c7; font-weight: bold; border: 1px solid #000;">TUJUAN KERJASAMA</th>
                <th style="background-color: #fef3c7; font-weight: bold; border: 1px solid #000;">SASARAN YANG INGIN DICAPAI</th>
                
                <!-- PELAKSANAAN KEGIATAN -->
                <th style="background-color: #dcfce7; font-weight: bold; border: 1px solid #000;">DESKRIPSI SINGKAT KEGIATAN</th>
                <th style="background-color: #dcfce7; font-weight: bold; border: 1px solid #000;">CAKUPAN DAN SKALA KEGIATAN</th>
                <th style="background-color: #dcfce7; font-weight: bold; border: 1px solid #000;">JUMLAH PESERTA YANG TERLIBAT</th>
                <th style="background-color: #dcfce7; font-weight: bold; border: 1px solid #000;">SUMBER DAYA YANG DIGUNAKAN</th>
                
                <!-- HASIL DAN CAPAIAN -->
                <th style="background-color: #e0f2fe; font-weight: bold; border: 1px solid #000;">OUTPUT (Hasil Langsung)</th>
                <th style="background-color: #e0f2fe; font-weight: bold; border: 1px solid #000;">OUTCOME (Dampak Jangka Menengah)</th>
                <th style="background-color: #e0f2fe; font-weight: bold; border: 1px solid #000;">MANFAAT BAGI MAHASISWA</th>
                <th style="background-color: #e0f2fe; font-weight: bold; border: 1px solid #000;">MANFAAT BAGI POLIMDO</th>
                <th style="background-color: #e0f2fe; font-weight: bold; border: 1px solid #000;">MANFAAT BAGI MITRA DUDIKA</th>
                
                <!-- EVALUASI KINERJA -->
                <th style="background-color: #f3e8ff; font-weight: bold; border: 1px solid #000;">KESESUAIAN DENGAN RENCANA</th>
                <th style="background-color: #f3e8ff; font-weight: bold; border: 1px solid #000;">KUALITAS PELAKSANAAN</th>
                <th style="background-color: #f3e8ff; font-weight: bold; border: 1px solid #000;">KETERLIBATAN MITRA DUDIKA</th>
                <th style="background-color: #f3e8ff; font-weight: bold; border: 1px solid #000;">EFISIENSI PENGGUNAAN SUMBER DAYA</th>
                <th style="background-color: #f3e8ff; font-weight: bold; border: 1px solid #000;">KEPUASAN PIHAK TERKAIT</th>
                
                <!-- PERMASALAHAN DAN SOLUSI -->
                <th style="background-color: #fee2e2; font-weight: bold; border: 1px solid #000;">KENDALA YANG DIHADAPI</th>
                <th style="background-color: #fee2e2; font-weight: bold; border: 1px solid #000;">UPAYA MENGATASI KENDALA</th>
                <th style="background-color: #fee2e2; font-weight: bold; border: 1px solid #000;">REKOMENDASI PERBAIKAN</th>
                
                <!-- DOKUMENTASI & KESIMPULAN -->
                <th style="background-color: #f1f5f9; font-weight: bold; border: 1px solid #000;">DOKUMEN PENDUKUNG (Link)</th>
                <th style="background-color: #f1f5f9; font-weight: bold; border: 1px solid #000;">RINGKASAN EVALUASI</th>
                <th style="background-color: #f1f5f9; font-weight: bold; border: 1px solid #000;">SARAN</th>
                <th style="background-color: #f1f5f9; font-weight: bold; border: 1px solid #000;">TINDAK LANJUT</th>
                <th style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
                @php
                    $pengusul = '';
                    if ($item->jurusans->isNotEmpty()) {
                        $pengusul = $item->jurusans->pluck('nama_jurusan')->implode(', ');
                    } elseif ($item->unitKerjas->isNotEmpty()) {
                        $pengusul = $item->unitKerjas->pluck('nama_unit_pelaksana')->implode(', ');
                    }
                    
                    $mitraNames = $item->mitras->pluck('nama_mitra')->join(', ');
                    $mitraNegara = $item->mitras->pluck('negara')->unique()->join(', ');
                    
                    $tujuanText = $item->tujuans->pluck('tujuan')->join("\n");
                    $sasaranText = $item->tujuans->pluck('sasaran')->join("\n");
                    
                    $pelaksanaan = $item->pelaksanaans->first();
                    $hasil = $item->hasils->first();
                    $eval = $item->evaluasis->first();
                    $masalah = $item->permasalahanSolusis->first();
                    $kesimpulan = $item->kesimpulans->first();
                    $dokumentasi = $item->dokumentasis->pluck('link_drive')->filter()->join("\n");
                @endphp
                <tr>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $item->nama_kegiatan }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $item->jenisKerjasama->pluck('nama_kerjasama')->join(', ') }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $mitraNames }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $mitraNegara ?: 'Indonesia' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $pengusul ?: 'N/A' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $item->periode_mulai ? $item->periode_mulai->format('d/m/Y') : '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $item->periode_selesai ? $item->periode_selesai->format('d/m/Y') : '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $item->nomor_mou ?? '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $item->tanggal_mou ? $item->tanggal_mou->format('d/m/Y') : '-' }}</td>
                    
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $tujuanText }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $sasaranText }}</td>
                    
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $pelaksanaan->deskripsi ?? '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $pelaksanaan->cakupan ?? '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $pelaksanaan->jumlah_peserta ?? 0 }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $pelaksanaan->sumber_daya ?? '-' }}</td>
                    
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $hasil->hasil_langsung ?? '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $hasil->dampak ?? '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $hasil->manfaat_mahasiswa ?? '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $hasil->manfaat_polimdo ?? '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $hasil->manfaat_mitra ?? '-' }}</td>
                    
                    <td style="border: 1px solid #000; vertical-align: top; text-align: center;">{{ $eval->sesuai_rencana ?? '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top; text-align: center;">{{ $eval->kualitas ?? '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top; text-align: center;">{{ $eval->keterlibatan ?? '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top; text-align: center;">{{ $eval->efisiensi ?? '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top; text-align: center;">{{ $eval->kepuasan ?? '-' }}</td>
                    
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $masalah->kendala ?? '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $masalah->solusi ?? '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $masalah->rekomendasi ?? '-' }}</td>
                    
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $dokumentasi ?: '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $kesimpulan->ringkasan ?? '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $kesimpulan->saran ?? '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $kesimpulan->tindak_lanjut ?? '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top; font-weight: bold;">{{ $item->status_label }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
