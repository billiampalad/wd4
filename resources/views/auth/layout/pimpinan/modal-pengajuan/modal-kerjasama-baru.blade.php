{{-- MODAL DETAIL: KERJA SAMA BARU --}}
<div id="submissionDetailModalBaru" class="subdetail-modal-overlay" hidden>
    <div class="subdetail-modal" role="dialog" aria-modal="true" aria-labelledby="subdetailTitleBaru">
        <div class="subdetail-modal-header">
            <div class="subdetail-title-group">
                <span id="subdetailCodeBaru" class="submission-card-code">CODE-000</span>
                <span id="subdetailStatusBadgeBaru" class="submission-status pending">Menunggu Review</span>
                <h3 id="subdetailTitleBaru">Judul Pengajuan</h3>
            </div>
            <button type="button" class="subdetail-modal-close" id="subdetailBtnCloseBaru" aria-label="Tutup modal">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <div class="subdetail-modal-body">
            {{-- Detail Grid Identitas Mitra --}}
            <div class="subdetail-grid">
                <div class="subdetail-box">
                    <span class="subdetail-label"><i class="fas fa-building"></i> Nama Mitra</span>
                    <strong id="subdetailMitraNameBaru" class="subdetail-value">—</strong>
                </div>
                <div class="subdetail-box">
                    <span class="subdetail-label"><i class="fas fa-layer-group"></i> Klasifikasi &amp; Kategori</span>
                    <span id="subdetailKlasifikasiBaru" class="subdetail-value">—</span>
                </div>
                <div class="subdetail-box">
                    <span class="subdetail-label"><i class="fas fa-globe"></i> Negara</span>
                    <span id="subdetailNegaraBaru" class="subdetail-value">—</span>
                </div>
                <div class="subdetail-box subdetail-box-full">
                    <span class="subdetail-label"><i class="fas fa-map-marker-alt"></i> Alamat Mitra</span>
                    <span id="subdetailAlamatBaru" class="subdetail-value">—</span>
                </div>
                <div class="subdetail-box subdetail-box-full">
                    <span class="subdetail-label"><i class="fas fa-link"></i> Website</span>
                    <span id="subdetailWebsiteBaru" class="subdetail-value">—</span>
                </div>
            </div>

            {{-- Penandatangan & PJ --}}
            <div class="subdetail-grid">
                <div class="subdetail-box">
                    <span class="subdetail-label"><i class="fas fa-user-pen"></i> Penandatangan</span>
                    <span id="subdetailPenandatanganBaru" class="subdetail-value">—</span>
                </div>
                <div class="subdetail-box">
                    <span class="subdetail-label"><i class="fas fa-user-tie"></i> Penanggung Jawab</span>
                    <span id="subdetailPjBaru" class="subdetail-value">—</span>
                </div>
                <div class="subdetail-box subdetail-box-full">
                    <span class="subdetail-label"><i class="fas fa-address-book"></i> Kontak Pengaju (Email / WA)</span>
                    <span id="subdetailKontakBaru" class="subdetail-value">—</span>
                </div>
            </div>

            {{-- Tujuan & Ruang Lingkup --}}
            <div class="subdetail-section">
                <span class="subdetail-label"><i class="fas fa-bullseye"></i> Tujuan Pengajuan</span>
                <p id="subdetailTujuanBaru" class="subdetail-text-content">—</p>
            </div>

            <div class="subdetail-section">
                <span class="subdetail-label"><i class="fas fa-list-check"></i> Ruang Lingkup</span>
                <p id="subdetailRuangLingkupBaru" class="subdetail-text-content">—</p>
            </div>

            <div id="subdetailPesanWrapperBaru" class="subdetail-note-box" hidden>
                <strong><i class="fas fa-comment-dots"></i> Catatan dari Mitra</strong>
                <p id="subdetailPesanTambahanBaru" class="margin-0"></p>
            </div>

            <div id="subdetailHistoryNoteWrapperBaru" class="subdetail-note-box is-history" hidden>
                <strong><i class="fas fa-sticky-note"></i> Catatan Pimpinan</strong>
                <p id="subdetailHistoryNoteBaru" class="margin-0"></p>
            </div>

            {{-- Textarea Catatan Validasi Pimpinan (hanya untuk antrean aktif) --}}
            <div id="subdetailFormBlockBaru" class="subdetail-form-block">
                <div class="submission-form-head">
                    <label for="subdetailCatatanTextareaBaru">Catatan Validasi Pimpinan</label>
                    <span class="submission-counter" id="subdetailCounterBaru">0 karakter</span>
                </div>
                <textarea id="subdetailCatatanTextareaBaru" class="submission-textarea" rows="3"
                    placeholder="Tambahkan catatan validasi. Wajib diisi jika pengajuan ditolak."></textarea>
            </div>
        </div>

        <div class="subdetail-modal-footer">
            <button type="button" class="notif-btn-cancel" id="subdetailBtnCancelBaru">
                <i class="fas fa-xmark"></i> Tutup
            </button>

            <div id="subdetailActiveActionsBaru" class="subdetail-footer-actions">
                <button type="button" class="ev-btn-reject" id="subdetailBtnRejectBaru">
                    <i class="fas fa-ban"></i> Tolak
                </button>
                <button type="button" class="ev-btn-approve" id="subdetailBtnApproveBaru">
                    <i class="fas fa-circle-check"></i> Setujui
                </button>
            </div>
        </div>
    </div>
</div>
