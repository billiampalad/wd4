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
            };

            // Input Listener with Debounce
            input.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(handleFilter, debounceMs);
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
        },

        /**
         * Safely apply text highlight to text nodes inside element
         * @param {Node} node
         * @param {RegExp} regex
         */
        applyHighlightToNode: function (node, regex) {
            if (!node) return;

            // Skip non-text or excluded elements (buttons, actions, svgs, icons)
            if (node.nodeType === Node.ELEMENT_NODE) {
                const tagName = node.tagName.toLowerCase();
                if (
                    tagName === 'button' ||
                    tagName === 'svg' ||
                    tagName === 'i' ||
                    tagName === 'script' ||
                    tagName === 'style' ||
                    tagName === 'select' ||
                    tagName === 'option' ||
                    node.classList.contains('actions') ||
                    node.classList.contains('btn-action') ||
                    node.classList.contains('um-actions') ||
                    node.hasAttribute('data-no-highlight')
                ) {
                    return;
                }
            }

            if (node.nodeType === Node.TEXT_NODE) {
                const text = node.nodeValue;
                if (!text || !regex.test(text)) return;

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

            const targets = document.querySelectorAll(targetSelector);
            let visibleCount = 0;
            let totalCount = 0;

            // Prepare highlight regex
            const rawQuery = input.value.trim();
            const highlightRegex = (enableHighlight && rawQuery.length > 0)
                ? new RegExp(this.escapeRegExp(rawQuery), 'gi')
                : null;

            targets.forEach((el) => {
                if (el.classList.contains('um-search-empty') || el.classList.contains('empty-state-row')) {
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
            }

            if (clearBtn) {
                clearBtn.style.display = 'none';
            }

            // Trigger reset on targets and clear all highlights
            const targetSelector = wrapper.getAttribute('data-search-target');
            const emptySelector = wrapper.getAttribute('data-search-empty');
            const countTargetSelector = wrapper.getAttribute('data-search-count-target');

            if (targetSelector) {
                const targets = document.querySelectorAll(targetSelector);
                targets.forEach((el) => {
                    this.removeHighlights(el);
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
