<!-- Main Content -->
<main id="mainContent">
    <!-- Page Header -->
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-home" style="font-size:11px;"></i>
            <span class="sep">/</span>
            <a href="{{ route('unit.evaluasi') }}" style="text-decoration:none; color:var(--accent); font-weight:600;">Evaluasi Kinerja</a>
            <span class="sep">/</span>
            <span class="current">{{ $existingEval ? 'Edit Evaluasi' : 'Beri Evaluasi' }}</span>
        </div>
        <h2 id="pageTitle">{{ $existingEval ? 'Edit Evaluasi Kinerja' : 'Beri Evaluasi Kinerja' }}</h2>
        <p id="pageDesc">Nilai kinerja kerjasama untuk kegiatan <strong>{{ $kegiatan->nama_kegiatan }}</strong>.</p>
    </div>

    @if($errors->any())
        <div style="background: linear-gradient(135deg, rgba(239,68,68,.12), rgba(220,38,38,.08)); border: 1px solid rgba(239,68,68,.3); color: #991b1b; padding: 14px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600;">
            <i class="fas fa-exclamation-circle" style="margin-right:8px; color:#ef4444;"></i>
            Mohon lengkapi semua penilaian sebelum menyimpan.
        </div>
    @endif

    <!-- ═══════════════════════════════════════════
         INFO KEGIATAN
    ═══════════════════════════════════════════ -->
    <div class="card um-card" style="margin-bottom: 24px;">
        <div class="card-header um-header"
            style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px;
                   background: linear-gradient(135deg, rgba(79,70,229,0.06), rgba(99,102,241,0.03));">
            <div class="um-title"
                style="font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 10px;">
                <div style="width:32px; height:32px; border-radius:8px; background:rgba(79,70,229,0.12); color:#4f46e5;
                            display:flex; align-items:center; justify-content:center; font-size:14px;">
                    <i class="fas fa-info-circle"></i>
                </div>
                <span style="font-size:14px;">Informasi Kegiatan</span>
            </div>
        </div>
        <div class="card-body" style="padding: 20px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                <div>
                    <label style="display:block; font-size:11px; font-weight:700; color:var(--text-sub); text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">
                        Nama Kegiatan
                    </label>
                    <p style="font-size:14px; font-weight:600; color:var(--text); margin:0;">{{ $kegiatan->nama_kegiatan }}</p>
                </div>
                <div>
                    <label style="display:block; font-size:11px; font-weight:700; color:var(--text-sub); text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">
                        Mitra
                    </label>
                    <p style="font-size:14px; color:var(--text); margin:0;">{{ $kegiatan->mitras->pluck('nama_mitra')->join(', ') ?: '-' }}</p>
                </div>
                <div>
                    <label style="display:block; font-size:11px; font-weight:700; color:var(--text-sub); text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">
                        Jenis Kerjasama
                    </label>
                    <p style="font-size:14px; color:var(--text); margin:0;">
                        <span class="tag tag-purple" style="font-size: 11px;">
                            <i class="fas fa-handshake" style="font-size:9px; margin-right:4px;"></i>
                            {{ $kegiatan->jenisKerjasama->pluck('nama_kerjasama')->join(', ') ?: '-' }}
                        </span>
                    </p>
                </div>
                <div>
                    <label style="display:block; font-size:11px; font-weight:700; color:var(--text-sub); text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">
                        Periode
                    </label>
                    <p style="font-size:14px; color:var(--text); margin:0;">
                        {{ $kegiatan->periode_mulai ? $kegiatan->periode_mulai->format('d M Y') : '-' }}
                        s/d
                        {{ $kegiatan->periode_selesai ? $kegiatan->periode_selesai->format('d M Y') : '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════
         FORM EVALUASI
    ═══════════════════════════════════════════ -->
    <form
        action="{{ $existingEval ? route('unit.evaluasi.update', $kegiatan->id) : route('unit.evaluasi.store', $kegiatan->id) }}"
        method="POST" id="formEvaluasi">
        @csrf
        @if($existingEval)
            @method('PUT')
        @endif

        <div class="card um-card" style="margin-bottom: 24px;">
            <div class="card-header um-header"
                style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px;
                       background: linear-gradient(135deg, rgba(245,158,11,0.06), rgba(217,119,6,0.03));">
                <div class="um-title"
                    style="font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 10px;">
                    <div style="width:32px; height:32px; border-radius:8px; background:rgba(245,158,11,0.12); color:#d97706;
                                display:flex; align-items:center; justify-content:center; font-size:14px;">
                        <i class="fas fa-star"></i>
                    </div>
                    <div>
                        <span style="display:block; font-size:14px;">Penilaian Kinerja</span>
                        <span style="display:block; font-size:11px; font-weight:500; color:var(--text-sub);">Klik bintang untuk memberi skor (1-5)</span>
                    </div>
                </div>
            </div>
            <div class="card-body" style="padding: 24px;">
                <div style="display: flex; flex-direction: column; gap: 24px;">

                    @php
                        $criteria = [
                            ['name' => 'sesuai_rencana', 'label' => 'Kesesuaian dengan Rencana', 'icon' => 'fa-bullseye', 'desc' => 'Apakah pelaksanaan kegiatan sesuai dengan rencana kerjasama?'],
                            ['name' => 'kualitas', 'label' => 'Kualitas Output', 'icon' => 'fa-gem', 'desc' => 'Bagaimana kualitas hasil/output yang dihasilkan dari kerjasama?'],
                            ['name' => 'keterlibatan', 'label' => 'Keterlibatan Pihak', 'icon' => 'fa-users', 'desc' => 'Seberapa aktif keterlibatan semua pihak dalam kerjasama?'],
                            ['name' => 'efisiensi', 'label' => 'Efisiensi Sumber Daya', 'icon' => 'fa-chart-line', 'desc' => 'Apakah penggunaan waktu, biaya, dan sumber daya efisien?'],
                            ['name' => 'kepuasan', 'label' => 'Kepuasan Keseluruhan', 'icon' => 'fa-face-smile', 'desc' => 'Tingkat kepuasan keseluruhan terhadap kerjasama ini.'],
                        ];
                    @endphp

                    @foreach($criteria as $c)
                        <div class="eval-criteria-row"
                             style="display:flex; align-items:flex-start; gap:16px; padding:20px; border-radius:12px;
                                    background: var(--card-bg, #f8fafc); border: 1px solid var(--border, rgba(0,0,0,.06));
                                    transition: all .2s;">
                            <div style="flex-shrink:0; width:40px; height:40px; border-radius:10px;
                                        background:linear-gradient(135deg, rgba(79,70,229,.1), rgba(99,102,241,.06));
                                        display:flex; align-items:center; justify-content:center; color:#4f46e5; font-size:16px;">
                                <i class="fas {{ $c['icon'] }}"></i>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; margin-bottom:6px;">
                                    <div>
                                        <h4 style="margin:0; font-size:14px; font-weight:700; color:var(--text);">{{ $c['label'] }}</h4>
                                        <p style="margin:2px 0 0; font-size:12px; color:var(--text-sub);">{{ $c['desc'] }}</p>
                                    </div>
                                    <div class="star-display"
                                         style="font-family:'DM Mono',monospace; font-size:13px; font-weight:700; color:var(--text-sub);
                                                padding:4px 10px; border-radius:6px; background:rgba(79,70,229,.08);"
                                         id="score-display-{{ $c['name'] }}">
                                        {{ old($c['name'], $existingEval?->{$c['name']} ?? 0) }}/5
                                    </div>
                                </div>
                                <div class="star-rating" data-name="{{ $c['name'] }}" style="display:flex; gap:4px; margin-top:8px;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button"
                                            class="star-btn {{ $i <= old($c['name'], $existingEval?->{$c['name']} ?? 0) ? 'active' : '' }}"
                                            data-value="{{ $i }}"
                                            style="background:none; border:none; cursor:pointer; padding:4px 6px; font-size:22px;
                                                   color: {{ $i <= old($c['name'], $existingEval?->{$c['name']} ?? 0) ? '#f59e0b' : 'rgba(0,0,0,.15)' }};
                                                   transition: all .15s; transform-origin:center;">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    @endfor
                                    <input type="hidden" name="{{ $c['name'] }}" id="input-{{ $c['name'] }}"
                                           value="{{ old($c['name'], $existingEval?->{$c['name']} ?? '') }}">
                                </div>
                                @error($c['name'])
                                    <p style="color:#ef4444; font-size:12px; margin:6px 0 0; font-weight:600;">
                                        <i class="fas fa-circle-exclamation" style="font-size:10px;"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>

        <!-- Catatan -->
        <div class="card um-card" style="margin-bottom: 24px;">
            <div class="card-header um-header"
                style="display: flex; align-items: center; padding: 15px 20px;">
                <div class="um-title"
                    style="font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 10px;">
                    <div style="width:32px; height:32px; border-radius:8px; background:rgba(16,185,129,0.1); color:#10b981;
                                display:flex; align-items:center; justify-content:center; font-size:14px;">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <div>
                        <span style="display:block; font-size:14px;">Catatan Tambahan</span>
                        <span style="display:block; font-size:11px; font-weight:500; color:var(--text-sub);">Opsional — berikan catatan atau komentar evaluasi</span>
                    </div>
                </div>
            </div>
            <div class="card-body" style="padding: 20px;">
                <textarea name="catatan" id="catatan" rows="4"
                    placeholder="Tulis catatan evaluasi Anda di sini... (misal: saran perbaikan, hal positif, kendala, dll.)"
                    style="width:100%; padding:14px 16px; border-radius:10px; border:1px solid var(--border, rgba(0,0,0,.1));
                           background:var(--card-bg, #fff); color:var(--text); font-size:13px; font-family:'Plus Jakarta Sans',sans-serif;
                           resize:vertical; min-height:100px; transition:border .2s, box-shadow .2s; outline:none; box-sizing:border-box;"
                    onfocus="this.style.borderColor='var(--accent)'; this.style.boxShadow='0 0 0 3px rgba(79,70,229,.1)'"
                    onblur="this.style.borderColor='var(--border, rgba(0,0,0,.1))'; this.style.boxShadow='none'"
                >{{ old('catatan', $existingEval?->catatan ?? '') }}</textarea>
            </div>
        </div>

        <!-- Summary & Action Buttons -->
        <div class="card um-card" style="margin-bottom: 24px;">
            <div class="card-body" style="padding: 20px;">
                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div style="width:44px; height:44px; border-radius:12px;
                                    background:linear-gradient(135deg, var(--accent), var(--accent2));
                                    display:flex; align-items:center; justify-content:center; color:#fff; font-size:18px;">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <div>
                            <span style="display:block; font-size:12px; font-weight:600; color:var(--text-sub); text-transform:uppercase; letter-spacing:.5px;">
                                Rata-rata Skor
                            </span>
                            <span id="avgScoreDisplay"
                                  style="display:block; font-family:'DM Mono',monospace; font-size:22px; font-weight:800; color:var(--accent);">
                                0.0/5
                            </span>
                        </div>
                    </div>
                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                        <a href="{{ route('unit.evaluasi') }}"
                            style="display:inline-flex; align-items:center; gap:8px; padding:10px 20px;
                                   background:var(--card-bg, #f1f5f9); color:var(--text-sub); border-radius:10px;
                                   text-decoration:none; font-size:13px; font-weight:700; border:1px solid var(--border, rgba(0,0,0,.1));
                                   transition:all .2s;"
                            onmouseover="this.style.background='rgba(239,68,68,.08)'; this.style.color='#ef4444'; this.style.borderColor='rgba(239,68,68,.3)'"
                            onmouseout="this.style.background='var(--card-bg, #f1f5f9)'; this.style.color='var(--text-sub)'; this.style.borderColor='var(--border, rgba(0,0,0,.1))'">
                            <i class="fas fa-arrow-left" style="font-size:11px;"></i>
                            Kembali
                        </a>
                        <button type="submit"
                            style="display:inline-flex; align-items:center; gap:8px; padding:10px 24px;
                                   background:linear-gradient(135deg, var(--accent), var(--accent2)); color:#fff;
                                   border-radius:10px; border:none; cursor:pointer; font-size:13px; font-weight:700;
                                   box-shadow:0 4px 12px rgba(79,70,229,.3); transition:all .2s;"
                            onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 16px rgba(79,70,229,.4)'"
                            onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 12px rgba(79,70,229,.3)'">
                            <i class="fas fa-save" style="font-size:12px;"></i>
                            {{ $existingEval ? 'Perbarui Evaluasi' : 'Simpan Evaluasi' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
                    if (idx < value) {
                        b.classList.add('active');
                        b.style.color = '#f59e0b';
                        b.style.transform = 'scale(1.15)';
                        setTimeout(function() { b.style.transform = 'scale(1)'; }, 150);
                    } else {
                        b.classList.remove('active');
                        b.style.color = 'rgba(0,0,0,.15)';
                    }
                });

                scoreDisplay.textContent = value + '/5';
                updateAverage();
            });

            btn.addEventListener('mouseenter', function () {
                const hoverValue = parseInt(this.dataset.value);
                buttons.forEach(function (b, idx) {
                    if (idx < hoverValue) {
                        b.style.color = '#fbbf24';
                        b.style.transform = 'scale(1.1)';
                    }
                });
            });

            btn.addEventListener('mouseleave', function () {
                const currentValue = parseInt(hiddenInput.value) || 0;
                buttons.forEach(function (b, idx) {
                    b.style.transform = 'scale(1)';
                    if (idx < currentValue) {
                        b.style.color = '#f59e0b';
                    } else {
                        b.style.color = 'rgba(0,0,0,.15)';
                    }
                });
            });
        });
    });

    function updateAverage() {
        let total = 0;
        let count = 0;
        criteriaNames.forEach(function (name) {
            const val = parseInt(document.getElementById('input-' + name).value) || 0;
            if (val > 0) {
                total += val;
                count++;
            }
        });
        const avg = count > 0 ? (total / count).toFixed(1) : '0.0';
        const avgEl = document.getElementById('avgScoreDisplay');
        avgEl.textContent = avg + '/5';

        // Color coding
        const avgNum = parseFloat(avg);
        if (avgNum >= 4) {
            avgEl.style.color = '#10b981';
        } else if (avgNum >= 3) {
            avgEl.style.color = '#f59e0b';
        } else if (avgNum > 0) {
            avgEl.style.color = '#ef4444';
        } else {
            avgEl.style.color = 'var(--accent)';
        }
    }

    // Initial calculation for edit mode
    updateAverage();
});
</script>
