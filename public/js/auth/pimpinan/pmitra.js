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
         * MODAL 1: SUBMISSION DETAIL
         * ============================ */
        const detailModal = document.getElementById('submissionDetailModal');
        const detailCode = document.getElementById('subdetailCode');
        const detailStatusBadge = document.getElementById('subdetailStatusBadge');
        const detailTitle = document.getElementById('subdetailTitle');
        const detailMitraName = document.getElementById('subdetailMitraName');
        const detailKlasifikasi = document.getElementById('subdetailKlasifikasi');
        const detailNegara = document.getElementById('subdetailNegara');
        const detailAlamat = document.getElementById('subdetailAlamat');
        const detailWebsite = document.getElementById('subdetailWebsite');
        const detailPenandatangan = document.getElementById('subdetailPenandatangan');
        const detailPj = document.getElementById('subdetailPj');
        const detailKontak = document.getElementById('subdetailKontak');
        const detailTujuan = document.getElementById('subdetailTujuan');
        const detailRuangLingkup = document.getElementById('subdetailRuangLingkup');
        const detailPesanWrapper = document.getElementById('subdetailPesanWrapper');
        const detailPesanTambahan = document.getElementById('subdetailPesanTambahan');
        const detailHistoryNoteWrapper = document.getElementById('subdetailHistoryNoteWrapper');
        const detailHistoryNote = document.getElementById('subdetailHistoryNote');
        const detailFormBlock = document.getElementById('subdetailFormBlock');
        const detailCatatanTextarea = document.getElementById('subdetailCatatanTextarea');
        const detailCounter = document.getElementById('subdetailCounter');
        const detailActiveActions = document.getElementById('subdetailActiveActions');
        const detailBtnClose = document.getElementById('subdetailBtnClose');
        const detailBtnCancel = document.getElementById('subdetailBtnCancel');
        const detailBtnApprove = document.getElementById('subdetailBtnApprove');
        const detailBtnReject = document.getElementById('subdetailBtnReject');

        let activeRow = null;

        // Counter textarea di Modal Detail
        const updateDetailCounter = () => {
            if (!detailCounter || !detailCatatanTextarea) return;
            const len = detailCatatanTextarea.value.trim().length;
            detailCounter.textContent = `${len} karakter`;
        };
        detailCatatanTextarea?.addEventListener('input', () => {
            detailCatatanTextarea.classList.remove('is-required');
            updateDetailCounter();
        });

        const perpanjanganBanner = document.getElementById('subdetailPerpanjanganBanner');
        const perpanjanganGrid = document.getElementById('subdetailPerpanjanganGrid');
        const detailJenisDokumen = document.getElementById('subdetailJenisDokumen');
        const detailDocNumber = document.getElementById('subdetailDocNumber');
        const detailPeriode = document.getElementById('subdetailPeriode');
        const fileSuratWrapper = document.getElementById('subdetailFileSuratWrapper');
        const fileSuratLink = document.getElementById('subdetailFileSuratLink');

        function openDetailModal(row) {
            activeRow = row;
            const ds = row.dataset;
            const isHistory = row.classList.contains('submission-history-row');
            const isPerpanjangan = ds.isPerpanjangan === '1' || Boolean(ds.mitraId);

            if (isPerpanjangan) {
                if (perpanjanganBanner) perpanjanganBanner.hidden = false;
                if (perpanjanganGrid) perpanjanganGrid.hidden = false;
                if (detailJenisDokumen) detailJenisDokumen.textContent = ds.jenisDokumen || '—';
                if (detailDocNumber) detailDocNumber.textContent = ds.docNumber || '—';
                if (detailPeriode) {
                    if (ds.startDate && ds.endDate && ds.startDate !== '-' && ds.endDate !== '-') {
                        detailPeriode.textContent = `${ds.startDate} s/d ${ds.endDate}`;
                    } else {
                        detailPeriode.textContent = 'Belum diatur';
                    }
                }
                if (ds.fileSurat && fileSuratWrapper && fileSuratLink) {
                    fileSuratWrapper.hidden = false;
                    fileSuratLink.href = ds.fileSurat;
                } else if (fileSuratWrapper) {
                    fileSuratWrapper.hidden = true;
                }
            } else {
                if (perpanjanganBanner) perpanjanganBanner.hidden = true;
                if (perpanjanganGrid) perpanjanganGrid.hidden = true;
                if (fileSuratWrapper) fileSuratWrapper.hidden = true;
            }

            detailCode.textContent = ds.submissionCode || '—';
            detailTitle.textContent = ds.submissionTitle || '—';
            detailMitraName.textContent = ds.mitraName || '—';
            detailKlasifikasi.textContent = `${ds.klasifikasi || 'Umum'} (${ds.kategori || 'Nasional'})`;
            detailNegara.textContent = ds.negara || '—';
            detailAlamat.textContent = ds.alamat || '—';

            if (ds.website) {
                detailWebsite.innerHTML = `<a href="${ds.website}" target="_blank" rel="noreferrer">${ds.website}</a>`;
            } else {
                detailWebsite.textContent = 'Belum ada website';
            }

            detailPenandatangan.innerHTML = `${ds.penandatanganNama || '—'}<br><small class="text-sub">${ds.penandatanganJabatan || '-'}</small>`;
            detailPj.innerHTML = `${ds.pjNama || '—'}<br><small class="text-sub">${ds.pjJabatan || '-'}</small>`;
            detailKontak.innerHTML = `<i class="fas fa-envelope"></i> ${ds.mitraEmail || '—'}<br><i class="fab fa-whatsapp"></i> ${ds.mitraPhone || '—'}`;
            detailTujuan.textContent = ds.tujuan || '—';
            detailRuangLingkup.textContent = ds.ruangLingkup || '—';

            if (ds.pesanTambahan) {
                detailPesanWrapper.hidden = false;
                detailPesanTambahan.textContent = ds.pesanTambahan;
            } else {
                detailPesanWrapper.hidden = true;
            }

            if (isHistory) {
                detailStatusBadge.className = `submission-status ${ds.statusClass || 'pending'}`;
                detailStatusBadge.textContent = ds.statusLabel || 'Proses';
                if (detailFormBlock) {
                    detailFormBlock.hidden = true;
                    detailFormBlock.style.display = 'none';
                }
                if (detailActiveActions) {
                    detailActiveActions.hidden = true;
                    detailActiveActions.style.display = 'none';
                }

                if (ds.catatanPimpinan) {
                    detailHistoryNoteWrapper.hidden = false;
                    detailHistoryNote.textContent = ds.catatanPimpinan;
                } else {
                    detailHistoryNoteWrapper.hidden = true;
                }
            } else {
                detailStatusBadge.className = 'submission-status pending';
                detailStatusBadge.textContent = 'Menunggu Review';
                if (detailFormBlock) {
                    detailFormBlock.hidden = false;
                    detailFormBlock.style.display = 'flex';
                }
                if (detailActiveActions) {
                    detailActiveActions.hidden = false;
                    detailActiveActions.style.display = 'flex';
                }
                detailHistoryNoteWrapper.hidden = true;
                if (detailCatatanTextarea) detailCatatanTextarea.value = '';
                updateDetailCounter();
            }

            detailModal.hidden = false;
            requestAnimationFrame(() => detailModal.classList.add('is-visible'));
            document.body.style.overflow = 'hidden';
        }

        function closeDetailModal() {
            detailModal.classList.remove('is-visible');
            setTimeout(() => {
                detailModal.hidden = true;
            }, 260);
            document.body.style.overflow = '';
        }

        detailBtnClose?.addEventListener('click', closeDetailModal);
        detailBtnCancel?.addEventListener('click', closeDetailModal);
        detailModal?.addEventListener('click', (e) => {
            if (e.target === detailModal) closeDetailModal();
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

        function generateEmailTemplate(ds, decision, note) {
            const isApproved = decision === 'disetujui';
            const systemUrl = 'https://kerjasamapolimdo.org/';
            if (isApproved) {
                return `Yth. ${ds.mitraName},\n\nDengan hormat, kami dari ${appName} ingin memberitahukan bahwa pengajuan kerja sama Anda dengan kode ${ds.submissionCode} — "${ds.submissionTitle}" telah DISETUJUI.\n\n${note ? `Catatan: ${note}\n\n` : ''}Tim kami akan segera menghubungi Anda untuk langkah selanjutnya.\n\nInformasi selengkapnya dapat diakses melalui portal resmi kami:\n${systemUrl}\n\nHormat kami,\n${appName}`;
            }
            return `Yth. ${ds.mitraName},\n\nDengan hormat, kami dari ${appName} ingin memberitahukan bahwa pengajuan kerja sama Anda dengan kode ${ds.submissionCode} — "${ds.submissionTitle}" saat ini belum dapat kami setujui.\n\n${note ? `Catatan dari pimpinan: ${note}\n\n` : ''}Kami tetap menghargai minat Anda dan berharap dapat bekerja sama di kesempatan mendatang.\n\nInformasi selengkapnya dapat diakses melalui portal resmi kami:\n${systemUrl}\n\nHormat kami,\n${appName}`;
        }

        function generateWaTemplate(ds, decision, note) {
            const isApproved = decision === 'disetujui';
            const systemUrl = 'https://kerjasamapolimdo.org/';
            if (isApproved) {
                return `Halo *${ds.mitraName}*,\n\nKami dari *${appName}* ingin memberitahukan bahwa pengajuan kerja sama Anda dengan kode *${ds.submissionCode}* — _${ds.submissionTitle}_ telah *DISETUJUI*. ✅\n\n${note ? `Catatan: _${note}_\n\n` : ''}Terima kasih atas minat dan kepercayaan Anda. Tim kami akan segera menghubungi Anda untuk langkah selanjutnya.\n\nInformasi selengkapnya dapat diakses melalui portal resmi kami:\n${systemUrl}\n\nSalam hangat,\n${appName}`;
            }
            return `Halo *${ds.mitraName}*,\n\nKami dari *${appName}* ingin memberitahukan bahwa pengajuan kerja sama Anda dengan kode *${ds.submissionCode}* — _${ds.submissionTitle}_ saat ini *belum dapat kami setujui*. ❌\n\n${note ? `Catatan dari pimpinan: _${note}_\n\n` : ''}Kami tetap menghargai minat Anda. Jangan ragu untuk mengajukan kembali di kemudian hari.\n\nInformasi selengkapnya dapat diakses melalui portal resmi kami:\n${systemUrl}\n\nSalam hangat,\n${appName}`;
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
            pendingRow = row;
            pendingDecision = decision;
            pendingNote = note;
            const ds = row.dataset;
            const isApproved = decision === 'disetujui';
            const isHistory = row.classList.contains('submission-history-row');

            notifHeader.className = 'notif-modal-header ' + (isApproved ? 'is-approved' : 'is-rejected');
            notifIcon.innerHTML = isApproved
                ? '<i class="fas fa-circle-check"></i>'
                : '<i class="fas fa-circle-xmark"></i>';

            if (isHistory) {
                notifTitle.textContent = `Kirim Notifikasi ke ${ds.mitraName}`;
                notifSubtitle.textContent = `Kirim notifikasi status (${isApproved ? 'Disetujui' : 'Ditolak'}) via Email atau WhatsApp.`;
            } else {
                notifTitle.textContent = isApproved
                    ? `Setujui Pengajuan dari ${ds.mitraName}?`
                    : `Tolak Pengajuan dari ${ds.mitraName}?`;
                notifSubtitle.textContent = isApproved
                    ? 'Data mitra akan disimpan ke master mitra dan notifikasi terkirim.'
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
                btnConfirmText.textContent = isApproved
                    ? 'Setujui & Kirim Notifikasi'
                    : 'Tolak & Kirim Notifikasi';
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
                if (row) openDetailModal(row);
                return;
            }

            if (action === 'send-notif-history') {
                if (!row) return;
                const decision = row.dataset.status || (row.dataset.statusClass === 'approved' ? 'disetujui' : 'ditolak');
                const note = row.dataset.catatanPimpinan || '';
                openNotifModal(row, decision, note);
                return;
            }

            if (action === 'approve' || action === 'reject') {
                if (!row) return;
                const decision = action === 'approve' ? 'disetujui' : 'ditolak';

                // Prompt note validation if rejecting from table directly
                if (decision === 'ditolak') {
                    // Open detail modal first or prompt for note
                    openDetailModal(row);
                    detailCatatanTextarea?.focus();
                    detailCatatanTextarea?.classList.add('is-required');

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

                openNotifModal(row, decision, '');
            }
        });

        // Action Buttons inside Detail Modal
        detailBtnApprove?.addEventListener('click', () => {
            if (!activeRow) return;
            const note = detailCatatanTextarea?.value.trim() || '';
            closeDetailModal();
            openNotifModal(activeRow, 'disetujui', note);
        });

        detailBtnReject?.addEventListener('click', () => {
            if (!activeRow) return;
            const note = detailCatatanTextarea?.value.trim() || '';

            if (note.length === 0) {
                detailCatatanTextarea?.classList.add('is-required');
                detailCatatanTextarea?.focus();

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
            openNotifModal(activeRow, 'ditolak', note);
        });
    };

    document.addEventListener('DOMContentLoaded', boot);
    document.addEventListener('turbo:load', boot);
})();
