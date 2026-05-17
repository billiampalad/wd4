function openModal(id, nama, mitra, nomou, periode, statusLabel, statusClass) {
    document.getElementById('modal-title').textContent = nama;
    document.getElementById('modal-mitra').textContent = mitra;
    document.getElementById('modal-nomou').textContent = nomou;
    document.getElementById('modal-periode').textContent = periode;

    const badge = document.getElementById('modal-status-badge');
    badge.textContent = statusLabel;
    badge.className = 'status-pill ' + statusClass;

    document.getElementById('detailModal').classList.add('open');
}

function closeModal(event) {
    if (event.target === document.getElementById('detailModal')) {
        document.getElementById('detailModal').classList.remove('open');
    }
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.getElementById('detailModal').classList.remove('open');
    }
});

(function () {
    const navLinks = Array.from(document.querySelectorAll('[data-nav-link][href^="#"]'));
    const sections = Array.from(new Set(navLinks
        .map(function (link) {
            const id = link.getAttribute('href');
            return id && id.length > 1 ? document.querySelector(id) : null;
        })
        .filter(Boolean)));
    const mobileSidebar = document.getElementById('mobileSidebar');
    const mobileMenuToggle = document.querySelector('[data-mobile-menu-toggle]');
    const mobileMenuOverlay = document.querySelector('[data-mobile-menu-overlay]');
    const mobileMenuCloseButtons = Array.from(document.querySelectorAll('[data-mobile-menu-close]'));

    if (!navLinks.length || !sections.length) {
        return;
    }

    let pendingNavTarget = null;
    let navScrollTimer = null;

    function setActiveNav(id) {
        navLinks.forEach(function (link) {
            const isActive = link.getAttribute('href') === '#' + id;
            link.classList.toggle('is-active', isActive);

            if (isActive) {
                link.setAttribute('aria-current', 'page');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    }

    function closeMobileMenu() {
        if (!mobileSidebar || !mobileMenuOverlay || !mobileMenuToggle) {
            return;
        }

        mobileSidebar.classList.remove('is-open');
        mobileMenuOverlay.classList.remove('is-open');
        document.body.classList.remove('mobile-sidebar-open');
        mobileSidebar.setAttribute('aria-hidden', 'true');
        mobileMenuToggle.setAttribute('aria-expanded', 'false');
    }

    function openMobileMenu() {
        if (!mobileSidebar || !mobileMenuOverlay || !mobileMenuToggle) {
            return;
        }

        mobileSidebar.classList.add('is-open');
        mobileMenuOverlay.classList.add('is-open');
        document.body.classList.add('mobile-sidebar-open');
        mobileSidebar.setAttribute('aria-hidden', 'false');
        mobileMenuToggle.setAttribute('aria-expanded', 'true');
    }

    if (mobileMenuToggle && mobileSidebar && mobileMenuOverlay) {
        mobileMenuToggle.addEventListener('click', function () {
            if (mobileSidebar.classList.contains('is-open')) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });

        mobileMenuOverlay.addEventListener('click', closeMobileMenu);
        mobileMenuCloseButtons.forEach(function (button) {
            button.addEventListener('click', closeMobileMenu);
        });
    }

    function getNavHeight() {
        const nav = document.querySelector('.top-nav');
        return nav ? Math.ceil(nav.getBoundingClientRect().height) : 0;
    }

    function scrollToSection(section) {
        const sectionTop = section.getBoundingClientRect().top + window.pageYOffset;
        const targetTop = Math.max(sectionTop - getNavHeight(), 0);

        window.scrollTo({
            top: targetTop,
            behavior: 'smooth',
        });
    }

    function lockActiveNav(targetId) {
        pendingNavTarget = targetId;
        window.clearTimeout(navScrollTimer);

        navScrollTimer = window.setTimeout(function () {
            setActiveNav(targetId);
            pendingNavTarget = null;
        }, 180);
    }

    navLinks.forEach(function (link) {
        link.addEventListener('click', function (event) {
            const targetId = link.getAttribute('href').slice(1);
            const targetSection = document.getElementById(targetId);

            if (!targetSection) {
                return;
            }

            event.preventDefault();
            setActiveNav(targetId);
            lockActiveNav(targetId);
            scrollToSection(targetSection);
            closeMobileMenu();
            window.history.pushState(null, '', '#' + targetId);
        });
    });

    window.addEventListener('scrollend', function () {
        if (pendingNavTarget) {
            setActiveNav(pendingNavTarget);
            pendingNavTarget = null;
            window.clearTimeout(navScrollTimer);
        }
    });

    window.addEventListener('scroll', function () {
        if (pendingNavTarget) {
            lockActiveNav(pendingNavTarget);
        }
    }, { passive: true });

    window.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeMobileMenu();
        }
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 900) {
            closeMobileMenu();
        }
    });

    const observer = new IntersectionObserver(function (entries) {
        if (pendingNavTarget) {
            return;
        }

        const visibleEntry = entries
            .filter(function (entry) {
                return entry.isIntersecting;
            })
            .sort(function (a, b) {
                return b.intersectionRatio - a.intersectionRatio;
            })[0];

        if (visibleEntry) {
            setActiveNav(visibleEntry.target.id);
        }
    }, {
        rootMargin: '-38% 0px -48% 0px',
        threshold: [0.08, 0.18, 0.32, 0.5],
    });

    sections.forEach(function (section) {
        observer.observe(section);
    });

    if (window.location.hash) {
        const initialSection = document.querySelector(window.location.hash);

        if (initialSection) {
            setActiveNav(initialSection.id);
        }
    }
})();

