{{-- MODAL DETAIL: PERPANJANGAN KERJA SAMA --}}
<div id="submissionDetailModalPerpanjangan" class="subdetail-modal-overlay" hidden>
    <div class="subdetail-modal" role="dialog" aria-modal="true" aria-labelledby="subdetailTitlePerpanjangan">
        <div class="subdetail-modal-header">
            <div class="subdetail-title-group">
                <span id="subdetailCodePerpanjangan" class="submission-card-code">CODE-000</span>
                <span id="subdetailStatusBadgePerpanjangan" class="submission-status pending">Menunggu Review</span>
                <h3 id="subdetailTitlePerpanjangan">Judul Pengajuan</h3>
            </div>
            <button type="button" class="subdetail-modal-close" id="subdetailBtnClosePerpanjangan" aria-label="Tutup modal">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <div class="subdetail-modal-body">
            {{-- Banner Informasi Khusus Perpanjangan --}}
            <div id="subdetailPerpanjanganBanner" class="subdetail-perpanjangan-banner">
                <div class="perpanjangan-banner-icon">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <div class="perpanjangan-banner-info">
                    <strong>Pengajuan Perpanjangan Kerja Sama</strong>
                    <span>Pengajuan ini memperpanjang dokumen kerja sama yang sudah terdaftar dalam sistem.</span>
                </div>
            </div>

            {{-- Detail Grid Dokumen Lama & Periode Baru (Perpanjangan) --}}
            <div id="subdetailPerpanjanganGrid" class="subdetail-grid">
                <div class="subdetail-box">
                    <span class="subdetail-label"><i class="fas fa-file-contract"></i> Jenis Dokumen Lama</span>
                    <strong id="subdetailJenisDokumen" class="subdetail-value">—</strong>
                </div>
                <div class="subdetail-box">
                    <span class="subdetail-label"><i class="fas fa-hashtag"></i> Nomor Dokumen Lama</span>
                    <strong id="subdetailDocNumber" class="subdetail-value">—</strong>
                </div>
                <div class="subdetail-box subdetail-box-full">
                    <span class="subdetail-label"><i class="fas fa-calendar-range"></i> Rencana Periode Kerja Sama Baru</span>
                    <span id="subdetailPeriode" class="subdetail-value">—</span>
                </div>
            </div>

            {{-- Detail Grid Identitas Mitra --}}
            <div class="subdetail-grid">
                <div class="subdetail-box">
                    <span class="subdetail-label"><i class="fas fa-building"></i> Nama Mitra</span>
                    <strong id="subdetailMitraNamePerpanjangan" class="subdetail-value">—</strong>
                </div>
                <div class="subdetail-box">
                    <span class="subdetail-label"><i class="fas fa-layer-group"></i> Klasifikasi &amp; Kategori</span>
                    <span id="subdetailKlasifikasiPerpanjangan" class="subdetail-value">—</span>
                </div>
                <div class="subdetail-box">
                    <span class="subdetail-label"><i class="fas fa-globe"></i> Negara</span>
                    <span id="subdetailNegaraPerpanjangan" class="subdetail-value">—</span>
                </div>
                <div class="subdetail-box subdetail-box-full">
                    <span class="subdetail-label"><i class="fas fa-map-marker-alt"></i> Alamat Mitra</span>
                    <span id="subdetailAlamatPerpanjangan" class="subdetail-value">—</span>
                </div>
                <div class="subdetail-box subdetail-box-full">
                    <span class="subdetail-label"><i class="fas fa-link"></i> Website</span>
                    <span id="subdetailWebsitePerpanjangan" class="subdetail-value">—</span>
                </div>
            </div>

            {{-- Penandatangan & PJ --}}
            <div class="subdetail-grid">
                <div class="subdetail-box">
                    <span class="subdetail-label"><i class="fas fa-user-pen"></i> Penandatangan</span>
                    <span id="subdetailPenandatanganPerpanjangan" class="subdetail-value">—</span>
                </div>
                <div class="subdetail-box">
                    <span class="subdetail-label"><i class="fas fa-user-tie"></i> Penanggung Jawab</span>
                    <span id="subdetailPjPerpanjangan" class="subdetail-value">—</span>
                </div>
                <div class="subdetail-box subdetail-box-full">
                    <span class="subdetail-label"><i class="fas fa-address-book"></i> Kontak Pengaju (Email / WA)</span>
                    <span id="subdetailKontakPerpanjangan" class="subdetail-value">—</span>
                </div>
            </div>

            {{-- Tujuan & Ruang Lingkup --}}
            <div class="subdetail-section">
                <span class="subdetail-label"><i class="fas fa-bullseye"></i> Tujuan Pengajuan</span>
                <p id="subdetailTujuanPerpanjangan" class="subdetail-text-content">—</p>
            </div>

            <div class="subdetail-section">
                <span class="subdetail-label"><i class="fas fa-list-check"></i> Ruang Lingkup</span>
                <p id="subdetailRuangLingkupPerpanjangan" class="subdetail-text-content">—</p>
            </div>

            {{-- Berkas Surat Permohonan Perpanjangan --}}
            <div id="subdetailFileSuratWrapper" class="subdetail-section">
                <span class="subdetail-label"><i class="fas fa-file-pdf"></i> Surat Permohonan Perpanjangan</span>
                <div class="subdetail-file-download">
                    <a id="subdetailFileSuratLink" href="#" target="_blank" rel="noopener noreferrer" class="subdetail-file-btn">
                        <i class="fas fa-file-arrow-down"></i>
                        <span>Unduh / Lihat Surat Permohonan (.pdf)</span>
                    </a>
                </div>
            </div>

            <div id="subdetailPesanWrapperPerpanjangan" class="subdetail-note-box" hidden>
                <strong><i class="fas fa-comment-dots"></i> Catatan dari Mitra</strong>
                <p id="subdetailPesanTambahanPerpanjangan" class="margin-0"></p>
            </div>

            <div id="subdetailHistoryNoteWrapperPerpanjangan" class="subdetail-note-box is-history" hidden>
                <strong><i class="fas fa-sticky-note"></i> Catatan Pimpinan</strong>
                <p id="subdetailHistoryNotePerpanjangan" class="margin-0"></p>
            </div>

            {{-- Textarea Catatan Validasi Pimpinan (hanya untuk antrean aktif) --}}
            <div id="subdetailFormBlockPerpanjangan" class="subdetail-form-block">
                <div class="submission-form-head">
                    <label for="subdetailCatatanTextareaPerpanjangan">Catatan Validasi Pimpinan</label>
                    <span class="submission-counter" id="subdetailCounterPerpanjangan">0 karakter</span>
                </div>
                <textarea id="subdetailCatatanTextareaPerpanjangan" class="submission-textarea" rows="3"
                    placeholder="Tambahkan catatan validasi. Wajib diisi jika pengajuan ditolak."></textarea>
            </div>
        </div>

        <div class="subdetail-modal-footer">
            <button type="button" class="notif-btn-cancel" id="subdetailBtnCancelPerpanjangan">
                <i class="fas fa-xmark"></i> Tutup
            </button>

            <div id="subdetailActiveActionsPerpanjangan" class="subdetail-footer-actions">
                <button type="button" class="ev-btn-reject" id="subdetailBtnRejectPerpanjangan">
                    <i class="fas fa-ban"></i> Tolak
                </button>
                <button type="button" class="ev-btn-approve" id="subdetailBtnApprovePerpanjangan">
                    <i class="fas fa-circle-check"></i> Setujui
                </button>
            </div>
        </div>
    </div>
</div>
