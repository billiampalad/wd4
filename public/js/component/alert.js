/**
 * ============================================================================
 * Custom Alert & Confirmation Modal Component
 * ============================================================================
 */

(function (global) {
    'use strict';

    const CustomAlert = {
        activeModal: null,

        /**
         * Open a modal by ID or Element
         * @param {string|HTMLElement} target
         */
        open: function (target) {
            const modal = typeof target === 'string' ? document.getElementById(target) : target;
            if (!modal) return;

            // Close any currently active modal first
            if (this.activeModal && this.activeModal !== modal) {
                this.close(this.activeModal);
            }

            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            this.activeModal = modal;

            // Focus cancel button or confirm button
            const focusTarget = modal.querySelector('[data-alert-cancel]') || modal.querySelector('[data-alert-confirm]');
            if (focusTarget) {
                setTimeout(() => focusTarget.focus(), 50);
            }

            // Dispatch event
            modal.dispatchEvent(new CustomEvent('alert:open', { bubbles: true }));
        },

        /**
         * Close a modal by ID or Element
         * @param {string|HTMLElement} [target]
         */
        close: function (target) {
            const modal = target 
                ? (typeof target === 'string' ? document.getElementById(target) : target)
                : this.activeModal;

            if (!modal) return;

            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';

            if (this.activeModal === modal) {
                this.activeModal = null;
            }

            // Dispatch event
            modal.dispatchEvent(new CustomEvent('alert:close', { bubbles: true }));
        },

        /**
         * Programmatic confirmation dialog
         * @param {Object} options
         * @param {string} [options.title]
         * @param {string} [options.message]
         * @param {string} [options.type] - 'danger' | 'warning' | 'primary'
         * @param {string} [options.cancelText]
         * @param {string} [options.confirmText]
         * @param {string} [options.confirmColor]
         * @param {Function} [options.onConfirm]
         * @param {Function} [options.onCancel]
         */
        confirm: function (options) {
            const opts = Object.assign({
                id: 'dynamicCustomAlert',
                title: 'Konfirmasi',
                message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                type: 'danger',
                cancelText: 'Batal',
                confirmText: 'Lanjutkan',
                confirmColor: 'danger',
                onConfirm: null,
                onCancel: null
            }, options);

            let modal = document.getElementById(opts.id);
            if (!modal) {
                modal = document.createElement('div');
                modal.id = opts.id;
                modal.className = 'custom-alert-overlay';
                modal.setAttribute('role', 'dialog');
                modal.setAttribute('aria-modal', 'true');
                document.body.appendChild(modal);
            }

            modal.innerHTML = `
                <div class="custom-alert-card" role="document">
                    <div class="custom-alert-icon-wrapper" data-alert-type="${opts.type}">
                        <div class="custom-alert-icon-bg">
                            <svg class="custom-alert-icon-svg" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21.1716 7.75736C22.6719 5.25686 25.3281 5.25686 26.8284 7.75736L43.4558 35.4697C44.9561 37.9702 43.628 40 40.7274 40H7.27258C4.37202 40 3.04388 37.9702 4.54416 35.4697L21.1716 7.75736Z" fill="currentColor" />
                                <path d="M24 16V26" stroke="white" stroke-width="3.2" stroke-linecap="round" />
                                <circle cx="24" cy="32" r="1.8" fill="white" />
                            </svg>
                        </div>
                    </div>
                    <div class="custom-alert-content">
                        <h3 class="custom-alert-title">${opts.title}</h3>
                        <p class="custom-alert-message">${opts.message}</p>
                    </div>
                    <div class="custom-alert-actions">
                        <button type="button" class="custom-alert-btn custom-alert-btn-cancel" data-alert-cancel>
                            ${opts.cancelText}
                        </button>
                        <button type="button" class="custom-alert-btn custom-alert-btn-confirm custom-alert-btn-${opts.confirmColor}" data-alert-confirm>
                            ${opts.confirmText}
                        </button>
                    </div>
                </div>
            `;

            const cancelBtn = modal.querySelector('[data-alert-cancel]');
            const confirmBtn = modal.querySelector('[data-alert-confirm]');

            const handleCancel = () => {
                this.close(modal);
                if (typeof opts.onCancel === 'function') opts.onCancel();
            };

            const handleConfirm = () => {
                this.close(modal);
                if (typeof opts.onConfirm === 'function') opts.onConfirm();
            };

            cancelBtn.onclick = handleCancel;
            confirmBtn.onclick = handleConfirm;

            this.open(modal);
        },

        /**
         * Specialized helper for Account Deactivation
         * @param {Object} options
         */
        showDeactivate: function (options) {
            const opts = Object.assign({
                accountName: '',
                onConfirm: null,
                formId: null
            }, options);

            const message = opts.accountName 
                ? `Apakah Anda yakin ingin menonaktifkan akun "${opts.accountName}"? Pengguna tidak akan dapat mengakses sistem ini lagi.`
                : 'Apakah Anda yakin ingin menonaktifkan akun ini? Tindakan ini akan membatasi akses pengguna ke sistem.';

            this.confirm({
                title: 'Nonaktifkan Akun',
                message: message,
                type: 'danger',
                cancelText: 'Batal',
                confirmText: 'Nonaktifkan',
                confirmColor: 'danger',
                onConfirm: () => {
                    if (opts.formId) {
                        const form = document.getElementById(opts.formId);
                        if (form) form.submit();
                    } else if (typeof opts.onConfirm === 'function') {
                        opts.onConfirm();
                    }
                }
            });
        },

        /**
         * Initialize event listeners for static components
         */
        init: function () {
            // Click outside or cancel button
            document.addEventListener('click', (e) => {
                // Trigger button with data-alert-target
                const trigger = e.target.closest('[data-alert-target]');
                if (trigger) {
                    e.preventDefault();
                    const targetId = trigger.getAttribute('data-alert-target');
                    CustomAlert.open(targetId);
                    return;
                }

                // Cancel button
                const cancelBtn = e.target.closest('[data-alert-cancel]');
                if (cancelBtn) {
                    const modal = cancelBtn.closest('.custom-alert-overlay');
                    CustomAlert.close(modal);
                    return;
                }

                // Confirm button with target form
                const confirmBtn = e.target.closest('[data-alert-confirm]');
                if (confirmBtn) {
                    const formTargetId = confirmBtn.getAttribute('data-form-target');
                    if (formTargetId) {
                        const form = document.getElementById(formTargetId);
                        if (form) {
                            form.submit();
                            const modal = confirmBtn.closest('.custom-alert-overlay');
                            CustomAlert.close(modal);
                        }
                    }
                }

                // Click on backdrop overlay directly
                if (e.target.classList.contains('custom-alert-overlay')) {
                    CustomAlert.close(e.target);
                }
            });

            // ESC key to close
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && CustomAlert.activeModal) {
                    CustomAlert.close(CustomAlert.activeModal);
                }
            });
        }
    };

    // Auto initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => CustomAlert.init());
    } else {
        CustomAlert.init();
    }

    // Expose globally
    global.CustomAlert = CustomAlert;

})(typeof window !== 'undefined' ? window : this);
