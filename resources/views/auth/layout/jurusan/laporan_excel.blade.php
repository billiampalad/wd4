<table>
    <thead>
        <tr>
            <th colspan="12" style="font-size: 16pt; font-weight: bold; text-align: center;">LAPORAN DATA KERJASAMA JURUSAN</th>
        </tr>
        <tr>
            <th colspan="12" style="font-style: italic; text-align: center;">Dicetak pada: {{ date('d/m/Y H:i') }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th style="background-color: #f2f2f2; font-weight: bold; border: 1px solid #000;">No</th>
            <th style="background-color: #f2f2f2; font-weight: bold; border: 1px solid #000;">Nama Kegiatan</th>
            <th style="background-color: #f2f2f2; font-weight: bold; border: 1px solid #000;">Jenis Kerjasama</th>
            <th style="background-color: #f2f2f2; font-weight: bold; border: 1px solid #000;">Mitra</th>
            <th style="background-color: #f2f2f2; font-weight: bold; border: 1px solid #000;">Periode Mulai</th>
            <th style="background-color: #f2f2f2; font-weight: bold; border: 1px solid #000;">Periode Selesai</th>
            <th style="background-color: #f2f2f2; font-weight: bold; border: 1px solid #000;">Status</th>
            <th style="background-color: #f2f2f2; font-weight: bold; border: 1px solid #000;">Tujuan & Sasaran</th>
            <th style="background-color: #f2f2f2; font-weight: bold; border: 1px solid #000;">Pelaksanaan/Deskripsi</th>
            <th style="background-color: #f2f2f2; font-weight: bold; border: 1px solid #000;">Jumlah Peserta</th>
            <th style="background-color: #f2f2f2; font-weight: bold; border: 1px solid #000;">Dampak & Manfaat</th>
            <th style="background-color: #f2f2f2; font-weight: bold; border: 1px solid #000;">Hasil Evaluasi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $item)
            <tr>
                <td style="border: 1px solid #000;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000;">{{ $item->nama_kegiatan }}</td>
                <td style="border: 1px solid #000;">{{ $item->jenisKerjasama->pluck('nama_kerjasama')->join(', ') ?: '-' }}</td>
                <td style="border: 1px solid #000;">{{ $item->mitras->pluck('nama_mitra')->join(', ') }}</td>
                <td style="border: 1px solid #000;">{{ $item->periode_mulai ? $item->periode_mulai->format('d/m/Y') : '-' }}</td>
                <td style="border: 1px solid #000;">{{ $item->periode_selesai ? $item->periode_selesai->format('d/m/Y') : '-' }}</td>
                <td style="border: 1px solid #000;">{{ $item->status == 'selesai' ? 'Selesai/Layak' : ($item->status == 'menunggu' ? 'Menunggu Evaluasi' : 'Draft') }}</td>
                <td style="border: 1px solid #000;">
                    @foreach($item->tujuans as $t)
                        Tujuan: {{ $t->tujuan }}; Sasaran: {{ $t->sasaran }} <br>
                    @endforeach
                </td>
                <td style="border: 1px solid #000;">
                    @foreach($item->pelaksanaans as $p)
                        {{ $p->deskripsi }} ({{ $p->sumber_daya }}) <br>
                    @endforeach
                </td>
                <td style="border: 1px solid #000;">
                    {{ $item->pelaksanaans->sum('jumlah_peserta') }}
                </td>
                <td style="border: 1px solid #000;">
                    @foreach($item->hasils as $h)
                        Dampak: {{ $h->dampak }}; Manfaat MHS: {{ $h->manfaat_mahasiswa }} <br>
                    @endforeach
                </td>
                <td style="border: 1px solid #000;">
                    @foreach($item->evaluasis as $e)
                        Catatan: {{ $e->catatan }} ({{ $e->penilai ? $e->penilai->name : '-' }}) <br>
                    @endforeach
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
