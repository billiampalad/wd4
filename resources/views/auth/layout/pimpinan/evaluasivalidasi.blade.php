<main id="mainContent" x-data="{ activeTab: 'jurusan' }">
    <div class="page-header">
        <div class="breadcrumb">
            <i class="fas fa-home" style="font-size:11px;"></i>
            <span class="sep">/</span>
            <span class="current">Evaluasi & Validasi</span>
        </div>
        <h2 id="pageTitle">Evaluasi & Validasi Laporan</h2>
        <p id="pageDesc">Berikan penilaian untuk laporan Jurusan dan validasi hasil evaluasi Unit Kerja.</p>
    </div>

    {{-- Tabs --}}
    <div class="card-tabs" style="display: flex; gap: 4px; margin-bottom: 20px;">
        <button class="md-tab-btn" :class="{ 'active': activeTab === 'jurusan' }" @click="activeTab = 'jurusan'">
            <i class="fas fa-university"></i> Evaluasi Jurusan
            @if($laporanJurusan->count() > 0)
                <span class="badge-count">{{ $laporanJurusan->count() }}</span>
            @endif
        </button>
        <button class="md-tab-btn" :class="{ 'active': activeTab === 'unit' }" @click="activeTab = 'unit'">
            <i class="fas fa-building"></i> Validasi Unit Kerja
            @if($laporanUnit->count() > 0)
                <span class="badge-count">{{ $laporanUnit->count() }}</span>
            @endif
        </button>
    </div>

    {{-- Tab content: Jurusan --}}
    <div x-show="activeTab === 'jurusan'">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-clipboard-list"></i> Antrean Evaluasi Jurusan</div>
            </div>
            <div class="table-wrap">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Jurusan</th>
                            <th>Nama Kegiatan</th>
                            <th>Mitra</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporanJurusan as $index => $kegiatan)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $kegiatan->jurusans->first()->nama_jurusan ?? '-' }}</strong></td>
                                <td>{{ $kegiatan->nama_kegiatan }}</td>
                                <td>
                                    @foreach($kegiatan->mitras as $mitra)
                                        <span class="tag" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9; font-size: 10px;">{{ $mitra->nama_mitra }}</span>
                                    @endforeach
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('pimpinan.evaluasi.show', $kegiatan->id) }}" class="rfc-btn" style="font-size: 11px; padding: 6px 14px;">
                                        <i class="fas fa-star"></i> Beri Penilaian
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="um-empty">
                                    <div class="empty-state-container">
                                        <div class="empty-state-icon">
                                            <i class="fas fa-clipboard-check"></i>
                                        </div>
                                        <h4>Semua Laporan Telah Dievaluasi</h4>
                                        <p>Saat ini tidak ada antrean laporan dari jurusan yang perlu Anda nilai.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Tab content: Unit Kerja --}}
    <div x-show="activeTab === 'unit'">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-check-double"></i> Antrean Validasi Unit Kerja</div>
            </div>
            <div class="table-wrap">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Unit Kerja</th>
                            <th>Nama Kegiatan</th>
                            <th style="text-align: center;">Skor Internal</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporanUnit as $index => $kegiatan)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $kegiatan->unitKerjas->first()->nama_unit_pelaksana ?? '-' }}</strong></td>
                                <td>{{ $kegiatan->nama_kegiatan }}</td>
                                <td style="text-align: center;">
                                    @php
                                        $eval = $kegiatan->evaluasis->first();
                                        $avg = $eval ? ($eval->sesuai_rencana + $eval->kualitas + $eval->keterlibatan + $eval->efisiensi + $eval->kepuasan) / 5 : 0;
                                    @endphp
                                    <span class="tag tag-green" style="font-weight: 800;">{{ number_format($avg, 1) }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('pimpinan.evaluasi.show', $kegiatan->id) }}" class="rfc-btn" style="font-size: 11px; padding: 6px 14px;">
                                        <i class="fas fa-check"></i> Validasi Laporan
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="um-empty">
                                    <div class="empty-state-container">
                                        <div class="empty-state-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                            <i class="fas fa-check-double"></i>
                                        </div>
                                        <h4>Pekerjaan Selesai!</h4>
                                        <p>Semua laporan dari unit kerja telah divalidasi dengan sukses.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</main>

