<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Kerjasama</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .kop-surat {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .kop-surat h2 {
            margin: 0;
            font-size: 13pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.5px;
        }

        .kop-surat h3 {
            margin: 3px 0 0;
            font-size: 11pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #334155;
        }

        .kop-surat p {
            margin: 2px 0;
            font-size: 8.5pt;
            color: #64748b;
        }

        .title-area {
            text-align: center;
            margin-bottom: 16px;
        }

        .title-area h4 {
            margin: 0;
            font-size: 12pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.5px;
        }

        .title-area .sub {
            margin-top: 3px;
            font-size: 8.5pt;
            color: #64748b;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table.data-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: 700;
            font-size: 8.5pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #cbd5e1;
            padding: 7px 6px;
            text-align: left;
        }

        table.data-table td {
            border: 1px solid #e2e8f0;
            padding: 6px 6px;
            vertical-align: top;
            font-size: 8.5pt;
        }

        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-aktif { background: #dcfce7; color: #166534; }
        .badge-proses { background: #e0e7ff; color: #3730a3; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-muted { background: #f1f5f9; color: #475569; }

        .badge-jenis {
            background: #ede9fe;
            color: #5b21b6;
            font-weight: 700;
            font-size: 7.5pt;
            padding: 1px 5px;
            border-radius: 3px;
        }

        .footer-sign {
            margin-top: 25px;
            float: right;
            width: 220px;
            text-align: center;
        }

        .footer-sign p {
            margin: 0;
            font-size: 9pt;
        }

        .footer-sign .space {
            height: 50px;
        }

        .clear {
            clear: both;
        }

        @page {
            margin: 1cm;
        }
    </style>
</head>
<body>
    <div class="kop-surat">
        <h2>KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</h2>
        <h3>POLITEKNIK NEGERI MANADO</h3>
        <p>Jl. Kampus Bahu, Manado 95115, Sulawesi Utara | Telepon: (0431) 861152 | Website: www.polimdo.ac.id</p>
    </div>

    <div class="title-area">
        <h4>Laporan Rekapitulasi Data Kerja Sama</h4>
        <div class="sub">Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }} WITA</div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">No</th>
                <th style="width: 140px;">Nomor Dokumen</th>
                <th>Judul Kerja Sama & Ruang Lingkup</th>
                <th style="width: 50px; text-align: center;">Jenis</th>
                <th style="width: 130px;">Mitra</th>
                <th style="width: 120px;">Unit Pelaksana</th>
                <th style="width: 110px;">Masa Berlaku</th>
                <th style="width: 75px; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
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

                    $status = strtolower($item->status_berlaku ?? '');
                    $badgeClass = 'badge-muted';
                    if ($status === 'aktif') $badgeClass = 'badge-aktif';
                    elseif ($status === 'proses') $badgeClass = 'badge-proses';
                    elseif (str_contains($status, 'perpanjangan')) $badgeClass = 'badge-warning';
                    elseif (in_array($status, ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa'])) $badgeClass = 'badge-danger';
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td><strong style="color: #0f172a;">{{ $docNumber }}</strong></td>
                    <td>
                        <strong style="color: #0f172a;">{{ $title }}</strong>
                        @if($item->ruang_lingkup)
                            <div style="font-size: 7.5pt; color: #64748b; margin-top: 2px;">{{ Str::limit($item->ruang_lingkup, 100) }}</div>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <span class="badge-jenis">{{ $item->jenis ?? 'MoU' }}</span>
                    </td>
                    <td>{{ $item->mitra->nama_mitra ?? '-' }}</td>
                    <td>{{ $pelaksanaName }}</td>
                    <td style="font-size: 8pt;">
                        {{ $item->start_date ? $item->start_date->format('d/m/Y') : '-' }} s/d<br>
                        {{ $item->end_date ? $item->end_date->format('d/m/Y') : '-' }}
                    </td>
                    <td style="text-align: center;">
                        <span class="badge {{ $badgeClass }}">{{ $item->status_berlaku ?? 'Aktif' }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 25px; color: #64748b;">
                        Tidak ada data kerja sama yang sesuai dengan kriteria filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-sign">
        <p>Manado, {{ now()->translatedFormat('d F Y') }}</p>
        <p>Mengetahui,</p>
        <p><strong>Pimpinan Politeknik Negeri Manado</strong></p>
        <div class="space"></div>
        <p>__________________________</p>
        <p>NIP. ............................</p>
    </div>

    <div class="clear"></div>
    <p style="font-size: 7.5pt; color: #94a3b8; margin-top: 20px;">
        Dokumen ini di-generate secara otomatis oleh SIM Kerjasama Politeknik Negeri Manado.
    </p>
</body>
</html>