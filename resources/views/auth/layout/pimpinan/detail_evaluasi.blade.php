<main id="mainContent" class="eval-container" x-data="evaluationForm()">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-home" style="font-size:11px;"></i>
            <span class="sep">/</span>
            <a href="{{ route('pimpinan.dashboard') }}">Dashboard</a>
            <span class="sep">/</span>
            <span class="current">Administrasi</span>
            <span class="sep">/</span>
            <span class="current">Validasi</span>
        </div>
        <h2 id="pageTitle" class="context-title" style="margin-top: 8px;">Evaluasi Kerjasama Strategis</h2>
        <p id="pageDesc" style="color: var(--slate);">Tinjau keselarasan, rincian, dan capaian kerjasama sebelum pengesahan.</p>
    </div>

    {{-- Skeleton Loading --}}
    <div x-show="isLoading" class="eval-grid mt-6">
        <div class="lg:col-span-8 flex flex-col gap-6">
            <div class="floating-card skeleton" style="height: 200px;"></div>
            <div class="floating-card skeleton" style="height: 150px;"></div>
        </div>
        <div class="lg:col-span-4">
            <div class="floating-card skeleton" style="height: 400px;"></div>
        </div>
    </div>

    {{-- Main Content --}}
    <div x-show="!isLoading" style="display: none;" class="eval-grid mt-6">
        
        {{-- Kolom Kiri: Ringkasan Data (70%) --}}
        <div class="lg:col-span-8 flex flex-col gap-6">
            
            {{-- Informasi Utama --}}
            <div class="floating-card">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-extrabold text-slate-800" style="line-height: 1.3;">{{ $kegiatan->title }}</h3>
                    @php
                        $statusClass = $kegiatan->status_dokumen == 'Menunggu Evaluasi' ? 'bg-amber-100 text-amber-600' : ($kegiatan->status_dokumen == 'Disahkan' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600');
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusClass }}">
                        {{ $kegiatan->status_dokumen }}
                    </span>
                </div>
                
                <div class="flex items-center gap-2 mb-6">
                    <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded text-xs font-bold">{{ $kegiatan->mitra->nama_mitra ?? 'Mitra N/A' }}</span>
                    <span class="text-slate-400 text-sm">•</span>
                    <span class="text-slate-600 text-sm font-semibold">{{ $kegiatan->jenis }}</span>
                </div>

                @php $detail = $kegiatan->details->first(); @endphp
                <div class="p-4 rounded-xl" style="background: var(--slate-light); border-left: 4px solid var(--emerald);">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Analisis Keselarasan (Sasaran)</h4>
                    <p class="text-sm font-medium text-slate-700">
                        {{ $detail->sasaran->deskripsi ?? 'Belum ada data sasaran yang dihubungkan.' }}
                    </p>
                </div>

                <div class="metric-tiles">
                    <div class="metric-tile">
                        <div class="metric-label">Nilai Kontrak</div>
                        <div class="metric-value">{{ $detail && $detail->nilai_kontrak ? 'Rp ' . number_format($detail->nilai_kontrak, 0, ',', '.') : 'Rp 0' }}</div>
                    </div>
                    <div class="metric-tile blue">
                        <div class="metric-label">Masa Berlaku</div>
                        <div class="metric-value">
                            {{ $kegiatan->start_date ? $kegiatan->start_date->format('M Y') : '-' }} - 
                            {{ $kegiatan->end_date ? $kegiatan->end_date->format('M Y') : 'Selesai' }}
                        </div>
                    </div>
                    <div class="metric-tile amber">
                        <div class="metric-label">Target Luaran</div>
                        <div class="metric-value">
                            {{ $detail->volume_luaran ?? 0 }} {{ $detail->satuan_luaran ?? 'Item' }}
                        </div>
                    </div>
                </div>
                
                {{-- Riwayat --}}
                <div class="mt-6 border-t pt-4 border-slate-100">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3">Activity Timeline</h4>
                    <div class="timeline">
                        <div class="timeline-item">
                            <span class="text-xs text-slate-400 block mb-1">{{ $kegiatan->created_at->format('d M Y, H:i') }}</span>
                            <span class="text-sm font-medium text-slate-700">Dokumen diinput oleh <span class="font-bold">{{ $kegiatan->pjInternal->name ?? 'Sistem' }}</span></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rincian Kegiatan & Hasil --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="floating-card">
                    <h3 class="text-md font-bold mb-4 flex items-center gap-2 text-slate-800">
                        <i class="fas fa-bullseye text-amber-500"></i> Indikator & Tujuan
                    </h3>
                    <div class="flex flex-col gap-4">
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase">Tujuan</span>
                            <p class="text-sm text-slate-700 font-medium mt-1">{{ $detail->tujuan ?? '-' }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase">Indikator Kinerja</span>
                            <p class="text-sm text-slate-700 font-medium mt-1">{{ $detail->indikator_kinerja ?? '-' }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase">PJ Internal</span>
                            <p class="text-sm text-slate-700 font-medium mt-1">{{ $kegiatan->pjInternal->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="floating-card">
                    <h3 class="text-md font-bold mb-4 flex items-center gap-2 text-slate-800">
                        <i class="fas fa-file-contract text-blue-500"></i> Lampiran Pendukung
                    </h3>
                    
                    @if($kegiatan->laporanFiles->count() > 0 || $kegiatan->dokumentasis->count() > 0)
                        <div class="flex flex-col gap-3">
                            @foreach($kegiatan->dokumentasis as $dok)
                                @if($dok->link_drive)
                                <a href="{{ $dok->link_drive }}" target="_blank" class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:border-blue-400 hover:bg-blue-50 transition-colors group">
                                    <div class="text-blue-500 text-xl"><i class="fas fa-link"></i></div>
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-slate-700 group-hover:text-blue-700">{{ $dok->keterangan ?: 'Link Eksternal' }}</p>
                                        <p class="text-xs text-slate-500">Klik untuk buka</p>
                                    </div>
                                </a>
                                @endif
                            @endforeach
                            
                            @foreach($kegiatan->laporanFiles as $file)
                                <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:border-red-400 hover:bg-red-50 transition-colors group">
                                    <div class="text-red-500 text-xl"><i class="fas fa-file-pdf"></i></div>
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-slate-700 group-hover:text-red-700">{{ basename($file->file_path) }}</p>
                                        <p class="text-xs text-slate-500">Unduh PDF</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-center border border-dashed border-slate-300 rounded-lg">
                            <i class="fas fa-folder-open text-slate-300 text-3xl mb-2 block"></i>
                            <span class="text-sm text-slate-500">Tidak ada lampiran dokumen</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Evaluasi Unit Kerja (Historical) --}}
            @if($kegiatan->unitKerjas->count() > 0 && $kegiatan->evaluasis->count() > 0)
            <div class="floating-card">
                 <h3 class="text-md font-bold mb-4 flex items-center gap-2 text-slate-800">
                    <i class="fas fa-clipboard-check text-emerald-500"></i> Historis Evaluasi Internal
                </h3>
                @php $e = $kegiatan->evaluasis->first(); @endphp
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <div class="text-center p-3 bg-slate-50 rounded-lg">
                        <div class="text-xs text-slate-500 mb-1">Rencana</div>
                        <div class="text-lg font-bold text-emerald-600">{{ $e->sesuai_rencana ?? '-' }}/5</div>
                    </div>
                    <div class="text-center p-3 bg-slate-50 rounded-lg">
                        <div class="text-xs text-slate-500 mb-1">Kualitas</div>
                        <div class="text-lg font-bold text-emerald-600">{{ $e->kualitas ?? '-' }}/5</div>
                    </div>
                    <div class="text-center p-3 bg-slate-50 rounded-lg">
                        <div class="text-xs text-slate-500 mb-1">Mitra</div>
                        <div class="text-lg font-bold text-emerald-600">{{ $e->keterlibatan ?? '-' }}/5</div>
                    </div>
                    <div class="text-center p-3 bg-slate-50 rounded-lg">
                        <div class="text-xs text-slate-500 mb-1">Efisiensi</div>
                        <div class="text-lg font-bold text-emerald-600">{{ $e->efisiensi ?? '-' }}/5</div>
                    </div>
                    <div class="text-center p-3 bg-slate-50 rounded-lg">
                        <div class="text-xs text-slate-500 mb-1">Kepuasan</div>
                        <div class="text-lg font-bold text-emerald-600">{{ $e->kepuasan ?? '-' }}/5</div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Kolom Kanan: Panel Aksi Evaluasi (30%) --}}
        <div class="lg:col-span-4 relative">
            <div class="floating-card action-zone">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white" style="background: linear-gradient(135deg, var(--emerald), #047857);">
                        <i class="fas fa-star"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Panel Validasi</h3>
                        <p class="text-xs text-slate-500">Berikan penilaian & keputusan</p>
                    </div>
                </div>

                <form id="evaluateForm" method="POST" action="{{ route('pimpinan.evaluate', $kegiatan->id) }}" @submit.prevent="submitForm">
                    @csrf
                    <input type="hidden" name="status_validasi" x-model="status">

                    {{-- Star Rating untuk Jurusan yang belum dinilai --}}
                    @if($kegiatan->jurusans->count() > 0 && $kegiatan->status_dokumen == 'Menunggu Evaluasi')
                        <div class="mb-6">
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3">Beri Skor Kualitas</h4>
                            @php
                                $criteria = [
                                    ['name' => 'sesuai_rencana', 'label' => 'Rencana'],
                                    ['name' => 'kualitas', 'label' => 'Kualitas'],
                                    ['name' => 'keterlibatan', 'label' => 'Mitra'],
                                    ['name' => 'efisiensi', 'label' => 'Efisiensi'],
                                    ['name' => 'kepuasan', 'label' => 'Kepuasan'],
                                ];
                            @endphp

                            @foreach($criteria as $c)
                                <div class="star-rating-box">
                                    <span class="text-sm font-bold text-slate-700">{{ $c['label'] }}</span>
                                    <div class="flex gap-1 star-group" x-data="{ rating: 0, hover: 0 }">
                                        <template x-for="i in 5">
                                            <i class="fas fa-star star-btn" 
                                               :class="{ 'active': i <= (hover || rating) }" 
                                               @mouseenter="hover = i" 
                                               @mouseleave="hover = 0" 
                                               @click="rating = i; $refs.input{{$c['name']}}.value = i"></i>
                                        </template>
                                        <input type="hidden" name="{{ $c['name'] }}" x-ref="input{{$c['name']}}" required>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mb-4">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2 block">Catatan Pimpinan</label>
                        <div class="template-chips">
                            <span class="chip" @click="appendComment('Data sudah lengkap.')">Data Lengkap</span>
                            <span class="chip" @click="appendComment('Sangat sesuai dengan visi.')">Sesuai Visi</span>
                            <span class="chip" @click="appendComment('Perlu perbaikan rincian.')">Perlu Perbaikan</span>
                        </div>
                        <textarea name="ringkasan" x-model="comment" id="ringkasan" class="action-textarea" :class="{ 'shake': showError }" placeholder="Tulis arahan di sini..."></textarea>
                        <p x-show="showError" class="text-xs text-red-500 mt-1 font-bold" style="display: none;"><i class="fas fa-exclamation-circle"></i> Catatan wajib diisi jika menolak!</p>
                    </div>

                    <div class="mb-4">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2 block">Tindak Lanjut (Opsional)</label>
                        <textarea name="tindak_lanjut" class="action-textarea" style="min-height: 60px;" placeholder="Instruksi ke depan..."></textarea>
                    </div>

                    <div class="border-t border-slate-100 pt-4 mt-6">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3 block">Ubah Status / Keputusan</label>
                        <div class="btn-group">
                            <button type="button" @click="handleAction('tidak_layak')" class="btn-reject">
                                <i class="fas fa-times"></i> Ditolak
                            </button>
                            <button type="button" @click="handleAction('layak')" class="btn-approve">
                                <i class="fas fa-check"></i> Aktifkan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<style>
/* Modern Dashboard Theme: Deep Navy & Vibrant Accents */
:root {
    --deep-navy: #0F172A;
    --navy-light: #1E293B;
    --emerald: #10B981;
    --emerald-dark: #059669;
    --amber: #F59E0B;
    --ruby: #EF4444;
    --slate: #64748B;
    --slate-light: #F8FAFC;
}

.eval-container {
    font-family: 'Plus Jakarta Sans', sans-serif;
    color: var(--deep-navy);
}

/* Skeleton Loading Shimmer */
.skeleton {
    background: #e2e8f0;
    background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    border-radius: 12px;
}
@keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Float Card */
.floating-card {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
    border: 1px solid rgba(226, 232, 240, 0.8);
    padding: 24px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.floating-card:hover {
    box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.08);
}

/* Metric Tiles */
.metric-tiles {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 16px;
    margin-top: 24px;
}
.metric-tile {
    background: var(--slate-light);
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    position: relative;
    overflow: hidden;
}
.metric-tile::before {
    content: '';
    position: absolute;
    top: 0; left: 0; width: 4px; height: 100%;
    background: var(--emerald);
    border-radius: 4px 0 0 4px;
}
.metric-tile.blue::before { background: #3B82F6; }
.metric-tile.amber::before { background: var(--amber); }

.metric-label {
    font-size: 11px;
    text-transform: uppercase;
    font-weight: 800;
    color: var(--slate);
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}
.metric-value {
    font-size: 16px;
    font-weight: 800;
    color: var(--deep-navy);
}

/* Timeline */
.timeline {
    border-left: 2px solid #e2e8f0;
    padding-left: 16px;
    margin-left: 8px;
}
.timeline-item {
    position: relative;
}
.timeline-item::before {
    content: '';
    position: absolute;
    left: -21px;
    top: 4px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--emerald);
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px var(--emerald);
}

/* Action Panel */
.action-zone {
    position: sticky;
    top: 90px;
}
.action-textarea {
    width: 100%;
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    padding: 12px;
    font-size: 13px;
    font-weight: 500;
    font-family: inherit;
    resize: vertical;
    min-height: 100px;
    transition: all 0.2s;
    background: #f8fafc;
}
.action-textarea:focus {
    outline: none;
    border-color: var(--emerald);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.shake {
    animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
    border-color: var(--ruby) !important;
    background: rgba(239, 68, 68, 0.02) !important;
}
@keyframes shake {
    10%, 90% { transform: translate3d(-1px, 0, 0); }
    20%, 80% { transform: translate3d(2px, 0, 0); }
    30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
    40%, 60% { transform: translate3d(4px, 0, 0); }
}

.btn-group {
    display: flex;
    gap: 12px;
}
.btn-approve {
    flex: 1;
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    color: white;
    border: none;
    border-radius: 10px;
    padding: 12px;
    font-weight: 800;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
}
.btn-approve:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(16, 185, 129, 0.35);
}
.btn-approve:active {
    transform: translateY(0);
}
.btn-reject {
    flex: 1;
    background: transparent;
    color: var(--ruby);
    border: 2px solid var(--ruby);
    border-radius: 10px;
    padding: 12px;
    font-weight: 800;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.btn-reject:hover {
    background: rgba(239, 68, 68, 0.08);
}

.template-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.chip {
    font-size: 11px;
    background: #f1f5f9;
    color: #475569;
    padding: 4px 10px;
    border-radius: 20px;
    cursor: pointer;
    font-weight: 700;
    transition: all 0.2s;
    border: 1px solid #e2e8f0;
}
.chip:hover {
    background: #e2e8f0;
    color: var(--deep-navy);
    border-color: #cbd5e1;
}

/* Star Rating */
.star-rating-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 14px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin-bottom: 8px;
}
.star-btn {
    font-size: 16px;
    cursor: pointer;
    transition: transform 0.1s, color 0.2s;
    color: #e2e8f0;
}
.star-btn.active {
    color: var(--amber);
}
.star-btn:hover {
    transform: scale(1.1);
}
</style>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('evaluationForm', () => ({
        isLoading: true,
        comment: '',
        status: '',
        showError: false,
        
        init() {
            // Simulate fast loading skeleton
            setTimeout(() => {
                this.isLoading = false;
            }, 800);
        },

        appendComment(text) {
            if (this.comment.length > 0) {
                this.comment += ' ' + text;
            } else {
                this.comment = text;
            }
            this.showError = false;
        },

        handleAction(actionStatus) {
            this.status = actionStatus;
            
            // Realtime Validation: Reject without comment
            if (actionStatus === 'tidak_layak' && this.comment.trim() === '') {
                this.showError = true;
                setTimeout(() => { this.showError = false; }, 2000);
                return;
            }

            if (actionStatus === 'layak') {
                this.triggerConfetti();
                setTimeout(() => {
                    document.getElementById('evaluateForm').submit();
                }, 1200);
            } else {
                document.getElementById('evaluateForm').submit();
            }
        },

        triggerConfetti() {
            var duration = 15 * 100;
            var animationEnd = Date.now() + duration;
            var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 9999 };

            function randomInRange(min, max) {
                return Math.random() * (max - min) + min;
            }

            var interval = setInterval(function() {
                var timeLeft = animationEnd - Date.now();

                if (timeLeft <= 0) {
                    return clearInterval(interval);
                }

                var particleCount = 50 * (timeLeft / duration);
                confetti(Object.assign({}, defaults, { particleCount,
                    origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 }
                }));
                confetti(Object.assign({}, defaults, { particleCount,
                    origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 }
                }));
            }, 250);
        }
    }));
});
</script>