<style>
    .md-tab-btn {
        padding: 12px 24px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 10px;
        color: var(--text-sub);
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .md-tab-btn.active {
        background: var(--accent);
        color: white;
        border-color: var(--accent);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
    }
    .badge-count {
        background: var(--danger);
        color: white;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 800;
    }
    .modal-overlay-custom {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .modal-content-custom {
        background: var(--surface);
        width: 100%;
        max-width: 650px;
        border-radius: 18px;
        padding: 28px;
        box-shadow: var(--shadow-lg);
        max-height: 90vh;
        overflow-y: auto;
    }
    .modal-header-custom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .modal-header-custom h3 { font-weight: 800; color: var(--text); font-size: 18px; }
    .close-btn-custom { background: none; border: none; font-size: 24px; color: var(--text-sub); cursor: pointer; }
    .section-title { font-size: 12px; font-weight: 800; color: var(--text-sub); margin-bottom: 14px; text-transform: uppercase; letter-spacing: 1px; }
    .grid-fields { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
    .field-group { margin-bottom: 18px; }
    .field-group label { display: block; font-size: 12px; font-weight: 700; margin-bottom: 8px; color: var(--text); }
    .full-width { grid-column: span 2; }
    .info-box { background: rgba(16, 185, 129, 0.08); padding: 16px; border-radius: 12px; border: 1px solid rgba(16, 185, 129, 0.2); margin-bottom: 24px; }
    .info-content { display: flex; align-items: center; gap: 12px; color: #059669; font-size: 13px; font-weight: 600; }
    .text-area { width: 100%; padding: 12px; height: 100px; font-family: inherit; resize: vertical; border-radius: 10px; }
    .select-input { width: 100%; padding: 12px; border-radius: 10px; cursor: pointer; }
    .modal-footer-custom { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--border); }
    .btn-cancel { background: var(--surface2); color: var(--text-sub); }

    /* Empty State Styling */
    .um-empty {
        padding: 60px 20px !important;
        background: var(--surface);
    }
    .empty-state-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    .empty-state-icon {
        width: 64px;
        height: 64px;
        background: rgba(79, 70, 229, 0.1);
        color: var(--accent);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 16px;
        animation: float 3s ease-in-out infinite;
    }
    .empty-state-container h4 {
        font-size: 16px;
        font-weight: 800;
        color: var(--text);
        margin-bottom: 8px;
    }
    .empty-state-container p {
        font-size: 13px;
        color: var(--text-sub);
        max-width: 300px;
        margin: 0 auto;
        line-height: 1.5;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
</style>

<script>
    function openEvaluateModal(id, type) {
        const modal = document.getElementById('evaluateModal');
        const form = document.getElementById('evaluateForm');
        const title = document.getElementById('modalTitle');
        const jurusanFields = document.getElementById('jurusanFields');
        const unitFields = document.getElementById('unitFields');

        form.action = `/pimpinan/evaluate/${id}`;
        modal.style.display = 'flex';

        if (type === 'jurusan') {
            title.innerText = 'Evaluasi Laporan Jurusan';
            jurusanFields.style.display = 'block';
            unitFields.style.display = 'none';
            form.querySelectorAll('#jurusanFields input').forEach(el => el.required = true);
        } else {
            title.innerText = 'Validasi Laporan Unit Kerja';
            jurusanFields.style.display = 'none';
            unitFields.style.display = 'block';
            form.querySelectorAll('#jurusanFields input').forEach(el => el.required = false);
        }
    }

    function closeModal() {
        document.getElementById('evaluateModal').style.display = 'none';
    }
</script>
