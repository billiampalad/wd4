(function () {
    const boot = () => {
        const root = document.querySelector('.submission-dashboard');
        if (!root || root.dataset.pmitraReady === 'true') return;
        root.dataset.pmitraReady = 'true';

        const cards = Array.from(document.querySelectorAll('[data-submission-card]'));
        const searchInput = document.getElementById('submissionSearch');
        const categoryFilter = document.getElementById('submissionCategoryFilter');
        const emptyState = document.querySelector('.submission-filter-empty');

        /* ============================
         * Search & Filter
         * ============================ */
        const applyFilter = () => {
            const query = (searchInput?.value || '').trim().toLowerCase();
            const category = (categoryFilter?.value || 'all').toLowerCase();
            let visibleCount = 0;

            cards.forEach((card) => {
                const haystack = card.dataset.search || '';
                const cardCategory = card.dataset.category || '';
                const matchesQuery = !query || haystack.includes(query);
                const matchesCategory = category === 'all' || cardCategory === category;
                const isVisible = matchesQuery && matchesCategory;

                card.hidden = !isVisible;
                if (isVisible) visibleCount += 1;
            });

            if (emptyState) {
                emptyState.hidden = visibleCount > 0 || cards.length === 0;
            }
        };

        searchInput?.addEventListener('input', applyFilter);
        categoryFilter?.addEventListener('change', applyFilter);
        applyFilter();

        /* ============================
         * Notification Modal Elements
         * ============================ */
        const modal = document.getElementById('notifConfirmModal');
        const modalHeader = document.getElementById('notifModalHeader');
        const modalIcon = document.getElementById('notifModalIcon');
        const modalTitle = document.getElementById('notifModalTitle');
        const modalSubtitle = document.getElementById('notifModalSubtitle');
        const mitraNameEl = document.getElementById('notifMitraName');
        const mitraEmailEl = document.getElementById('notifMitraEmail');
        const mitraPhoneEl = document.getElementById('notifMitraPhone');
        const toggleEmail = document.getElementById('notifToggleEmail');
        const toggleWa = document.getElementById('notifToggleWa');
        const previewEmail = document.getElementById('notifPreviewEmail');
        const previewWa = document.getElementById('notifPreviewWa');
        const messageEmail = document.getElementById('notifMessageEmail');
        const messageWa = document.getElementById('notifMessageWa');
        const btnCancel = document.getElementById('notifBtnCancel');
        const btnConfirm = document.getElementById('notifBtnConfirm');
        const btnConfirmText = document.getElementById('notifBtnConfirmText');
        const tabs = document.querySelectorAll('[data-notif-tab]');

        // Track the currently pending form + decision
        let pendingForm = null;
        let pendingDecision = null;
        let pendingButtonName = null;

        const appName = document.querySelector('meta[name="app-name"]')?.content || 'Institusi Kami';

        /* ============================
         * Template generators
         * ============================ */
        function generateEmailTemplate(card, decision, note) {
            const name = card.dataset.mitraName || '—';
            const code = card.dataset.submissionCode || '—';
            const title = card.dataset.submissionTitle || '—';
            const isApproved = decision === 'disetujui';

            if (isApproved) {
                return `Yth. ${name},\n\nDengan hormat, kami dari ${appName} ingin memberitahukan bahwa pengajuan kerja sama Anda dengan kode ${code} — "${title}" telah DISETUJUI.\n\n${note ? `Catatan: ${note}\n\n` : ''}Tim kami akan segera menghubungi Anda untuk langkah selanjutnya.\n\nHormat kami,\n${appName}`;
            }
            return `Yth. ${name},\n\nDengan hormat, kami dari ${appName} ingin memberitahukan bahwa pengajuan kerja sama Anda dengan kode ${code} — "${title}" saat ini belum dapat kami setujui.\n\n${note ? `Catatan dari pimpinan: ${note}\n\n` : ''}Kami tetap menghargai minat Anda dan berharap dapat bekerja sama di kesempatan mendatang.\n\nHormat kami,\n${appName}`;
        }

        function generateWaTemplate(card, decision, note) {
            const name = card.dataset.mitraName || '—';
            const code = card.dataset.submissionCode || '—';
            const title = card.dataset.submissionTitle || '—';
            const isApproved = decision === 'disetujui';

            if (isApproved) {
                return `Halo *${name}*,\n\nKami dari *${appName}* ingin memberitahukan bahwa pengajuan kerja sama Anda dengan kode *${code}* — _${title}_ telah *DISETUJUI*. ✅\n\n${note ? `Catatan: _${note}_\n\n` : ''}Terima kasih atas minat dan kepercayaan Anda. Tim kami akan segera menghubungi Anda untuk langkah selanjutnya.\n\nSalam hangat,\n${appName}`;
            }
            return `Halo *${name}*,\n\nKami dari *${appName}* ingin memberitahukan bahwa pengajuan kerja sama Anda dengan kode *${code}* — _${title}_ saat ini *belum dapat kami setujui*. ❌\n\n${note ? `Catatan dari pimpinan: _${note}_\n\n` : ''}Kami tetap menghargai minat Anda. Jangan ragu untuk mengajukan kembali di kemudian hari.\n\nSalam hangat,\n${appName}`;
        }

        /* ============================
         * Tab switching
         * ============================ */
        tabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                tabs.forEach((t) => t.classList.remove('active'));
                tab.classList.add('active');

                const target = tab.dataset.notifTab;
                if (target === 'email') {
                    previewEmail.hidden = false;
                    previewWa.hidden = true;
                } else {
                    previewEmail.hidden = true;
                    previewWa.hidden = false;
                }
            });
        });

        /* ============================
         * Modal open / close
         * ============================ */
        function openModal(form, card, decision, buttonName, note) {
            pendingForm = form;
            pendingDecision = decision;
            pendingButtonName = buttonName;

            const isApproved = decision === 'disetujui';
            const mitraName = card.dataset.mitraName || '—';
            const mitraEmail = card.dataset.mitraEmail || '—';
            const mitraPhone = card.dataset.mitraPhone || '—';

            // Update header
            modalHeader.className = 'notif-modal-header ' + (isApproved ? 'is-approved' : 'is-rejected');
            modalIcon.innerHTML = isApproved
                ? '<i class="fas fa-circle-check"></i>'
                : '<i class="fas fa-circle-xmark"></i>';
            modalTitle.textContent = isApproved
                ? `Setujui Pengajuan dari ${mitraName}?`
                : `Tolak Pengajuan dari ${mitraName}?`;
            modalSubtitle.textContent = isApproved
                ? 'Data mitra akan disimpan ke master mitra dan notifikasi dikirim.'
                : 'Pengajuan akan ditolak dan mitra akan diberitahu.';

            // Update recipient info
            mitraNameEl.textContent = mitraName;
            mitraEmailEl.textContent = mitraEmail || 'Tidak tersedia';
            mitraPhoneEl.textContent = mitraPhone || 'Tidak tersedia';

            // Enable/disable toggles based on availability
            if (!mitraEmail) {
                toggleEmail.checked = false;
                toggleEmail.disabled = true;
            } else {
                toggleEmail.checked = true;
                toggleEmail.disabled = false;
            }

            if (!mitraPhone) {
                toggleWa.checked = false;
                toggleWa.disabled = true;
            } else {
                toggleWa.checked = true;
                toggleWa.disabled = false;
            }

            // Generate preview messages
            messageEmail.value = generateEmailTemplate(card, decision, note);
            messageWa.value = generateWaTemplate(card, decision, note);

            // Reset tabs to email
            tabs.forEach((t) => t.classList.remove('active'));
            tabs[0]?.classList.add('active');
            previewEmail.hidden = false;
            previewWa.hidden = true;

            // Update confirm button style
            btnConfirm.className = 'notif-btn-confirm ' + (isApproved ? 'is-approved' : 'is-rejected');
            btnConfirmText.textContent = isApproved
                ? 'Setujui & Kirim Notifikasi'
                : 'Tolak & Kirim Notifikasi';

            // Show modal
            modal.hidden = false;
            requestAnimationFrame(() => {
                modal.classList.add('is-visible');
            });

            // Trap focus
            btnCancel.focus();
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modal.classList.remove('is-visible');
            setTimeout(() => {
                modal.hidden = true;
            }, 280);
            pendingForm = null;
            pendingDecision = null;
            pendingButtonName = null;
            document.body.style.overflow = '';
        }

        btnCancel?.addEventListener('click', closeModal);

        // Close on overlay click
        modal?.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        // Close on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal?.hidden) closeModal();
        });

        /* ============================
         * Confirm action
         * ============================ */
        btnConfirm?.addEventListener('click', () => {
            if (!pendingForm) return;

            // Inject hidden inputs for notification preferences
            const injectHidden = (name, value) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                pendingForm.appendChild(input);
            };

            // Inject the decision button value
            injectHidden(pendingButtonName, pendingDecision);

            // Notification toggles
            injectHidden('send_email', toggleEmail.checked ? '1' : '0');
            injectHidden('send_whatsapp', toggleWa.checked ? '1' : '0');

            // Custom messages
            if (toggleEmail.checked) {
                injectHidden('custom_message_email', messageEmail.value);
            }
            if (toggleWa.checked) {
                injectHidden('custom_message_whatsapp', messageWa.value);
            }

            // Disable confirm button to prevent double submit
            btnConfirm.disabled = true;
            btnConfirmText.textContent = 'Memproses...';

            pendingForm.submit();
        });

        /* ============================
         * Intercept submission buttons
         * ============================ */
        document.querySelectorAll('.submission-form').forEach((form) => {
            const textarea = form.querySelector('.submission-textarea');
            const counter = form.querySelector('[data-note-counter]');

            const updateCounter = () => {
                if (!counter || !textarea) return;
                const length = textarea.value.trim().length;
                counter.textContent = `${length} karakter`;
            };

            textarea?.addEventListener('input', () => {
                textarea.classList.remove('is-required');
                updateCounter();
            });
            updateCounter();

            form.querySelectorAll('button[type="submit"][name="keputusan"]').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();

                    const decision = button.value;
                    const note = textarea?.value.trim() || '';

                    // Validate: catatan wajib jika ditolak
                    if (decision === 'ditolak' && note.length === 0) {
                        textarea?.classList.add('is-required');
                        textarea?.focus();

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

                    // Find the parent submission card
                    const card = form.closest('[data-submission-card]');
                    if (!card) return;

                    // Open the confirmation modal
                    openModal(form, card, decision, button.name, note);
                });
            });
        });
    };

    document.addEventListener('DOMContentLoaded', boot);
    document.addEventListener('turbo:load', boot);
})();
