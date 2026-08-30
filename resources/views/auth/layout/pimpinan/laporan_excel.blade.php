<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <table>
        <thead>
            <tr>
                <th colspan="8" style="font-size: 14pt; font-weight: bold; text-align: center;">LAPORAN REKAPITULASI KERJASAMA POLITEKNIK NEGERI MANADO</th>
            </tr>
            <tr>
                <th colspan="8" style="font-style: italic; text-align: center;">Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }} WITA</th>
            </tr>
            <tr>
                <th style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000; text-align: center;">No</th>
                <th style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000;">Nomor Dokumen</th>
                <th style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000;">Judul Kerjasama</th>
                <th style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000; text-align: center;">Jenis</th>
                <th style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000;">Mitra Kerjasama</th>
                <th style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000;">Unit Pelaksana</th>
                <th style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000; text-align: center;">Masa Berlaku</th>
                <th style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000; text-align: center;">Status Berlaku</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
                @php
                    $title = $item->judul ?: ($item->title ?: 'Dokumen Kerjasama');
                    $docNumber = $item->doc_number ?: ($item->nomor_dokumen ?: ($item->pksNumbers->first()?->nomor_pks ?? '-'));
                    
                    $pelaksanaName = $item->internal_instansi ?: 'Politeknik Negeri Manado';
                    if ($item->jurusans->isNotEmpty() || $item->jurusan) {
                        $pelaksanaName = $item->jurusans->pluck('nama_jurusan')->filter()->implode(', ') ?: ($item->jurusan?->nama_jurusan ?? 'Jurusan');
                    } elseif ($item->upas->isNotEmpty() || $item->upa) {
                        $pelaksanaName = $item->upas->pluck('nama_upa')->filter()->implode(', ') ?: ($item->upa?->nama_upa ?? 'UPA');
                    } elseif ($item->pusats->isNotEmpty() || $item->pusat) {
                        $pelaksanaName = $item->pusats->pluck('nama_pusat')->filter()->implode(', ') ?: ($item->pusat?->nama_pusat ?? 'Pusat');
                    }

                    $masaBerlaku = ($item->start_date ? $item->start_date->format('d/m/Y') : '-') . ' s/d ' . ($item->end_date ? $item->end_date->format('d/m/Y') : '-');
                @endphp
                <tr>
                    <td style="border: 1px solid #000; text-align: center;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000;">{{ $docNumber }}</td>
                    <td style="border: 1px solid #000;">{{ $title }}</td>
                    <td style="border: 1px solid #000; text-align: center;">{{ $item->jenis ?? 'MoU' }}</td>
                    <td style="border: 1px solid #000;">{{ $item->mitra ? $item->mitra->nama_mitra : '-' }}</td>
                    <td style="border: 1px solid #000;">{{ $pelaksanaName }}</td>
                    <td style="border: 1px solid #000; text-align: center;">{{ $masaBerlaku }}</td>
                    <td style="border: 1px solid #000; text-align: center;">{{ $item->status_berlaku ?? 'Aktif' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
