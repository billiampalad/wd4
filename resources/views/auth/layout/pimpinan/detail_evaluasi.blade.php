<main id="mainContent" x-data="evaluationForm()">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <div class="ev-header-container">
        <div class="ev-breadcrumb">
            <i class="fas fa-home"></i> <span class="mx-2">/</span>
            <a href="{{ route('pimpinan.dashboard') }}">Dashboard</a> <span class="mx-2">/</span>
            Administrasi <span class="mx-2">/</span>
            <span class="text-blue">Validasi</span>
        </div>
        <h2 id="pageTitle" class="ev-page-title">Evaluasi Kerjasama Strategis</h2>
        <p class="ev-page-desc">Tinjau keselarasan, rincian, dan capaian kerjasama sebelum pengesahan.</p>
    </div>

    {{-- Skeleton Loading --}}
    <div x-show="isLoading" class="ev-skeleton-detail"
        style="position: relative; inset: auto; background: transparent; padding: 0; margin-top: 24px;" x-cloak>
        <div class="ev-skeleton-col-main">
            <div class="ev-skeleton-box" style="height: 200px;"></div>
            <div class="ev-skeleton-box" style="height: 150px;"></div>
        </div>
        <div class="ev-skeleton-col-side">
            <div class="ev-skeleton-box" style="height: 400px;"></div>
        </div>
    </div>

    {{-- Main Content --}}
    <div x-show="!isLoading" style="margin-top: 24px;" x-cloak>
        <div class="ev-detail-grid">

            {{-- Kolom Kiri: Ringkasan Data (70%) --}}
            <div class="ev-detail-main">

                {{-- Informasi Utama --}}
                <div class="ev-card">
                    <div class="ev-card-header">
                        <h3 class="ev-card-title">{{ $kegiatan->title }}</h3>
                        @php
                            $statusClass = $kegiatan->status_dokumen == 'Menunggu Evaluasi' ? 'ev-badge-amber' : ($kegiatan->status_dokumen == 'Disahkan' ? 'ev-badge-emerald' : 'ev-badge-blue');
                        @endphp
                        <span class="ev-badge {{ $statusClass }}">
                            {{ $kegiatan->status_dokumen }}
                        </span>
                    </div>

                    <div class="ev-card-tags">
                        <span class="ev-tag ev-tag-blue">
                            <i class="fas fa-handshake" style="margin-right:4px;opacity:0.7;"></i>
                            {{ $kegiatan->mitra->nama_mitra ?? 'Mitra N/A' }}
                        </span>
                        <span style="color:#cbd5e1;">•</span>
                        <span class="ev-tag ev-tag-slate">
                            <i class="fas fa-file-contract" style="margin-right:4px;opacity:0.7;"></i>
                            {{ $kegiatan->jenis }}
                        </span>
                    </div>

                    @php $detail = $kegiatan->details->first(); @endphp

                    <div class="ev-alert-emerald">
                        <h4 class="ev-alert-title">
                            <i class="fas fa-bullseye"></i> Analisis Keselarasan (Sasaran)
                        </h4>
                        <p class="ev-alert-text">
                            {{ $detail->sasaran->deskripsi ?? 'Belum ada data sasaran yang dihubungkan.' }}
                        </p>
                    </div>

                    <div class="ev-metrics">
                        <div class="ev-metric-card is-emerald">
                            <div class="ev-metric-bg ev-metric-bg-emerald"></div>
                            <div class="ev-metric-label is-emerald"><i class="fas fa-coins"></i> Nilai Kontrak</div>
                            <div class="ev-metric-val">
                                {{ $detail && $detail->nilai_kontrak ? 'Rp ' . number_format($detail->nilai_kontrak, 0, ',', '.') : 'Rp 0' }}
                            </div>
                        </div>
                        <div class="ev-metric-card is-amber">
                            <div class="ev-metric-bg ev-metric-bg-amber"></div>
                            <div class="ev-metric-label is-amber"><i class="fas fa-calendar-alt"></i> Masa Berlaku</div>
                            <div class="ev-metric-val" style="font-size: 14px; margin-top: 4px;">
                                {{ $kegiatan->start_date ? $kegiatan->start_date->format('M Y') : '-' }} <i
                                    class="fas fa-arrow-right" style="color:#cbd5e1;margin:0 4px;"></i>
                                {{ $kegiatan->end_date ? $kegiatan->end_date->format('M Y') : 'Selesai' }}
                            </div>
                        </div>
                        <div class="ev-metric-card is-blue">
                            <div class="ev-metric-bg ev-metric-bg-blue"></div>
                            <div class="ev-metric-label is-blue"><i class="fas fa-chart-line"></i> Target Luaran</div>
                            <div class="ev-metric-val">{{ $detail->volume_luaran ?? 0 }}
                                <small>{{ $detail->satuan_luaran ?? 'Item' }}</small>
                            </div>
                        </div>
                    </div>

                    {{-- Riwayat --}}
                    <div class="ev-history">
                        <h4 class="ev-history-title">Activity Timeline</h4>
                        <div class="ev-history-item">
                            <div class="ev-history-avatar">
                                {{ strtoupper(substr($kegiatan->pjInternal->name ?? 'S', 0, 1)) }}
                            </div>
                            <div class="ev-history-info">
                                <div class="ev-history-info-name">Dokumen diinput oleh
                                    <span>{{ $kegiatan->pjInternal->name ?? 'Sistem' }}</span>
                                </div>
                                <div class="ev-history-info-time"><i class="far fa-clock"></i>
                                    {{ $kegiatan->created_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Rincian Kegiatan & Hasil --}}
                <div style="display: grid; grid-template-columns: 1fr; gap: 24px;">
                    @media (min-width: 768px) {
                    /* In case we want 2 cols here, but we use 1fr for simplicity to match dashboard */
                    }
                    <div class="ev-card" style="margin-bottom: 0;">
                        <h4 class="ev-attachment-title" style="margin-bottom: 20px;">
                            <i class="fas fa-bullseye" style="color: #f59e0b;"></i> Indikator & Tujuan
                        </h4>
                        <div style="display: flex; flex-direction: column; gap: 16px;">
                            <div>
                                <span class="ev-label" style="margin-bottom: 4px;">Tujuan</span>
                                <p style="font-size: 14px; font-weight: 600; color: #334155; margin:0;">
                                    {{ $detail->tujuan ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <span class="ev-label" style="margin-bottom: 4px;">Indikator Kinerja</span>
                                <p style="font-size: 14px; font-weight: 600; color: #334155; margin:0;">
                                    {{ $detail->indikator_kinerja ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <span class="ev-label" style="margin-bottom: 4px;">PJ Internal</span>
                                <p style="font-size: 14px; font-weight: 600; color: #334155; margin:0;">
                                    {{ $kegiatan->pjInternal->name ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="ev-card">
                        <h4 class="ev-attachment-title">
                            <i class="fas fa-paperclip"></i> Lampiran Pendukung
                        </h4>

                        @if($kegiatan->laporanFiles->count() > 0)
                            <div class="ev-attachment-grid">
                                @foreach($kegiatan->laporanFiles as $file)
                                    <a href="{{ Storage::url($file->file_path) }}" target="_blank"
                                        class="ev-file-btn ev-file-btn-pdf">
                                        <div class="ev-file-icon red"><i class="fas fa-file-pdf"></i></div>
                                        <div class="ev-file-info">
                                            <div class="ev-file-name">{{ basename($file->file_path) }}</div>
                                            <div class="ev-file-desc">Unduh PDF</div>
                                        </div>
                                        <i class="fas fa-download ev-file-action"></i>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="ev-empty-file">
                                <i class="fas fa-folder-open"></i>
                                Tidak ada lampiran dokumen.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Evaluasi Unit Kerja (Historical) --}}
                @if(($kegiatan->upas->count() > 0 || $kegiatan->pusats->count() > 0) && $kegiatan->evaluasis->count() > 0)
                    <div class="ev-card">
                        <h4 class="ev-attachment-title" style="margin-bottom: 20px;">
                            <i class="fas fa-clipboard-check" style="color: #10b981;"></i> Historis Evaluasi Internal
                        </h4>
                        @php $e = $kegiatan->evaluasis->first(); @endphp
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 16px;">
                            <div
                                style="text-align: center; padding: 12px; background: #f8fafc; border-radius: 12px; border: 1px solid #f1f5f9;">
                                <div style="font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 4px;">Rencana
                                </div>
                                <div style="font-size: 18px; font-weight: 800; color: #059669;">
                                    {{ $e->sesuai_rencana ?? '-' }}/5
                                </div>
                            </div>
                            <div
                                style="text-align: center; padding: 12px; background: #f8fafc; border-radius: 12px; border: 1px solid #f1f5f9;">
                                <div style="font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 4px;">Kualitas
                                </div>
                                <div style="font-size: 18px; font-weight: 800; color: #059669;">{{ $e->kualitas ?? '-' }}/5
                                </div>
                            </div>
                            <div
                                style="text-align: center; padding: 12px; background: #f8fafc; border-radius: 12px; border: 1px solid #f1f5f9;">
                                <div style="font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 4px;">Mitra
                                </div>
                                <div style="font-size: 18px; font-weight: 800; color: #059669;">
                                    {{ $e->keterlibatan ?? '-' }}/5
                                </div>
                            </div>
                            <div
                                style="text-align: center; padding: 12px; background: #f8fafc; border-radius: 12px; border: 1px solid #f1f5f9;">
                                <div style="font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 4px;">
                                    Efisiensi</div>
                                <div style="font-size: 18px; font-weight: 800; color: #059669;">{{ $e->efisiensi ?? '-' }}/5
                                </div>
                            </div>
                            <div
                                style="text-align: center; padding: 12px; background: #f8fafc; border-radius: 12px; border: 1px solid #f1f5f9;">
                                <div style="font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 4px;">Kepuasan
                                </div>
                                <div style="font-size: 18px; font-weight: 800; color: #059669;">{{ $e->kepuasan ?? '-' }}/5
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Kolom Kanan: Panel Aksi Evaluasi (30%) --}}
            <div>
                <div class="ev-panel">
                    <div class="ev-panel-header">
                        <h3 class="ev-panel-title">
                            <i class="fas fa-star" style="color: #fbbf24;"></i> Panel Validasi
                        </h3>
                        <p class="ev-panel-desc">Berikan penilaian & keputusan</p>
                    </div>

                    <div class="ev-panel-body ev-scroll">
                        <form id="evaluateForm" method="POST" action="{{ route('pimpinan.evaluate', $kegiatan->id) }}"
                            @submit.prevent="submitForm">
                            @csrf
                            <input type="hidden" name="status_validasi" x-model="status">

                            {{-- Star Rating untuk Jurusan yang belum dinilai --}}
                            @if($kegiatan->jurusans->count() > 0 && $kegiatan->status_dokumen == 'Menunggu Evaluasi')
                                <div class="ev-form-group">
                                    <label class="ev-label">Beri Skor Kualitas</label>
                                    @php
                                        $criteria = [
                                            ['name' => 'sesuai_rencana', 'label' => 'Rencana'],
                                            ['name' => 'kualitas', 'label' => 'Kualitas'],
                                            ['name' => 'keterlibatan', 'label' => 'Mitra'],
                                            ['name' => 'efisiensi', 'label' => 'Efisiensi'],
                                            ['name' => 'kepuasan', 'label' => 'Kepuasan'],
                                        ];
                                    @endphp

                                    <div class="ev-rating-list">
                                        @foreach($criteria as $c)
                                            <div class="ev-rating-row">
                                                <span class="ev-rating-label">{{ $c['label'] }}</span>
                                                <div class="ev-stars" x-data="{ rating: 0, hover: 0 }">
                                                    <template x-for="i in 5">
                                                        <i class="fas fa-star ev-star-btn"
                                                            :class="(hover || rating) >= i ? 'active' : ''"
                                                            @mouseenter="hover = i" @mouseleave="hover = 0"
                                                            @click="rating = i; $refs.input{{$c['name']}}.value = i"></i>
                                                    </template>
                                                    <input type="hidden" name="{{ $c['name'] }}" x-ref="input{{$c['name']}}"
                                                        required>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="ev-form-group">
                                <label class="ev-label">Catatan Pimpinan</label>

                                <div class="ev-templates">
                                    <button type="button" @click="appendComment('Data sudah lengkap.')"
                                        class="ev-template-btn ev-template-blue">Data Lengkap</button>
                                    <button type="button" @click="appendComment('Sangat sesuai dengan visi.')"
                                        class="ev-template-btn ev-template-emerald">Sesuai Visi</button>
                                    <button type="button" @click="appendComment('Perlu perbaikan rincian.')"
                                        class="ev-template-btn ev-template-amber">Perlu Perbaikan</button>
                                </div>
                                <textarea name="ringkasan" x-model="comment" id="ringkasan" class="ev-textarea"
                                    :class="{ 'error': showError }" placeholder="Tulis arahan di sini..."></textarea>
                                <div x-show="showError" class="ev-error-msg" x-cloak><i
                                        class="fas fa-exclamation-circle"></i> Catatan wajib diisi jika menolak!</div>
                            </div>

                            <div class="ev-form-group">
                                <label class="ev-label">Tindak Lanjut (Opsional)</label>
                                <textarea name="tindak_lanjut" class="ev-textarea" style="height: 60px;"
                                    placeholder="Instruksi ke depan..."></textarea>
                            </div>

                        </form>
                    </div>

                    <div class="ev-panel-footer">
                        <button type="button" @click="handleAction('tidak_layak')" class="ev-btn-reject">
                            <i class="fas fa-times"></i> <span>Ditolak</span>
                        </button>
                        <button type="button" @click="handleAction('layak')" class="ev-btn-approve">
                            <i class="fas fa-check-circle"></i> Aktifkan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>