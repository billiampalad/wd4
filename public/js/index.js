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

        document.dispatchEvent(new CustomEvent('landingthemechange', {
            detail: {
                theme: isDark ? 'dark' : 'light',
            },
        }));
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
    const analyticsSection = document.getElementById('visualisasi-data');
    const mainSection = document.getElementById('data-kerjasama');

    if (!mainSection) {
        return;
    }

    let activeController = null;
    let searchDebounceTimer = null;
    const searchDebounceDelay = 320;
    const statFilterCards = Array.from(document.querySelectorAll('[data-landing-stat]'));

    function getMainContent(section = mainSection) {
        return section.querySelector('.main-wrap') || section;
    }

    function getAnalyticsContent(section = analyticsSection) {
        if (!section) {
            return null;
        }

        return section.querySelector('.analytics-shell') || section;
    }

    function syncFilterState(form) {
        if (!form) {
            return;
        }

        form.querySelectorAll('.filter-option').forEach(function (option) {
            const input = option.querySelector('input');
            option.classList.toggle('is-active', Boolean(input && input.checked));
        });
    }

    function syncScopeDependentFields(form) {
        if (!form) {
            return;
        }

        const selectedScopeInput = form.querySelector('input[name="data_scope"]:checked');
        const selectedScope = selectedScopeInput ? selectedScopeInput.value : 'kerjasama';
        const statusScopeInput = form.querySelector('input[name="status_scope"]');
        const sortSelect = form.querySelector('select[name="sort"]');

        if (selectedScope !== 'kerjasama' && statusScopeInput) {
            statusScopeInput.value = 'all';
        }

        if (!sortSelect) {
            return;
        }

        if (selectedScope === 'mitra' && sortSelect.value === 'ending_soon') {
            sortSelect.value = 'title';
        }

        if (selectedScope === 'kerjasama' && ['title_desc', 'most_cooperations'].includes(sortSelect.value)) {
            sortSelect.value = 'latest';
        }
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
            syncScopeDependentFields(form);
            syncFilterState(form);
            syncSearchResetState(form);
            loadKerjasamaSection(buildRequestUrl(form));
        }, searchDebounceDelay);
    }

    function getNavHeight() {
        const nav = document.querySelector('.top-nav');
        return nav ? Math.ceil(nav.getBoundingClientRect().height) : 0;
    }

    function scrollToMainSection() {
        const sectionTop = mainSection.getBoundingClientRect().top + window.pageYOffset;
        const targetTop = Math.max(sectionTop - getNavHeight(), 0);

        window.scrollTo({
            top: targetTop,
            behavior: 'smooth',
        });
    }

    function parseJsonScript(container, selector, fallback) {
        if (!container) {
            return fallback;
        }

        const script = container.querySelector(selector);

        if (!script) {
            return fallback;
        }

        try {
            return JSON.parse(script.textContent || '');
        } catch (error) {
            return fallback;
        }
    }

    function hexToRgba(hex, alpha) {
        const normalized = String(hex || '').trim().replace('#', '');

        if (normalized.length !== 6) {
            return 'rgba(55, 138, 221, ' + alpha + ')';
        }

        const value = Number.parseInt(normalized, 16);
        const r = (value >> 16) & 255;
        const g = (value >> 8) & 255;
        const b = value & 255;
        return 'rgba(' + r + ', ' + g + ', ' + b + ', ' + alpha + ')';
    }

    function getAnalyticsToneColor(tone) {
        const style = getComputedStyle(document.documentElement);
        const map = {
            success: style.getPropertyValue('--green-500').trim() || '#1D9E75',
            warning: style.getPropertyValue('--amber-500').trim() || '#BA7517',
            danger: '#d14a4a',
            neutral: '#708198',
            violet: '#6e55d9',
            info: style.getPropertyValue('--blue-500').trim() || '#378ADD',
            indigo: '#455fdf',
        };

        return map[tone] || map.info;
    }

    function getAnalyticsTheme() {
        const style = getComputedStyle(document.documentElement);
        const isDark = document.documentElement.dataset.theme === 'dark';

        return {
            text: style.getPropertyValue('--ink-muted').trim() || (isDark ? '#a9b8cc' : '#4a5568'),
            title: style.getPropertyValue('--ink').trim() || (isDark ? '#e7eef8' : '#0d1b2a'),
            faint: style.getPropertyValue('--ink-faint').trim() || (isDark ? '#7f93ad' : '#8a9ab0'),
            surface: style.getPropertyValue('--surface').trim() || (isDark ? '#0f1726' : '#ffffff'),
            grid: isDark ? 'rgba(148, 163, 184, 0.12)' : 'rgba(13, 27, 42, 0.08)',
        };
    }

    function wrapChartLabel(label, limit = 20) {
        const text = String(label || '').trim();

        if (!text) {
            return ['-'];
        }

        if (text.length <= limit) {
            return [text];
        }

        const words = text.split(/\s+/);
        const lines = [];
        let currentLine = '';

        words.forEach(function (word) {
            const nextLine = currentLine ? currentLine + ' ' + word : word;

            if (nextLine.length <= limit || !currentLine) {
                currentLine = nextLine;
                return;
            }

            lines.push(currentLine);
            currentLine = word;
        });

        if (currentLine) {
            lines.push(currentLine);
        }

        return lines.slice(0, 3);
    }

    const doughnutCenterTextPlugin = {
        id: 'landingDoughnutCenterText',
        beforeDraw: function (chart, args, options) {
            if (!options || !options.line1) {
                return;
            }

            const meta = chart.getDatasetMeta(0);

            if (!meta || !meta.data || !meta.data[0]) {
                return;
            }

            const arc = meta.data[0];
            const ctx = chart.ctx;
            const theme = getAnalyticsTheme();
            const x = arc.x;
            const y = arc.y;

            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = theme.title;
            ctx.font = '700 22px "DM Sans", sans-serif';
            ctx.fillText(options.line1, x, y - 6);
            ctx.fillStyle = theme.faint;
            ctx.font = '500 11px "DM Sans", sans-serif';
            ctx.fillText(options.line2 || '', x, y + 14);
            ctx.restore();
        },
    };

    function createAnalyticsChart(canvas, config) {
        if (!canvas || typeof Chart === 'undefined') {
            return null;
        }

        if (canvas._landingChart) {
            canvas._landingChart.destroy();
        }

        canvas._landingChart = new Chart(canvas, config);
        return canvas._landingChart;
    }

    function animateAnalyticsCards(section = analyticsSection) {
        const shell = getAnalyticsContent(section);

        if (!shell) {
            return;
        }

        shell.querySelectorAll('[data-analytics-card]').forEach(function (card, index) {
            card.classList.remove('is-visible');
            card.style.setProperty('--analytics-delay', String(index * 70) + 'ms');

            window.requestAnimationFrame(function () {
                card.classList.add('is-visible');
            });
        });
    }

    function initLandingAnalyticsSection(section = analyticsSection) {
        const shell = getAnalyticsContent(section);

        if (!shell) {
            return;
        }

        if (typeof Chart === 'undefined') {
            animateAnalyticsCards(section);
            return;
        }

        const payload = parseJsonScript(shell, '[data-analytics-payload]', {});
        const theme = getAnalyticsTheme();
        const commonAnimation = {
            duration: 900,
            easing: 'easeOutCubic',
        };

        const tooltipCallbacks = {
            label: function (context) {
                const value = Number(context.raw || 0);
                return context.label + ': ' + value.toLocaleString('id-ID');
            },
        };

        const statusCanvas = shell.querySelector('[data-analytics-chart="status"]');
        const trendCanvas = shell.querySelector('[data-analytics-chart="trend"]');
        const mitraCanvas = shell.querySelector('[data-analytics-chart="mitra"]');
        const classificationCanvas = shell.querySelector('[data-analytics-chart="classifications"]');
        const fieldsCanvas = shell.querySelector('[data-analytics-chart="fields"]');

        if (statusCanvas && payload.status && Array.isArray(payload.status.values) && payload.status.values.length) {
            createAnalyticsChart(statusCanvas, {
                type: 'doughnut',
                data: {
                    labels: payload.status.labels,
                    datasets: [{
                        data: payload.status.values,
                        backgroundColor: (payload.status.tones || []).map(getAnalyticsToneColor),
                        borderColor: theme.surface,
                        borderWidth: 4,
                        hoverOffset: 8,
                    }],
                },
                plugins: [doughnutCenterTextPlugin],
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    animation: commonAnimation,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            callbacks: tooltipCallbacks,
                        },
                        landingDoughnutCenterText: {
                            line1: Number(payload.status.total || 0).toLocaleString('id-ID'),
                            line2: 'Portofolio',
                        },
                    },
                },
            });
        }

        if (mitraCanvas && payload.mitra && Array.isArray(payload.mitra.values) && payload.mitra.values.length) {
            createAnalyticsChart(mitraCanvas, {
                type: 'doughnut',
                data: {
                    labels: payload.mitra.labels,
                    datasets: [{
                        data: payload.mitra.values,
                        backgroundColor: (payload.mitra.tones || []).map(getAnalyticsToneColor),
                        borderColor: theme.surface,
                        borderWidth: 4,
                        hoverOffset: 8,
                    }],
                },
                plugins: [doughnutCenterTextPlugin],
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '74%',
                    animation: commonAnimation,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            callbacks: tooltipCallbacks,
                        },
                        landingDoughnutCenterText: {
                            line1: Number(payload.mitra.total || 0).toLocaleString('id-ID'),
                            line2: 'Mitra',
                        },
                    },
                },
            });
        }

        if (trendCanvas && payload.trend && Array.isArray(payload.trend.values) && payload.trend.values.length) {
            createAnalyticsChart(trendCanvas, {
                type: 'bar',
                data: {
                    labels: payload.trend.labels,
                    datasets: [{
                        data: payload.trend.values,
                        borderRadius: 18,
                        borderSkipped: false,
                        backgroundColor: payload.trend.values.map(function (_, index) {
                            return index === payload.trend.values.length - 1
                                ? getAnalyticsToneColor('success')
                                : getAnalyticsToneColor('info');
                        }),
                        hoverBackgroundColor: payload.trend.values.map(function (_, index) {
                            return index === payload.trend.values.length - 1
                                ? hexToRgba(getAnalyticsToneColor('success'), 0.82)
                                : hexToRgba(getAnalyticsToneColor('info'), 0.82);
                        }),
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: commonAnimation,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            callbacks: tooltipCallbacks,
                        },
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: theme.text,
                                font: {
                                    size: 11,
                                    weight: '700',
                                },
                            },
                            grid: {
                                display: false,
                            },
                            border: {
                                display: false,
                            },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                stepSize: 1,
                                color: theme.faint,
                                font: {
                                    size: 10,
                                },
                            },
                            grid: {
                                color: theme.grid,
                                drawBorder: false,
                            },
                            border: {
                                display: false,
                            },
                        },
                    },
                },
            });
        }

        function buildHorizontalBarChart(canvas, labels, values, tone) {
            if (!canvas || !Array.isArray(values) || !values.length) {
                return;
            }

            createAnalyticsChart(canvas, {
                type: 'bar',
                data: {
                    labels: labels.map(function (label) {
                        return wrapChartLabel(label, 22);
                    }),
                    datasets: [{
                        data: values,
                        borderRadius: 999,
                        borderSkipped: false,
                        backgroundColor: values.map(function () {
                            return hexToRgba(getAnalyticsToneColor(tone), 0.88);
                        }),
                        hoverBackgroundColor: values.map(function () {
                            return getAnalyticsToneColor(tone);
                        }),
                        barThickness: 12,
                        maxBarThickness: 12,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: commonAnimation,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return Number(context.raw || 0).toLocaleString('id-ID') + ' data';
                                },
                                title: function (items) {
                                    const index = items[0] ? items[0].dataIndex : 0;
                                    return labels[index] || '';
                                },
                            },
                        },
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                stepSize: 1,
                                color: theme.faint,
                                font: {
                                    size: 10,
                                },
                            },
                            grid: {
                                color: theme.grid,
                                drawBorder: false,
                            },
                            border: {
                                display: false,
                            },
                        },
                        y: {
                            ticks: {
                                color: theme.text,
                                font: {
                                    size: 10,
                                    weight: '700',
                                },
                            },
                            grid: {
                                display: false,
                            },
                            border: {
                                display: false,
                            },
                        },
                    },
                },
            });
        }

        if (classificationCanvas && payload.classifications) {
            buildHorizontalBarChart(
                classificationCanvas,
                payload.classifications.labels || [],
                payload.classifications.values || [],
                'indigo',
            );
        }

        if (fieldsCanvas && payload.fields) {
            buildHorizontalBarChart(
                fieldsCanvas,
                payload.fields.labels || [],
                payload.fields.values || [],
                'success',
            );
        }

        animateAnalyticsCards(section);
    }

    function syncStatCardStateFromUrl(url) {
        const parsedUrl = new URL(url, window.location.origin);
        const scope = parsedUrl.searchParams.get('data_scope') || 'kerjasama';
        const kategori = parsedUrl.searchParams.get('kategori_mitra') || 'all';
        const status = parsedUrl.searchParams.get('status_scope') || 'all';

        statFilterCards.forEach(function (card) {
            const cardScope = card.dataset.statScope || 'kerjasama';
            const cardKategori = card.dataset.statKategori || 'all';
            const cardStatus = card.dataset.statStatus || 'all';
            const isActive = cardScope === scope && cardKategori === kategori && cardStatus === status;

            card.classList.toggle('is-active', isActive);

            if (isActive) {
                card.setAttribute('aria-current', 'true');
            } else {
                card.removeAttribute('aria-current');
            }
        });
    }

    function syncFilterStateFromUrl(url) {
        const parsedUrl = new URL(url, window.location.origin);
        const dataScope = parsedUrl.searchParams.get('data_scope') || 'kerjasama';
        const kategoriMitra = parsedUrl.searchParams.get('kategori_mitra') || 'all';
        const sort = parsedUrl.searchParams.get('sort') || 'latest';
        const statusScope = parsedUrl.searchParams.get('status_scope') || 'all';
        const form = mainSection.querySelector('[data-landing-filter]');

        if (!form) {
            return;
        }

        const nextScopeInput = form.querySelector('input[name="data_scope"][value="' + dataScope + '"]');

        if (nextScopeInput) {
            nextScopeInput.checked = true;
        }

        const nextInput = form.querySelector('input[name="kategori_mitra"][value="' + kategoriMitra + '"]');

        if (nextInput) {
            nextInput.checked = true;
        }

        const searchInput = form.querySelector('input[name="search"]');

        if (searchInput) {
            searchInput.value = parsedUrl.searchParams.get('search') || '';
        }

        const sortSelect = form.querySelector('select[name="sort"]');

        if (sortSelect) {
            sortSelect.value = sort;
        }

        const statusScopeInput = form.querySelector('input[name="status_scope"]');

        if (statusScopeInput) {
            statusScopeInput.value = statusScope;
        }

        syncScopeDependentFields(form);
        syncFilterState(form);
        syncSearchResetState(form);
        syncStatCardStateFromUrl(url);
    }

    function buildRequestUrl(form) {
        const url = new URL(form.action || window.location.href, window.location.origin);
        const params = new URLSearchParams(new FormData(form));

        if (!params.get('search')) {
            params.delete('search');
        }

        if ((params.get('data_scope') || 'kerjasama') === 'kerjasama') {
            params.delete('data_scope');
        }

        if ((params.get('kategori_mitra') || 'all') === 'all') {
            params.delete('kategori_mitra');
        }

        if ((params.get('sort') || 'latest') === 'latest') {
            params.delete('sort');
        }

        if ((params.get('status_scope') || 'all') === 'all') {
            params.delete('status_scope');
        }

        url.search = params.toString();
        return url.toString();
    }

    function replaceLandingResults(nextAnalyticsSection, nextMainSection) {
        const currentMainContent = getMainContent();
        const nextMainContent = getMainContent(nextMainSection);
        currentMainContent.innerHTML = nextMainContent.innerHTML;

        const currentAnalyticsContent = getAnalyticsContent();
        const nextAnalyticsContent = getAnalyticsContent(nextAnalyticsSection);

        if (currentAnalyticsContent && nextAnalyticsContent) {
            currentAnalyticsContent.innerHTML = nextAnalyticsContent.innerHTML;
        }

        document.querySelectorAll('.results-overview, .cards-grid, .pagination-wrap, .empty-state, .analytics-shell').forEach(function (element) {
            element.classList.add('result-refresh-enter');
        });

        initLandingAnalyticsSection();
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

        if (analyticsSection) {
            analyticsSection.classList.add('is-loading');
            analyticsSection.setAttribute('aria-busy', 'true');
        }

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
            const nextAnalyticsSection = parsed.getElementById('visualisasi-data');
            const nextMainSection = parsed.getElementById('data-kerjasama');

            if (!nextMainSection || (analyticsSection && !nextAnalyticsSection)) {
                throw new Error('Landing section not found in response');
            }

            replaceLandingResults(nextAnalyticsSection, nextMainSection);
            syncFilterStateFromUrl(url);

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

                if (analyticsSection) {
                    analyticsSection.classList.remove('is-loading');
                    analyticsSection.removeAttribute('aria-busy');
                }
            }
        }
    }

    document.addEventListener('change', function (event) {
        const filterInput = event.target.closest('[data-landing-filter] input[name="data_scope"], [data-landing-filter] input[name="kategori_mitra"], [data-landing-filter] select[name="sort"]');

        if (!filterInput) {
            return;
        }

        cancelPendingSearchDebounce();
        syncScopeDependentFields(filterInput.form);
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
        syncScopeDependentFields(filterForm);
        syncFilterState(filterForm);
        syncSearchResetState(filterForm);
        loadKerjasamaSection(buildRequestUrl(filterForm));
    });

    document.addEventListener('click', function (event) {
        const paginationLink = event.target.closest('#data-kerjasama .pagination-wrap a');
        const resetSearchButton = event.target.closest('[data-search-reset]');
        const resetFiltersButton = event.target.closest('#data-kerjasama [data-reset-filters]');
        const statCard = event.target.closest('[data-landing-stat]');

        if (statCard) {
            const filterForm = mainSection.querySelector('form[data-landing-filter]');

            if (!filterForm) {
                return;
            }

            event.preventDefault();

            const defaultScope = statCard.dataset.statScope || 'kerjasama';
            const defaultKategori = statCard.dataset.statKategori || 'all';
            const defaultStatus = statCard.dataset.statStatus || 'all';
            const defaultSort = statCard.dataset.statSort || 'latest';
            const scopeInput = filterForm.querySelector('input[name="data_scope"][value="' + defaultScope + '"]');
            const kategoriInput = filterForm.querySelector('input[name="kategori_mitra"][value="' + defaultKategori + '"]');
            const searchInput = filterForm.querySelector('input[name="search"]');
            const statusScopeInput = filterForm.querySelector('input[name="status_scope"]');
            const sortSelect = filterForm.querySelector('select[name="sort"]');

            if (scopeInput) {
                scopeInput.checked = true;
            }

            if (kategoriInput) {
                kategoriInput.checked = true;
            }

            if (searchInput) {
                searchInput.value = '';
            }

            if (statusScopeInput) {
                statusScopeInput.value = defaultStatus;
            }

            if (sortSelect) {
                sortSelect.value = defaultSort;
            }

            cancelPendingSearchDebounce();
            syncScopeDependentFields(filterForm);
            syncFilterState(filterForm);
            syncSearchResetState(filterForm);

            const nextUrl = buildRequestUrl(filterForm);
            syncStatCardStateFromUrl(nextUrl);
            scrollToMainSection();
            loadKerjasamaSection(nextUrl);
            return;
        }

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
            syncScopeDependentFields(filterForm);
            syncFilterState(filterForm);
            syncSearchResetState(filterForm);
            const nextUrl = buildRequestUrl(filterForm);
            syncStatCardStateFromUrl(nextUrl);
            loadKerjasamaSection(nextUrl);
            return;
        }

        if (resetFiltersButton) {
            const filterForm = mainSection.querySelector('form[data-landing-filter]');

            if (!filterForm) {
                return;
            }

            const defaultKategori = filterForm.querySelector('input[name="kategori_mitra"][value="all"]');
            const defaultScope = filterForm.querySelector('input[name="data_scope"][value="kerjasama"]');
            const searchInput = filterForm.querySelector('input[name="search"]');
            const sortSelect = filterForm.querySelector('select[name="sort"]');

            if (defaultScope) {
                defaultScope.checked = true;
            }

            if (defaultKategori) {
                defaultKategori.checked = true;
            }

            if (searchInput) {
                searchInput.value = '';
            }

            if (sortSelect) {
                sortSelect.value = 'latest';
            }

            const statusScopeInput = filterForm.querySelector('input[name="status_scope"]');

            if (statusScopeInput) {
                statusScopeInput.value = 'all';
            }

            cancelPendingSearchDebounce();
            syncScopeDependentFields(filterForm);
            syncFilterState(filterForm);
            syncSearchResetState(filterForm);
            const nextUrl = buildRequestUrl(filterForm);
            syncStatCardStateFromUrl(nextUrl);
            loadKerjasamaSection(nextUrl);
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

    document.addEventListener('landingthemechange', function () {
        initLandingAnalyticsSection();
    });

    syncFilterStateFromUrl(window.location.href);
    initLandingAnalyticsSection();
})();
