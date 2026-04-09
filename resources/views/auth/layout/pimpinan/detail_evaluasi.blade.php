<main id="mainContent">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-home" style="font-size:11px;"></i>
            <span class="sep">/</span>
            <a href="{{ route('pimpinan.evaluasi') }}">Evaluasi & Validasi</a>
            <span class="sep">/</span>
            <span class="current">Detail Evaluasi</span>
        </div>
        <h2 id="pageTitle">Detail Evaluasi Laporan</h2>
        <p id="pageDesc">Tinjau detail laporan sebelum memberikan penilaian atau validasi.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-6">
        {{-- Kolom Kiri: Informasi Detail (Lebih Lebar) --}}
        <div class="lg:col-span-8 flex flex-col gap-6">

            {{-- Informasi Umum --}}
            <div class="eval-card">
                <div class="eval-header">
                    <div class="icon-box"><i class="fas fa-info-circle"></i></div>
                    <h3>I. Informasi Umum</h3>
                </div>
                <div class="spec-grid">
                    <div class="spec-item">
                        <span class="spec-label">Nama Kegiatan</span>
                        <span class="spec-val">{{ $kegiatan->nama_kegiatan }}</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Status</span>
                        @php
                            $statusClass = $kegiatan->status == 'menunggu_evaluasi' || $kegiatan->status == 'menunggu_validasi' ? 'warning' : ($kegiatan->status == 'selesai' ? 'success' : 'danger');
                        @endphp
                        <span
                            class="badge-status {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $kegiatan->status)) }}</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Jenis Kerjasama</span>
                        <span
                            class="spec-val">{{ $kegiatan->jenisKerjasama->pluck('nama_kerjasama')->join(', ') ?: '-' }}</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Nomor MOU</span>
                        <span class="spec-val">{{ $kegiatan->nomor_mou ?? '-' }}</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Tanggal MOU</span>
                        <span
                            class="spec-val">{{ $kegiatan->tanggal_mou ? $kegiatan->tanggal_mou->format('d M Y') : '-' }}</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Periode</span>
                        <span class="spec-val">
                            {{ $kegiatan->periode_mulai ? $kegiatan->periode_mulai->format('d M Y') : '-' }} s/d
                            {{ $kegiatan->periode_selesai ? $kegiatan->periode_selesai->format('d M Y') : 'Selesai' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Unit Pelaksana & Mitra --}}
            <div class="eval-card">
                <div class="eval-header">
                    <div class="icon-box"
                        style="background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(5,150,105,0.1)); color: var(--success);">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>II. Unit Pelaksana & Mitra</h3>
                </div>
                <div class="spec-grid">
                    <div class="spec-item">
                        <span class="spec-label">Unit Pelaksana</span>
                        @php
                            $pengusul = $kegiatan->jurusans->pluck('nama_jurusan')->merge($kegiatan->unitKerjas->pluck('nama_unit_pelaksana'))->join(', ');
                        @endphp
                        <span class="spec-val">{{ $pengusul ?: 'N/A' }}</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Nama Mitra Dudika</span>
                        <span class="spec-val">{{ $kegiatan->mitras->pluck('nama_mitra')->join(', ') ?: '-' }}</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Negara / Wilayah</span>
                        <span
                            class="spec-val">{{ $kegiatan->mitras->pluck('negara')->unique()->join(', ') ?: 'Indonesia' }}</span>
                    </div>
                </div>
            </div>

            {{-- Pelaksanaan & Hasil --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="eval-card">
                    <div class="eval-header">
                        <div class="icon-box"
                            style="width: 36px; height: 36px; font-size: 14px; background: rgba(245,158,11,0.1); color: var(--warning);">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h3 style="font-size: 15px;">III. Pelaksanaan</h3>
                    </div>
                    @php $pel = $kegiatan->pelaksanaans->first(); @endphp
                    @if($pel)
                        <div class="mt-3 flex flex-col gap-3">
                            <div><span class="spec-label">Deskripsi</span><span
                                    class="spec-val text-sm font-normal">{{ $pel->deskripsi ?? '-' }}</span></div>
                            <div><span class="spec-label" style="margin-top:10px">Cakupan</span><span
                                    class="spec-val text-sm font-normal">{{ $pel->cakupan ?? '-' }}</span></div>
                            <div><span class="spec-label" style="margin-top:10px">Peserta</span><span
                                    class="spec-val text-sm font-normal">{{ $pel->jumlah_peserta ?? 0 }} Orang</span></div>
                            <div><span class="spec-label" style="margin-top:10px">Sumber Data</span><span
                                    class="spec-val text-sm font-normal">{{ $pel->sumber_data ?? '-' }}</span></div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 mt-2">Tidak ada data pelaksanaan.</p>
                    @endif
                </div>

                <div class="eval-card">
                    <div class="eval-header">
                        <div class="icon-box"
                            style="width: 36px; height: 36px; font-size: 14px; background: rgba(14,165,233,0.1); color: var(--accent3);">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 style="font-size: 15px;">IV. Capaian & Hasil</h3>
                    </div>
                    @php $h = $kegiatan->hasils->first(); @endphp
                    @if($h)
                        <div class="mt-3 flex flex-col gap-3">
                            <div><span class="spec-label">Output</span><span
                                    class="spec-val text-sm font-normal">{{ $h->hasil_langsung ?? '-' }}</span></div>
                            <div><span class="spec-label" style="margin-top:10px">Outcome</span><span
                                    class="spec-val text-sm font-normal">{{ $h->dampak ?? '-' }}</span></div>
                            <div><span class="spec-label" style="margin-top:10px">Manfaat Mahasiswa</span><span
                                    class="spec-val text-sm font-normal">{{ $h->manfaat_mahasiswa ?? '-' }}</span></div>
                            <div><span class="spec-label" style="margin-top:10px">Manfaat Institusi</span><span
                                    class="spec-val text-sm font-normal">{{ $h->manfaat_polimdo ?? '-' }}</span></div>
                            <div><span class="spec-label" style="margin-top:10px">Manfaat Mitra</span><span
                                    class="spec-val text-sm font-normal">{{ $h->manfaat_mitra ?? '-' }}</span></div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 mt-2">Tidak ada data hasil.</p>
                    @endif
                </div>
            </div>

            {{-- Evaluasi Unit Kerja --}}
            @if($kegiatan->unitKerjas->count() > 0)
                <div class="eval-card">
                    <div class="eval-header">
                        <div class="icon-box" style="background: rgba(239,68,68,0.1); color: var(--danger);"><i
                                class="fas fa-clipboard-check"></i></div>
                        <h3>V. Historis Evaluasi Internal (Unit Kerja)</h3>
                    </div>
                    @php $e = $kegiatan->evaluasis->first(); @endphp
                    @if($e)
                        <div class="spec-grid">
                            <div class="spec-item"><span class="spec-label">Kesesuaian Rencana</span><span
                                    class="spec-val text-xl text-danger">{{ $e->sesuai_rencana ?? '-' }}<span
                                        class="text-sm text-gray-400">/5</span></span></div>
                            <div class="spec-item"><span class="spec-label">Kualitas Pelaksanaan</span><span
                                    class="spec-val text-xl text-danger">{{ $e->kualitas ?? '-' }}<span
                                        class="text-sm text-gray-400">/5</span></span></div>
                            <div class="spec-item"><span class="spec-label">Keterlibatan Mitra</span><span
                                    class="spec-val text-xl text-danger">{{ $e->keterlibatan ?? '-' }}<span
                                        class="text-sm text-gray-400">/5</span></span></div>
                            <div class="spec-item"><span class="spec-label">Kepuasan Pihak Terkait</span><span
                                    class="spec-val text-xl text-danger">{{ $e->kepuasan ?? '-' }}<span
                                        class="text-sm text-gray-400">/5</span></span></div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 mt-2">Evaluasi unit belum diisi.</p>
                    @endif
                </div>
            @endif

            {{-- Permasalahan & Solusi --}}
            @if($kegiatan->permasalahanSolusis->count() > 0)
                <div class="eval-card">
                    <div class="eval-header">
                        <div class="icon-box" style="background: rgba(245,158,11,0.1); color: var(--warning);"><i
                                class="fas fa-exclamation-circle"></i></div>
                        <h3>VI. Permasalahan & Solusi</h3>
                    </div>
                    <div class="mt-4">
                        @foreach($kegiatan->permasalahanSolusis as $ps)
                            <div class="solusi-box">
                                <p><strong>Kendala:</strong> {{ $ps->permasalahan ?? '-' }}</p>
                                <p><strong>Solusi:</strong> {{ $ps->solusi ?? '-' }}</p>
                                <p><strong>Rekomendasi:</strong> {{ $ps->rekomendasi ?? '-' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Dokumentasi Pendukung --}}
            @if($kegiatan->dokumentasis->whereNotNull('link_drive')->isNotEmpty())
                <div class="eval-card">
                    <div class="eval-header">
                        <div class="icon-box" style="background: rgba(14,165,233,0.1); color: var(--accent3);"><i
                                class="fas fa-folder-open"></i></div>
                        <h3>VII. Dokumentasi Pendukung</h3>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-4">
                        @foreach($kegiatan->dokumentasis as $dok)
                            @if($dok->link_drive)
                                <a href="{{ $dok->link_drive }}" target="_blank" class="doc-link">
                                    <i class="fab fa-google-drive"></i>
                                    <span>{{ $dok->keterangan ?: 'Buka File Drive' }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- Kolom Kanan: Form Evaluasi --}}
        <div class="lg:col-span-4 relative">
            <div class="eval-form-box sticky top-[100px]">
                <div class="eval-header" style="border-bottom: none; margin-bottom: 10px; padding-bottom: 0;">
                    <div class="icon-box"
                        style="background: linear-gradient(135deg, var(--accent), var(--accent2)); color: #fff;"><i
                            class="fas fa-star"></i></div>
                    <h3>Beri Penilaian</h3>
                </div>
                <p class="text-xs text-gray-500 mb-6 font-medium" style="padding-bottom: 10px;">Beri skor, rekomendasi
                    tindak lanjut, dan keputusan akhir pada laporan ini.</p>

                <form id="evaluateForm" method="POST" action="{{ route('pimpinan.evaluate', $kegiatan->id) }}">
                    @csrf

                    @if($kegiatan->unitKerjas->count() > 0)
                        <div class="info-box mb-4"
                            style="background: rgba(79,70,229,0.05); border-left: 3px solid var(--accent); padding: 12px; border-radius: 6px;">
                            <p class="text-xs text-accent font-medium"><i class="fas fa-info-circle mr-1"></i> Skor internal
                                telah diisi. Pimpinan cukup memberi ringkasan & validasi.</p>
                        </div>
                    @elseif($kegiatan->jurusans->count() > 0)
                        <div class="mb-6">
                            @php
                                $criteria = [
                                    ['name' => 'sesuai_rencana', 'label' => 'Kesesuaian Rencana', 'icon' => 'fa-bullseye'],
                                    ['name' => 'kualitas', 'label' => 'Kualitas Pelaksanaan', 'icon' => 'fa-gem'],
                                    ['name' => 'keterlibatan', 'label' => 'Keterlibatan Mitra', 'icon' => 'fa-users'],
                                    ['name' => 'efisiensi', 'label' => 'Efisiensi Sumber Daya', 'icon' => 'fa-chart-line'],
                                    ['name' => 'kepuasan', 'label' => 'Kepuasan Terkait', 'icon' => 'fa-face-smile'],
                                ];
                            @endphp

                            @foreach($criteria as $c)
                                <div class="rating-group">
                                    <div class="rating-icon-box"><i class="fas {{ $c['icon'] }}"></i></div>
                                    <div class="rating-content">
                                        <div class="rating-header">
                                            <h4 class="rating-title">{{ $c['label'] }}</h4>
                                            <div class="rating-score" id="score-display-{{ $c['name'] }}">0/5</div>
                                        </div>
                                        <div class="star-rating" data-name="{{ $c['name'] }}">
                                            @for($i = 1; $i <= 5; $i++)
                                                <button type="button" class="star-btn" data-value="{{ $i }}"
                                                    style="color: rgba(0,0,0,.15);"><i class="fas fa-star"></i></button>
                                            @endfor
                                            <input type="hidden" name="{{ $c['name'] }}" id="input-{{ $c['name'] }}" value="0"
                                                required>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="field-group mt-4">
                        <label for="ringkasan"
                            style="font-size:12px; font-weight:700; margin-bottom: 8px; display: block;">Ringkasan
                            Evaluasi (Opsional)</label>
                        <textarea name="ringkasan" id="ringkasan" class="custom-textarea"
                            placeholder="Ringkasan capaian kinerja..."></textarea>
                    </div>

                    <div class="field-group mt-4">
                        <label for="saran"
                            style="font-size:12px; font-weight:700; margin-bottom: 8px; display: block; padding-top: 12px;">Saran Tindak
                            Lanjut (Opsional)</label>
                        <textarea name="saran" id="saran" class="custom-textarea"
                            placeholder="Apa yang perlu diperbaiki ke depannya..."></textarea>
                    </div>

                    <div class="field-group mt-4">
                        <label for="catatan"
                            style="font-size:12px; font-weight:700; margin-bottom: 8px; display: block; padding-top: 12px;">Catatan
                            Tambahan (Opsional)</label>
                        <textarea name="catatan" id="catatan" class="custom-textarea"
                            placeholder="Catatan ekstra untuk diperhatikan..."></textarea>
                    </div>

                    <div class="field-group mt-6">
                        <label for="status_validasi"
                            style="font-size:13px; font-weight:800; color:var(--text); margin-bottom: 8px; display: block; padding-top: 12px;">Keputusan
                            Validasi Akhir</label>

                        <div class="custom-dropdown-container">
                            <!-- Native select hidden but used for form submission -->
                            <select name="status_validasi" id="status_validasi" required
                                style="opacity: 0; position: absolute; bottom: 0; width: 100%; height: 1px; z-index: -1;">
                                <option value="" disabled selected></option>
                                <option value="layak">Layak / Disetujui</option>
                                <option value="tidak_layak">Tidak Layak / Perlu Revisi</option>
                            </select>

                            <div class="custom-dropdown-trigger" id="customDropdownTrigger">
                                <span>Pilih Keputusan Akhir...</span>
                                <i class="fas fa-chevron-down custom-dropdown-arrow"></i>
                            </div>

                            <div class="custom-dropdown-menu" id="customDropdownMenu">
                                <div class="custom-dropdown-option" data-value="layak">
                                    <div class="icon green"><i class="fas fa-check"></i></div>
                                    <div class="text">
                                        <h4>Layak / Disetujui</h4>
                                        <p>Laporan disetujui dan proses selesai</p>
                                    </div>
                                    <i class="fas fa-check check-icon"></i>
                                </div>
                                <div class="custom-dropdown-option" data-value="tidak_layak">
                                    <div class="icon red"><i class="fas fa-times"></i></div>
                                    <div class="text">
                                        <h4>Tidak Layak / Perlu Revisi</h4>
                                        <p>Laporan dikembalikan untuk diperbaiki</p>
                                    </div>
                                    <i class="fas fa-check check-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <a href="{{ route('pimpinan.evaluasi') }}" class="rfc-btn btn-cancel">Batal</a>
                        <button type="submit" class="rfc-btn">Kirim Penilaian <i
                                class="fas fa-paper-plane ml-2"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
