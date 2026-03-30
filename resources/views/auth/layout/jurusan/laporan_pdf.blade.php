<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Kerjasama</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18pt; }
        .header p { margin: 5px 0 0; font-size: 11pt; color: #666; }
        
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; font-weight: bold; }
        
        .section-title { font-weight: bold; background: #eee; padding: 5px 10px; margin-top: 15px; border-left: 4px solid #3182ce; }
        .detail-row { margin-bottom: 5px; }
        .detail-label { font-weight: bold; display: inline-block; width: 150px; }
        
        .page-break { page-break-after: always; }
        
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 8pt; font-weight: bold; }
        .badge-selesai { background: #c6f6d5; color: #22543d; }
        .badge-menunggu { background: #feebc8; color: #975a16; }
        .badge-draft { background: #edf2f7; color: #4a5568; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA KERJASAMA JURUSAN</h1>
        <p>Sistem Informasi Kerjasama Polimdo & DUDIKA</p>
        <p>Dicetak pada: {{ date('d/m/Y H:i') }}</p>
    </div>

    @foreach($data as $index => $item)
        <div class="item-container">
            <div class="section-title">#{{ $index + 1 }}: {{ $item->nama_kegiatan }}</div>
            
            <table class="table" style="margin-top: 10px;">
                <tr>
                    <th width="25%">Jenis Kerjasama</th>
                    <td>{{ $item->jenisKerjasama->pluck('nama_kerjasama')->join(', ') ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Mitra</th>
                    <td>{{ $item->mitras->pluck('nama_mitra')->join(', ') }}</td>
                </tr>
                <tr>
                    <th>Periode</th>
                    <td>{{ $item->periode_mulai ? $item->periode_mulai->format('d/m/Y') : '-' }} s/d {{ $item->periode_selesai ? $item->periode_selesai->format('d/m/Y') : '-' }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge badge-{{ $item->status }}">
                            {{ $item->status == 'selesai' ? 'Selesai/Layak' : ($item->status == 'menunggu' ? 'Menunggu Evaluasi' : 'Draft') }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Nomor MoU/MoA</th>
                    <td>{{ $item->nomor_mou ?? '-' }} (Tgl: {{ $item->tanggal_mou ? $item->tanggal_mou->format('d/m/Y') : '-' }})</td>
                </tr>
            </table>

            @if($item->tujuans->count() > 0)
                <strong>Tujuan & Sasaran:</strong>
                <ul>
                    @foreach($item->tujuans as $tujuan)
                        <li><strong>Tujuan:</strong> {{ $tujuan->tujuan }} <br> <strong>Sasaran:</strong> {{ $tujuan->sasaran }}</li>
                    @endforeach
                </ul>
            @endif

            @if($item->pelaksanaans->count() > 0)
                <strong>Pelaksanaan:</strong>
                <ul>
                    @foreach($item->pelaksanaans as $p)
                        <li>{{ $p->deskripsi }} (Peserta: {{ $p->jumlah_peserta ?? 0 }}, Sumber Daya: {{ $p->sumber_daya ?? '-' }})</li>
                    @endforeach
                </ul>
            @endif

            @if($item->hasils->count() > 0)
                <strong>Dampak & Manfaat:</strong>
                <ul>
                    @foreach($item->hasils as $h)
                        <li>{{ $h->dampak ?? '-' }} <br> Manfaat Mahasiswa: {{ $h->manfaat_mahasiswa ?? '-' }}</li>
                    @endforeach
                </ul>
            @endif

            @if($item->evaluasis->count() > 0)
                <strong>Hasil Evaluasi:</strong>
                <ul>
                    @foreach($item->evaluasis as $e)
                        <li><strong>Catatan:</strong> {{ $e->catatan ?? '-' }} (Oleh: {{ $e->penilai ? $e->penilai->name : '-' }})</li>
                    @endforeach
                </ul>
            @endif
        </div>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
