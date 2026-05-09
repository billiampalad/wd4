<main id="mainContent" class="eval-master-detail" x-data="evalDashboard()">
    <!-- Confetti Script -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <section class="dk-hero">
        <div class="dk-hero-content">
            <div class="breadcrumb dk-breadcrumb">
                <a href="{{ route('pimpinan.dashboard') }}"
                    style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:8px"><i
                        class="fas fa-home"></i></a>
                <span class="sep">/</span>
                <span class="current">Evaluasi Validasi</span>
            </div>
            <div class="dk-hero-main">
                <div class="dk-hero-icon"><i class="fas fa-file-signature"></i></div>
                <div>
                    <span class="dk-eyebrow">Validasi Kerjasama Strategis</span>
                    <h2 id="pageTitle">Evaluasi Dokumen</h2>
                    <p id="pageDesc">Pilih antrean dokumen untuk meninjau keselarasan dan memberikan validasi secara cepat.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Master Detail Layout -->
    <div class="eval-layout">
        
        <!-- LEFT PANEL: Inbox List -->
        <div class="eval-left-panel">
            <!-- Tabs -->
            <div class="eval-tabs">
                <button class="eval-tab-btn" :class="activeTab === 'jurusan' ? 'active jurusan' : ''" @click="activeTab = 'jurusan'">
                    <i class="fas fa-graduation-cap"></i> Jurusan
                    @if($laporanJurusan->count() > 0)
                        <span class="eval-badge jurusan">{{ $laporanJurusan->count() }}</span>
                    @endif
                </button>
                <button class="eval-tab-btn" :class="activeTab === 'upa' ? 'active upa' : ''" @click="activeTab = 'upa'">
                    <i class="fas fa-cogs"></i> UPA
                    @if($laporanUpa->count() > 0)
                        <span class="eval-badge upa">{{ $laporanUpa->count() }}</span>
                    @endif
                </button>
                <button class="eval-tab-btn" :class="activeTab === 'pusat' ? 'active pusat' : ''" @click="activeTab = 'pusat'">
                    <i class="fas fa-building"></i> Pusat
                    @if($laporanPusat->count() > 0)
                        <span class="eval-badge pusat">{{ $laporanPusat->count() }}</span>
                    @endif
                </button>
                @if($laporanUnit->count() > 0)
                <button class="eval-tab-btn" :class="activeTab === 'unit' ? 'active unit' : ''" @click="activeTab = 'unit'">
                    <i class="fas fa-university"></i> Institusi
                    <span class="eval-badge unit">{{ $laporanUnit->count() }}</span>
                </button>
                @endif
            </div>

            <!-- List Content -->
            <div class="eval-list ev-scroll">
                <!-- Skeleton for List -->
                <div x-show="isLoading" class="ev-skeleton-list" x-cloak>
                    <div class="ev-skeleton-item"></div>
                    <div class="ev-skeleton-item"></div>
                    <div class="ev-skeleton-item"></div>
                </div>

                <!-- Tab: Jurusan -->
                <div x-show="!isLoading && activeTab === 'jurusan'" class="eval-list-container space-y-3" x-transition.opacity x-cloak>
                    @forelse($laporanJurusan as $keg)
                        <div class="eval-list-item" :class="activeId === {{ $keg->id }} ? 'active jurusan' : ''" @click="openDetail({{ $keg->id }})">
                            <div class="eval-item-header jurusan">
                                <span>{{ $keg->jurusans->first()->nama_jurusan ?? 'Jurusan N/A' }}</span>
                                <i class="fas fa-chevron-right" :style="activeId === {{ $keg->id }} ? 'color: #3b82f6;' : 'opacity: 0.3;'"></i>
                            </div>
                            <h4 class="eval-item-title">{{ $keg->title }}</h4>
                            <div class="eval-item-meta">
                                <span><i class="fas fa-handshake mr-1"></i> {{ $keg->mitra->nama_mitra ?? 'Tanpa Mitra' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="eval-empty-state">
                            <div class="eval-empty-icon jurusan"><i class="fas fa-check-double"></i></div>
                            <h4>Semua Selesai!</h4>
                            <p>Tidak ada laporan jurusan yang mengantre.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Tab: UPA -->
                <div x-show="!isLoading && activeTab === 'upa'" class="eval-list-container space-y-3" x-transition.opacity x-cloak>
                    @forelse($laporanUpa as $keg)
                        <div class="eval-list-item" :class="activeId === {{ $keg->id }} ? 'active upa' : ''" @click="openDetail({{ $keg->id }})">
                            <div class="eval-item-header upa">
                                <span>{{ $keg->upas->first()->nama_upa ?? 'UPA N/A' }}</span>
                                <i class="fas fa-chevron-right" :style="activeId === {{ $keg->id }} ? 'color: #10b981;' : 'opacity: 0.3;'"></i>
                            </div>
                            <h4 class="eval-item-title">{{ $keg->title }}</h4>
                            <div class="eval-item-meta">
                                <span><i class="fas fa-handshake mr-1"></i> {{ $keg->mitra->nama_mitra ?? 'Tanpa Mitra' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="eval-empty-state">
                            <div class="eval-empty-icon upa"><i class="fas fa-check-double"></i></div>
                            <h4>Semua Selesai!</h4>
                            <p>Tidak ada laporan UPA yang mengantre.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Tab: Pusat -->
                <div x-show="!isLoading && activeTab === 'pusat'" class="eval-list-container space-y-3" x-transition.opacity x-cloak>
                    @forelse($laporanPusat as $keg)
                        <div class="eval-list-item" :class="activeId === {{ $keg->id }} ? 'active pusat' : ''" @click="openDetail({{ $keg->id }})">
                            <div class="eval-item-header pusat">
                                <span>{{ $keg->pusats->first()->nama_pusat ?? 'Pusat N/A' }}</span>
                                <i class="fas fa-chevron-right" :style="activeId === {{ $keg->id }} ? 'color: #8b5cf6;' : 'opacity: 0.3;'"></i>
                            </div>
                            <h4 class="eval-item-title">{{ $keg->title }}</h4>
                            <div class="eval-item-meta">
                                <span><i class="fas fa-handshake mr-1"></i> {{ $keg->mitra->nama_mitra ?? 'Tanpa Mitra' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="eval-empty-state">
                            <div class="eval-empty-icon pusat"><i class="fas fa-check-double"></i></div>
                            <h4>Semua Selesai!</h4>
                            <p>Tidak ada laporan pusat yang mengantre.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Tab: Unit Kerja -->
                <div x-show="!isLoading && activeTab === 'unit'" class="eval-list-container space-y-3" x-transition.opacity x-cloak>
                    @forelse($laporanUnit as $keg)
                        @php
                            $namaUnit = '-';
                            if ($keg->upas->count() > 0) $namaUnit = $keg->upas->first()->nama_upa;
                            elseif ($keg->pusats->count() > 0) $namaUnit = $keg->pusats->first()->nama_pusat;
                        @endphp
                        <div class="eval-list-item" :class="activeId === {{ $keg->id }} ? 'active unit' : ''" @click="openDetail({{ $keg->id }})">
                            <div class="eval-item-header unit">
                                <span>{{ $namaUnit }}</span>
                                <i class="fas fa-chevron-right" :style="activeId === {{ $keg->id }} ? 'color: #64748b;' : 'opacity: 0.3;'"></i>
                            </div>
                            <h4 class="eval-item-title">{{ $keg->title }}</h4>
                            <div class="eval-item-meta">
                                <span><i class="fas fa-handshake mr-1"></i> {{ $keg->mitra->nama_mitra ?? 'Tanpa Mitra' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="eval-empty-state">
                            <div class="eval-empty-icon unit"><i class="fas fa-check-double"></i></div>
                            <h4>Semua Selesai!</h4>
                            <p>Tidak ada laporan unit kerja yang mengantre.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Detail View -->
        <div class="eval-right-panel">
            <!-- Skeleton Loading for Detail -->
            <div x-show="isDetailLoading" class="ev-skeleton-detail" x-cloak>
                <div class="ev-skeleton-col-main">
                    <div class="ev-skeleton-box" style="height: 200px;"></div>
                    <div class="ev-skeleton-box" style="height: 150px;"></div>
                </div>
                <div class="ev-skeleton-col-side">
                    <div class="ev-skeleton-box" style="height: 400px;"></div>
                </div>
            </div>

            <!-- Empty State Detail -->
            <div x-show="!activeId && !isDetailLoading" class="eval-empty-state" style="position: absolute; inset: 0;" x-cloak>
                <div class="eval-empty-icon" style="width: 80px; height: 80px; font-size: 32px; background: var(--surface); box-shadow: 0 0 0 6px var(--surface2); color: var(--text-sub); margin-bottom: 20px; opacity: 0.5;">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <h3 style="font-size: 20px; font-weight: 800; color: var(--text); margin-bottom: 8px;">Pilih Dokumen Evaluasi</h3>
                <p style="font-size: 13px; color: var(--text-sub); max-width: 400px; line-height: 1.5;">Silakan pilih laporan dari daftar di sebelah kiri untuk melihat rincian lengkap dan memberikan penilaian pengesahan.</p>
            </div>

            <!-- Scrollable Detail Content -->
            <div class="eval-detail-content ev-scroll" x-show="activeId && !isDetailLoading" x-cloak>
                
                @php
                    $allLaporan = $laporanJurusan->concat($laporanUpa)->concat($laporanPusat)->concat($laporanUnit);
                    $allLaporan->load(['details.sasaran', 'pjInternal', 'laporanFiles', 'details.jenisKerjasama']);
                @endphp
                
                @foreach($allLaporan as $keg)
                <div x-show="activeId === {{ $keg->id }}" x-cloak>
                    
                    <!-- 2-COLUMN LAYOUT IN DETAIL -->
                    <div class="ev-grid">
                        
                        <!-- BAGIAN KIRI: Context Card -->
                        <div class="ev-col-main">
                            
                            <!-- Context Card Utama -->
                            <div class="ev-card">
                                <div class="ev-card-header">
                                    <h3 class="ev-card-title">{{ $keg->title }}</h3>
                                    <span class="ev-badge-status {{ strtolower($keg->status_dokumen) == 'disahkan' ? 'disahkan' : 'menunggu' }}">
                                        {{ $keg->status_dokumen }}
                                    </span>
                                </div>

                                <div class="ev-card-tags">
                                    <span class="ev-tag ev-tag-blue">
                                        <i class="fas fa-handshake" style="margin-right: 4px; opacity: 0.7;"></i> {{ $keg->mitra->nama_mitra ?? 'Mitra N/A' }}
                                    </span>
                                    <span style="color: #cbd5e1;">•</span>
                                    <span class="ev-tag ev-tag-slate">
                                        <i class="fas fa-file-contract" style="margin-right: 4px; opacity: 0.7;"></i> {{ $keg->jenis }}
                                    </span>
                                </div>

                                @php $detail = $keg->details->first(); @endphp
                                
                                <!-- Analisis Keselarasan -->
                                <div class="ev-alert-emerald">
                                    <h4 class="ev-alert-title">
                                        <i class="fas fa-bullseye"></i> Analisis Keselarasan Visi
                                    </h4>
                                    <p class="ev-alert-text">
                                        {{ $detail->sasaran->deskripsi ?? 'Belum ada deskripsi sasaran yang dihubungkan dengan dokumen ini.' }}
                                    </p>
                                </div>

                                <!-- Metric Tiles -->
                                <div class="ev-metrics">
                                    <div class="ev-metric-card is-emerald">
                                        <div class="ev-metric-bg ev-metric-bg-emerald"></div>
                                        <div class="ev-metric-label is-emerald"><i class="fas fa-coins"></i> Aspek Finansial</div>
                                        <div class="ev-metric-val">{{ $detail && $detail->nilai_kontrak ? 'Rp ' . number_format($detail->nilai_kontrak, 0, ',', '.') : 'Rp 0' }}</div>
                                    </div>
                                    <div class="ev-metric-card is-blue">
                                        <div class="ev-metric-bg ev-metric-bg-blue"></div>
                                        <div class="ev-metric-label is-blue"><i class="fas fa-chart-line"></i> Target Luaran</div>
                                        <div class="ev-metric-val">{{ $detail->volume_luaran ?? 0 }} <small>{{ $detail->satuan_luaran ?? 'Item' }}</small></div>
                                    </div>
                                    <div class="ev-metric-card is-amber">
                                        <div class="ev-metric-bg ev-metric-bg-amber"></div>
                                        <div class="ev-metric-label is-amber"><i class="fas fa-calendar-alt"></i> Durasi Berlaku</div>
                                        <div class="ev-metric-val" style="font-size: 14px; margin-top: 4px;">
                                            {{ $keg->start_date ? $keg->start_date->format('M Y') : '-' }} <i class="fas fa-arrow-right" style="color: #cbd5e1; margin: 0 4px;"></i> {{ $keg->end_date ? $keg->end_date->format('M Y') : 'Selesai' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Activity Timeline -->
                                <div class="ev-history">
                                    <h4 class="ev-history-title">Riwayat Singkat</h4>
                                    <div class="ev-history-item">
                                        <div class="ev-history-avatar">
                                            {{ strtoupper(substr($keg->pjInternal->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="ev-history-info-name">Diinput oleh <span>{{ $keg->pjInternal->name ?? 'Sistem' }}</span></div>
                                            <div class="ev-history-info-time"><i class="far fa-clock"></i> {{ $keg->created_at->format('d M Y, H:i') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Lampiran Pendukung -->
                            <div class="ev-card">
                                <h4 class="ev-attachment-title">
                                    <i class="fas fa-paperclip"></i> Lampiran Pendukung
                                </h4>
                                @if($keg->laporanFiles->count() > 0 || $keg->document_link)
                                    <div class="ev-attachment-grid">
                                        @if($keg->document_link)
                                            <a href="{{ $keg->document_link }}" target="_blank" class="ev-file-link blue">
                                                <div class="icon-box"><i class="fas fa-link"></i></div>
                                                <div class="info-box">
                                                    <div class="info-name">Dokumen Utama</div>
                                                    <div class="info-desc">Klik untuk buka tautan</div>
                                                </div>
                                                <i class="fas fa-external-link-alt action-icon"></i>
                                            </a>
                                        @endif

                                        @foreach($keg->laporanFiles as $file)
                                            <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="ev-file-link red">
                                                <div class="icon-box"><i class="fas fa-file-pdf"></i></div>
                                                <div class="info-box">
                                                    <div class="info-name">{{ basename($file->file_path) }}</div>
                                                    <div class="info-desc">Unduh file PDF</div>
                                                </div>
                                                <i class="fas fa-download action-icon"></i>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="ev-empty-file">
                                        <i class="fas fa-folder-open"></i>
                                        Tidak ada lampiran dokumen yang disertakan.
                                    </div>
                                @endif
                            </div>

                        </div>

                        <!-- BAGIAN KANAN: Evaluation Panel -->
                        <div class="ev-col-side">
                            <div class="ev-panel">
                                <!-- Floating Card Header -->
                                <div class="ev-panel-header">
                                    <h3 class="ev-panel-title">
                                        <i class="fas fa-star"></i> Panel Validasi
                                    </h3>
                                    <p class="ev-panel-desc">Berikan skor dan keputusan akhir untuk pengesahan dokumen ini.</p>
                                </div>

                                <div class="ev-panel-body ev-scroll">
                                    <form method="POST" action="{{ route('pimpinan.evaluate', $keg->id) }}" id="form_{{ $keg->id }}" @submit.prevent>
                                        @csrf
                                        <input type="hidden" name="status_validasi" x-model="status">

                                        <!-- Star Rating (Hanya untuk Jurusan yang belum dinilai) -->
                                        @if($keg->tipe_pelaksana === 'jurusan')
                                            <div class="ev-form-group">
                                                <label class="ev-label">Skor Kualitas (Wajib)</label>
                                                <div class="ev-rating-list">
                                                    @foreach([
                                                        ['name' => 'sesuai_rencana', 'label' => 'Sesuai Rencana'],
                                                        ['name' => 'kualitas', 'label' => 'Kualitas Pelaksanaan'],
                                                        ['name' => 'keterlibatan', 'label' => 'Keterlibatan Mitra'],
                                                        ['name' => 'efisiensi', 'label' => 'Efisiensi Anggaran'],
                                                        ['name' => 'kepuasan', 'label' => 'Tingkat Kepuasan']
                                                    ] as $c)
                                                    <div class="ev-rating-row">
                                                        <span class="ev-rating-label">{{ $c['label'] }}</span>
                                                        <div class="ev-stars" x-data="{ rating: 0, hover: 0 }">
                                                            <template x-for="i in 5">
                                                                <i class="fas fa-star ev-star-btn" 
                                                                   :class="(hover || rating) >= i ? 'active' : ''"
                                                                   @mouseenter="hover = i" 
                                                                   @mouseleave="hover = 0" 
                                                                   @click="rating = i; $refs.input{{$c['name']}}_{{ $keg->id }}.value = i"></i>
                                                            </template>
                                                            <input type="hidden" name="{{ $c['name'] }}" x-ref="input{{$c['name']}}_{{ $keg->id }}" required>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Executive Comments -->
                                        <div class="ev-form-group" style="margin-bottom: 0;">
                                            <div class="ev-label-flex">
                                                <label class="ev-label">Catatan / Arahan Pimpinan</label>
                                                <span class="ev-autosave">Auto-save</span>
                                            </div>
                                            
                                            <!-- Templates -->
                                            <div class="ev-templates">
                                                <button type="button" @click="appendComment('Data sudah lengkap dan valid.', {{ $keg->id }})" class="ev-template-btn ev-template-blue">Lengkap</button>
                                                <button type="button" @click="appendComment('Sangat relevan dengan visi IKU.', {{ $keg->id }})" class="ev-template-btn ev-template-emerald">Sesuai Visi</button>
                                                <button type="button" @click="appendComment('Mohon perbaiki rincian anggaran.', {{ $keg->id }})" class="ev-template-btn ev-template-amber">Revisi Anggaran</button>
                                            </div>

                                            <textarea name="ringkasan" x-model="comments[{{ $keg->id }}]" class="ev-textarea" :class="{ 'error': showErrors[{{ $keg->id }}] }" placeholder="Tulis catatan eksekutif di sini..."></textarea>
                                            <div x-show="showErrors[{{ $keg->id }}]" class="ev-error-msg" x-cloak>
                                                <i class="fas fa-exclamation-circle"></i> Catatan wajib diisi jika Anda meminta revisi.
                                            </div>
                                        </div>

                                    </form>
                                </div>

                                <!-- Action Buttons -->
                                <div class="ev-panel-footer">
                                    <button type="button" @click="handleAction('revisi', {{ $keg->id }})" class="ev-btn-reject">
                                        <i class="fas fa-pen-to-square"></i> <span>Revisi</span>
                                    </button>
                                    <button type="button" @click="handleAction('layak', {{ $keg->id }})" class="ev-btn-approve">
                                        <i class="fas fa-check-circle"></i> Sahkan & Setujui
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</main>