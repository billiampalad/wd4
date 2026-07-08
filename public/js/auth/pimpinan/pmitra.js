(function () {
    const boot = () => {
        const root = document.querySelector('.submission-dashboard');
        if (!root || root.dataset.pmitraReady === 'true') return;
        root.dataset.pmitraReady = 'true';

        const rows = Array.from(document.querySelectorAll('[data-submission-row]'));
        const categoryFilter = document.getElementById('submissionCategoryFilter');
        const emptyState = document.querySelector('.submission-filter-empty');

        /* ============================
         * Category Filter for Table Rows
         * ============================ */
        const applyFilter = () => {
            const category = (categoryFilter?.value || 'all').toLowerCase();
            let visibleCount = 0;

            rows.forEach((row) => {
                // Only filter active pending rows in the first table
                if (!row.classList.contains('submission-row')) return;

                const cardCategory = (row.dataset.category || '').toLowerCase();
                const matchesCategory = category === 'all' || cardCategory === category;

                row.hidden = !matchesCategory;
                if (matchesCategory) visibleCount += 1;
            });

            if (emptyState) {
                emptyState.hidden = visibleCount > 0 || rows.filter((r) => r.classList.contains('submission-row')).length === 0;
            }
        };

        categoryFilter?.addEventListener('change', applyFilter);
        applyFilter();

        /* ============================
         * Hidden Form & App Config
         * ============================ */
        const hiddenForm = document.getElementById('submissionHiddenForm');
        const hiddenKeputusan = document.getElementById('hiddenKeputusan');
        const hiddenCatatanPimpinan = document.getElementById('hiddenCatatanPimpinan');
        const hiddenSendEmail = document.getElementById('hiddenSendEmail');
        const hiddenSendWa = document.getElementById('hiddenSendWa');
        const hiddenCustomEmail = document.getElementById('hiddenCustomEmail');
        const hiddenCustomWa = document.getElementById('hiddenCustomWa');
        const appName = document.querySelector('meta[name="app-name"]')?.content || 'Institusi Kami';

        /* ============================
        /* ============================
         * MODAL 1: SUBMISSION DETAIL
         * ============================ */
        const modalBaru = {
            overlay: document.getElementById('submissionDetailModalBaru'),
            btnClose: document.getElementById('subdetailBtnCloseBaru'),
            btnCancel: document.getElementById('subdetailBtnCancelBaru'),
            btnApprove: document.getElementById('subdetailBtnApproveBaru'),
            btnReject: document.getElementById('subdetailBtnRejectBaru'),
            catatan: document.getElementById('subdetailCatatanTextareaBaru'),
            counter: document.getElementById('subdetailCounterBaru'),
            code: document.getElementById('subdetailCodeBaru'),
            statusBadge: document.getElementById('subdetailStatusBadgeBaru'),
            title: document.getElementById('subdetailTitleBaru'),
            mitraName: document.getElementById('subdetailMitraNameBaru'),
            klasifikasi: document.getElementById('subdetailKlasifikasiBaru'),
            negara: document.getElementById('subdetailNegaraBaru'),
            alamat: document.getElementById('subdetailAlamatBaru'),
            website: document.getElementById('subdetailWebsiteBaru'),
            penandatangan: document.getElementById('subdetailPenandatanganBaru'),
            pj: document.getElementById('subdetailPjBaru'),
            kontak: document.getElementById('subdetailKontakBaru'),
            tujuan: document.getElementById('subdetailTujuanBaru'),
            ruangLingkup: document.getElementById('subdetailRuangLingkupBaru'),
            pesanWrapper: document.getElementById('subdetailPesanWrapperBaru'),
            pesanTambahan: document.getElementById('subdetailPesanTambahanBaru'),
            historyNoteWrapper: document.getElementById('subdetailHistoryNoteWrapperBaru'),
            historyNote: document.getElementById('subdetailHistoryNoteBaru'),
            formBlock: document.getElementById('subdetailFormBlockBaru'),
            activeActions: document.getElementById('subdetailActiveActionsBaru'),
        };

        const modalPerpanjangan = {
            overlay: document.getElementById('submissionDetailModalPerpanjangan'),
            btnClose: document.getElementById('subdetailBtnClosePerpanjangan'),
            btnCancel: document.getElementById('subdetailBtnCancelPerpanjangan'),
            btnApprove: document.getElementById('subdetailBtnApprovePerpanjangan'),
            btnReject: document.getElementById('subdetailBtnRejectPerpanjangan'),
            catatan: document.getElementById('subdetailCatatanTextareaPerpanjangan'),
            counter: document.getElementById('subdetailCounterPerpanjangan'),
            code: document.getElementById('subdetailCodePerpanjangan'),
            statusBadge: document.getElementById('subdetailStatusBadgePerpanjangan'),
            title: document.getElementById('subdetailTitlePerpanjangan'),
            mitraName: document.getElementById('subdetailMitraNamePerpanjangan'),
            klasifikasi: document.getElementById('subdetailKlasifikasiPerpanjangan'),
            negara: document.getElementById('subdetailNegaraPerpanjangan'),
            alamat: document.getElementById('subdetailAlamatPerpanjangan'),
            website: document.getElementById('subdetailWebsitePerpanjangan'),
            penandatangan: document.getElementById('subdetailPenandatanganPerpanjangan'),
            pj: document.getElementById('subdetailPjPerpanjangan'),
            kontak: document.getElementById('subdetailKontakPerpanjangan'),
            tujuan: document.getElementById('subdetailTujuanPerpanjangan'),
            ruangLingkup: document.getElementById('subdetailRuangLingkupPerpanjangan'),
            pesanWrapper: document.getElementById('subdetailPesanWrapperPerpanjangan'),
            pesanTambahan: document.getElementById('subdetailPesanTambahanPerpanjangan'),
            historyNoteWrapper: document.getElementById('subdetailHistoryNoteWrapperPerpanjangan'),
            historyNote: document.getElementById('subdetailHistoryNotePerpanjangan'),
            formBlock: document.getElementById('subdetailFormBlockPerpanjangan'),
            activeActions: document.getElementById('subdetailActiveActionsPerpanjangan'),
            // Khusus perpanjangan:
            jenisDokumen: document.getElementById('subdetailJenisDokumen'),
            docNumber: document.getElementById('subdetailDocNumber'),
            periode: document.getElementById('subdetailPeriode'),
            fileSuratWrapper: document.getElementById('subdetailFileSuratWrapper'),
            fileSuratLink: document.getElementById('subdetailFileSuratLink'),
        };

        let activeModalConfig = null;
        let activeRow = null;

        // Counter textarea di Modal Detail
        const updateDetailCounter = (config) => {
            if (!config || !config.counter || !config.catatan) return;
            const len = config.catatan.value.trim().length;
            config.counter.textContent = `${len} karakter`;
        };

        [modalBaru, modalPerpanjangan].forEach((config) => {
            config.catatan?.addEventListener('input', () => {
                config.catatan.classList.remove('is-required');
                updateDetailCounter(config);
            });
        });

        function openDetailModal(row, targetAction = 'detail') {
            activeRow = row;
            const ds = row.dataset;
            const isHistory = row.classList.contains('submission-history-row');
            const isPerpanjangan = ds.isPerpanjangan === '1';

            activeModalConfig = isPerpanjangan ? modalPerpanjangan : modalBaru;

            // Tutup overlay lainnya
            const otherConfig = isPerpanjangan ? modalBaru : modalPerpanjangan;
            if (otherConfig.overlay) {
                otherConfig.overlay.hidden = true;
                otherConfig.overlay.classList.remove('is-visible');
            }

            if (isPerpanjangan) {
                if (activeModalConfig.jenisDokumen) activeModalConfig.jenisDokumen.textContent = ds.jenisDokumen || '—';
                if (activeModalConfig.docNumber) activeModalConfig.docNumber.textContent = ds.docNumber || '—';
                if (activeModalConfig.periode) {
                    if (ds.startDate && ds.endDate && ds.startDate !== '-' && ds.endDate !== '-') {
                        activeModalConfig.periode.textContent = `${ds.startDate} s/d ${ds.endDate}`;
                    } else {
                        activeModalConfig.periode.textContent = 'Belum diatur';
                    }
                }
                if (ds.fileSurat && activeModalConfig.fileSuratWrapper && activeModalConfig.fileSuratLink) {
                    activeModalConfig.fileSuratWrapper.hidden = false;
                    activeModalConfig.fileSuratLink.href = ds.fileSurat;
                } else if (activeModalConfig.fileSuratWrapper) {
                    activeModalConfig.fileSuratWrapper.hidden = true;
                }
            }

            activeModalConfig.code.textContent = ds.submissionCode || '—';
            activeModalConfig.title.textContent = ds.submissionTitle || '—';
            activeModalConfig.mitraName.textContent = ds.mitraName || '—';
            activeModalConfig.klasifikasi.textContent = `${ds.klasifikasi || 'Umum'} (${ds.kategori || 'Nasional'})`;
            activeModalConfig.negara.textContent = ds.negara || '—';
            activeModalConfig.alamat.textContent = ds.alamat || '—';

            if (ds.website) {
                activeModalConfig.website.innerHTML = `<a href="${ds.website}" target="_blank" rel="noreferrer">${ds.website}</a>`;
            } else {
                activeModalConfig.website.textContent = 'Belum ada website';
            }

            activeModalConfig.penandatangan.innerHTML = `${ds.penandatanganNama || '—'}<br><small class="text-sub">${ds.penandatanganJabatan || '-'}</small>`;
            activeModalConfig.pj.innerHTML = `${ds.pjNama || '—'}<br><small class="text-sub">${ds.pjJabatan || '-'}</small>`;
            activeModalConfig.kontak.innerHTML = `<i class="fas fa-envelope"></i> ${ds.mitraEmail || '—'}<br><i class="fab fa-whatsapp"></i> ${ds.mitraPhone || '—'}`;
            activeModalConfig.tujuan.textContent = ds.tujuan || '—';
            activeModalConfig.ruangLingkup.textContent = ds.ruangLingkup || '—';

            if (ds.pesanTambahan) {
                activeModalConfig.pesanWrapper.hidden = false;
                activeModalConfig.pesanTambahan.textContent = ds.pesanTambahan;
            } else {
                activeModalConfig.pesanWrapper.hidden = true;
            }

            if (isHistory) {
                activeModalConfig.statusBadge.className = `submission-status ${ds.statusClass || 'pending'}`;
                activeModalConfig.statusBadge.textContent = ds.statusLabel || 'Proses';
                if (activeModalConfig.formBlock) {
                    activeModalConfig.formBlock.hidden = true;
                    activeModalConfig.formBlock.style.display = 'none';
                }
                if (activeModalConfig.activeActions) {
                    activeModalConfig.activeActions.hidden = true;
                    activeModalConfig.activeActions.style.display = 'none';
                }

                if (ds.catatanPimpinan) {
                    activeModalConfig.historyNoteWrapper.hidden = false;
                    activeModalConfig.historyNote.textContent = ds.catatanPimpinan;
                } else {
                    activeModalConfig.historyNoteWrapper.hidden = true;
                }
            } else {
                activeModalConfig.statusBadge.className = 'submission-status pending';
                activeModalConfig.statusBadge.textContent = 'Menunggu Review';

                if (targetAction === 'detail') {
                    if (activeModalConfig.formBlock) {
                        activeModalConfig.formBlock.hidden = true;
                        activeModalConfig.formBlock.style.display = 'none';
                    }
                    if (activeModalConfig.activeActions) {
                        activeModalConfig.activeActions.hidden = true;
                        activeModalConfig.activeActions.style.display = 'none';
                    }
                } else {
                    if (activeModalConfig.formBlock) {
                        activeModalConfig.formBlock.hidden = false;
                        activeModalConfig.formBlock.style.display = 'flex';
                    }
                    if (activeModalConfig.activeActions) {
                        activeModalConfig.activeActions.hidden = false;
                        activeModalConfig.activeActions.style.display = 'flex';

                        if (activeModalConfig.btnApprove && activeModalConfig.btnReject) {
                            if (targetAction === 'approve') {
                                activeModalConfig.btnApprove.style.display = 'inline-flex';
                                activeModalConfig.btnReject.style.display = 'none';
                            } else if (targetAction === 'reject') {
                                activeModalConfig.btnApprove.style.display = 'none';
                                activeModalConfig.btnReject.style.display = 'inline-flex';
                            }
                        }
                    }
                }

                activeModalConfig.historyNoteWrapper.hidden = true;
                if (activeModalConfig.catatan) activeModalConfig.catatan.value = '';
                updateDetailCounter(activeModalConfig);
            }

            activeModalConfig.overlay.hidden = false;
            requestAnimationFrame(() => activeModalConfig.overlay.classList.add('is-visible'));
            document.body.style.overflow = 'hidden';
        }

        function closeDetailModal() {
            if (!activeModalConfig) return;
            activeModalConfig.overlay.classList.remove('is-visible');
            const targetOverlay = activeModalConfig.overlay;
            setTimeout(() => {
                targetOverlay.hidden = true;
            }, 260);
            activeRow = null;
            activeModalConfig = null;
            document.body.style.overflow = '';
        }

        [modalBaru, modalPerpanjangan].forEach((config) => {
            config.btnClose?.addEventListener('click', closeDetailModal);
            config.btnCancel?.addEventListener('click', closeDetailModal);
            config.overlay?.addEventListener('click', (e) => {
                if (e.target === config.overlay) closeDetailModal();
            });
        });

        /* ============================
         * MODAL 2: NOTIFICATION CONFIRM
         * ============================ */
        const notifModal = document.getElementById('notifConfirmModal');
        const notifHeader = document.getElementById('notifModalHeader');
        const notifIcon = document.getElementById('notifModalIcon');
        const notifTitle = document.getElementById('notifModalTitle');
        const notifSubtitle = document.getElementById('notifModalSubtitle');
        const notifMitraName = document.getElementById('notifMitraName');
        const notifMitraEmail = document.getElementById('notifMitraEmail');
        const notifMitraPhone = document.getElementById('notifMitraPhone');
        const toggleEmail = document.getElementById('notifToggleEmail');
        const toggleWa = document.getElementById('notifToggleWa');
        const previewEmail = document.getElementById('notifPreviewEmail');
        const previewWa = document.getElementById('notifPreviewWa');
        const messageEmail = document.getElementById('notifMessageEmail');
        const messageWa = document.getElementById('notifMessageWa');
        const btnCancelNotif = document.getElementById('notifBtnCancel');
        const btnConfirmNotif = document.getElementById('notifBtnConfirm');
        const btnConfirmText = document.getElementById('notifBtnConfirmText');
        const tabs = document.querySelectorAll('[data-notif-tab]');

        let pendingRow = null;
        let pendingDecision = null;
        let pendingNote = '';

        const notifPerpanjanganBadgeRow = document.getElementById('notifPerpanjanganBadgeRow');

        function generateEmailTemplate(ds, decision, note) {
            const isApproved = decision === 'disetujui';
            const isPerpanjangan = ds.isPerpanjangan === '1';
            const typeText = isPerpanjangan ? 'perpanjangan kerja sama' : 'kerja sama baru';
            const systemUrl = 'https://kerjasamapolimdo.org/';
            if (isApproved) {
                return `Yth. ${ds.mitraName},\n\nDengan hormat, kami dari ${appName} ingin memberitahukan bahwa pengajuan ${typeText} Anda dengan kode ${ds.submissionCode} — "${ds.submissionTitle}" telah DISETUJUI.\n\n${note ? `Catatan: ${note}\n\n` : ''}Tim kami akan segera menghubungi Anda untuk langkah selanjutnya.\n\nInformasi selengkapnya dapat diakses melalui portal resmi kami:\n${systemUrl}\n\nHormat kami,\n${appName}`;
            }
            return `Yth. ${ds.mitraName},\n\nDengan hormat, kami dari ${appName} ingin memberitahukan bahwa pengajuan ${typeText} Anda dengan kode ${ds.submissionCode} — "${ds.submissionTitle}" saat ini belum dapat kami setujui.\n\n${note ? `Catatan dari pimpinan: ${note}\n\n` : ''}Kami tetap menghargai minat Anda dan berharap dapat bekerja sama di kesempatan mendatang.\n\nInformasi selengkapnya dapat diakses melalui portal resmi kami:\n${systemUrl}\n\nHormat kami,\n${appName}`;
        }

        function generateWaTemplate(ds, decision, note) {
            const isApproved = decision === 'disetujui';
            const isPerpanjangan = ds.isPerpanjangan === '1';
            const typeText = isPerpanjangan ? 'perpanjangan kerja sama' : 'kerja sama baru';
            const systemUrl = 'https://kerjasamapolimdo.org/';
            if (isApproved) {
                return `Halo *${ds.mitraName}*,\n\nKami dari *${appName}* ingin memberitahukan bahwa pengajuan ${typeText} Anda dengan kode *${ds.submissionCode}* — _${ds.submissionTitle}_ telah *DISETUJUI*. ✅\n\n${note ? `Catatan: _${note}_\n\n` : ''}Terima kasih atas minat dan kepercayaan Anda. Tim kami akan segera menghubungi Anda untuk langkah selanjutnya.\n\nInformasi selengkapnya dapat diakses melalui portal resmi kami:\n${systemUrl}\n\nSalam hangat,\n${appName}`;
            }
            return `Halo *${ds.mitraName}*,\n\nKami dari *${appName}* ingin memberitahukan bahwa pengajuan ${typeText} Anda dengan kode *${ds.submissionCode}* — _${ds.submissionTitle}_ saat ini *belum dapat kami setujui*. ❌\n\n${note ? `Catatan dari pimpinan: _${note}_\n\n` : ''}Kami tetap menghargai minat Anda. Jangan ragu untuk mengajukan kembali di kemudian hari.\n\nInformasi selengkapnya dapat diakses melalui portal resmi kami:\n${systemUrl}\n\nSalam hangat,\n${appName}`;
        }

        // Tab Switcher
        tabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                tabs.forEach((t) => t.classList.remove('active'));
                tab.classList.add('active');
                if (tab.dataset.notifTab === 'email') {
                    previewEmail.hidden = false;
                    previewWa.hidden = true;
                } else {
                    previewEmail.hidden = true;
                    previewWa.hidden = false;
                }
            });
        });

        function openNotifModal(row, decision, note) {
            if (!row) return;
            pendingRow = row;
            pendingDecision = decision;
            pendingNote = note;
            const ds = row.dataset;
            const isApproved = decision === 'disetujui';
            const isHistory = row.classList.contains('submission-history-row');
            const isPerpanjangan = ds.isPerpanjangan === '1';

            if (notifPerpanjanganBadgeRow) {
                notifPerpanjanganBadgeRow.hidden = !isPerpanjangan;
            }

            notifHeader.className = 'notif-modal-header ' + (isApproved ? 'is-approved' : 'is-rejected');
            notifIcon.innerHTML = isApproved
                ? '<i class="fas fa-circle-check"></i>'
                : '<i class="fas fa-circle-xmark"></i>';

            if (isHistory) {
                notifTitle.textContent = `Kirim Notifikasi ke ${ds.mitraName}`;
                notifSubtitle.textContent = `Kirim notifikasi status (${isApproved ? 'Disetujui' : 'Ditolak'}) via Email atau WhatsApp.`;
            } else {
                notifTitle.textContent = isApproved
                    ? `Setujui ${isPerpanjangan ? 'Perpanjangan' : 'Pengajuan'} dari ${ds.mitraName}?`
                    : `Tolak ${isPerpanjangan ? 'Perpanjangan' : 'Pengajuan'} dari ${ds.mitraName}?`;
                notifSubtitle.textContent = isApproved
                    ? (isPerpanjangan ? 'Pengajuan perpanjangan akan disetujui dan status dokumen diperbarui.' : 'Data mitra akan disimpan ke master mitra dan notifikasi terkirim.')
                    : 'Pengajuan akan ditolak dan mitra akan diberitahu.';
            }

            notifMitraName.textContent = ds.mitraName || '—';
            notifMitraEmail.textContent = ds.mitraEmail || 'Tidak tersedia';
            notifMitraPhone.textContent = ds.mitraPhone || 'Tidak tersedia';

            if (!ds.mitraEmail) {
                toggleEmail.checked = false;
                toggleEmail.disabled = true;
            } else {
                toggleEmail.checked = true;
                toggleEmail.disabled = false;
            }

            if (!ds.mitraPhone) {
                toggleWa.checked = false;
                toggleWa.disabled = true;
            } else {
                toggleWa.checked = true;
                toggleWa.disabled = false;
            }

            messageEmail.value = generateEmailTemplate(ds, decision, note);
            messageWa.value = generateWaTemplate(ds, decision, note);

            tabs.forEach((t) => t.classList.remove('active'));
            tabs[0]?.classList.add('active');
            previewEmail.hidden = false;
            previewWa.hidden = true;

            btnConfirmNotif.className = 'notif-btn-confirm ' + (isApproved ? 'is-approved' : 'is-rejected');
            if (isHistory) {
                btnConfirmText.textContent = 'Kirim Notifikasi';
            } else {
                btnConfirmText.textContent = isApproved ? 'Setujui' : 'Tolak';
            }

            notifModal.hidden = false;
            requestAnimationFrame(() => notifModal.classList.add('is-visible'));
            document.body.style.overflow = 'hidden';
        }

        function closeNotifModal() {
            notifModal.classList.remove('is-visible');
            setTimeout(() => {
                notifModal.hidden = true;
            }, 260);
            pendingRow = null;
            pendingDecision = null;
            pendingNote = '';
            document.body.style.overflow = '';
        }

        btnCancelNotif?.addEventListener('click', closeNotifModal);
        notifModal?.addEventListener('click', (e) => {
            if (e.target === notifModal) closeNotifModal();
        });

        // ESC listener for both modals
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (!notifModal?.hidden) closeNotifModal();
                else if (!detailModal?.hidden) closeDetailModal();
            }
        });

        // Submit Action via Hidden Form
        btnConfirmNotif?.addEventListener('click', () => {
            if (!pendingRow || !hiddenForm) return;

            hiddenForm.action = pendingRow.dataset.reviewUrl;
            hiddenKeputusan.value = pendingDecision;
            hiddenCatatanPimpinan.value = pendingNote;
            hiddenSendEmail.value = toggleEmail.checked ? '1' : '0';
            hiddenSendWa.value = toggleWa.checked ? '1' : '0';
            hiddenCustomEmail.value = toggleEmail.checked ? messageEmail.value : '';
            hiddenCustomWa.value = toggleWa.checked ? messageWa.value : '';

            btnConfirmNotif.disabled = true;
            btnConfirmText.textContent = 'Memproses...';

            hiddenForm.submit();
        });

        /* ============================
         * Row Event Delegation
         * ============================ */
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-action]');
            if (!btn) return;

            const action = btn.dataset.action;
            const row = btn.closest('[data-submission-row]');

            if (action === 'detail' || action === 'detail-history') {
                if (row) openDetailModal(row, 'detail');
                return;
            }

            if (action === 'send-notif-history') {
                if (!row) return;
                const decision = row.dataset.status || (row.dataset.statusClass === 'approved' ? 'disetujui' : 'ditolak');
                const note = row.dataset.catatanPimpinan || '';
                openNotifModal(row, decision, note);
                return;
            }

            if (action === 'approve') {
                if (row) openDetailModal(row, 'approve');
                return;
            }

            if (action === 'reject') {
                if (!row) return;
                openDetailModal(row, 'reject');
                if (activeModalConfig && activeModalConfig.catatan) {
                    activeModalConfig.catatan.focus();
                    activeModalConfig.catatan.classList.add('is-required');
                }

                if (window.Swal) {
                    window.Swal.fire({
                        icon: 'warning',
                        title: 'Catatan wajib diisi',
                        text: 'Tambahkan alasan penolakan pada kolom catatan di detail modal.',
                        confirmButtonText: 'Baik'
                    });
                }
                return;
            }
        });

        // Action Buttons inside Detail Modal
        [modalBaru, modalPerpanjangan].forEach((config) => {
            config.btnApprove?.addEventListener('click', () => {
                if (!activeRow) return;
                const targetRow = activeRow;
                const note = config.catatan?.value.trim() || '';
                closeDetailModal();
                openNotifModal(targetRow, 'disetujui', note);
            });

            config.btnReject?.addEventListener('click', () => {
                if (!activeRow) return;
                const targetRow = activeRow;
                const note = config.catatan?.value.trim() || '';

                if (note.length === 0) {
                    config.catatan?.classList.add('is-required');
                    config.catatan?.focus();

                    if (window.Swal) {
                        window.Swal.fire({
                            icon: 'warning',
                            title: 'Catatan wajib diisi',
                            text: 'Tambahkan alasan penolakan agar mitra memahami hasil validasi.',
                            confirmButtonText: 'Baik'
                        });
                    }
                    return;
                }

                closeDetailModal();
                openNotifModal(targetRow, 'ditolak', note);
            });
        });
    };

    document.addEventListener('DOMContentLoaded', boot);
    document.addEventListener('turbo:load', boot);
})();
