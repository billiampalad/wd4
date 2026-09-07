/**
 * ============================================================================
 * Custom Search Component JS
 * Supports Client-side Table/Card Live Filtering, Debounce,
 * Clear Buttons, Keyboard Shortcuts (Ctrl+K / /), and Turbo Load Support.
 * ============================================================================
 */

(function (global) {
    'use strict';

    const CustomSearch = {
        instances: new Map(),

        /**
         * Initialize all custom search elements within container or document
         * @param {HTMLElement|Document} [container=document]
         */
        init: function (container = document) {
            const searchWrappers = container.querySelectorAll('[data-custom-search]');
            searchWrappers.forEach((wrapper) => {
                this.bindSearch(wrapper);
            });

            this.bindGlobalShortcuts();
        },

        /**
         * Bind single search element events
         * @param {HTMLElement} wrapper
         */
        bindSearch: function (wrapper) {
            if (!wrapper) return;

            const input = wrapper.querySelector('.custom-search-input');
            const clearBtn = wrapper.querySelector('.custom-search-clear');
            const targetSelector = wrapper.getAttribute('data-search-target');
            const emptySelector = wrapper.getAttribute('data-search-empty');
            const querySpanSelector = wrapper.getAttribute('data-search-query-span');
            const countTargetSelector = wrapper.getAttribute('data-search-count-target');
            const debounceMs = parseInt(wrapper.getAttribute('data-search-debounce') || '200', 10);

            if (!input) return;

            let debounceTimer = null;

            // Debounced Filter Handler
            const handleFilter = () => {
                const query = input.value.trim().toLowerCase();

                // Toggle Clear Button & has-value class
                if (query.length > 0) {
                    wrapper.classList.add('has-value');
                    if (clearBtn) clearBtn.style.display = 'inline-flex';
                } else {
                    wrapper.classList.remove('has-value');
                    if (clearBtn) clearBtn.style.display = 'none';
                }

                // If live filtering target is specified
                if (targetSelector) {
                    this.executeClientFilter({
                        wrapper,
                        input,
                        query,
                        targetSelector,
                        emptySelector,
                        querySpanSelector,
                        countTargetSelector,
                    });
                }

                // Dispatch custom event for external listeners
                wrapper.dispatchEvent(
                    new CustomEvent('search:input', {
                        bubbles: true,
                        detail: { query: input.value, searchId: wrapper.getAttribute('data-search-id') },
                    })
                );
            };

            // Input Listener with Debounce
            input.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(handleFilter, debounceMs);
            });

            // Keydown Listener (Escape to clear/blur)
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    if (input.value.trim() !== '') {
                        e.preventDefault();
                        this.clear(wrapper);
                    } else {
                        input.blur();
                    }
                }
            });

            // Clear Button Click
            if (clearBtn) {
                clearBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.clear(wrapper);
                    input.focus();
                });
            }

            // Store instance
            const id = wrapper.getAttribute('data-search-id') || input.id;
            if (id) {
                this.instances.set(id, { wrapper, input, handleFilter });
            }

            // Initial check if input has initial value
            if (input.value.trim().length > 0) {
                handleFilter();
            }
        },

        /**
         * Perform filtering on matching elements
         */
        executeClientFilter: function (params) {
            const {
                wrapper,
                input,
                query,
                targetSelector,
                emptySelector,
                querySpanSelector,
                countTargetSelector,
            } = params;

            const targets = document.querySelectorAll(targetSelector);
            let visibleCount = 0;
            let totalCount = 0;

            targets.forEach((el) => {
                // Ignore empty state elements if they share the same selector
                if (el.classList.contains('um-search-empty') || el.classList.contains('empty-state-row')) {
                    return;
                }

                totalCount++;
                const text = el.textContent.toLowerCase();

                if (!query || text.includes(query)) {
                    el.style.display = '';
                    visibleCount++;
                } else {
                    el.style.display = 'none';
                }
            });

            // Update Empty State Element
            if (emptySelector) {
                const emptyEl = document.querySelector(emptySelector) || document.getElementById(emptySelector.replace(/^#/, ''));
                if (emptyEl) {
                    if (visibleCount === 0 && query !== '' && totalCount > 0) {
                        emptyEl.style.display = '';
                    } else {
                        emptyEl.style.display = 'none';
                    }
                }
            }

            // Update Query Text Span
            if (querySpanSelector) {
                const querySpan = document.querySelector(querySpanSelector) || document.getElementById(querySpanSelector.replace(/^#/, ''));
                if (querySpan) {
                    querySpan.textContent = input.value.trim();
                }
            }

            // Update Count Display
            if (countTargetSelector) {
                const countTarget = document.querySelector(countTargetSelector) || document.getElementById(countTargetSelector.replace(/^#/, ''));
                if (countTarget) {
                    countTarget.textContent = visibleCount;
                    if (visibleCount === 0 && query !== '') {
                        countTarget.classList.add('no-results');
                        countTarget.classList.remove('has-results');
                    } else {
                        countTarget.classList.remove('no-results');
                        if (query !== '') countTarget.classList.add('has-results');
                    }
                }
            }

            // Dispatch filter complete event
            wrapper.dispatchEvent(
                new CustomEvent('search:filter', {
                    bubbles: true,
                    detail: {
                        query,
                        visibleCount,
                        totalCount,
                        searchId: wrapper.getAttribute('data-search-id'),
                    },
                })
            );
        },

        /**
         * Clear search value and reset filters
         * @param {HTMLElement|string} target
         */
        clear: function (target) {
            const wrapper = typeof target === 'string'
                ? document.querySelector(`[data-search-id="${target}"]`) || document.getElementById(target)?.closest('[data-custom-search]')
                : target;

            if (!wrapper) return;

            const input = wrapper.querySelector('.custom-search-input');
            const clearBtn = wrapper.querySelector('.custom-search-clear');

            if (input) {
                input.value = '';
                wrapper.classList.remove('has-value');
            }

            if (clearBtn) {
                clearBtn.style.display = 'none';
            }

            // Trigger reset on targets
            const targetSelector = wrapper.getAttribute('data-search-target');
            const emptySelector = wrapper.getAttribute('data-search-empty');
            const countTargetSelector = wrapper.getAttribute('data-search-count-target');

            if (targetSelector) {
                const targets = document.querySelectorAll(targetSelector);
                targets.forEach((el) => {
                    el.style.display = '';
                });
            }

            if (emptySelector) {
                const emptyEl = document.querySelector(emptySelector) || document.getElementById(emptySelector.replace(/^#/, ''));
                if (emptyEl) emptyEl.style.display = 'none';
            }

            if (countTargetSelector) {
                const countTarget = document.querySelector(countTargetSelector) || document.getElementById(countTargetSelector.replace(/^#/, ''));
                if (countTarget) {
                    countTarget.textContent = targetSelector ? document.querySelectorAll(targetSelector).length : '';
                    countTarget.classList.remove('no-results', 'has-results');
                }
            }

            wrapper.dispatchEvent(
                new CustomEvent('search:clear', {
                    bubbles: true,
                    detail: { searchId: wrapper.getAttribute('data-search-id') },
                })
            );
        },

        /**
         * Focus search input by ID or element
         * @param {string|HTMLElement} target
         */
        focus: function (target) {
            const input = typeof target === 'string'
                ? document.getElementById(target) || document.querySelector(`[data-search-id="${target}"] .custom-search-input`)
                : target.querySelector('.custom-search-input');

            if (input) {
                input.focus();
                input.select();
            }
        },

        /**
         * Set loading spinner state
         * @param {HTMLElement|string} target
         * @param {boolean} isLoading
         */
        setLoading: function (target, isLoading = true) {
            const wrapper = typeof target === 'string'
                ? document.querySelector(`[data-search-id="${target}"]`) || document.getElementById(target)?.closest('[data-custom-search]')
                : target;

            if (!wrapper) return;

            if (isLoading) {
                wrapper.classList.add('is-loading');
            } else {
                wrapper.classList.remove('is-loading');
            }
        },

        /**
         * Setup Global Keyboard Shortcuts (Ctrl+K, Cmd+K, /)
         */
        bindGlobalShortcuts: function () {
            if (this._shortcutsBound) return;
            this._shortcutsBound = true;

            document.addEventListener('keydown', (e) => {
                // Don't trigger if user is actively typing in input / textarea / contenteditable
                const activeTag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
                const isEditable = activeTag === 'input' || activeTag === 'textarea' || document.activeElement.isContentEditable;

                // Check for Ctrl+K or Cmd+K
                if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
                    const searchInput = document.querySelector('[data-custom-search] .custom-search-input');
                    if (searchInput) {
                        e.preventDefault();
                        searchInput.focus();
                        searchInput.select();
                    }
                    return;
                }

                // Check for '/' shortcut when not in input
                if (e.key === '/' && !isEditable) {
                    const shortcutSearch = document.querySelector('[data-search-shortcut] .custom-search-input') || document.querySelector('[data-custom-search] .custom-search-input');
                    if (shortcutSearch) {
                        e.preventDefault();
                        shortcutSearch.focus();
                        shortcutSearch.select();
                    }
                }
            });
        },
    };

    // Auto initialization on DOM ready and Turbo events
    document.addEventListener('DOMContentLoaded', () => CustomSearch.init());
    document.addEventListener('turbo:load', () => CustomSearch.init());
    document.addEventListener('turbo:render', () => CustomSearch.init());

    // Expose to global window
    global.CustomSearch = CustomSearch;

})(typeof window !== 'undefined' ? window : this);
