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
                    $pengusul = $item->pelaksana_name;
                    
                    $mitraNames = $item->mitra->nama_mitra ?? '-';
                    $mitraNegara = $item->mitra->negara ?? 'Indonesia';
                    
                    $tujuanText = '-';
                    $sasaranText = '-';
                    
                    $pelaksanaan = null;
                    $hasil = null;
                    $eval = $item->evaluasis ? $item->evaluasis->first() : null;
                    $masalah = null;
                    $kesimpulan = null;
                    $dokumentasi = '-';
                @endphp
                <tr>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $item->title }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $item->jenis }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $mitraNames }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $mitraNegara }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $pengusul }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $item->start_date ? $item->start_date->format('d/m/Y') : '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $item->end_date ? $item->end_date->format('d/m/Y') : '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $item->doc_number ?? '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $item->start_date ? $item->start_date->format('d/m/Y') : '-' }}</td>
                    
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $tujuanText }}</td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $sasaranText }}</td>
                    
                    <td style="border: 1px solid #000; vertical-align: top;">-</td>
                    <td style="border: 1px solid #000; vertical-align: top;">-</td>
                    <td style="border: 1px solid #000; vertical-align: top;">-</td>
                    <td style="border: 1px solid #000; vertical-align: top;">-</td>
                    
                    <td style="border: 1px solid #000; vertical-align: top;">-</td>
                    <td style="border: 1px solid #000; vertical-align: top;">-</td>
                    <td style="border: 1px solid #000; vertical-align: top;">-</td>
                    <td style="border: 1px solid #000; vertical-align: top;">-</td>
                    <td style="border: 1px solid #000; vertical-align: top;">-</td>
                    
                    <td style="border: 1px solid #000; vertical-align: top; text-align: center;">{{ $eval ? $eval->sesuai_rencana : '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top; text-align: center;">{{ $eval ? $eval->kualitas : '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top; text-align: center;">{{ $eval ? $eval->keterlibatan : '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top; text-align: center;">{{ $eval ? $eval->efisiensi : '-' }}</td>
                    <td style="border: 1px solid #000; vertical-align: top; text-align: center;">{{ $eval ? $eval->kepuasan : '-' }}</td>
                    
                    <td style="border: 1px solid #000; vertical-align: top;">-</td>
                    <td style="border: 1px solid #000; vertical-align: top;">-</td>
                    <td style="border: 1px solid #000; vertical-align: top;">-</td>
                    
                    <td style="border: 1px solid #000; vertical-align: top;">-</td>
                    <td style="border: 1px solid #000; vertical-align: top;">-</td>
                    <td style="border: 1px solid #000; vertical-align: top;">-</td>
                    <td style="border: 1px solid #000; vertical-align: top;">-</td>
                    <td style="border: 1px solid #000; vertical-align: top; font-weight: bold;">{{ ucfirst($item->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