(function () {
    const themeToggle = document.querySelector('[data-theme-toggle]');
    const themeLabel = themeToggle ? themeToggle.querySelector('[data-theme-toggle-label]') : null;
    const storageKey = 'welcome-theme';

    function applyTheme(theme) {
        const isDark = theme === 'dark';
        document.documentElement.dataset.theme = isDark ? 'dark' : 'light';

        if (themeToggle) {
            themeToggle.setAttribute('aria-pressed', String(isDark));
            themeToggle.setAttribute('aria-label', isDark ? 'Ubah ke mode terang' : 'Ubah ke mode gelap');
        }

        if (themeLabel) {
            themeLabel.textContent = isDark ? 'Mode Terang' : 'Mode Gelap';
        }
    }

    function getInitialTheme() {
        const datasetTheme = document.documentElement.dataset.theme;

        if (datasetTheme === 'dark' || datasetTheme === 'light') {
            return datasetTheme;
        }

        try {
            const savedTheme = localStorage.getItem(storageKey);

            if (savedTheme === 'dark' || savedTheme === 'light') {
                return savedTheme;
            }
        } catch (error) {
            return 'light';
        }

        return 'light';
    }

    applyTheme(getInitialTheme());

    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';

            try {
                localStorage.setItem(storageKey, nextTheme);
            } catch (error) {
                // Ignore storage failures and still apply the visual theme.
            }

            applyTheme(nextTheme);
        });
    }
})();

