<main id="mainContent">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-home" style="font-size:11px;"></i>
            <span class="sep">/</span>
            <span class="current">Monitoring Data</span>
        </div>
        <h2 id="pageTitle">Monitoring Seluruh Kerjasama</h2>
        <p id="pageDesc">Pantau status dan detail seluruh kegiatan kerjasama dari Jurusan dan Unit Kerja.</p>
    </div>

    <div class="card">
        <div class="card-header" style="justify-content: space-between;">
            <div class="card-title">
                <i class="fas fa-desktop"></i> Daftar Kerjasama Global
            </div>
            <div class="header-actions" style="display: flex; gap: 10px;">
                <div class="search-bar" style="width: 250px;">
                    <i class="fas fa-search"></i>
                    <input type="text" id="monitoringSearch" placeholder="Cari kegiatan/pengusul..." class="search-input">
                </div>
            </div>
        </div>

        <div class="table-wrap">
            <table class="um-table" id="monitoringTable">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Pengusul</th>
                        <th>Nama Kegiatan</th>
                        <th>Mitra</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dataKerjasama as $index => $kegiatan)
                        <tr class="um-row">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @php
                                    $isJurusan = $kegiatan->jurusans->count() > 0;
                                    $namaPengusul = $isJurusan 
                                        ? $kegiatan->jurusans->first()->nama_jurusan 
                                        : ($kegiatan->unitKerjas->count() > 0 ? $kegiatan->unitKerjas->first()->nama_unit_pelaksana : '-');
                                @endphp
                                <div style="font-weight: 700; color: var(--text);">{{ $namaPengusul }}</div>
                                <div style="font-size: 11px; color: var(--text-sub);">{{ $isJurusan ? 'Jurusan' : 'Unit Kerja' }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--accent);">{{ $kegiatan->nama_kegiatan }}</div>
                                <div style="font-size: 11px; color: var(--text-sub);">PJ: {{ $kegiatan->penanggung_jawab ?? '-' }}</div>
                            </td>
                            <td>
                                @foreach($kegiatan->mitras as $mitra)
                                    <span class="tag" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9; font-size: 10px; margin-bottom: 2px; display: inline-block;">
                                        {{ $mitra->nama_mitra }}
                                    </span>
                                @endforeach
                            </td>
                            <td style="text-align: center;">
                                <span class="tag {{ $kegiatan->status_class }}" style="font-size: 10px; padding: 4px 10px;">
                                    {{ $kegiatan->status_label }}
                                </span>
                            </td>
                            <td class="um-td">
                                <a href="{{ route('pimpinan.monitoring.detail', $kegiatan->id) }}" class="icon-btn" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="um-empty">
                                <div class="um-empty-state">
                                    <i class="fas fa-folder-open"></i>
                                    <p>Belum ada data kerjasama yang masuk.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
    document.getElementById('monitoringSearch').addEventListener('input', function(e) {
        const q = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#monitoringTable tbody tr.um-row');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });
    });
</script>
