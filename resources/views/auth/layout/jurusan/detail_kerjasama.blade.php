<!-- Main Content -->
<main id="mainContent">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-home" style="font-size:11px;"></i>
            <span class="sep">/</span>
            <a href="{{ route('jurusan.dkerjasama') }}" style="color: inherit; text-decoration: none;">Data Kerjasama</a>
            <span class="sep">/</span>
            <span class="current">Detail</span>
        </div>
        <h2 id="pageTitle">{{ $kegiatan->nama_kegiatan }}</h2>
        <p id="pageDesc">Detail lengkap kegiatan kerjasama dan data terkait.</p>
    </div>

    @if(session('success'))
    <div style="background: linear-gradient(135deg, rgba(16,185,129,.12), rgba(5,150,105,.08)); border: 1px solid rgba(16,185,129,.3); color: #065f46; padding: 14px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-check-circle" style="font-size: 16px; color: #10b981;"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- ═══ STATUS HEADER ═══ --}}
    <div class="md-stats-container">
        <div class="md-stat-card">
            <div class="md-stat-icon md-icon-primary">
                <i class="fas fa-handshake"></i>
            </div>
            <div class="md-stat-info">
                <div class="md-stat-label">Jenis</div>
                <div class="md-stat-value">
                    {{ $kegiatan->jenisKerjasama->pluck('nama_kerjasama')->join(', ') ?: '-' }}
                </div>
            </div>
        </div>
        <div class="md-stat-card">
            <div class="md-stat-icon md-icon-warning">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="md-stat-info">
                <div class="md-stat-label">Periode</div>
                <div class="md-stat-value">
                    {{ $kegiatan->periode_mulai?->format('d M Y') ?? '-' }} — {{ $kegiatan->periode_selesai?->format('d M Y') ?? '-' }}
                </div>
            </div>
        </div>
        <div class="md-stat-card" style="flex: 0; min-width: auto; padding: 0 24px; justify-content: center;">
            <span class="tag {{ $kegiatan->status_class }}" style="font-size: 13px; padding: 8px 16px;">
                <i class="fas fa-circle" style="font-size:7px;"></i> {{ $kegiatan->status_label }}
            </span>
        </div>
    </div>

    {{-- ═══ TABS ═══ --}}
    <div class="modern-card" x-data="{ activeTab: localStorage.getItem('activeDetailTab') || 'umum' }" x-init="$watch('activeTab', value => localStorage.setItem('activeDetailTab', value)); $nextTick(() => localStorage.removeItem('activeDetailTab'))">
        @php
            $isEditMode = in_array($kegiatan->status, ['draft', 'revisi']);
            $kesimpulanPimpinan = $kegiatan->kesimpulans->sortByDesc('id')->first();
        @endphp
        {{-- Tab Navigation --}}
        <div class="md-tab-nav" id="tabNav">
            <button class="md-tab-btn" :class="{ 'active': activeTab === 'umum' }" @click="activeTab = 'umum'">
                <i class="fas fa-info-circle"></i> Informasi Umum
            </button>
            <button class="md-tab-btn" :class="{ 'active': activeTab === 'tujuan' }" @click="activeTab = 'tujuan'">
                <i class="fas fa-bullseye"></i> Tujuan & Sasaran
            </button>
            <button class="md-tab-btn" :class="{ 'active': activeTab === 'pelaksanaan' }" @click="activeTab = 'pelaksanaan'">
                <i class="fas fa-cogs"></i> Pelaksanaan
            </button>
            <button class="md-tab-btn" :class="{ 'active': activeTab === 'hasil' }" @click="activeTab = 'hasil'">
                <i class="fas fa-chart-line"></i> Hasil & Capaian
            </button>
            <button class="md-tab-btn" :class="{ 'active': activeTab === 'masalah' }" @click="activeTab = 'masalah'">
                <i class="fas fa-exclamation-triangle"></i> Permasalahan & Solusi
            </button>
            <button class="md-tab-btn" :class="{ 'active': activeTab === 'dokumentasi' }" @click="activeTab = 'dokumentasi'">
                <i class="fas fa-file-alt"></i> Dokumentasi
            </button>
        </div>

        {{-- ═══ TAB 1: Informasi Umum ═══ --}}
        <div class="tab-content mc-body" x-show="activeTab === 'umum'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" style="padding: 24px; display: none;">
            <div class="mc-grid-2">
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <div class="md-stat-label" style="margin-bottom: 4px;">Nama Kegiatan</div>
                        <div style="font-size: 14px; font-weight: 700; color: var(--text);">{{ $kegiatan->nama_kegiatan }}</div>
                    </div>
                    <div>
                        <div class="md-stat-label" style="margin-bottom: 4px;">Jenis Kerjasama</div>
                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                            @forelse($kegiatan->jenisKerjasama as $jk)
                            <span class="tag tag-purple" style="font-size: 11px;">{{ $jk->nama_kerjasama }}</span>
                            @empty
                            <span style="font-size: 13px; color: var(--text-sub);">-</span>
                            @endforelse
                        </div>
                    </div>
                    <div>
                        <div class="md-stat-label" style="margin-bottom: 4px;"></div>
                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                            @forelse($kegiatan->jurusans as $jur)
                            <span class="tag tag-blue" style="font-size: 11px;">{{ $jur->nama_jurusan }}</span>
                            @empty
                            <span style="font-size: 13px; color: var(--text-sub);">-</span>
                            @endforelse
                        </div>
                    </div>
                    <div>
                        <div class="md-stat-label" style="margin-bottom: 4px;">Dibuat Oleh</div>
                        <div style="font-size: 14px; color: var(--text);">{{ $kegiatan->creator?->name ?? '-' }}</div>
                    </div>
                </div>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <div class="md-stat-label" style="margin-bottom: 4px;">Nomor MoU</div>
                        <div style="font-size: 14px; color: var(--text); font-family: 'DM Mono', monospace;">{{ $kegiatan->nomor_mou ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="md-stat-label" style="margin-bottom: 4px;">Tanggal MoU</div>
                        <div style="font-size: 14px; color: var(--text);">{{ $kegiatan->tanggal_mou?->format('d M Y') ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="md-stat-label" style="margin-bottom: 4px;">Penanggung Jawab</div>
                        <div style="font-size: 14px; color: var(--text);">{{ $kegiatan->penanggung_jawab ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="md-stat-label" style="margin-bottom: 8px;">Mitra Kerjasama</div>
                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                            @forelse($kegiatan->mitras as $m)
                            <span class="tag tag-blue" style="font-size: 11px;">{{ $m->nama_mitra }}</span>
                            @empty
                            <span style="font-size: 13px; color: var(--text-sub);">Belum ada mitra</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Button Selanjutnya to Tab 2 --}}
            <div style="margin-top: 24px; text-align: right;">
                <button type="button" @click="activeTab = 'tujuan'" class="rfc-btn rfc-btn-primary">
                    Selanjutnya <i class="fas fa-arrow-right"></i>
                </button>
            </div>

            @include('auth.layout.jurusan._detail_tab_footer_kirim_revisi')
        </div>

        {{-- ═══ TAB 2: Tujuan & Sasaran ═══ --}}
        <div class="tab-content mc-body" x-show="activeTab === 'tujuan'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" style="padding: 24px; display: none;">
            @php
                $tujuanSasaran = $kegiatan->tujuans->first();
            @endphp

            @if($isEditMode)
                <div style="background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; padding: 24px; margin-bottom: 20px;">
                    <div style="font-weight: 800; font-size: 14px; color: var(--text); margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-bullseye" style="color: var(--accent);"></i>
                        Form Tujuan & Sasaran Kerja Sama
                    </div>

                    {{-- Edit (update) jika data sudah ada --}}
                    @if($tujuanSasaran)
                        <form id="tujuanForm" action="{{ route('jurusan.kerjasama.tujuan.update', [$kegiatan->id, $tujuanSasaran->id]) }}" method="POST" style="margin: 0;">
                            @csrf
                            @method('PUT')
                            <div class="mc-grid-2">
                                <div class="mc-group">
                                    <label class="mc-label">Tujuan Kegiatan <span class="mc-req">*</span></label>
                                    <textarea name="tujuan" rows="4" required placeholder="Meningkatkan kompetensi praktis mahasiswa di bidang jaringan komputer melalui magang industri..."
                                        class="mc-input no-icon" style="resize: vertical;">{{ old('tujuan', $tujuanSasaran->tujuan ?? '') }}</textarea>
                                </div>
                                <div class="mc-group">
                                    <label class="mc-label">Sasaran Kegiatan <span class="mc-req">*</span></label>
                                    <textarea name="sasaran" rows="4" required placeholder="Mahasiswa D3 Teknik Informatika Semester 5 (sebanyak 50 orang) dan 2 Dosen Pembimbing..."
                                        class="mc-input no-icon" style="resize: vertical;">{{ old('sasaran', $tujuanSasaran->sasaran ?? '') }}</textarea>
                                </div>
                            </div>

                            <div style="margin-top: 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                                <button type="button" @click="activeTab = 'umum'" class="rfc-btn" style="background: var(--surface); color: var(--text-sub); border: 1px solid var(--border);">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </button>
                                <button type="submit" class="rfc-btn rfc-btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                            </div>
                        </form>
                    @else
                        {{-- Create jika belum ada data --}}
                        <form id="tujuanForm" action="{{ route('jurusan.kerjasama.tujuan.store', $kegiatan->id) }}" method="POST" style="margin: 0;">
                            @csrf
                            <div class="mc-grid-2">
                                <div class="mc-group">
                                    <label class="mc-label">Tujuan Kegiatan <span class="mc-req">*</span></label>
                                    <textarea name="tujuan" rows="4" required placeholder="Meningkatkan kompetensi praktis mahasiswa..."
                                        class="mc-input no-icon" style="resize: vertical;">{{ old('tujuan') }}</textarea>
                                </div>
                                <div class="mc-group">
                                    <label class="mc-label">Sasaran Kegiatan <span class="mc-req">*</span></label>
                                    <textarea name="sasaran" rows="4" required placeholder="Mahasiswa D3 Teknik Informatika Semester 5..."
                                        class="mc-input no-icon" style="resize: vertical;">{{ old('sasaran') }}</textarea>
                                </div>
                            </div>

                            <div style="margin-top: 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                                <button type="button" @click="activeTab = 'umum'" class="rfc-btn" style="background: var(--surface); color: var(--text-sub); border: 1px solid var(--border);">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </button>
                                <button type="submit" class="rfc-btn rfc-btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
                {{-- List data yang sudah diinput --}}
                <div style="margin-top: 24px;">
                    <div style="font-weight: 800; font-size: 14px; color: var(--text); margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-list-ul" style="color: var(--accent);"></i>
                        Data Terdaftar
                    </div>
                    @foreach($kegiatan->tujuans as $t)
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 20px; margin-bottom: 16px; position: relative; box-shadow: 0 2px 12px rgba(0,0,0,0.02);">
                        <div style="margin-bottom: 16px; padding-right: 40px;">
                            <div class="md-stat-label" style="margin-bottom: 6px;">Tujuan Kegiatan</div>
                            <div style="font-size: 13px; color: var(--text); line-height: 1.6; white-space: pre-line;">{{ $t->tujuan }}</div>
                        </div>
                        <div>
                            <div class="md-stat-label" style="margin-bottom: 6px;">Sasaran Kegiatan</div>
                            <div style="font-size: 13px; color: var(--text); line-height: 1.6; white-space: pre-line;">{{ $t->sasaran }}</div>
                        </div>
                        <div style="position: absolute; top: 16px; right: 16px;">
                            <form action="{{ route('jurusan.kerjasama.tujuan.destroy', [$kegiatan->id, $t->id]) }}" method="POST" onsubmit="return confirm('Hapus data tujuan & sasaran ini?')" style="display: inline;">
                                @csrf @method('DELETE')
                                <button type="submit" style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border); background: var(--surface); color: var(--danger); cursor: pointer; font-size: 12px; transition: all 0.2s;" onmouseover="this.style.background='var(--danger)'; this.style.color='#fff';" onmouseout="this.style.background='var(--surface)'; this.style.color='var(--danger)';">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Button Selanjutnya (Hanya muncul jika sudah ada data) --}}
                @if($tujuanSasaran)
                <div style="margin-top: 20px; text-align: right;">
                    <button type="button" @click="activeTab = 'pelaksanaan'" class="rfc-btn rfc-btn-primary">
                        Selanjutnya <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
                @endif
            @else
                {{-- Read-only view --}}
                <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 24px;">
                    <div style="font-weight: 800; font-size: 14px; color: var(--text); margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-bullseye" style="color: var(--accent);"></i>
                        Tujuan & Sasaran
                    </div>

                    @forelse($kegiatan->tujuans as $t)
                        <div style="margin-bottom: 20px; {{ !$loop->last ? 'border-bottom: 1px dashed var(--border); padding-bottom: 20px;' : '' }}">
                            <div style="margin-bottom: 16px;">
                                <div class="md-stat-label" style="margin-bottom: 6px;">Tujuan Kegiatan</div>
                                <div style="font-size: 13px; color: var(--text); line-height: 1.6; white-space: pre-line;">{{ $t->tujuan }}</div>
                            </div>

                            <div>
                                <div class="md-stat-label" style="margin-bottom: 6px;">Sasaran Kegiatan</div>
                                <div style="font-size: 13px; color: var(--text); line-height: 1.6; white-space: pre-line;">{{ $t->sasaran }}</div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: left; padding: 6px 0;">
                            <div style="background: rgba(245,158,11,.08); border: 1px solid rgba(245,158,11,.22); border-radius: 12px; padding: 14px 16px; color: var(--text-sub);">
                                <div style="display: flex; gap: 10px; align-items: flex-start;">
                                    <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(245,158,11,.14); color: #f59e0b; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 800; color: var(--text); font-size: 13px; margin-bottom: 6px;">
                                            Data tujuan dan sasaran belum diinput.
                                        </div>
                                        <div style="font-size: 13px; color: var(--text-sub); line-height: 1.6;">
                                            Silakan lengkapi data.
                                        </div>
                                        <div style="margin-top: 10px;">
                                            <button type="button" onclick="alert('Lengkapi data hanya tersedia pada mode Edit (Draft/Revisi).');"
                                                class="rfc-btn rfc-btn-primary" style="padding: 8px 14px; font-size: 12px;">
                                                <i class="fas fa-pen-to-square"></i> Lengkapi Data
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

                @if($tujuanSasaran)
                <div style="margin-top: 20px; text-align: right;">
                    <button type="button" @click="activeTab = 'pelaksanaan'" class="rfc-btn rfc-btn-primary">
                        Selanjutnya <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
                @endif
            @endif
            @include('auth.layout.jurusan._detail_tab_footer_kirim_revisi')
        </div>

        {{-- ═══ TAB 3: Pelaksanaan ═══ --}}
        <div class="tab-content mc-body" x-show="activeTab === 'pelaksanaan'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" style="padding: 24px; display: none;">
            @if($isEditMode)
            <form action="{{ route('jurusan.kerjasama.pelaksanaan.store', $kegiatan->id) }}" method="POST" style="background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; padding: 24px; margin-bottom: 20px;">
                @csrf
                <div style="font-weight: 800; font-size: 14px; color: var(--text); margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-plus-circle" style="color: var(--accent);"></i> Tambah Pelaksanaan
                </div>
                <div class="mc-grid-2">
                    <div class="mc-group" style="grid-column: 1 / -1;">
                        <label class="mc-label">Deskripsi <span class="mc-req">*</span></label>
                        <textarea name="deskripsi" rows="3" required placeholder="Deskripsi pelaksanaan..."
                            class="mc-input no-icon" style="resize: vertical;"></textarea>
                    </div>
                    <div class="mc-group">
                        <label class="mc-label">Cakupan</label>
                        <input type="text" name="cakupan" placeholder="Cakupan kegiatan" class="mc-input no-icon" />
                    </div>
                    <div class="mc-group">
                        <label class="mc-label">Jumlah Peserta</label>
                        <input type="number" name="jumlah_peserta" placeholder="0" min="0" class="mc-input no-icon" />
                    </div>
                    <div class="mc-group" style="grid-column: 1 / -1;">
                        <label class="mc-label">Sumber Daya</label>
                        <textarea name="sumber_daya" rows="2" placeholder="Sumber daya yang digunakan..."
                            class="mc-input no-icon" style="resize: vertical;"></textarea>
                    </div>
                </div>
                <div style="margin-top: 16px; text-align: right;">
                    <button type="submit" class="rfc-btn rfc-btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
            @endif

            @forelse($kegiatan->pelaksanaans as $p)
            <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 20px; margin-bottom: 16px; position: relative; box-shadow: 0 2px 12px rgba(0,0,0,0.02);">
                <div style="font-size: 13px; color: var(--text); line-height: 1.6; margin-bottom: 12px; {{ $isEditMode ? 'padding-right: 40px;' : '' }}">{{ $p->deskripsi }}</div>
                <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                    @if($p->cakupan)
                    <span style="font-size: 12px; color: var(--text-sub);"><strong style="color:var(--text);">Cakupan:</strong> {{ $p->cakupan }}</span>
                    @endif
                    @if($p->jumlah_peserta)
                    <span style="font-size: 12px; color: var(--text-sub);"><strong style="color:var(--text);">Peserta:</strong> {{ $p->jumlah_peserta }} orang</span>
                    @endif
                    @if($p->sumber_daya)
                    <span style="font-size: 12px; color: var(--text-sub);"><strong style="color:var(--text);">Sumber Daya:</strong> {{ $p->sumber_daya }}</span>
                    @endif
                </div>
                @if($isEditMode)
                <div style="position: absolute; top: 16px; right: 16px;">
                    <form action="{{ route('jurusan.kerjasama.pelaksanaan.destroy', [$kegiatan->id, $p->id]) }}" method="POST" onsubmit="return confirm('Hapus data pelaksanaan ini?')" style="display: inline;">
                        @csrf @method('DELETE')
                        <button type="submit" style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border); background: var(--surface); color: var(--danger); cursor: pointer; font-size: 12px; transition: all 0.2s;" onmouseover="this.style.background='var(--danger)'; this.style.color='#fff';" onmouseout="this.style.background='var(--surface)'; this.style.color='var(--danger)';">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @empty
            <div style="text-align: center; padding: 40px; color: var(--text-sub); font-size: 13px; background: var(--surface); border-radius: 12px; border: 1px dashed var(--border);">
                <div style="width: 48px; height: 48px; background: var(--surface2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px auto;">
                    <i class="fas fa-cogs" style="font-size: 20px; color: #9ca3af;"></i>
                </div>
                Belum ada data pelaksanaan.
            </div>
            @endforelse
            @include('auth.layout.jurusan._detail_tab_footer_kirim_revisi')
        </div>

        {{-- ═══ TAB 4: Hasil & Capaian ═══ --}}
        <div class="tab-content mc-body" x-show="activeTab === 'hasil'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" style="padding: 24px; display: none;">
            @if($isEditMode)
            <form action="{{ route('jurusan.kerjasama.hasil.store', $kegiatan->id) }}" method="POST" style="background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; padding: 24px; margin-bottom: 20px;">
                @csrf
                <div style="font-weight: 800; font-size: 14px; color: var(--text); margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-plus-circle" style="color: var(--accent);"></i> Tambah Hasil & Capaian
                </div>
                <div class="mc-grid-2">
                    <div class="mc-group">
                        <label class="mc-label">Hasil Langsung</label>
                        <textarea name="hasil_langsung" rows="3" placeholder="Hasil langsung kegiatan..."
                            class="mc-input no-icon" style="resize: vertical;"></textarea>
                    </div>
                    <div class="mc-group">
                        <label class="mc-label">Dampak</label>
                        <textarea name="dampak" rows="3" placeholder="Dampak kegiatan..."
                            class="mc-input no-icon" style="resize: vertical;"></textarea>
                    </div>
                    <div class="mc-group">
                        <label class="mc-label">Manfaat Mahasiswa</label>
                        <textarea name="manfaat_mahasiswa" rows="2" placeholder="Manfaat bagi mahasiswa..."
                            class="mc-input no-icon" style="resize: vertical;"></textarea>
                    </div>
                    <div class="mc-group">
                        <label class="mc-label">Manfaat Polimdo</label>
                        <textarea name="manfaat_polimdo" rows="2" placeholder="Manfaat bagi Polimdo..."
                            class="mc-input no-icon" style="resize: vertical;"></textarea>
                    </div>
                    <div class="mc-group" style="grid-column: 1 / -1;">
                        <label class="mc-label">Manfaat Mitra</label>
                        <textarea name="manfaat_mitra" rows="2" placeholder="Manfaat bagi mitra..."
                            class="mc-input no-icon" style="resize: vertical;"></textarea>
                    </div>
                </div>
                <div style="margin-top: 16px; text-align: right;">
                    <button type="submit" class="rfc-btn rfc-btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
            @endif

            @forelse($kegiatan->hasils as $h)
            <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 20px; margin-bottom: 16px; position: relative; box-shadow: 0 2px 12px rgba(0,0,0,0.02);">
                <div class="mc-grid-2" style="{{ $isEditMode ? 'padding-right: 40px;' : '' }}">
                    @if($h->hasil_langsung)
                    <div><div class="md-stat-label" style="margin-bottom: 4px;">Hasil Langsung</div><div style="font-size: 13px; color: var(--text); line-height: 1.5;">{{ $h->hasil_langsung }}</div></div>
                    @endif
                    @if($h->dampak)
                    <div><div class="md-stat-label" style="margin-bottom: 4px;">Dampak</div><div style="font-size: 13px; color: var(--text); line-height: 1.5;">{{ $h->dampak }}</div></div>
                    @endif
                    @if($h->manfaat_mahasiswa)
                    <div><div class="md-stat-label" style="margin-bottom: 4px;">Manfaat Mahasiswa</div><div style="font-size: 13px; color: var(--text); line-height: 1.5;">{{ $h->manfaat_mahasiswa }}</div></div>
                    @endif
                    @if($h->manfaat_polimdo)
                    <div><div class="md-stat-label" style="margin-bottom: 4px;">Manfaat Polimdo</div><div style="font-size: 13px; color: var(--text); line-height: 1.5;">{{ $h->manfaat_polimdo }}</div></div>
                    @endif
                    @if($h->manfaat_mitra)
                    <div style="grid-column: 1 / -1;"><div class="md-stat-label" style="margin-bottom: 4px;">Manfaat Mitra</div><div style="font-size: 13px; color: var(--text); line-height: 1.5;">{{ $h->manfaat_mitra }}</div></div>
                    @endif
                </div>
                @if($isEditMode)
                <div style="position: absolute; top: 16px; right: 16px;">
                    <form action="{{ route('jurusan.kerjasama.hasil.destroy', [$kegiatan->id, $h->id]) }}" method="POST" onsubmit="return confirm('Hapus data hasil ini?')" style="display: inline;">
                        @csrf @method('DELETE')
                        <button type="submit" style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border); background: var(--surface); color: var(--danger); cursor: pointer; font-size: 12px; transition: all 0.2s;" onmouseover="this.style.background='var(--danger)'; this.style.color='#fff';" onmouseout="this.style.background='var(--surface)'; this.style.color='var(--danger)';">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @empty
            <div style="text-align: center; padding: 40px; color: var(--text-sub); font-size: 13px; background: var(--surface); border-radius: 12px; border: 1px dashed var(--border);">
                <div style="width: 48px; height: 48px; background: var(--surface2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px auto;">
                    <i class="fas fa-chart-line" style="font-size: 20px; color: #9ca3af;"></i>
                </div>
                Belum ada data hasil & capaian.
            </div>
            @endforelse
            @include('auth.layout.jurusan._detail_tab_footer_kirim_revisi')
        </div>

        {{-- ═══ TAB 5: Permasalahan & Solusi ═══ --}}
        <div class="tab-content mc-body" x-show="activeTab === 'masalah'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" style="padding: 24px; display: none;">
            @if($isEditMode)
            <form action="{{ route('jurusan.kerjasama.permasalahan.store', $kegiatan->id) }}" method="POST" style="background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; padding: 24px; margin-bottom: 20px;">
                @csrf
                <div style="font-weight: 800; font-size: 14px; color: var(--text); margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-plus-circle" style="color: var(--accent);"></i> Tambah Permasalahan & Solusi
                </div>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div class="mc-group">
                        <label class="mc-label">Kendala / Permasalahan</label>
                        <textarea name="kendala" rows="3" placeholder="Jelaskan kendala atau permasalahan yang dihadapi dalam pelaksanaan kegiatan kerjasama..."
                            class="mc-input no-icon" style="resize: vertical;">{{ old('kendala') }}</textarea>
                    </div>
                    <div class="mc-group">
                        <label class="mc-label">Solusi</label>
                        <textarea name="solusi" rows="3" placeholder="Jelaskan solusi yang diterapkan untuk mengatasi kendala tersebut..."
                            class="mc-input no-icon" style="resize: vertical;">{{ old('solusi') }}</textarea>
                    </div>
                    <div class="mc-group">
                        <label class="mc-label">Rekomendasi</label>
                        <textarea name="rekomendasi" rows="3" placeholder="Berikan rekomendasi untuk perbaikan di masa mendatang..."
                            class="mc-input no-icon" style="resize: vertical;">{{ old('rekomendasi') }}</textarea>
                    </div>
                </div>
                <div style="margin-top: 16px; text-align: right;">
                    <button type="submit" class="rfc-btn rfc-btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
            @endif

            @forelse($kegiatan->permasalahanSolusis as $ps)
            <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 20px; margin-bottom: 16px; position: relative; box-shadow: 0 2px 12px rgba(0,0,0,0.02);">
                <div style="display: flex; flex-direction: column; gap: 16px; {{ $isEditMode ? 'padding-right: 40px;' : '' }}">
                    <div>
                        <div class="md-stat-label" style="margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                            <span style="width: 24px; height: 24px; border-radius: 6px; background: rgba(239,68,68,.1); color: #ef4444; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">
                                <i class="fas fa-exclamation-triangle"></i>
                            </span>
                            Kendala / Permasalahan
                        </div>
                        <div style="font-size: 13px; color: var(--text); line-height: 1.6; white-space: pre-line;">{{ $ps->kendala ?: '-' }}</div>
                    </div>
                    <div>
                        <div class="md-stat-label" style="margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                            <span style="width: 24px; height: 24px; border-radius: 6px; background: rgba(16,185,129,.1); color: #10b981; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">
                                <i class="fas fa-check-circle"></i>
                            </span>
                            Solusi
                        </div>
                        <div style="font-size: 13px; color: var(--text); line-height: 1.6; white-space: pre-line;">{{ $ps->solusi ?: '-' }}</div>
                    </div>
                    <div>
                        <div class="md-stat-label" style="margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                            <span style="width: 24px; height: 24px; border-radius: 6px; background: rgba(79,70,229,.1); color: #4f46e5; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">
                                <i class="fas fa-lightbulb"></i>
                            </span>
                            Rekomendasi
                        </div>
                        <div style="font-size: 13px; color: var(--text); line-height: 1.6; white-space: pre-line;">{{ $ps->rekomendasi ?: '-' }}</div>
                    </div>
                </div>
                @if($isEditMode)
                <div style="position: absolute; top: 16px; right: 16px;">
                    <form action="{{ route('jurusan.kerjasama.permasalahan.destroy', [$kegiatan->id, $ps->id]) }}" method="POST" onsubmit="return confirm('Hapus data permasalahan & solusi ini?')" style="display: inline;">
                        @csrf @method('DELETE')
                        <button type="submit" style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border); background: var(--surface); color: var(--danger); cursor: pointer; font-size: 12px; transition: all 0.2s;" onmouseover="this.style.background='var(--danger)'; this.style.color='#fff';" onmouseout="this.style.background='var(--surface)'; this.style.color='var(--danger)';">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @empty
            <div style="text-align: center; padding: 40px; color: var(--text-sub); font-size: 13px; background: var(--surface); border-radius: 12px; border: 1px dashed var(--border);">
                <div style="width: 48px; height: 48px; background: var(--surface2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px auto;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 20px; color: #9ca3af;"></i>
                </div>
                Belum ada data permasalahan & solusi.
            </div>
            @endforelse
            @include('auth.layout.jurusan._detail_tab_footer_kirim_revisi')
        </div>

        {{-- ═══ TAB 6: Dokumentasi ═══ --}}
        <div class="tab-content mc-body" x-show="activeTab === 'dokumentasi'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" style="padding: 24px; display: none;">
            @if($isEditMode)
            <form action="{{ route('jurusan.kerjasama.dokumentasi.store', $kegiatan->id) }}" method="POST" style="background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; padding: 24px; margin-bottom: 20px;">
                @csrf
                <div style="font-weight: 800; font-size: 14px; color: var(--text); margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-plus-circle" style="color: var(--accent);"></i> Tambah Dokumentasi
                </div>
                <div class="mc-grid-2">
                    <div class="mc-group">
                        <label class="mc-label">Link Google Drive <span class="mc-req">*</span></label>
                        <div class="mc-input-wrap">
                            <i class="fas fa-link mc-icon-left"></i>
                            <input type="text" name="link_drive" required placeholder="https://drive.google.com/..." class="mc-input" />
                        </div>
                    </div>
                    <div class="mc-group">
                        <label class="mc-label">Keterangan</label>
                        <div class="mc-input-wrap">
                            <i class="fas fa-comment-dots mc-icon-left"></i>
                            <input type="text" name="keterangan" placeholder="Keterangan dokumentasi..." class="mc-input" />
                        </div>
                    </div>
                </div>
                <div style="margin-top: 16px; text-align: right;">
                    <button type="submit" class="rfc-btn rfc-btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
            @endif

            @forelse($kegiatan->dokumentasis as $d)
            <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 16px 20px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; position: relative;">
                <div style="display: flex; align-items: center; gap: 14px; flex: 1; min-width: 0;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(79, 70, 229, 0.1); display: flex; align-items: center; justify-content: center; color: #4f46e5; font-size: 16px; flex-shrink: 0;">
                        <i class="fas fa-link"></i>
                    </div>
                    <div style="min-width: 0;">
                        <a href="{{ $d->link_drive }}" target="_blank" style="font-size: 13px; font-weight: 700; color: var(--accent); text-decoration: none; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $d->link_drive }}</a>
                        @if($d->keterangan)
                        <div style="font-size: 12px; color: var(--text-sub); margin-top: 4px;">{{ $d->keterangan }}</div>
                        @endif
                    </div>
                </div>
                @if($isEditMode)
                <form action="{{ route('jurusan.kerjasama.dokumentasi.destroy', [$kegiatan->id, $d->id]) }}" method="POST" onsubmit="return confirm('Hapus dokumentasi ini?')" style="flex-shrink: 0; margin-left: 12px;">
                    @csrf @method('DELETE')
                    <button type="submit" style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border); background: var(--surface); color: var(--danger); cursor: pointer; font-size: 12px; transition: all 0.2s;" onmouseover="this.style.background='var(--danger)'; this.style.color='#fff';" onmouseout="this.style.background='var(--surface)'; this.style.color='var(--danger)';">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
                @endif
            </div>
            @empty
            <div style="text-align: center; padding: 40px; color: var(--text-sub); font-size: 13px; background: var(--surface); border-radius: 12px; border: 1px dashed var(--border);">
                <div style="width: 48px; height: 48px; background: var(--surface2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px auto;">
                    <i class="fas fa-file-alt" style="font-size: 20px; color: #9ca3af;"></i>
                </div>
                Belum ada dokumentasi.
            </div>
            @endforelse
            @include('auth.layout.jurusan._detail_tab_footer_kirim_revisi')
        </div>
    </div>

    @if(in_array($kegiatan->status, ['draft', 'revisi'], true))
    <form id="submitToPimpinanForm" action="{{ route('jurusan.kerjasama.submit', $kegiatan->id) }}" method="POST" style="display: none;">
        @csrf
    </form>
    @endif

    {{-- ═══ SUBMIT TO PIMPINAN SECTION ═══ --}}
    @if($kegiatan->status === 'draft')
    <div style="margin-top: 24px; background: linear-gradient(135deg, rgba(79,70,229,.06), rgba(99,102,241,.04)); border: 1.5px solid rgba(79,70,229,.2); border-radius: 14px; padding: 24px; display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;">
        <div style="display: flex; align-items: flex-start; gap: 14px; flex: 1; min-width: 260px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, #4f46e5, #6366f1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-paper-plane" style="color: #fff; font-size: 16px;"></i>
            </div>
            <div>
                <div style="font-weight: 800; font-size: 14px; color: var(--text); margin-bottom: 4px;">Kirim ke Pimpinan</div>
                <div style="font-size: 13px; color: var(--text-sub); line-height: 1.5;">Pastikan semua data kerjasama sudah lengkap dan benar sebelum mengirim ke Pimpinan untuk dievaluasi.</div>
            </div>
        </div>

        <button type="button" onclick="confirmSubmitKerjasamaJurusan()" style="padding: 12px 28px; background: linear-gradient(135deg, #4f46e5, #6366f1); color: #fff; border: none; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: all 0.3s; box-shadow: 0 4px 14px rgba(79,70,229,.3); white-space: nowrap;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(79,70,229,.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px rgba(79,70,229,.3)';">
            <i class="fas fa-paper-plane"></i> Kirim ke Pimpinan
        </button>
    </div>
    @elseif($kegiatan->status === 'revisi')
    <div style="margin-top: 24px; background: linear-gradient(135deg, rgba(245,158,11,.08), rgba(251,191,36,.05)); border: 1.5px solid rgba(245,158,11,.28); border-radius: 14px; padding: 20px 24px; display: flex; align-items: flex-start; gap: 14px; flex-wrap: wrap;">
        <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(245,158,11,.14); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <i class="fas fa-pen-to-square" style="color: #d97706; font-size: 16px;"></i>
        </div>
        <div style="flex: 1; min-width: 240px;">
            <div style="font-weight: 800; font-size: 14px; color: var(--text); margin-bottom: 4px;">Perlu Revisi — Catatan dari Pimpinan</div>
            <div style="font-size: 13px; color: var(--text-sub); line-height: 1.5; margin-bottom: 16px;">Silakan sesuaikan data kerjasama sesuai ringkasan, saran, dan tindak lanjut berikut, lalu kirim ulang dari tab detail.</div>

            @if($kesimpulanPimpinan)
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 14px 16px;">
                        <div class="md-stat-label" style="margin-bottom: 6px;">Ringkasan</div>
                        <div style="font-size: 13px; color: var(--text); line-height: 1.65; white-space: pre-line;">{{ $kesimpulanPimpinan->ringkasan ?: '—' }}</div>
                    </div>
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 14px 16px;">
                        <div class="md-stat-label" style="margin-bottom: 6px;">Saran</div>
                        <div style="font-size: 13px; color: var(--text); line-height: 1.65; white-space: pre-line;">{{ $kesimpulanPimpinan->saran ?: '—' }}</div>
                    </div>
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 14px 16px;">
                        <div class="md-stat-label" style="margin-bottom: 6px;">Tindak lanjut</div>
                        <div style="font-size: 13px; color: var(--text); line-height: 1.65; white-space: pre-line;">{{ $kesimpulanPimpinan->tindak_lanjut ?: '—' }}</div>
                    </div>
                </div>
            @else
                <div style="font-size: 13px; color: var(--text-sub); background: rgba(245,158,11,.06); border: 1px dashed rgba(245,158,11,.35); border-radius: 12px; padding: 14px 16px;">
                    Catatan kesimpulan dari Pimpinan belum tersedia di sistem. Hubungi Pimpinan jika diperlukan.
                </div>
            @endif
        </div>
    </div>
    @elseif($kegiatan->status === 'menunggu_evaluasi')
    <div style="margin-top: 24px; background: linear-gradient(135deg, rgba(59,130,246,.06), rgba(96,165,250,.04)); border: 1.5px solid rgba(59,130,246,.2); border-radius: 14px; padding: 20px 24px; display: flex; align-items: center; gap: 14px;">
        <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(59,130,246,.12); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <i class="fas fa-clock" style="color: #3b82f6; font-size: 16px;"></i>
        </div>
        <div>
            <div style="font-weight: 800; font-size: 14px; color: var(--text); margin-bottom: 2px;">Menunggu Evaluasi Pimpinan</div>
            <div style="font-size: 13px; color: var(--text-sub);">Data kerjasama telah dikirim dan sedang menunggu evaluasi dari Pimpinan.</div>
        </div>
    </div>
    @endif

    {{-- Back button --}}
    <div style="margin-top: 24px;">
        <a href="{{ route('jurusan.dkerjasama') }}" class="rfc-btn" style="background: var(--surface); color: var(--text-sub); border: 1px solid var(--border); text-decoration: none; font-size: 13px; font-weight: 700;">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Kerjasama
        </a>
    </div>