(function () {
    const mainSection = document.getElementById('data-kerjasama');

    if (!mainSection) {
        return;
    }

    let activeController = null;
    let searchDebounceTimer = null;
    const searchDebounceDelay = 320;

    function getMainContent(section = mainSection) {
        return section.querySelector('.main-wrap') || section;
    }

    function syncFilterState(form) {
        if (!form) {
            return;
        }

        form.querySelectorAll('.filter-option').forEach(function (option) {
            const input = option.querySelector('input[name="kategori_mitra"]');
            option.classList.toggle('is-active', Boolean(input && input.checked));
        });
    }

    function syncSearchResetState(form) {
        if (!form) {
            return;
        }

        const searchInput = form.querySelector('input[name="search"]');
        const resetButton = form.querySelector('[data-search-reset]');

        if (!searchInput || !resetButton) {
            return;
        }

        resetButton.hidden = searchInput.value.trim() === '';
    }

    function cancelPendingSearchDebounce() {
        if (searchDebounceTimer) {
            window.clearTimeout(searchDebounceTimer);
            searchDebounceTimer = null;
        }
    }

    function scheduleSearchLoad(form) {
        if (!form) {
            return;
        }

        cancelPendingSearchDebounce();
        searchDebounceTimer = window.setTimeout(function () {
            searchDebounceTimer = null;
            syncFilterState(form);
            syncSearchResetState(form);
            loadKerjasamaSection(buildRequestUrl(form));
        }, searchDebounceDelay);
    }

    function syncFilterStateFromUrl(url) {
        const parsedUrl = new URL(url, window.location.origin);
        const kategoriMitra = parsedUrl.searchParams.get('kategori_mitra') || 'all';
        const sort = parsedUrl.searchParams.get('sort') || 'latest';
        const form = mainSection.querySelector('[data-landing-filter]');

        if (!form) {
            return;
        }

        const nextInput = form.querySelector('input[name="kategori_mitra"][value="' + kategoriMitra + '"]');

        if (nextInput) {
            nextInput.checked = true;
            syncFilterState(form);
        }

        const searchInput = form.querySelector('input[name="search"]');

        if (searchInput) {
            searchInput.value = parsedUrl.searchParams.get('search') || '';
        }

        const sortSelect = form.querySelector('select[name="sort"]');

        if (sortSelect) {
            sortSelect.value = sort;
        }

        syncSearchResetState(form);
    }

    function buildRequestUrl(form) {
        const url = new URL(form.action || window.location.href, window.location.origin);
        const params = new URLSearchParams(new FormData(form));

        if (!params.get('search')) {
            params.delete('search');
        }

        if ((params.get('kategori_mitra') || 'all') === 'all') {
            params.delete('kategori_mitra');
        }

        if ((params.get('sort') || 'latest') === 'latest') {
            params.delete('sort');
        }

        url.search = params.toString();
        return url.toString();
    }

    function replaceKerjasamaResults(nextMainSection) {
        const currentContent = getMainContent();
        const nextContent = getMainContent(nextMainSection);
        const resultClasses = ['results-overview', 'cards-grid', 'pagination-wrap', 'empty-state'];
        const currentResults = Array.from(currentContent.children).filter(function (element) {
            return resultClasses.some(function (className) {
                return element.classList.contains(className);
            });
        });
        const nextResults = Array.from(nextContent.children).filter(function (element) {
            return resultClasses.some(function (className) {
                return element.classList.contains(className);
            });
        });
        let insertAfter = currentContent.querySelector('.section-top');

        currentResults.forEach(function (element) {
            element.remove();
        });

        nextResults.forEach(function (element) {
            const nextElement = document.importNode(element, true);
            nextElement.classList.add('result-refresh-enter');
            insertAfter.after(nextElement);
            insertAfter = nextElement;
        });
    }

    async function loadKerjasamaSection(url, options = {}) {
        const { pushState = true } = options;

        if (activeController) {
            activeController.abort();
        }

        const controller = new AbortController();
        activeController = controller;
        mainSection.classList.add('is-loading');
        mainSection.setAttribute('aria-busy', 'true');

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: controller.signal,
            });

            if (!response.ok) {
                throw new Error('Failed to load landing data');
            }

            const html = await response.text();
            const parsed = new DOMParser().parseFromString(html, 'text/html');
            const nextMainSection = parsed.getElementById('data-kerjasama');

            if (!nextMainSection) {
                throw new Error('Landing section not found in response');
            }

            replaceKerjasamaResults(nextMainSection);

            if (pushState) {
                window.history.pushState({ landingAsync: true }, '', url);
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                window.location.href = url;
            }
        } finally {
            if (activeController === controller) {
                activeController = null;
                mainSection.classList.remove('is-loading');
                mainSection.removeAttribute('aria-busy');
            }
        }
    }

    document.addEventListener('change', function (event) {
        const filterInput = event.target.closest('[data-landing-filter] input[name="kategori_mitra"], [data-landing-filter] select[name="sort"]');

        if (!filterInput) {
            return;
        }

        cancelPendingSearchDebounce();
        syncFilterState(filterInput.form);
        syncSearchResetState(filterInput.form);
        loadKerjasamaSection(buildRequestUrl(filterInput.form));
    });

    document.addEventListener('input', function (event) {
        const searchInput = event.target.closest('[data-landing-filter] input[name="search"]');

        if (!searchInput) {
            return;
        }

        syncSearchResetState(searchInput.form);
        scheduleSearchLoad(searchInput.form);
    });

    document.addEventListener('submit', function (event) {
        const filterForm = event.target.closest('form[data-landing-filter]');

        if (!filterForm) {
            return;
        }

        event.preventDefault();
        cancelPendingSearchDebounce();
        syncFilterState(filterForm);
        syncSearchResetState(filterForm);
        loadKerjasamaSection(buildRequestUrl(filterForm));
    });

    document.addEventListener('click', function (event) {
        const paginationLink = event.target.closest('#data-kerjasama .pagination-wrap a');
        const resetSearchButton = event.target.closest('[data-search-reset]');
        const resetFiltersButton = event.target.closest('#data-kerjasama [data-reset-filters]');

        if (resetSearchButton) {
            const filterForm = resetSearchButton.closest('form[data-landing-filter]');

            if (!filterForm) {
                return;
            }

            const searchInput = filterForm.querySelector('input[name="search"]');

            if (searchInput) {
                searchInput.value = '';
            }

            cancelPendingSearchDebounce();
            syncFilterState(filterForm);
            syncSearchResetState(filterForm);
            loadKerjasamaSection(buildRequestUrl(filterForm));
            return;
        }

        if (resetFiltersButton) {
            const filterForm = mainSection.querySelector('form[data-landing-filter]');

            if (!filterForm) {
                return;
            }

            const defaultKategori = filterForm.querySelector('input[name="kategori_mitra"][value="all"]');
            const searchInput = filterForm.querySelector('input[name="search"]');
            const sortSelect = filterForm.querySelector('select[name="sort"]');

            if (defaultKategori) {
                defaultKategori.checked = true;
            }

            if (searchInput) {
                searchInput.value = '';
            }

            if (sortSelect) {
                sortSelect.value = 'latest';
            }

            cancelPendingSearchDebounce();
            syncFilterState(filterForm);
            syncSearchResetState(filterForm);
            loadKerjasamaSection(buildRequestUrl(filterForm));
            return;
        }

        if (!paginationLink || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        event.preventDefault();
        cancelPendingSearchDebounce();
        loadKerjasamaSection(paginationLink.href);
    });

    window.addEventListener('popstate', function () {
        cancelPendingSearchDebounce();
        syncFilterStateFromUrl(window.location.href);
        loadKerjasamaSection(window.location.href, { pushState: false });
    });

    syncFilterStateFromUrl(window.location.href);
})();
