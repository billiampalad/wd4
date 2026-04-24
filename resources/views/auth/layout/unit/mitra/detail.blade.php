<!-- Main Content -->
<main id="mainContent">
    <!-- Page Header -->
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-home" style="font-size:11px;"></i>
            <span class="sep">/</span>
            <span style="color: inherit; text-decoration: none;">Kerjasama</span>
            <span class="sep">/</span>
            <a href="{{ route('unit.mitra') }}" style="color: inherit; text-decoration: none;">Mitra</a>
            <span class="sep">/</span>
            <span class="current">Detail Mitra</span>
        </div>
        <h2 id="pageTitle">Detail Informasi Mitra</h2>
        <p id="pageDesc">Informasi lengkap mengenai mitra dan riwayat kerjasama.</p>
    </div>

    <div style="display: grid; grid-template-columns: 350px 1fr; gap: 24px;">
        {{-- Sidebar: Mitra Info --}}
        <div>
            <div class="modern-card" style="padding: 24px;">
                <div style="text-align: center; margin-bottom: 24px;">
                    <div
                        style="width: 80px; height: 80px; border-radius: 20px; background: var(--accent2); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 16px;">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3 style="font-size: 18px; font-weight: 700; color: var(--text); margin-bottom: 8px;">
                        {{ $mitra->nama_mitra }}</h3>
                    <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                        <span
                            class="tag tag-{{ $mitra->kategori == 'nasional' ? 'blue' : 'purple' }}">{{ ucfirst($mitra->kategori) }}</span>
                        @if($mitra->klasifikasi)
                            <span class="tag tag-green">{{ $mitra->klasifikasi->nama }}</span>
                        @endif
                    </div>
                </div>

                <div
                    style="display: flex; flex-direction: column; gap: 16px; border-top: 1px solid var(--border); padding-top: 20px;">
                    <div>
                        <div
                            style="font-size: 11px; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                            Alamat</div>
                        <div
                            style="font-size: 13px; font-weight: 500; color: var(--text); display: flex; align-items: flex-start; gap: 8px;">
                            <i class="fas fa-map-marker-alt"
                                style="color: var(--accent); font-size: 12px; margin-top: 3px;"></i>
                            <span>{{ $mitra->alamat ?? '-' }}</span>
                        </div>
                    </div>

                    <div>
                        <div
                            style="font-size: 11px; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                            Negara</div>
                        <div
                            style="font-size: 14px; font-weight: 600; color: var(--text); display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-globe-asia" style="color: var(--accent); font-size: 12px;"></i>
                            {{ $mitra->negara ?? 'Indonesia' }}
                        </div>
                    </div>

                    <div>
                        <div
                            style="font-size: 11px; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                            Telepon</div>
                        <div
                            style="font-size: 14px; font-weight: 600; color: var(--text); display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-phone" style="color: var(--accent); font-size: 12px;"></i>
                            {{ $mitra->telp ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div
                            style="font-size: 11px; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                            Website</div>
                        <div
                            style="font-size: 14px; font-weight: 600; color: var(--accent); display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-globe" style="font-size: 12px;"></i>
                            @if($mitra->website)
                                <a href="{{ $mitra->website }}" target="_blank"
                                    style="color: var(--accent); text-decoration: none;">{{ $mitra->website }}</a>
                            @else
                                <span style="color: var(--text);">-</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div
                            style="font-size: 11px; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                            Total Kerjasama</div>
                        <div
                            style="font-size: 14px; font-weight: 600; color: var(--text); display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-handshake" style="color: var(--accent); font-size: 12px;"></i>
                            {{ $mitra->kegiatanKerjasamas->count() }} Kegiatan
                        </div>
                    </div>
                </div>

                <div style="margin-top: 24px; display: flex; flex-direction: column; gap: 10px;">
                    <a href="{{ route('unit.mitra.edit', $mitra->id) }}" class="rfc-btn rfc-btn-primary"
                        style="text-decoration: none; justify-content: center; width: 100%;">
                        <i class="fas fa-edit"></i> Edit Data Mitra
                    </a>
                    <a href="{{ route('unit.mitra') }}" class="rfc-btn"
                        style="text-decoration: none; justify-content: center; width: 100%; background: var(--surface2); color: var(--text); border: 1px solid var(--border);">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        {{-- Main: Activities List --}}
        <div class="modern-card">
            <div
                style="padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                <h4 style="font-size: 15px; font-weight: 700; color: var(--text); margin: 0;">Riwayat Kegiatan Kerjasama
                </h4>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-wrap um-table-wrap">
                    <table class="um-table">
                        <thead>
                            <tr>
                                <th class="um-th um-th-num">#</th>
                                <th class="um-th">Nama Kegiatan</th>
                                <th class="um-th">Periode</th>
                                <th class="um-th">Status</th>
                                <th class="um-th">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mitra->kegiatanKerjasamas as $index => $kegiatan)
                                <tr class="um-row">
                                    <td class="um-td um-td-num">
                                        <span class="um-num">{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td class="um-td">
                                        <div style="font-weight: 600; color: var(--text); margin-bottom: 2px;">
                                            {{ $kegiatan->nama_kegiatan }}</div>
                                        <div style="font-size: 11px; color: var(--text-sub);">{{ $kegiatan->jenis_dokumen }}
                                            - {{ $kegiatan->nomor_mou }}</div>
                                    </td>
                                    <td class="um-td">
                                        <div style="font-size: 12px; color: var(--text);">
                                            {{ $kegiatan->periode_mulai ? date('d M Y', strtotime($kegiatan->periode_mulai)) : '-' }}
                                        </div>
                                    </td>
                                    <td class="um-td">
                                        @php
                                            $statusColors = [
                                                'draft' => 'blue',
                                                'submit' => 'orange',
                                                'revisi' => 'red',
                                                'selesai' => 'green'
                                            ];
                                            $color = $statusColors[$kegiatan->status] ?? 'gray';
                                        @endphp
                                        <span class="tag tag-{{ $color }}">{{ ucfirst($kegiatan->status) }}</span>
                                    </td>
                                    <td class="um-td um-td-aksi">
                                        <div class="um-actions">
                                            <a href="{{ route('unit.kerjasama.show', $kegiatan->id) }}" class="um-btn-view"
                                                title="Lihat Detail Kegiatan">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="um-empty">
                                        <div class="um-empty-state">
                                            <div class="um-empty-icon"><i class="fas fa-folder-open"></i></div>
                                            <p class="um-empty-text">Belum ada riwayat kegiatan untuk mitra ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>