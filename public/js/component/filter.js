/**
 * ============================================================================
 * Custom Filter Component JS (Panel Card Expandable)
 * Premium Filter Management Engine with Live Table/Card Filtering,
 * Presets, Dynamic Active Filter Badges, Smooth Transitions, and Export Handlers.
 * ============================================================================
 */

(function (global) {
    'use strict';

    const CustomFilter = {
        instances: new Map(),

        /**
         * Initialize all custom filter instances on the page
         * @param {HTMLElement|Document} [container=document]
         */
        init: function (container = document) {
            const filterElements = container.querySelectorAll('[data-custom-filter]');
            filterElements.forEach((el) => {
                this.bindFilter(el);
            });
        },

        /**
         * Bind single filter instance
         * @param {HTMLElement} filterEl
         */
        bindFilter: function (filterEl) {
            if (!filterEl || filterEl._customFilterInitialized) return;
            filterEl._customFilterInitialized = true;

            const filterId = filterEl.getAttribute('data-filter-id') || filterEl.id;
            const form = filterEl.querySelector('form');
            const toggleHeader = filterEl.querySelector('[data-filter-toggle]');
            const bodyWrapper = filterEl.querySelector('.custom-filter-body-wrapper');
            const targetSelector = filterEl.getAttribute('data-filter-target');
            const emptySelector = filterEl.getAttribute('data-filter-empty');
            const countSelector = filterEl.getAttribute('data-filter-count');
            const autoApply = filterEl.getAttribute('data-filter-auto-apply') === 'true';
            const presetPills = filterEl.querySelectorAll('.custom-filter-preset-pill');
            const resetBtn = filterEl.querySelector('[data-filter-reset]');
            const clearAllTagsBtn = filterEl.querySelector('.btn-clear-all-tags');
            const exportPdfBtn = filterEl.querySelector('[data-filter-export="pdf"]');
            const exportExcelBtn = filterEl.querySelector('[data-filter-export="excel"]');

            const state = {
                filterEl,
                form,
                filterId,
                targetSelector,
                emptySelector,
                countSelector,
                isCollapsed: filterEl.classList.contains('is-collapsed'),
            };

            this.instances.set(filterId, state);

            // 1. Toggle Collapse / Expand
            if (toggleHeader && bodyWrapper) {
                const handleToggle = (e) => {
                    if (e.target.closest('.custom-filter-header-actions') && !e.target.closest('.custom-filter-collapse-btn')) {
                        return;
                    }

                    state.isCollapsed = !state.isCollapsed;
                    if (state.isCollapsed) {
                        filterEl.classList.add('is-collapsed');
                        toggleHeader.setAttribute('aria-expanded', 'false');
                        this.slideUp(bodyWrapper, 250);
                    } else {
                        filterEl.classList.remove('is-collapsed');
                        toggleHeader.setAttribute('aria-expanded', 'true');
                        this.slideDown(bodyWrapper, 250);
                    }
                };

                toggleHeader.addEventListener('click', handleToggle);
                toggleHeader.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        handleToggle(e);
                    }
                });
            }

            // 2. Preset Pills Click Handling
            if (presetPills.length > 0) {
                presetPills.forEach((pill) => {
                    pill.addEventListener('click', (e) => {
                        e.preventDefault();
                        const val = pill.getAttribute('data-preset-value');
                        const groupName = pill.getAttribute('data-preset-name') || 'status';
                        const groupContainer = pill.closest('[data-preset-group]');

                        if (groupContainer) {
                            groupContainer.querySelectorAll('[data-preset-value]').forEach((p) => p.classList.remove('is-active'));
                        }
                        pill.classList.add('is-active');

                        // Update matching input in form
                        const matchingInput = form ? form.querySelector(`[name="${groupName}"]`) : null;
                        if (matchingInput) {
                            matchingInput.value = val;
                            matchingInput.dispatchEvent(new Event('change', { bubbles: true }));
                        }

                        this.updateActiveTags(state);

                        // Trigger filter if auto apply is on or client target exists
                        if (autoApply || targetSelector) {
                            this.applyFilter(filterId);
                        }

                        // Dispatch custom event
                        filterEl.dispatchEvent(new CustomEvent('filter:preset-change', {
                            bubbles: true,
                            detail: { group: groupName, value: val, filterId }
                        }));
                    });
                });
            }

            // 3. Form Input Change Listeners
            if (form) {
                form.addEventListener('input', () => {
                    this.updateActiveTags(state);
                    if (targetSelector || autoApply) {
                        this.applyFilter(filterId);
                    }
                });

                form.addEventListener('change', () => {
                    this.updateActiveTags(state);
                    if (targetSelector || autoApply) {
                        this.applyFilter(filterId);
                    }
                });

                // Form Submit Handling
                form.addEventListener('submit', (e) => {
                    const actionUrl = form.getAttribute('action');
                    if (targetSelector && (!actionUrl || actionUrl === '#' || actionUrl === window.location.href)) {
                        e.preventDefault();
                        this.applyFilter(filterId);
                    } else if (!actionUrl || actionUrl === '#') {
                        e.preventDefault();
                        this.applyFilter(filterId);
                    }
                });
            }

            // 4. Reset & Clear Tags Buttons
            if (resetBtn) {
                resetBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.resetFilter(filterId);
                });
            }

            if (clearAllTagsBtn) {
                clearAllTagsBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.resetFilter(filterId);
                });
            }

            // 5. Export Handlers
            if (exportPdfBtn) {
                exportPdfBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const pdfUrl = filterEl.getAttribute('data-pdf-url');
                    this.handleExport(state, pdfUrl, 'pdf');
                });
            }

            if (exportExcelBtn) {
                exportExcelBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const excelUrl = filterEl.getAttribute('data-excel-url');
                    this.handleExport(state, excelUrl, 'excel');
                });
            }

            // Initial active tags calculation
            this.updateActiveTags(state);
        },

        /**
         * Update and render active filter tags/chips bar
         * @param {Object} state
         */
        updateActiveTags: function (state) {
            const { filterEl, form } = state;
            if (!form) return;

            const activeTagsBar = filterEl.querySelector('.custom-filter-active-tags-bar');
            const activeTagsContainer = filterEl.querySelector('.active-tags-container');
            const activeBadgeEl = filterEl.querySelector('.custom-filter-active-count');
            const activeCountNum = activeBadgeEl ? activeBadgeEl.querySelector('.count-num') : null;

            if (!activeTagsContainer && !activeBadgeEl) return;

            const formData = new FormData(form);
            const activeEntries = [];

            formData.forEach((val, key) => {
                const strVal = String(val).trim();
                if (key === '_token' || strVal === '' || strVal.toLowerCase() === 'all' || strVal === '0') {
                    return;
                }

                const inputEl = form.querySelector(`[name="${key}"]`);
                let label = key;
                if (inputEl) {
                    const group = inputEl.closest('.custom-filter-group');
                    const groupLabel = group ? group.querySelector('.custom-filter-label') : null;
                    if (groupLabel) {
                        label = groupLabel.textContent.trim();
                    }
                }

                activeEntries.push({ key, label, value: strVal });
            });

            // Update Badge Count in Header
            if (activeBadgeEl && activeCountNum) {
                if (activeEntries.length > 0) {
                    activeCountNum.textContent = activeEntries.length;
                    activeBadgeEl.style.display = 'inline-flex';
                } else {
                    activeBadgeEl.style.display = 'none';
                }
            }

            // Render Active Chips
            if (activeTagsContainer && activeTagsBar) {
                if (activeEntries.length === 0) {
                    activeTagsBar.style.display = 'none';
                    activeTagsContainer.innerHTML = '';
                } else {
                    activeTagsBar.style.display = 'flex';
                    activeTagsContainer.innerHTML = '';

                    activeEntries.forEach((entry) => {
                        const chip = document.createElement('span');
                        chip.className = 'filter-active-chip';
                        chip.innerHTML = `
                            <span class="chip-key">${this.escapeHtml(entry.label)}:</span>
                            <span class="chip-val">${this.escapeHtml(entry.value)}</span>
                            <button type="button" class="chip-remove-btn" title="Hapus filter ini" data-remove-key="${entry.key}">
                                <i class="fas fa-times"></i>
                            </button>
                        `;

                        const removeBtn = chip.querySelector('.chip-remove-btn');
                        removeBtn.addEventListener('click', () => {
                            this.removeSingleFilter(state, entry.key);
                        });

                        activeTagsContainer.appendChild(chip);
                    });
                }
            }
        },

        /**
         * Remove single filter by key name
         * @param {Object} state
         * @param {string} key
         */
        removeSingleFilter: function (state, key) {
            const { form, filterId } = state;
            if (!form) return;

            const inputs = form.querySelectorAll(`[name="${key}"]`);
            inputs.forEach((input) => {
                if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                } else if (input.type === 'checkbox' || input.type === 'radio') {
                    input.checked = false;
                } else {
                    input.value = '';
                }
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });

            // If it belongs to a preset pill
            const presetPills = state.filterEl.querySelectorAll(`[data-preset-name="${key}"]`);
            presetPills.forEach((p) => {
                if (p.getAttribute('data-preset-value') === 'all' || p.getAttribute('data-preset-value') === '') {
                    p.classList.add('is-active');
                } else {
                    p.classList.remove('is-active');
                }
            });

            this.updateActiveTags(state);
            this.applyFilter(filterId);

            state.filterEl.dispatchEvent(new CustomEvent('filter:badge-remove', {
                bubbles: true,
                detail: { key, filterId }
            }));
        },

        /**
         * Reset all filters in instance
         * @param {string} filterId
         */
        resetFilter: function (filterId) {
            const state = this.instances.get(filterId);
            if (!state || !state.form) return;

            state.form.reset();

            state.form.querySelectorAll('input[type="text"], input[type="date"], input[type="search"]').forEach((input) => {
                input.value = '';
            });

            state.form.querySelectorAll('select').forEach((sel) => {
                sel.selectedIndex = 0;
            });

            state.filterEl.querySelectorAll('.custom-filter-preset-pill').forEach((pill) => {
                const val = pill.getAttribute('data-preset-value');
                if (val === 'all' || val === '') {
                    pill.classList.add('is-active');
                } else {
                    pill.classList.remove('is-active');
                }
            });

            this.updateActiveTags(state);
            this.applyFilter(filterId);

            state.filterEl.dispatchEvent(new CustomEvent('filter:reset', {
                bubbles: true,
                detail: { filterId }
            }));
        },

        /**
         * Apply filters (triggers client filtering or dispatches filter:apply event)
         * @param {string} filterId
         */
        applyFilter: function (filterId) {
            const state = this.instances.get(filterId);
            if (!state) return;

            const { form, targetSelector, filterEl } = state;
            const formData = form ? new FormData(form) : new FormData();
            const criteria = {};

            formData.forEach((val, key) => {
                if (key !== '_token' && String(val).trim() !== '') {
                    criteria[key] = String(val).trim().toLowerCase();
                }
            });

            if (targetSelector) {
                this.executeClientFilter(state, criteria);
            }

            const eventDetail = { filterId, criteria, formData };
            filterEl.dispatchEvent(new CustomEvent('filter:apply', { bubbles: true, detail: eventDetail }));
            document.dispatchEvent(new CustomEvent('filter:change', { bubbles: true, detail: eventDetail }));
        },

        /**
         * Client-side Table/Card Live Filter Evaluator
         * @param {Object} state
         * @param {Object} criteria
         */
        executeClientFilter: function (state, criteria) {
            const { targetSelector, emptySelector, countSelector } = state;
            if (!targetSelector) return;

            const items = document.querySelectorAll(targetSelector);
            let visibleCount = 0;

            items.forEach((item) => {
                let matches = true;

                for (const [key, filterVal] of Object.entries(criteria)) {
                    if (filterVal === 'all') continue;

                    const attrMatch = item.getAttribute(`data-filter-${key}`) || item.getAttribute(`data-${key}`);
                    if (attrMatch) {
                        if (!attrMatch.toLowerCase().includes(filterVal)) {
                            matches = false;
                            break;
                        }
                    } else {
                        const itemText = item.textContent.toLowerCase();
                        if (!itemText.includes(filterVal)) {
                            matches = false;
                            break;
                        }
                    }
                }

                if (matches) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            if (emptySelector) {
                const emptyEl = document.querySelector(emptySelector);
                if (emptyEl) {
                    emptyEl.style.display = visibleCount === 0 ? '' : 'none';
                }
            }

            if (countSelector) {
                const countEl = document.querySelector(countSelector);
                if (countEl) {
                    countEl.textContent = `${visibleCount} data ditemukan`;
                }
            }
        },

        /**
         * Handle PDF and Excel export with current filter parameters
         * @param {Object} state
         * @param {string} url
         * @param {string} type
         */
        handleExport: function (state, url, type) {
            if (!url) return;
            const { form } = state;
            const params = new URLSearchParams();

            if (form) {
                const formData = new FormData(form);
                formData.forEach((val, key) => {
                    if (key !== '_token' && String(val).trim() !== '') {
                        params.append(key, val);
                    }
                });
            }

            const separator = url.includes('?') ? '&' : '?';
            const targetUrl = params.toString() ? `${url}${separator}${params.toString()}` : url;

            window.open(targetUrl, '_blank');
        },

        /* Smooth Animation Helpers */
        slideUp: function (target, duration = 250) {
            target.style.transitionProperty = 'height, margin, padding, opacity';
            target.style.transitionDuration = duration + 'ms';
            target.style.boxSizing = 'border-box';
            target.style.height = target.offsetHeight + 'px';
            target.offsetHeight; // reflow
            target.style.overflow = 'hidden';
            target.style.height = 0;
            target.style.paddingTop = 0;
            target.style.paddingBottom = 0;
            target.style.marginTop = 0;
            target.style.marginBottom = 0;
            target.style.opacity = 0;

            window.setTimeout(() => {
                target.style.display = 'none';
                target.style.removeProperty('height');
                target.style.removeProperty('padding-top');
                target.style.removeProperty('padding-bottom');
                target.style.removeProperty('margin-top');
                target.style.removeProperty('margin-bottom');
                target.style.removeProperty('overflow');
                target.style.removeProperty('transition-duration');
                target.style.removeProperty('transition-property');
                target.style.removeProperty('opacity');
            }, duration);
        },

        slideDown: function (target, duration = 250) {
            target.style.removeProperty('display');
            let display = window.getComputedStyle(target).display;
            if (display === 'none') display = 'block';
            target.style.display = display;

            const height = target.offsetHeight;
            target.style.overflow = 'hidden';
            target.style.height = 0;
            target.style.paddingTop = 0;
            target.style.paddingBottom = 0;
            target.style.marginTop = 0;
            target.style.marginBottom = 0;
            target.style.opacity = 0;
            target.offsetHeight; // reflow

            target.style.transitionProperty = 'height, margin, padding, opacity';
            target.style.transitionDuration = duration + 'ms';
            target.style.height = height + 'px';
            target.style.removeProperty('padding-top');
            target.style.removeProperty('padding-bottom');
            target.style.removeProperty('margin-top');
            target.style.removeProperty('margin-bottom');
            target.style.opacity = 1;

            window.setTimeout(() => {
                target.style.removeProperty('height');
                target.style.removeProperty('overflow');
                target.style.removeProperty('transition-duration');
                target.style.removeProperty('transition-property');
                target.style.removeProperty('opacity');
            }, duration);
        },

        escapeHtml: function (str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => CustomFilter.init());
    } else {
        CustomFilter.init();
    }

    document.addEventListener('turbo:load', () => CustomFilter.init());
    document.addEventListener('turbolinks:load', () => CustomFilter.init());

    global.CustomFilter = CustomFilter;
})(typeof window !== 'undefined' ? window : this);
