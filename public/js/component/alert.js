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

            const getIconSvg = (type) => {
                if (type === 'success') {
                    return `<svg class="custom-alert-icon-svg" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="24" cy="24" r="19" fill="currentColor" />
                        <path d="M15 24.5L21 30.5L33 17.5" stroke="white" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>`;
                } else if (type === 'logout') {
                    return `<svg class="custom-alert-icon-svg" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="24" cy="24" r="19" fill="currentColor" />
                        <path d="M22 15H16C14.8954 15 14 15.8954 14 17V31C14 32.1046 14.8954 33 16 33H22" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M28 18L34 24L28 30" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M20 24H33" stroke="white" stroke-width="3" stroke-linecap="round" />
                    </svg>`;
                } else if (type === 'primary' || type === 'info') {
                    return `<svg class="custom-alert-icon-svg" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="24" cy="24" r="19" fill="currentColor" />
                        <path d="M24 21V33" stroke="white" stroke-width="3.5" stroke-linecap="round" />
                        <circle cx="24" cy="15" r="2" fill="white" />
                    </svg>`;
                }
                return `<svg class="custom-alert-icon-svg" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21.1716 7.75736C22.6719 5.25686 25.3281 5.25686 26.8284 7.75736L43.4558 35.4697C44.9561 37.9702 43.628 40 40.7274 40H7.27258C4.37202 40 3.04388 37.9702 4.54416 35.4697L21.1716 7.75736Z" fill="currentColor" />
                    <path d="M24 16V26" stroke="white" stroke-width="3.2" stroke-linecap="round" />
                    <circle cx="24" cy="32" r="1.8" fill="white" />
                </svg>`;
            };

            modal.innerHTML = `
                <div class="custom-alert-card" role="document">
                    <div class="custom-alert-icon-wrapper" data-alert-type="${opts.type}">
                        <div class="custom-alert-icon-bg">
                            ${getIconSvg(opts.type)}
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
                message: null,
                onConfirm: null,
                formId: null
            }, options);

            const message = opts.message || (opts.accountName
                ? `Pengguna <span class="custom-alert-highlight">${opts.accountName}</span> sementara tidak dapat mengakses sistem ini.`
                : 'Apakah Anda yakin ingin menonaktifkan akun ini? Tindakan ini akan membatasi akses pengguna ke sistem.');

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
         * Specialized helper for Logout Confirmation
         * @param {Object} [options]
         */
        showLogout: function (options) {
            const opts = Object.assign({
                title: 'Keluar dari Sistem',
                message: 'Apakah Anda yakin ingin mengakhiri sesi dan keluar dari sistem?',
                formId: null,
                onConfirm: null
            }, options);

            this.confirm({
                title: opts.title,
                message: opts.message,
                type: 'logout',
                cancelText: 'Batal',
                confirmText: 'Keluar',
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
         * Show floating toast notification matching reference design
         * @param {Object} options
         * @param {string} [options.type] - 'success' | 'info' | 'warning' | 'danger'
         * @param {string} [options.title]
         * @param {string} [options.message]
         * @param {number} [options.duration] - default 4000ms
         */
        toast: function (options) {
            const opts = Object.assign({
                type: 'success',
                title: 'Berhasil!',
                message: 'Aksi telah berhasil diproses.',
                duration: 4000
            }, typeof options === 'string' ? { message: options } : options);

            let container = document.querySelector('.custom-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.className = 'custom-toast-container';
                document.body.appendChild(container);
            }

            const getToastIcon = (type) => {
                if (type === 'success') {
                    return `<svg class="custom-toast-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="9.5" stroke="#10b981" stroke-width="2" fill="none" />
                        <path d="M8 12.2L10.8 15L16 9.5" stroke="#10b981" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>`;
                } else if (type === 'info' || type === 'information' || type === 'primary') {
                    return `<svg class="custom-toast-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="9.5" stroke="#0ea5e9" stroke-width="2" fill="none" />
                        <path d="M12 11V16" stroke="#0ea5e9" stroke-width="2.2" stroke-linecap="round" />
                        <circle cx="12" cy="8" r="1.2" fill="#0ea5e9" />
                    </svg>`;
                } else if (type === 'warning') {
                    return `<svg class="custom-toast-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.8 4.2C11.3 3.3 12.7 3.3 13.2 4.2L20.8 17.5C21.3 18.4 20.6 19.5 19.6 19.5H4.4C3.4 19.5 2.7 18.4 3.2 17.5L10.8 4.2Z" stroke="#f59e0b" stroke-width="2" fill="none" />
                        <path d="M12 9.5V13.5" stroke="#f59e0b" stroke-width="2.2" stroke-linecap="round" />
                        <circle cx="12" cy="16.5" r="1.2" fill="#f59e0b" />
                    </svg>`;
                }
                return `<svg class="custom-toast-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="9.5" stroke="#ef4444" stroke-width="2" fill="none" />
                    <path d="M12 8V12.5" stroke="#ef4444" stroke-width="2.2" stroke-linecap="round" />
                    <circle cx="12" cy="15.5" r="1.2" fill="#ef4444" />
                </svg>`;
            };

            const toast = document.createElement('div');
            toast.className = `custom-toast-card custom-toast-${opts.type}`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="custom-toast-icon-box">
                    ${getToastIcon(opts.type)}
                </div>
                <div class="custom-toast-body">
                    <h4 class="custom-toast-title">${opts.title}</h4>
                    <p class="custom-toast-desc">${opts.message}</p>
                </div>
                <button type="button" class="custom-toast-close" aria-label="Tutup notifikasi">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            `;

            const removeToast = () => {
                toast.classList.add('is-hiding');
                setTimeout(() => toast.remove(), 250);
            };

            toast.querySelector('.custom-toast-close').onclick = removeToast;

            container.appendChild(toast);

            if (opts.duration > 0) {
                setTimeout(removeToast, opts.duration);
            }

            return toast;
        },

        /**
         * Shortcut for Success Toast
         */
        success: function (message, title = 'Berhasil!') {
            return this.toast({ type: 'success', title: title, message: message });
        },

        /**
         * Shortcut for Info Toast
         */
        info: function (message, title = 'Informasi') {
            return this.toast({ type: 'info', title: title, message: message });
        },

        /**
         * Shortcut for Warning Toast
         */
        warning: function (message, title = 'Peringatan') {
            return this.toast({ type: 'warning', title: title, message: message });
        },

        /**
         * Shortcut for Error Toast
         */
        error: function (message, title = 'Gagal!') {
            return this.toast({ type: 'danger', title: title, message: message });
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
    document.addEventListener('turbo:load', () => CustomAlert.init());

    // Expose globally
    global.CustomAlert = CustomAlert;

})(typeof window !== 'undefined' ? window : this);