</main>

<script>
const KERJASAMA_JURUSAN_SUBMIT_IS_REVISI = @json($kegiatan->status === 'revisi');

function confirmSubmitKerjasamaJurusan() {
    const isRevisi = KERJASAMA_JURUSAN_SUBMIT_IS_REVISI;
    const title = isRevisi ? 'Kirim ulang ke Pimpinan?' : 'Apakah Anda Sudah Yakin?';
    const html = isRevisi
        ? `<div style="text-align: left; font-size: 14px; color: #64748b; line-height: 1.7; margin-top: 8px;">
                <p style="margin-bottom: 12px;">Anda akan mengirim kembali data yang <strong>sudah direvisi</strong> ke Pimpinan untuk evaluasi ulang.</p>
                <p style="margin-bottom: 12px;">Pastikan perbaikan sudah mengikuti catatan Pimpinan pada bagian <strong>Perlu Revisi</strong>.</p>
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <div style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-check-circle" style="color: #10b981; font-size: 13px;"></i><span>Informasi Umum</span></div>
                    <div style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-check-circle" style="color: #10b981; font-size: 13px;"></i><span>Tujuan & Sasaran</span></div>
                    <div style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-check-circle" style="color: #10b981; font-size: 13px;"></i><span>Pelaksanaan</span></div>
                    <div style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-check-circle" style="color: #10b981; font-size: 13px;"></i><span>Hasil & Capaian</span></div>
                    <div style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-check-circle" style="color: #10b981; font-size: 13px;"></i><span>Permasalahan & Solusi</span></div>
                    <div style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-check-circle" style="color: #10b981; font-size: 13px;"></i><span>Dokumentasi</span></div>
                </div>
                <p style="margin-top: 14px; color: #ef4444; font-weight: 600; font-size: 13px;">
                    <i class="fas fa-info-circle"></i> Setelah dikirim, data tidak dapat diedit hingga Pimpinan mengevaluasi kembali.
                </p>
            </div>`
        : `<div style="text-align: left; font-size: 14px; color: #64748b; line-height: 1.7; margin-top: 8px;">
                <p style="margin-bottom: 12px;">Pastikan semua data berikut sudah terisi dengan benar:</p>
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <div style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-check-circle" style="color: #10b981; font-size: 13px;"></i><span>Informasi Umum</span></div>
                    <div style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-check-circle" style="color: #10b981; font-size: 13px;"></i><span>Tujuan & Sasaran</span></div>
                    <div style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-check-circle" style="color: #10b981; font-size: 13px;"></i><span>Pelaksanaan</span></div>
                    <div style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-check-circle" style="color: #10b981; font-size: 13px;"></i><span>Hasil & Capaian</span></div>
                    <div style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-check-circle" style="color: #10b981; font-size: 13px;"></i><span>Permasalahan & Solusi</span></div>
                    <div style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-check-circle" style="color: #10b981; font-size: 13px;"></i><span>Dokumentasi</span></div>
                </div>
                <p style="margin-top: 14px; color: #ef4444; font-weight: 600; font-size: 13px;">
                    <i class="fas fa-info-circle"></i> Setelah dikirim, data tidak dapat diedit kembali.
                </p>
            </div>`;

    Swal.fire({
        title: title,
        html: html,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: isRevisi
            ? '<i class="fas fa-paper-plane"></i>&nbsp; Kirim (sudah direvisi)'
            : '<i class="fas fa-paper-plane"></i>&nbsp; Kirim ke Pimpinan',
        cancelButtonText: '<i class="fas fa-times"></i>&nbsp; Batal',
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        focusCancel: true,
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Mengirim...',
                text: 'Sedang mengirim data ke Pimpinan',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });
            const form = document.getElementById('submitToPimpinanForm');
            if (form) form.submit();
        }
    });
}
</script>