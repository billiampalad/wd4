/**
 * ============================================================================
 * Custom Search Component JS
 * Supports Client-side Table/Card Live Filtering, Real-time Text Highlighting,
 * Debounce, and Clear Button.
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
        },

        /**
         * Bind single search element events
         * @param {HTMLElement} wrapper
         */
        bindSearch: function (wrapper) {
            if (!wrapper || wrapper._customSearchInitialized) return;
            wrapper._customSearchInitialized = true;

            const input = wrapper.querySelector('.custom-search-input');
            const clearBtn = wrapper.querySelector('.custom-search-clear');
            const targetSelector = wrapper.getAttribute('data-search-target');
            const emptySelector = wrapper.getAttribute('data-search-empty');
            const querySpanSelector = wrapper.getAttribute('data-search-query-span');
            const countTargetSelector = wrapper.getAttribute('data-search-count-target');
            const enableHighlight = wrapper.getAttribute('data-search-highlight') !== 'false';
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

                // Perform filtering on targetSelector or auto fallback if mainContent exists
                const hasExplicitTarget = Boolean(targetSelector);
                const isDashboard = Boolean(document.getElementById('mainContent')?.classList.contains('unitdash'));
                
                if (hasExplicitTarget || !isDashboard) {
                    this.executeClientFilter({
                        wrapper,
                        input,
                        query,
                        targetSelector,
                        emptySelector,
                        querySpanSelector,
                        countTargetSelector,
                        enableHighlight,
                    });
                }

                // Dispatch custom event for external listeners
                wrapper.dispatchEvent(
                    new CustomEvent('search:input', {
                        bubbles: true,
                        detail: { query: input.value, searchId: wrapper.getAttribute('data-search-id') },
                    })
                );

                // Document-level event for paginav and global listeners
                document.dispatchEvent(
                    new CustomEvent('search:filter', {
                        bubbles: true,
                        detail: { query: input.value, searchId: wrapper.getAttribute('data-search-id') },
                    })
                );

                // Global events for dashboard synchronization
                window.dispatchEvent(
                    new CustomEvent('unit-dashboard-global-search', {
                        detail: query,
                    })
                );
                window.dispatchEvent(
                    new CustomEvent('pimpinan-global-search', {
                        detail: query,
                    })
                );
            };

            // Input Listener with Debounce
            input.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(handleFilter, debounceMs);
            });

            // Keydown Listener (Escape to clear)
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.clear(wrapper);
                    input.blur();
                }
            });

            // Clear Button Click
            if (clearBtn) {
                clearBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
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
         * Escape special RegExp characters
         */
        escapeRegExp: function (string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        },

        /**
         * Get target elements by selector or smart auto fallback
         * @param {string|null} targetSelector
         * @returns {NodeList|Array}
         */
        getEffectiveTargets: function (targetSelector) {
            if (targetSelector) {
                return document.querySelectorAll(targetSelector);
            }
            const fallbackSelectors = [
                '#mainContent [data-kerjasama-row]',
                '#mainContent .dk-table tbody tr.um-row',
                '#mainContent .dk-table tbody tr.dk-row',
                '#mainContent .um-table tbody tr.um-row',
                '#mainContent .mitra-table tbody tr',
                '#mainContent .table-responsive tbody tr:not([data-empty]):not(.empty-state-row)',
                '#mainContent table.ud-table tbody tr',
                '#mainContent table tbody tr:not([data-empty]):not(.empty-state-row):not(.um-search-empty)',
                // Fallbacks without #mainContent (e.g. Admin layout & views)
                '.main-content [data-kerjasama-row]',
                '.main-content .dk-table tbody tr.um-row',
                '.main-content .dk-table tbody tr.dk-row',
                '.main-content .um-table tbody tr.um-row',
                '.main-content .mitra-table tbody tr',
                '.main-content .table-responsive tbody tr:not([data-empty]):not(.empty-state-row)',
                '.main-content table.ud-table tbody tr',
                '.main-content table tbody tr:not([data-empty]):not(.empty-state-row):not(.um-search-empty)',
                'main .um-table tbody tr.um-row',
                'main .dk-table tbody tr',
                'main .mitra-table tbody tr',
                'main .table-responsive tbody tr:not([data-empty]):not(.empty-state-row)',
                'main table tbody tr:not([data-empty]):not(.empty-state-row):not(.um-search-empty)',
                '.um-table tbody tr.um-row',
                '.dk-table tbody tr.dk-row',
                '.table-responsive tbody tr:not([data-empty]):not(.empty-state-row)'
            ];

            for (let i = 0; i < fallbackSelectors.length; i++) {
                const found = document.querySelectorAll(fallbackSelectors[i]);
                if (found && found.length > 0) {
                    return found;
                }
            }
            return [];
        },

        /**
         * Remove all active highlights inside container
         * @param {HTMLElement|Document} container
         */
        removeHighlights: function (container = document) {
            if (!container) return;
            const marks = container.querySelectorAll('mark.search-highlight');
            marks.forEach((mark) => {
                const parent = mark.parentNode;
                if (parent) {
                    mark.replaceWith(...mark.childNodes);
                    parent.normalize();
                }
            });
            if (typeof container.normalize === 'function') {
                container.normalize();
            }
        },

        /**
         * Public method to highlight matching text inside container
         * @param {HTMLElement} container
         * @param {string} query
         */
        highlight: function (container, query) {
            if (!container) return;
            this.removeHighlights(container);
            const rawQuery = String(query || '').trim();
            if (!rawQuery) return;
            const regex = new RegExp(this.escapeRegExp(rawQuery), 'gi');
            this.applyHighlightToNode(container, regex);
        },

        /**
         * Safely apply text highlight to text nodes inside element
         * @param {Node} node
         * @param {RegExp} regex
         */
        applyHighlightToNode: function (node, regex) {
            if (!node || !regex) return;

            // Skip non-text or excluded elements (buttons, actions, svgs, icons)
            if (node.nodeType === Node.ELEMENT_NODE) {
                const tagName = (node.tagName || '').toLowerCase();
                const classList = node.classList;
                if (
                    tagName === 'button' ||
                    tagName === 'svg' ||
                    tagName === 'i' ||
                    tagName === 'script' ||
                    tagName === 'style' ||
                    tagName === 'select' ||
                    tagName === 'option' ||
                    tagName === 'input' ||
                    tagName === 'textarea' ||
                    (classList && (
                        classList.contains('actions') ||
                        classList.contains('btn-action') ||
                        classList.contains('um-actions') ||
                        classList.contains('search-highlight') ||
                        classList.contains('custom-search-wrapper') ||
                        classList.contains('custom-search-inner')
                    )) ||
                    node.hasAttribute('data-no-highlight')
                ) {
                    return;
                }
            }

            if (node.nodeType === Node.TEXT_NODE) {
                const text = node.nodeValue;
                if (!text || text.trim().length === 0) return;

                regex.lastIndex = 0;
                if (!regex.test(text)) return;

                const fragment = document.createDocumentFragment();
                let lastIndex = 0;
                regex.lastIndex = 0;
                let match;

                while ((match = regex.exec(text)) !== null) {
                    // Append text before match
                    if (match.index > lastIndex) {
                        fragment.appendChild(document.createTextNode(text.slice(lastIndex, match.index)));
                    }

                    // Create highlight mark
                    const mark = document.createElement('mark');
                    mark.className = 'search-highlight';
                    mark.textContent = match[0];
                    fragment.appendChild(mark);

                    lastIndex = regex.lastIndex;
                }

                // Append remaining text after last match
                if (lastIndex < text.length) {
                    fragment.appendChild(document.createTextNode(text.slice(lastIndex)));
                }

                regex.lastIndex = 0;
                node.replaceWith(fragment);
            } else if (node.nodeType === Node.ELEMENT_NODE && node.childNodes) {
                // Clone childNodes array because modifying DOM changes live NodeList
                const children = Array.from(node.childNodes);
                children.forEach((child) => this.applyHighlightToNode(child, regex));
            }
        },

        /**
         * Perform filtering and highlighting on matching elements
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
                enableHighlight = true,
            } = params;

            const targets = this.getEffectiveTargets(targetSelector);
            let visibleCount = 0;
            let totalCount = 0;

            // Prepare highlight regex
            const rawQuery = input.value.trim();
            const highlightRegex = (enableHighlight && rawQuery.length > 0)
                ? new RegExp(this.escapeRegExp(rawQuery), 'gi')
                : null;

            targets.forEach((el) => {
                if (el.classList.contains('um-search-empty') || el.classList.contains('empty-state-row') || el.hasAttribute('data-empty')) {
                    return;
                }

                // Clear existing highlights on this row first
                this.removeHighlights(el);

                totalCount++;
                const text = el.textContent.toLowerCase();

                if (!query || text.includes(query)) {
                    el.style.display = '';
                    visibleCount++;

                    // Apply new text highlight if enabled and query exists
                    if (highlightRegex) {
                        this.applyHighlightToNode(el, highlightRegex);
                    }
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
         * Clear search value, remove highlights, and reset filters
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
                input.dispatchEvent(new Event('input', { bubbles: true }));
            }

            if (clearBtn) {
                clearBtn.style.display = 'none';
            }

            // Trigger reset on targets and clear all highlights
            const targetSelector = wrapper.getAttribute('data-search-target');
            const emptySelector = wrapper.getAttribute('data-search-empty');
            const countTargetSelector = wrapper.getAttribute('data-search-count-target');

            const targets = this.getEffectiveTargets(targetSelector);
            targets.forEach((el) => {
                this.removeHighlights(el);
                el.style.display = '';
            });

            if (emptySelector) {
                const emptyEl = document.querySelector(emptySelector) || document.getElementById(emptySelector.replace(/^#/, ''));
                if (emptyEl) emptyEl.style.display = 'none';
            }

            if (countTargetSelector) {
                const countTarget = document.querySelector(countTargetSelector) || document.getElementById(countTargetSelector.replace(/^#/, ''));
                if (countTarget) {
                    countTarget.textContent = targets.length;
                    countTarget.classList.remove('no-results', 'has-results');
                }
            }

            wrapper.dispatchEvent(
                new CustomEvent('search:clear', {
                    bubbles: true,
                    detail: { searchId: wrapper.getAttribute('data-search-id') },
                })
            );

            // Document-level clear for paginav and global listeners
            document.dispatchEvent(
                new CustomEvent('search:filter', {
                    bubbles: true,
                    detail: { query: '', searchId: wrapper.getAttribute('data-search-id') },
                })
            );

            // Global events for dashboard synchronization
            window.dispatchEvent(
                new CustomEvent('unit-dashboard-global-search', {
                    detail: '',
                })
            );
            window.dispatchEvent(
                new CustomEvent('pimpinan-global-search', {
                    detail: '',
                })
            );
        },

        /**
         * Focus search input by ID or element
         * @param {string|HTMLElement} target
         */
        focus: function (target) {
            const wrapper = typeof target === 'string'
                ? document.querySelector(`[data-search-id="${target}"]`) || document.getElementById(target)?.closest('[data-custom-search]')
                : target;

            if (!wrapper) return;

            const input = wrapper.querySelector('.custom-search-input');
            if (input) {
                input.focus();
                input.select();
            }
        },
    };

    // Auto initialization on DOM ready and Turbo events
    document.addEventListener('DOMContentLoaded', () => CustomSearch.init());
    document.addEventListener('turbo:load', () => CustomSearch.init());
    document.addEventListener('turbo:render', () => CustomSearch.init());

    // Expose to global window
    global.CustomSearch = CustomSearch;

})(typeof window !== 'undefined' ? window : this);
