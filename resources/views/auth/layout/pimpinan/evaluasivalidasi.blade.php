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
                                    <button class="rfc-btn" onclick="openEvaluateModal('{{ $kegiatan->id }}', 'jurusan')" style="font-size: 11px; padding: 6px 14px;">
                                        <i class="fas fa-star"></i> Beri Penilaian
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="um-empty">Tidak ada antrean evaluasi jurusan.</td></tr>
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
                                    <button class="rfc-btn" onclick="openEvaluateModal('{{ $kegiatan->id }}', 'unit')" style="font-size: 11px; padding: 6px 14px;">
                                        <i class="fas fa-check"></i> Validasi Laporan
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="um-empty">Tidak ada antrean validasi unit kerja.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Penilaian --}}
    <div id="evaluateModal" class="modal-overlay-custom" style="display: none;">
        <div class="modal-content-custom">
            <div class="modal-header-custom">
                <h3 id="modalTitle">Form Penilaian Pimpinan</h3>
                <button onclick="closeModal()" class="close-btn-custom">&times;</button>
            </div>

            <form id="evaluateForm" method="POST">
                @csrf
                <div id="jurusanFields" style="display: none;">
                    <h4 class="section-title">Skor Kinerja (1-5)</h4>
                    <div class="grid-fields">
                        <div class="field-group">
                            <label>Kesesuaian Rencana</label>
                            <input type="number" name="sesuai_rencana" min="1" max="5" class="search-bar">
                        </div>
                        <div class="field-group">
                            <label>Kualitas Pelaksanaan</label>
                            <input type="number" name="kualitas" min="1" max="5" class="search-bar">
                        </div>
                        <div class="field-group">
                            <label>Keterlibatan Mitra</label>
                            <input type="number" name="keterlibatan" min="1" max="5" class="search-bar">
                        </div>
                        <div class="field-group">
                            <label>Efisiensi Sumber Daya</label>
                            <input type="number" name="efisiensi" min="1" max="5" class="search-bar">
                        </div>
                        <div class="field-group full-width">
                            <label>Kepuasan Pihak Terkait</label>
                            <input type="number" name="kepuasan" min="1" max="5" class="search-bar">
                        </div>
                    </div>
                </div>

                <div id="unitFields" style="display: none;" class="info-box">
                    <div class="info-content">
                        <i class="fas fa-info-circle"></i>
                        <span>Skor evaluasi internal telah diisi oleh Unit Kerja secara mandiri.</span>
                    </div>
                </div>

                <div class="field-group">
                    <label>Ringkasan Evaluasi (Teks - Opsional)</label>
                    <textarea name="ringkasan" class="search-bar text-area" placeholder="Berikan ringkasan capaian (Opsional)..."></textarea>
                </div>

                <div class="field-group">
                    <label>Saran Tindak Lanjut (Teks - Opsional)</label>
                    <textarea name="saran" class="search-bar text-area" placeholder="Apa yang perlu diperbaiki ke depannya (Opsional)?"></textarea>
                </div>

                <div class="field-group">
                    <label>Catatan Tambahan (Opsional)</label>
                    <textarea name="tindak_lanjut" class="search-bar text-area" style="height: 60px;" placeholder="Catatan untuk tindak lanjut..."></textarea>
                </div>

                <div class="field-group">
                    <label>Status Validasi Akhir</label>
                    <select name="status_validasi" class="search-bar select-input">
                        <option value="layak">Layak / Disetujui</option>
                        <option value="tidak_layak">Tidak Layak / Perlu Revisi</option>
                    </select>
                </div>

                <div class="modal-footer-custom">
                    <button type="button" onclick="closeModal()" class="rfc-btn btn-cancel">Batal</button>
                    <button type="submit" class="rfc-btn">Simpan Penilaian</button>
                </div>
            </form>
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
