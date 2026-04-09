<!-- Main Content -->
<main id="mainContent">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-home" style="font-size:11px;"></i>
            <span class="sep">/</span>
            <a href="{{ route('jurusan.hasil_evaluasi') }}"
                style="text-decoration:none; color:var(--accent); font-weight:600;">Hasil Evaluasi</a>
            <span class="sep">/</span>
            <span class="current">Detail Evaluasi</span>
        </div>
        <h2 id="pageTitle">Detail Evaluasi Kinerja</h2>
        <p id="pageDesc">Detail evaluasi kinerja untuk kegiatan <strong>{{ $kegiatan->nama_kegiatan }}</strong>.</p>
    </div>

    @if(session('success'))
        <div
            style="background: linear-gradient(135deg, rgba(16,185,129,.12), rgba(5,150,105,.08)); border: 1px solid rgba(16,185,129,.3); color: #065f46; padding: 14px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-check-circle" style="font-size: 16px; color: #10b981;"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div
            style="background: linear-gradient(135deg, rgba(239,68,68,.12), rgba(220,38,38,.08)); border: 1px solid rgba(239,68,68,.3); color: #991b1b; padding: 14px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-exclamation-circle" style="font-size: 16px; color: #ef4444;"></i>
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div
            style="background: linear-gradient(135deg, rgba(239,68,68,.12), rgba(220,38,38,.08)); border: 1px solid rgba(239,68,68,.3); color: #991b1b; padding: 14px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600;">
            <i class="fas fa-exclamation-circle" style="margin-right:8px; color:#ef4444;"></i>
            Mohon lengkapi semua penilaian sebelum menyimpan.
        </div>
    @endif

    {{-- ═══ STATUS HEADER ═══ --}}
    <div class="md-stats-container">
        <div class="md-stat-card">
            <div class="md-stat-icon md-icon-primary"><i class="fas fa-handshake"></i></div>
            <div class="md-stat-info">
                <div class="md-stat-label">Jenis</div>
                <div class="md-stat-value">{{ $kegiatan->jenisKerjasama->pluck('nama_kerjasama')->join(', ') ?: '-' }}
                </div>
            </div>
        </div>
        <div class="md-stat-card">
            <div class="md-stat-icon md-icon-warning"><i class="fas fa-calendar"></i></div>
            <div class="md-stat-info">
                <div class="md-stat-label">Periode</div>
                <div class="md-stat-value">{{ $kegiatan->periode_mulai?->format('d M Y') ?? '-' }} —
                    {{ $kegiatan->periode_selesai?->format('d M Y') ?? '-' }}</div>
            </div>
        </div>
        <div class="md-stat-card" style="flex: 0; min-width: auto; padding: 0 24px; justify-content: center;">
            <span class="tag {{ $kegiatan->status_class }}" style="font-size: 13px; padding: 8px 16px;">
                <i class="fas fa-circle" style="font-size:7px;"></i> {{ $kegiatan->status_label }}
            </span>
        </div>
    </div>

    {{-- ═══ DETAIL DATA KEGIATAN (Read-only) ═══ --}}
    <div class="modern-card" x-data="{ activeTab: 'umum' }">
        <div class="md-tab-nav">
            <button class="md-tab-btn" :class="{ 'active': activeTab === 'umum' }" @click="activeTab = 'umum'"><i
                    class="fas fa-info-circle"></i> Informasi Umum</button>
            <button class="md-tab-btn" :class="{ 'active': activeTab === 'tujuan' }" @click="activeTab = 'tujuan'"><i
                    class="fas fa-bullseye"></i> Tujuan & Sasaran</button>
            <button class="md-tab-btn" :class="{ 'active': activeTab === 'pelaksanaan' }"
                @click="activeTab = 'pelaksanaan'"><i class="fas fa-cogs"></i> Pelaksanaan</button>
            <button class="md-tab-btn" :class="{ 'active': activeTab === 'hasil' }" @click="activeTab = 'hasil'"><i
                    class="fas fa-chart-line"></i> Hasil & Capaian</button>
            <button class="md-tab-btn" :class="{ 'active': activeTab === 'masalah' }" @click="activeTab = 'masalah'"><i
                    class="fas fa-exclamation-triangle"></i> Permasalahan</button>
            <button class="md-tab-btn" :class="{ 'active': activeTab === 'dokumentasi' }"
                @click="activeTab = 'dokumentasi'"><i class="fas fa-file-alt"></i> Dokumentasi</button>
        </div>

        {{-- TAB: Informasi Umum --}}
        <div class="tab-content mc-body" x-show="activeTab === 'umum'" x-transition
            style="padding: 24px; display: none;">
            <div class="mc-grid-2">
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <div class="md-stat-label" style="margin-bottom:4px;">Nama Kegiatan</div>
                        <div style="font-size:14px; font-weight:700; color:var(--text);">{{ $kegiatan->nama_kegiatan }}
                        </div>
                    </div>
                    <div>
                        <div class="md-stat-label" style="margin-bottom:4px;">Jenis Kerjasama</div>
                        <div style="display:flex; flex-wrap:wrap; gap:6px;">
                            @forelse($kegiatan->jenisKerjasama as $jk)<span class="tag tag-purple"
                            style="font-size:11px;">{{ $jk->nama_kerjasama }}</span>@empty<span
                                style="font-size:13px; color:var(--text-sub);">-</span>@endforelse</div>
                    </div>
                    <div>
                        <div class="md-stat-label" style="margin-bottom:4px;">Dibuat Oleh</div>
                        <div style="font-size:14px; color:var(--text);">{{ $kegiatan->creator?->name ?? '-' }}</div>
                    </div>
                </div>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <div class="md-stat-label" style="margin-bottom:4px;">Nomor MoU</div>
                        <div style="font-size:14px; color:var(--text); font-family:'DM Mono',monospace;">
                            {{ $kegiatan->nomor_mou ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="md-stat-label" style="margin-bottom:4px;">Tanggal MoU</div>
                        <div style="font-size:14px; color:var(--text);">
                            {{ $kegiatan->tanggal_mou?->format('d M Y') ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="md-stat-label" style="margin-bottom:4px;">Penanggung Jawab</div>
                        <div style="font-size:14px; color:var(--text);">{{ $kegiatan->penanggung_jawab ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="md-stat-label" style="margin-bottom:8px;">Mitra Kerjasama</div>
                        <div style="display:flex; flex-wrap:wrap; gap:6px;">@forelse($kegiatan->mitras as $m)<span
                        class="tag tag-blue" style="font-size:11px;">{{ $m->nama_mitra }}</span>@empty<span
                                style="font-size:13px; color:var(--text-sub);">Belum ada mitra</span>@endforelse</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB: Tujuan & Sasaran --}}
        <div class="tab-content mc-body" x-show="activeTab === 'tujuan'" x-transition
            style="padding: 24px; display: none;">
            @php $tujuanSasaran = $kegiatan->tujuans->first(); @endphp
            @if($tujuanSasaran)
                <div style="background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:24px;">
                    <div style="margin-bottom:20px;">
                        <div class="md-stat-label" style="margin-bottom:6px;">Tujuan Kegiatan</div>
                        <div style="font-size:13px; color:var(--text); line-height:1.6; white-space:pre-line;">
                            {{ $tujuanSasaran->tujuan }}</div>
                    </div>
                    <div>
                        <div class="md-stat-label" style="margin-bottom:6px;">Sasaran Kegiatan</div>
                        <div style="font-size:13px; color:var(--text); line-height:1.6; white-space:pre-line;">
                            {{ $tujuanSasaran->sasaran }}</div>
                    </div>
                </div>
            @else
                <div
                    style="text-align:center; padding:40px; color:var(--text-sub); font-size:13px; background:var(--surface); border-radius:12px; border:1px dashed var(--border);">
                    <i class="fas fa-bullseye"
                        style="font-size:20px; color:#9ca3af; display:block; margin-bottom:12px;"></i>Belum ada data tujuan
                    & sasaran.</div>
            @endif
        </div>

        {{-- TAB: Pelaksanaan --}}
        <div class="tab-content mc-body" x-show="activeTab === 'pelaksanaan'" x-transition
            style="padding: 24px; display: none;">
            @forelse($kegiatan->pelaksanaans as $p)
                <div
                    style="background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:20px; margin-bottom:16px;">
                    <div style="font-size:13px; color:var(--text); line-height:1.6; margin-bottom:12px;">{{ $p->deskripsi }}
                    </div>
                    <div style="display:flex; gap:16px; flex-wrap:wrap;">
                        @if($p->cakupan)<span style="font-size:12px; color:var(--text-sub);"><strong
                        style="color:var(--text);">Cakupan:</strong> {{ $p->cakupan }}</span>@endif
                        @if($p->jumlah_peserta)<span style="font-size:12px; color:var(--text-sub);"><strong
                        style="color:var(--text);">Peserta:</strong> {{ $p->jumlah_peserta }} orang</span>@endif
                        @if($p->sumber_daya)<span style="font-size:12px; color:var(--text-sub);"><strong
                        style="color:var(--text);">Sumber Daya:</strong> {{ $p->sumber_daya }}</span>@endif
                    </div>
                </div>
            @empty
                <div
                    style="text-align:center; padding:40px; color:var(--text-sub); font-size:13px; background:var(--surface); border-radius:12px; border:1px dashed var(--border);">
                    <i class="fas fa-cogs"
                        style="font-size:20px; color:#9ca3af; display:block; margin-bottom:12px;"></i>Belum ada data
                    pelaksanaan.</div>
            @endforelse
        </div>

        {{-- TAB: Hasil & Capaian --}}
        <div class="tab-content mc-body" x-show="activeTab === 'hasil'" x-transition
            style="padding: 24px; display: none;">
            @forelse($kegiatan->hasils as $h)
                <div
                    style="background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:20px; margin-bottom:16px;">
                    <div class="mc-grid-2">
                        @if($h->hasil_langsung)
                            <div>
                                <div class="md-stat-label" style="margin-bottom:4px;">Hasil Langsung</div>
                                <div style="font-size:13px; color:var(--text); line-height:1.5;">{{ $h->hasil_langsung }}</div>
                        </div>@endif
                        @if($h->dampak)
                            <div>
                                <div class="md-stat-label" style="margin-bottom:4px;">Dampak</div>
                                <div style="font-size:13px; color:var(--text); line-height:1.5;">{{ $h->dampak }}</div>
                        </div>@endif
                        @if($h->manfaat_mahasiswa)
                            <div>
                                <div class="md-stat-label" style="margin-bottom:4px;">Manfaat Mahasiswa</div>
                                <div style="font-size:13px; color:var(--text); line-height:1.5;">{{ $h->manfaat_mahasiswa }}
                                </div>
                        </div>@endif
                        @if($h->manfaat_polimdo)
                            <div>
                                <div class="md-stat-label" style="margin-bottom:4px;">Manfaat Polimdo</div>
                                <div style="font-size:13px; color:var(--text); line-height:1.5;">{{ $h->manfaat_polimdo }}</div>
                        </div>@endif
                        @if($h->manfaat_mitra)
                            <div style="grid-column:1/-1;">
                                <div class="md-stat-label" style="margin-bottom:4px;">Manfaat Mitra</div>
                                <div style="font-size:13px; color:var(--text); line-height:1.5;">{{ $h->manfaat_mitra }}</div>
                        </div>@endif
                    </div>
                </div>
            @empty
                <div
                    style="text-align:center; padding:40px; color:var(--text-sub); font-size:13px; background:var(--surface); border-radius:12px; border:1px dashed var(--border);">
                    <i class="fas fa-chart-line"
                        style="font-size:20px; color:#9ca3af; display:block; margin-bottom:12px;"></i>Belum ada data hasil &
                    capaian.</div>
            @endforelse
        </div>

        {{-- TAB: Permasalahan & Solusi --}}
        <div class="tab-content mc-body" x-show="activeTab === 'masalah'" x-transition
            style="padding: 24px; display: none;">
            @forelse($kegiatan->permasalahanSolusis as $ps)
                <div
                    style="background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:20px; margin-bottom:16px;">
                    <div style="display:flex; flex-direction:column; gap:16px;">
                        <div>
                            <div class="md-stat-label"
                                style="margin-bottom:6px; display:flex; align-items:center; gap:8px;"><span
                                    style="width:24px; height:24px; border-radius:6px; background:rgba(239,68,68,.1); color:#ef4444; display:inline-flex; align-items:center; justify-content:center; font-size:11px;"><i
                                        class="fas fa-exclamation-triangle"></i></span>Kendala</div>
                            <div style="font-size:13px; color:var(--text); line-height:1.6; white-space:pre-line;">
                                {{ $ps->kendala ?: '-' }}</div>
                        </div>
                        <div>
                            <div class="md-stat-label"
                                style="margin-bottom:6px; display:flex; align-items:center; gap:8px;"><span
                                    style="width:24px; height:24px; border-radius:6px; background:rgba(16,185,129,.1); color:#10b981; display:inline-flex; align-items:center; justify-content:center; font-size:11px;"><i
                                        class="fas fa-check-circle"></i></span>Solusi</div>
                            <div style="font-size:13px; color:var(--text); line-height:1.6; white-space:pre-line;">
                                {{ $ps->solusi ?: '-' }}</div>
                        </div>
                        <div>
                            <div class="md-stat-label"
                                style="margin-bottom:6px; display:flex; align-items:center; gap:8px;"><span
                                    style="width:24px; height:24px; border-radius:6px; background:rgba(79,70,229,.1); color:#4f46e5; display:inline-flex; align-items:center; justify-content:center; font-size:11px;"><i
                                        class="fas fa-lightbulb"></i></span>Rekomendasi</div>
                            <div style="font-size:13px; color:var(--text); line-height:1.6; white-space:pre-line;">
                                {{ $ps->rekomendasi ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    style="text-align:center; padding:40px; color:var(--text-sub); font-size:13px; background:var(--surface); border-radius:12px; border:1px dashed var(--border);">
                    <i class="fas fa-exclamation-triangle"
                        style="font-size:20px; color:#9ca3af; display:block; margin-bottom:12px;"></i>Belum ada data
                    permasalahan & solusi.</div>
            @endforelse
        </div>

        {{-- TAB: Dokumentasi --}}
        <div class="tab-content mc-body" x-show="activeTab === 'dokumentasi'" x-transition
            style="padding: 24px; display: none;">
            @forelse($kegiatan->dokumentasis as $d)
                <div
                    style="background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:16px 20px; margin-bottom:12px; display:flex; align-items:center; gap:14px;">
                    <div
                        style="width:40px; height:40px; border-radius:10px; background:rgba(79,70,229,0.1); display:flex; align-items:center; justify-content:center; color:#4f46e5; font-size:16px; flex-shrink:0;">
                        <i class="fas fa-link"></i></div>
                    <div style="min-width:0; flex:1;">
                        <a href="{{ $d->link_drive }}" target="_blank"
                            style="font-size:13px; font-weight:700; color:var(--accent); text-decoration:none; display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $d->link_drive }}</a>
                        @if($d->keterangan)
                        <div style="font-size:12px; color:var(--text-sub); margin-top:4px;">{{ $d->keterangan }}</div>@endif
                    </div>
                </div>
            @empty
                <div
                    style="text-align:center; padding:40px; color:var(--text-sub); font-size:13px; background:var(--surface); border-radius:12px; border:1px dashed var(--border);">
                    <i class="fas fa-file-alt"
                        style="font-size:20px; color:#9ca3af; display:block; margin-bottom:12px;"></i>Belum ada dokumentasi.
                </div>
            @endforelse
        </div>
    </div>

    {{-- ═══ FORM EVALUASI ═══ --}}
    @if(!($readonly ?? false) && $kegiatan->status !== 'menunggu_validasi' && $kegiatan->status !== 'selesai')
        <form
            action="{{ $existingEval ? route('unit.evaluasi.update', $kegiatan->id) : route('unit.evaluasi.store', $kegiatan->id) }}"
            method="POST" id="formEvaluasi" onsubmit="return confirmSubmitEval(event)">
            @csrf
            @if($existingEval) @method('PUT') @endif

            <div class="card um-card" style="margin-top: 24px; margin-bottom: 24px;">
                <div class="card-header um-header"
                    style="display:flex; justify-content:space-between; align-items:center; padding:15px 20px; background:linear-gradient(135deg, rgba(245,158,11,0.06), rgba(217,119,6,0.03));">
                    <div class="um-title"
                        style="font-weight:700; color:var(--text); display:flex; align-items:center; gap:10px;">
                        <div
                            style="width:32px; height:32px; border-radius:8px; background:rgba(245,158,11,0.12); color:#d97706; display:flex; align-items:center; justify-content:center; font-size:14px;">
                            <i class="fas fa-star"></i></div>
                        <div><span style="display:block; font-size:14px;">Penilaian Kinerja</span><span
                                style="display:block; font-size:11px; font-weight:500; color:var(--text-sub);">Klik bintang
                                untuk memberi skor (1-5)</span></div>
                    </div>
                </div>
                <div class="card-body" style="padding: 24px;">
                    <div style="display:flex; flex-direction:column; gap:24px;">
                        @php
                            $criteria = [
                                ['name' => 'sesuai_rencana', 'label' => 'Kesesuaian dengan Rencana', 'icon' => 'fa-bullseye', 'desc' => 'Apakah pelaksanaan sesuai rencana kerjasama?'],
                                ['name' => 'kualitas', 'label' => 'Kualitas Output', 'icon' => 'fa-gem', 'desc' => 'Bagaimana kualitas hasil yang dihasilkan?'],
                                ['name' => 'keterlibatan', 'label' => 'Keterlibatan Pihak', 'icon' => 'fa-users', 'desc' => 'Seberapa aktif keterlibatan semua pihak?'],
                                ['name' => 'efisiensi', 'label' => 'Efisiensi Sumber Daya', 'icon' => 'fa-chart-line', 'desc' => 'Apakah penggunaan sumber daya efisien?'],
                                ['name' => 'kepuasan', 'label' => 'Kepuasan Keseluruhan', 'icon' => 'fa-face-smile', 'desc' => 'Tingkat kepuasan keseluruhan.'],
                            ];
                        @endphp

                        @foreach($criteria as $c)
                            <div
                                style="display:flex; align-items:flex-start; gap:16px; padding:20px; border-radius:12px; background:var(--card-bg, #f8fafc); border:1px solid var(--border, rgba(0,0,0,.06));">
                                <div
                                    style="flex-shrink:0; width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(79,70,229,.1), rgba(99,102,241,.06)); display:flex; align-items:center; justify-content:center; color:#4f46e5; font-size:16px;">
                                    <i class="fas {{ $c['icon'] }}"></i></div>
                                <div style="flex:1; min-width:0;">
                                    <div
                                        style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; margin-bottom:6px;">
                                        <div>
                                            <h4 style="margin:0; font-size:14px; font-weight:700; color:var(--text);">
                                                {{ $c['label'] }}</h4>
                                            <p style="margin:2px 0 0; font-size:12px; color:var(--text-sub);">{{ $c['desc'] }}
                                            </p>
                                        </div>
                                        <div style="font-family:'DM Mono',monospace; font-size:13px; font-weight:700; color:var(--text-sub); padding:4px 10px; border-radius:6px; background:rgba(79,70,229,.08);"
                                            id="score-display-{{ $c['name'] }}">
                                            {{ old($c['name'], $existingEval?->{$c['name']} ?? 0) }}/5</div>
                                    </div>
                                    <div class="star-rating" data-name="{{ $c['name'] }}"
                                        style="display:flex; gap:4px; margin-top:8px;">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button"
                                                class="star-btn {{ $i <= old($c['name'], $existingEval?->{$c['name']} ?? 0) ? 'active' : '' }}"
                                                data-value="{{ $i }}"
                                                style="background:none; border:none; cursor:pointer; padding:4px 6px; font-size:22px; color:{{ $i <= old($c['name'], $existingEval?->{$c['name']} ?? 0) ? '#f59e0b' : 'rgba(0,0,0,.15)' }}; transition:all .15s;"><i
                                                    class="fas fa-star"></i></button>
                                        @endfor
                                        <input type="hidden" name="{{ $c['name'] }}" id="input-{{ $c['name'] }}"
                                            value="{{ old($c['name'], $existingEval?->{$c['name']} ?? '') }}">
                                    </div>
                                    @error($c['name'])<p
                                        style="color:#ef4444; font-size:12px; margin:6px 0 0; font-weight:600;"><i
                                            class="fas fa-circle-exclamation" style="font-size:10px;"></i> {{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Catatan --}}
            <div class="card um-card" style="margin-bottom: 24px;">
                <div class="card-header um-header" style="display:flex; align-items:center; padding:15px 20px;">
                    <div class="um-title"
                        style="font-weight:700; color:var(--text); display:flex; align-items:center; gap:10px;">
                        <div
                            style="width:32px; height:32px; border-radius:8px; background:rgba(16,185,129,0.1); color:#10b981; display:flex; align-items:center; justify-content:center; font-size:14px;">
                            <i class="fas fa-comment-dots"></i></div>
                        <div><span style="display:block; font-size:14px;">Catatan Tambahan</span><span
                                style="display:block; font-size:11px; font-weight:500; color:var(--text-sub);">Opsional —
                                berikan catatan evaluasi</span></div>
                    </div>
                </div>
                <div class="card-body" style="padding: 20px;">
                    <textarea name="catatan" rows="4" placeholder="Tulis catatan evaluasi Anda di sini..."
                        style="width:100%; padding:14px 16px; border-radius:10px; border:1px solid var(--border, rgba(0,0,0,.1)); background:var(--card-bg, #fff); color:var(--text); font-size:13px; font-family:'Plus Jakarta Sans',sans-serif; resize:vertical; min-height:100px; outline:none; box-sizing:border-box;"
                        onfocus="this.style.borderColor='var(--accent)'; this.style.boxShadow='0 0 0 3px rgba(79,70,229,.1)'"
                        onblur="this.style.borderColor='var(--border)'; this.style.boxShadow='none'">{{ old('catatan', $existingEval?->catatan ?? '') }}</textarea>
                </div>
            </div>

            {{-- Summary & Save --}}
            <div class="card um-card" style="margin-bottom: 24px;">
                <div class="card-body" style="padding: 20px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div
                                style="width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg, var(--accent), var(--accent2)); display:flex; align-items:center; justify-content:center; color:#fff; font-size:18px;">
                                <i class="fas fa-calculator"></i></div>
                            <div><span
                                    style="display:block; font-size:12px; font-weight:600; color:var(--text-sub); text-transform:uppercase; letter-spacing:.5px;">Rata-rata
                                    Skor</span><span id="avgScoreDisplay"
                                    style="display:block; font-family:'DM Mono',monospace; font-size:22px; font-weight:800; color:var(--accent);">0.0/5</span>
                            </div>
                        </div>
                        <div style="display:flex; gap:10px; flex-wrap:wrap;">
                            <a href="{{ route('unit.evaluasi') }}"
                                style="display:inline-flex; align-items:center; gap:8px; padding:10px 20px; background:var(--card-bg, #f1f5f9); color:var(--text-sub); border-radius:10px; text-decoration:none; font-size:13px; font-weight:700; border:1px solid var(--border, rgba(0,0,0,.1));"><i
                                    class="fas fa-arrow-left" style="font-size:11px;"></i> Kembali</a>
                            <button type="submit"
                                style="display:inline-flex; align-items:center; gap:8px; padding:10px 24px; background:linear-gradient(135deg, #4f46e5, #6366f1); color:#fff; border-radius:10px; border:none; cursor:pointer; font-size:13px; font-weight:700; box-shadow:0 4px 14px rgba(79,70,229,.3); transition:all .3s;"
                                onmouseover="this.style.transform='translateY(-1px)'"
                                onmouseout="this.style.transform='none'"><i
                                    class="fas fa-paper-plane" style="font-size:12px;"></i>
                                Kirim ke Pimpinan</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif

    {{-- ═══ READ-ONLY EVALUASI (jika sudah dikirim) ═══ --}}
    @if(($kegiatan->status === 'menunggu_validasi' || $kegiatan->status === 'selesai') && $existingEval)
        <div class="card um-card" style="margin-top: 24px; margin-bottom: 24px;">
            <div class="card-header um-header"
                style="padding:15px 20px; background:linear-gradient(135deg, rgba(16,185,129,0.06), rgba(5,150,105,0.03));">
                <div class="um-title"
                    style="font-weight:700; color:var(--text); display:flex; align-items:center; gap:10px;">
                    <div
                        style="width:32px; height:32px; border-radius:8px; background:rgba(16,185,129,0.12); color:#10b981; display:flex; align-items:center; justify-content:center; font-size:14px;">
                        <i class="fas fa-clipboard-check"></i></div>
                    <div><span style="display:block; font-size:14px;">Hasil Evaluasi Anda</span><span
                            style="display:block; font-size:11px; font-weight:500; color:var(--text-sub);">Evaluasi sudah
                            dikirim ke Pimpinan</span></div>
                </div>
            </div>
            <div class="card-body" style="padding: 24px;">
                @php
                    $readCriteria = [
                        ['name' => 'sesuai_rencana', 'label' => 'Kesesuaian dengan Rencana'],
                        ['name' => 'kualitas', 'label' => 'Kualitas Output'],
                        ['name' => 'keterlibatan', 'label' => 'Keterlibatan Pihak'],
                        ['name' => 'efisiensi', 'label' => 'Efisiensi Sumber Daya'],
                        ['name' => 'kepuasan', 'label' => 'Kepuasan Keseluruhan'],
                    ];
                    $totalScore = ($existingEval->sesuai_rencana + $existingEval->kualitas + $existingEval->keterlibatan + $existingEval->efisiensi + $existingEval->kepuasan);
                    $avgScore = round($totalScore / 5, 1);
                @endphp
                <div
                    style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:20px;">
                    @foreach($readCriteria as $rc)
                        <div
                            style="background:var(--surface); border:1px solid var(--border); border-radius:10px; padding:16px; text-align:center;">
                            <div
                                style="font-size:11px; font-weight:700; color:var(--text-sub); text-transform:uppercase; letter-spacing:.5px; margin-bottom:8px;">
                                {{ $rc['label'] }}</div>
                            <div
                                style="font-family:'DM Mono',monospace; font-size:24px; font-weight:800; color:{{ $existingEval->{$rc['name']} >= 4 ? '#10b981' : ($existingEval->{$rc['name']} >= 3 ? '#f59e0b' : '#ef4444') }};">
                                {{ $existingEval->{$rc['name']} }}/5</div>
                        </div>
                    @endforeach
                </div>
                <div
                    style="background:linear-gradient(135deg, rgba(79,70,229,.06), rgba(99,102,241,.04)); border:1px solid rgba(79,70,229,.15); border-radius:12px; padding:16px 20px; display:flex; align-items:center; justify-content:space-between;">
                    <span style="font-size:13px; font-weight:700; color:var(--text);">Rata-rata Skor</span>
                    <span
                        style="font-family:'DM Mono',monospace; font-size:20px; font-weight:800; color:{{ $avgScore >= 4 ? '#10b981' : ($avgScore >= 3 ? '#f59e0b' : '#ef4444') }};">{{ $avgScore }}/5</span>
                </div>
                @if($existingEval->catatan)
                    <div
                        style="margin-top:16px; background:var(--surface); border:1px solid var(--border); border-radius:10px; padding:16px;">
                        <div class="md-stat-label" style="margin-bottom:6px;">Catatan</div>
                        <div style="font-size:13px; color:var(--text); line-height:1.6; white-space:pre-line;">
                            {{ $existingEval->catatan }}</div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if($kegiatan->status === 'menunggu_validasi')
        <div
            style="margin-bottom:24px; background:linear-gradient(135deg, rgba(139,92,246,.06), rgba(124,58,237,.04)); border:1.5px solid rgba(139,92,246,.2); border-radius:14px; padding:20px 24px; display:flex; align-items:center; gap:14px;">
            <div
                style="width:40px; height:40px; border-radius:10px; background:rgba(139,92,246,.12); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <i class="fas fa-clock" style="color:#8b5cf6; font-size:16px;"></i></div>
            <div>
                <div style="font-weight:800; font-size:14px; color:var(--text); margin-bottom:2px;">Menunggu Validasi
                    Pimpinan</div>
                <div style="font-size:13px; color:var(--text-sub);">Evaluasi telah dikirim dan sedang menunggu validasi dari
                    Pimpinan.</div>
            </div>
        </div>
    @elseif($kegiatan->status === 'selesai')
        <div
            style="margin-bottom:24px; background:linear-gradient(135deg, rgba(16,185,129,.06), rgba(5,150,105,.04)); border:1.5px solid rgba(16,185,129,.2); border-radius:14px; padding:20px 24px; display:flex; align-items:center; gap:14px;">
            <div
                style="width:40px; height:40px; border-radius:10px; background:rgba(16,185,129,.12); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <i class="fas fa-check-double" style="color:#10b981; font-size:16px;"></i></div>
            <div>
                <div style="font-weight:800; font-size:14px; color:var(--text); margin-bottom:2px;">Evaluasi Selesai</div>
                <div style="font-size:13px; color:var(--text-sub);">Pimpinan telah memvalidasi evaluasi ini. Kerjasama telah
                    dinyatakan selesai.</div>
            </div>
        </div>
    @endif

    <div style="margin-bottom: 24px;">
        <a href="{{ route('jurusan.hasil_evaluasi') }}" class="rfc-btn"
            style="background:var(--surface); color:var(--text-sub); border:1px solid var(--border); text-decoration:none; font-size:13px; font-weight:700;"><i
                class="fas fa-arrow-left"></i> Kembali ke Hasil Evaluasi</a>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const criteriaNames = ['sesuai_rencana', 'kualitas', 'keterlibatan', 'efisiensi', 'kepuasan'];
        document.querySelectorAll('.star-rating').forEach(function (ratingGroup) {
            const name = ratingGroup.dataset.name;
            const buttons = ratingGroup.querySelectorAll('.star-btn');
            const hiddenInput = document.getElementById('input-' + name);
            const scoreDisplay = document.getElementById('score-display-' + name);
            buttons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const value = parseInt(this.dataset.value);
                    hiddenInput.value = value;
                    buttons.forEach(function (b, idx) {
                        if (idx < value) { b.classList.add('active'); b.style.color = '#f59e0b'; b.style.transform = 'scale(1.15)'; setTimeout(function () { b.style.transform = 'scale(1)'; }, 150); }
                        else { b.classList.remove('active'); b.style.color = 'rgba(0,0,0,.15)'; }
                    });
                    scoreDisplay.textContent = value + '/5';
                    updateAverage();
                });
                btn.addEventListener('mouseenter', function () {
                    const hv = parseInt(this.dataset.value);
                    buttons.forEach(function (b, idx) { if (idx < hv) { b.style.color = '#fbbf24'; b.style.transform = 'scale(1.1)'; } });
                });
                btn.addEventListener('mouseleave', function () {
                    const cv = parseInt(hiddenInput.value) || 0;
                    buttons.forEach(function (b, idx) { b.style.transform = 'scale(1)'; b.style.color = idx < cv ? '#f59e0b' : 'rgba(0,0,0,.15)'; });
                });
            });
        });
        function updateAverage() {
            let total = 0, count = 0;
            criteriaNames.forEach(function (name) {
                const val = parseInt(document.getElementById('input-' + name)?.value) || 0;
                if (val > 0) { total += val; count++; }
            });
            const avg = count > 0 ? (total / count).toFixed(1) : '0.0';
            const el = document.getElementById('avgScoreDisplay');
            if (el) { el.textContent = avg + '/5'; el.style.color = parseFloat(avg) >= 4 ? '#10b981' : parseFloat(avg) >= 3 ? '#f59e0b' : parseFloat(avg) > 0 ? '#ef4444' : 'var(--accent)'; }
        }
        updateAverage();
    });

    function confirmSubmitEval(event) {
        event.preventDefault();
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Kirim Evaluasi ke Pimpinan?',
                html: '<div style="font-size:14px; color:#64748b; line-height:1.7;">Setelah dikirim, evaluasi tidak dapat diubah dan status akan menjadi <strong>Menunggu Validasi Pimpinan</strong>.</div>',
                icon: 'question', showCancelButton: true,
                confirmButtonText: '<i class="fas fa-paper-plane"></i>&nbsp; Kirim',
                cancelButtonText: '<i class="fas fa-times"></i>&nbsp; Batal',
                confirmButtonColor: '#4f46e5', cancelButtonColor: '#6b7280', reverseButtons: true,
            }).then(function (result) {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Mengirim...', text: 'Sedang mengirim evaluasi ke Pimpinan', allowOutsideClick: false, showConfirmButton: false, didOpen: function() { Swal.showLoading(); } });
                    document.getElementById('formEvaluasi').removeAttribute('onsubmit');
                    document.getElementById('formEvaluasi').submit();
                }
            });
        } else {
            if (confirm('Kirim evaluasi ke Pimpinan? Setelah dikirim, evaluasi tidak dapat diubah.')) {
                document.getElementById('formEvaluasi').removeAttribute('onsubmit');
                document.getElementById('formEvaluasi').submit();
            }
        }
        return false;
    }
</script>