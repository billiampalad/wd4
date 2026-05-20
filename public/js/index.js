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
    const geoMapDataSources = {
        world: 'https://cdn.jsdelivr.net/npm/world-atlas@2/countries-110m.json',
        indonesia: 'https://cdn.jsdelivr.net/gh/junwatu/indonesia.json@master/indonesia.json',
    };
    const geoTopologyCache = {
        world: null,
        worldPromise: null,
        indonesia: null,
        indonesiaPromise: null,
    };

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

    function normalizeGeoText(value) {
        return String(value || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, ' ')
            .trim();
    }

    function escapeHtml(value) {
        return String(value || '').replace(/[&<>"']/g, function (match) {
            return ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
            })[match] || match;
        });
    }

    const geoWorldAliases = {
        'dr congo': 'dem rep congo',
        'democratic republic of the congo': 'dem rep congo',
        'ivory coast': 'cote d ivoire',
        'east timor': 'timor leste',
        'cabo verde': 'cape verde',
        'united states': 'united states of america',
        'usa': 'united states of america',
        'uk': 'united kingdom',
        'south korea': 'korea',
        'north korea': 'dem rep korea',
        'vietnam': 'viet nam',
        'syria': 'syrian arab republic',
        'russia': 'russian federation',
        'iran': 'iran',
    };

    const geoIndonesiaAliases = {
        'dki jakarta': 'jakarta',
        'daerah khusus ibukota jakarta': 'jakarta',
        'jakarta raya': 'jakarta',
        'di yogyakarta': 'yogyakarta',
        'daerah istimewa yogyakarta': 'yogyakarta',
        'bangka belitung': 'kepulauan bangka belitung',
        'kep bangka belitung': 'kepulauan bangka belitung',
        'kepulauan bangka belitung': 'kepulauan bangka belitung',
        'irian jaya barat': 'papua barat',
    };

    const geoCountryCodeAliases = {
        indonesia: 'ID',
        id: 'ID',
        idn: 'ID',
        'united states': 'US',
        'united states of america': 'US',
        usa: 'US',
        uk: 'GB',
        'united kingdom': 'GB',
        australia: 'AU',
        japan: 'JP',
        china: 'CN',
        singapore: 'SG',
        malaysia: 'MY',
        thailand: 'TH',
        vietnam: 'VN',
        'viet nam': 'VN',
        philippines: 'PH',
        'new zealand': 'NZ',
        russia: 'RU',
        'russian federation': 'RU',
        india: 'IN',
        netherlands: 'NL',
        germany: 'DE',
        france: 'FR',
        spain: 'ES',
        italy: 'IT',
        'south korea': 'KR',
    };

    const geoIndonesiaProvinceCodes = {
        aceh: '11',
        'sumatera utara': '12',
        'sumatera barat': '13',
        riau: '14',
        jambi: '15',
        'sumatera selatan': '16',
        bengkulu: '17',
        lampung: '18',
        'kepulauan bangka belitung': '19',
        'kepulauan riau': '21',
        'dki jakarta': '31',
        'jawa barat': '32',
        'jawa tengah': '33',
        'di yogyakarta': '34',
        'jawa timur': '35',
        banten: '36',
        bali: '51',
        'nusa tenggara barat': '52',
        'nusa tenggara timur': '53',
        'kalimantan barat': '61',
        'kalimantan tengah': '62',
        'kalimantan selatan': '63',
        'kalimantan timur': '64',
        'kalimantan utara': '65',
        'sulawesi utara': '71',
        'sulawesi tengah': '72',
        'sulawesi selatan': '73',
        'sulawesi tenggara': '74',
        gorontalo: '75',
        'sulawesi barat': '76',
        maluku: '81',
        'maluku utara': '82',
        'papua barat': '91',
        'papua tengah': '92',
        'papua selatan': '93',
        papua: '94',
        'papua pegunungan': '95',
        'papua barat daya': '96',
        jakarta: '31',
        yogyakarta: '34',
    };

    function buildGeoCountIndex(rawCounts, aliases) {
        const index = {};
        let maxValue = 0;

        if (!rawCounts || typeof rawCounts !== 'object') {
            return { index, max: maxValue };
        }

        Object.keys(rawCounts).forEach(function (label) {
            const value = Number(rawCounts[label] || 0);

            if (!value) {
                return;
            }

            const normalized = normalizeGeoText(label);
            const canonical = aliases && aliases[normalized] ? aliases[normalized] : normalized;
            const nextValue = (index[canonical] || 0) + value;
            index[canonical] = nextValue;
            maxValue = Math.max(maxValue, nextValue);
        });

        return { index, max: maxValue };
    }

    function hexToRgb(hex) {
        const normalized = String(hex || '').trim().replace('#', '');

        if (normalized.length !== 6) {
            return { r: 55, g: 138, b: 221 };
        }

        const value = Number.parseInt(normalized, 16);
        return {
            r: (value >> 16) & 255,
            g: (value >> 8) & 255,
            b: value & 255,
        };
    }

    function clampNumber(value, min, max) {
        const num = Number(value);

        if (!Number.isFinite(num)) {
            return min;
        }

        return Math.min(Math.max(num, min), max);
    }

    function mixHexColors(colorA, colorB, t) {
        const ratio = clampNumber(t, 0, 1);
        const rgbA = hexToRgb(colorA);
        const rgbB = hexToRgb(colorB);
        const r = Math.round(rgbA.r + (rgbB.r - rgbA.r) * ratio);
        const g = Math.round(rgbA.g + (rgbB.g - rgbA.g) * ratio);
        const b = Math.round(rgbA.b + (rgbB.b - rgbA.b) * ratio);

        return '#' + [r, g, b].map(function (component) {
            return component.toString(16).padStart(2, '0');
        }).join('');
    }

    function getChoroplethFillColor(value, maxValue, toneColor, isDark) {
        if (!maxValue || maxValue <= 0 || !value) {
            return isDark ? '#162033' : '#f1f6fd';
        }

        const ratio = Math.pow(value / maxValue, 0.65);
        const from = isDark ? '#162033' : '#f1f6fd';
        return mixHexColors(from, toneColor, ratio);
    }

    async function loadGeoTopology(kind) {
        if (kind === 'world') {
            if (geoTopologyCache.world) {
                return geoTopologyCache.world;
            }

            if (!geoTopologyCache.worldPromise) {
                geoTopologyCache.worldPromise = fetch(geoMapDataSources.world)
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (topology) {
                        geoTopologyCache.world = topology;
                        return topology;
                    })
                    .catch(function (error) {
                        geoTopologyCache.worldPromise = null;
                        throw error;
                    });
            }

            return geoTopologyCache.worldPromise;
        }

        if (geoTopologyCache.indonesia) {
            return geoTopologyCache.indonesia;
        }

        if (!geoTopologyCache.indonesiaPromise) {
            geoTopologyCache.indonesiaPromise = fetch(geoMapDataSources.indonesia)
                .then(function (response) {
                    return response.json();
                })
                .then(function (topology) {
                    geoTopologyCache.indonesia = topology;
                    return topology;
                })
                .catch(function (error) {
                    geoTopologyCache.indonesiaPromise = null;
                    throw error;
                });
        }

        return geoTopologyCache.indonesiaPromise;
    }

    function destroyLeafletMap(container) {
        if (!container) {
            return;
        }

        if (container._landingLeafletMap) {
            container._landingLeafletMap.remove();
            container._landingLeafletMap = null;
        }

        if (container._leaflet_id) {
            delete container._leaflet_id;
        }

        while (container.firstChild) {
            container.removeChild(container.firstChild);
        }
    }

    function buildChoroplethTooltip(label, value, metricLabel, extra = {}) {
        const safeLabel = escapeHtml(label);
        const formatted = Number(value || 0).toLocaleString('id-ID');
        const unit = escapeHtml(metricLabel || 'Data');
        const share = Number(extra.share || 0);
        const shareText = Number.isFinite(share) && share > 0
            ? '<div class="geo-tooltip-sub">' + share.toFixed(1) + '% dari total</div>'
            : '';

        return '<div class="geo-tooltip-title">' + safeLabel + '</div>'
            + '<div class="geo-tooltip-value">' + formatted + '</div>'
            + '<div class="geo-tooltip-meta">' + unit + '</div>'
            + shareText;
    }

    function getGeoFeatureLabel(feature) {
        if (!feature || !feature.properties) {
            return '';
        }

        return feature.properties.name
            || feature.properties.NAME
            || feature.properties.NAME_1
            || feature.properties.state
            || feature.properties.provinsi
            || feature.properties.province
            || '';
    }

    function featureCrossesAntimeridian(feature) {
        if (!feature || !feature.geometry) {
            return false;
        }

        if (typeof feature._landingCrossesAntimeridian === 'boolean') {
            return feature._landingCrossesAntimeridian;
        }

        function scanLineString(points) {
            if (!Array.isArray(points) || points.length < 2) {
                return false;
            }

            let prevLon = null;
            for (let i = 0; i < points.length; i += 1) {
                const coord = points[i];
                if (!Array.isArray(coord) || coord.length < 2) {
                    continue;
                }

                const lon = Number(coord[0]);
                if (!Number.isFinite(lon)) {
                    continue;
                }

                if (prevLon !== null && Math.abs(lon - prevLon) > 180) {
                    return true;
                }

                prevLon = lon;
            }

            return false;
        }

        function scanPolygon(rings) {
            if (!Array.isArray(rings)) {
                return false;
            }

            for (let i = 0; i < rings.length; i += 1) {
                if (scanLineString(rings[i])) {
                    return true;
                }
            }

            return false;
        }

        const geometry = feature.geometry;
        let crosses = false;

        if (geometry.type === 'Polygon') {
            crosses = scanPolygon(geometry.coordinates);
        } else if (geometry.type === 'MultiPolygon' && Array.isArray(geometry.coordinates)) {
            for (let i = 0; i < geometry.coordinates.length; i += 1) {
                if (scanPolygon(geometry.coordinates[i])) {
                    crosses = true;
                    break;
                }
            }
        }

        feature._landingCrossesAntimeridian = crosses;
        return crosses;
    }

    async function renderChoroplethMap(container, kind, mapPayload) {
        if (!container || typeof L === 'undefined' || typeof topojson === 'undefined') {
            return;
        }

        const topology = await loadGeoTopology(kind);
        const objectName = kind === 'world' ? 'countries' : 'states_provinces';

        if (!topology || !topology.objects || !topology.objects[objectName]) {
            return;
        }

        const theme = getAnalyticsTheme();
        const isDark = document.documentElement.dataset.theme === 'dark';
        const toneColor = getAnalyticsToneColor('info');
        const borderColor = isDark ? hexToRgba('#94a3b8', 0.55) : hexToRgba('#0d1b2a', 0.22);
        const borderHover = theme.title;

        const rawCounts = kind === 'world' ? (mapPayload.world || {}) : (mapPayload.indonesia || {});
        const absoluteCounts = kind === 'world'
            ? (mapPayload.world_abs || mapPayload.world || {})
            : (mapPayload.indonesia_abs || mapPayload.indonesia || {});
        const aliasMap = kind === 'world' ? geoWorldAliases : geoIndonesiaAliases;
        const countsResult = buildGeoCountIndex(rawCounts, aliasMap);
        const countsIndex = countsResult.index;
        const maxValue = mapPayload && mapPayload.mode === 'share' ? 100 : countsResult.max;
        const totals = mapPayload && mapPayload.totals ? mapPayload.totals : {};
        const totalValue = kind === 'world' ? Number(totals.world_total || 0) : Number(totals.indonesia_total || 0);
        const metricLabel = mapPayload && mapPayload.mode === 'share'
            ? 'Persen dari total'
            : (mapPayload.unit || mapPayload.metric_label || 'Jumlah');
        const absoluteIndex = buildGeoCountIndex(absoluteCounts, aliasMap).index;

        const geojson = topojson.feature(topology, topology.objects[objectName]);
        destroyLeafletMap(container);

        const map = L.map(container, {
            zoomControl: false,
            attributionControl: false,
            scrollWheelZoom: false,
            doubleClickZoom: false,
            boxZoom: false,
            keyboard: false,
            tap: false,
            dragging: true,
        });

        container._landingLeafletMap = map;

        if (L.control && L.control.zoom) {
            L.control.zoom({ position: 'bottomright' }).addTo(map);
        }

        const suppressAntimeridianStroke = kind === 'world';

        function styleFeature(feature) {
            const label = getGeoFeatureLabel(feature);
            const key = normalizeGeoText(label);
            const canonical = aliasMap[key] || key;
            const value = Number(countsIndex[canonical] || 0);

            const style = {
                color: borderColor,
                weight: 1,
                fillColor: getChoroplethFillColor(value, maxValue, toneColor, isDark),
                fillOpacity: isDark ? 0.72 : 0.78,
            };

            if (suppressAntimeridianStroke && featureCrossesAntimeridian(feature)) {
                style.stroke = false;
                style.weight = 0;
            }

            return style;
        }

        function bindFeatureInteractions(feature, featureLayer) {
            const label = getGeoFeatureLabel(feature);
            const key = normalizeGeoText(label);
            const canonical = aliasMap[key] || key;
            const value = Number(countsIndex[canonical] || 0);

                featureLayer.bindTooltip(
                    buildChoroplethTooltip(label, value, metricLabel, {
                        share: totalValue > 0 ? (Number(absoluteIndex[canonical] || 0) / totalValue) * 100 : 0,
                    }),
                    {
                        sticky: true,
                        direction: 'top',
                        className: 'geo-tooltip',
                        opacity: 0.98,
                    }
                );

            featureLayer.on('mouseover', function () {
                featureLayer.setStyle({ weight: 2, color: borderHover });

                if (featureLayer.bringToFront) {
                    featureLayer.bringToFront();
                }
            });

            featureLayer.on('mouseout', function () {
                featureLayer.setStyle({ weight: 1, color: borderColor });
            });

            featureLayer.on('click', function () {
                const form = mainSection ? mainSection.querySelector('form[data-landing-filter]') : null;
                if (!form) {
                    return;
                }

                const geoCountryInput = form.querySelector('input[name="geo_country"]');
                const geoProvinceInput = form.querySelector('input[name="geo_province"]');
                const geoCountryCodeInput = form.querySelector('input[name="geo_country_code"]');
                const geoProvinceCodeInput = form.querySelector('input[name="geo_province_code"]');

                if (kind === 'world') {
                    if (geoCountryInput) {
                        geoCountryInput.value = canonical || label || '';
                    }
                    if (geoProvinceInput) {
                        geoProvinceInput.value = '';
                    }
                    if (geoProvinceCodeInput) {
                        geoProvinceCodeInput.value = '';
                    }
                    if (geoCountryCodeInput) {
                        const normalizedCountry = normalizeGeoText(canonical || label || '');
                        geoCountryCodeInput.value = geoCountryCodeAliases[normalizedCountry] || '';
                    }
                } else {
                    if (geoCountryInput) {
                        geoCountryInput.value = 'Indonesia';
                    }
                    if (geoProvinceInput) {
                        geoProvinceInput.value = canonical || label || '';
                    }
                    if (geoCountryCodeInput) {
                        geoCountryCodeInput.value = 'ID';
                    }
                    if (geoProvinceCodeInput) {
                        const normalizedProvince = normalizeGeoText(canonical || label || '');
                        geoProvinceCodeInput.value = geoIndonesiaProvinceCodes[normalizedProvince] || '';
                    }
                }

                cancelPendingSearchDebounce();
                syncScopeDependentFields(form);
                syncFilterState(form);
                syncSearchResetState(form);
                const nextUrl = buildRequestUrl(form);
                syncStatCardStateFromUrl(nextUrl);
                scrollToMainSection();
                loadKerjasamaSection(nextUrl);
            });
        }

        const layer = L.geoJSON(geojson, { style: styleFeature, onEachFeature: bindFeatureInteractions });

        layer.addTo(map);

        const layerBounds = (function () {
            try {
                return layer.getBounds();
            } catch (error) {
                return null;
            }
        })();

        window.requestAnimationFrame(function () {
            map.invalidateSize(true);

            if (!layerBounds) {
                map.setView([0, 0], kind === 'world' ? 1 : 4);
                return;
            }

            try {
                map.fitBounds(layerBounds.pad(0.04), {
                    padding: [24, 24],
                    maxZoom: kind === 'world' ? 2 : 6,
                });
            } catch (error) {
                map.setView([0, 0], kind === 'world' ? 1 : 4);
            }
        });
    }

    function initLandingGeoChoropleth(shell, payload) {
        if (!shell) {
            return;
        }

        const card = shell.querySelector('[data-geo-card]');

        if (!card) {
            return;
        }

        const mapPayload = payload && payload.maps ? payload.maps : null;
        const worldContainer = card.querySelector('[data-geo-map="world"]');
        const indonesiaContainer = card.querySelector('[data-geo-map="indonesia"]');
        const hint = card.querySelector('[data-geo-hint]');
        const legendMin = card.querySelector('[data-geo-legend-min]');
        const legendMax = card.querySelector('[data-geo-legend-max]');
        const legendBar = card.querySelector('.geo-legend-bar');
        const metricSelect = card.querySelector('[data-geo-metric]');
        const scaleButtons = Array.from(card.querySelectorAll('[data-geo-scale]'));
        const worldSummary = card.querySelector('[data-geo-summary="world"]');
        const indonesiaSummary = card.querySelector('[data-geo-summary="indonesia"]');
        const buttons = Array.from(card.querySelectorAll('[data-geo-toggle]'));
        const panels = Array.from(card.querySelectorAll('[role="tabpanel"]'));

        if (!mapPayload || !worldContainer || !indonesiaContainer) {
            return;
        }

        const metrics = mapPayload && mapPayload.metrics ? mapPayload.metrics : null;
        const defaultMetricKey = mapPayload && mapPayload.default_metric ? mapPayload.default_metric : 'cooperations_total';
        const storageMetricKey = 'landing-geo-metric';
        const storageScaleKey = 'landing-geo-scale';

        function sumCounts(counts) {
            if (!counts || typeof counts !== 'object') {
                return 0;
            }

            return Object.values(counts).reduce(function (total, value) {
                return total + Number(value || 0);
            }, 0);
        }

        function resolveMetricKey(nextKey) {
            const key = String(nextKey || '').trim();
            if (!metrics || typeof metrics !== 'object') {
                return defaultMetricKey;
            }

            return metrics[key] ? key : defaultMetricKey;
        }

        let activeMetricKey = resolveMetricKey((function () {
            if (card.dataset.geoMetric) {
                return card.dataset.geoMetric;
            }

            try {
                return localStorage.getItem(storageMetricKey);
            } catch (error) {
                return null;
            }
        })() || defaultMetricKey);

        let activeScale = String((function () {
            if (card.dataset.geoScale) {
                return card.dataset.geoScale;
            }

            try {
                return localStorage.getItem(storageScaleKey);
            } catch (error) {
                return null;
            }
        })() || 'absolute');

        if (!['absolute', 'share'].includes(activeScale)) {
            activeScale = 'absolute';
        }

        function buildScaledCounts(counts, scaleMode) {
            const absolute = counts || {};
            const total = sumCounts(absolute);

            if (scaleMode !== 'share') {
                return { display: absolute, absolute, total };
            }

            const display = {};
            Object.keys(absolute).forEach(function (key) {
                const value = Number(absolute[key] || 0);
                display[key] = total > 0 ? Math.round((value / total) * 1000) / 10 : 0;
            });

            return { display, absolute, total };
        }

        function getActiveMetric() {
            if (!metrics || typeof metrics !== 'object') {
                return null;
            }

            return metrics[activeMetricKey] || metrics[defaultMetricKey] || null;
        }

        function buildRenderPayload() {
            const metric = getActiveMetric();
            const metricLabel = metric && metric.label ? metric.label : 'Jumlah';
            const unit = metric && metric.unit ? metric.unit : 'Data';
            const worldScaled = buildScaledCounts(metric ? metric.world : {}, activeScale);
            const indonesiaScaled = buildScaledCounts(metric ? metric.indonesia : {}, activeScale);

            return {
                metric_key: activeMetricKey,
                metric_label: activeScale === 'share' ? metricLabel + ' (%)' : metricLabel,
                unit,
                mode: activeScale,
                world: worldScaled.display,
                indonesia: indonesiaScaled.display,
                world_abs: worldScaled.absolute,
                indonesia_abs: indonesiaScaled.absolute,
                totals: {
                    world_total: worldScaled.total,
                    indonesia_total: indonesiaScaled.total,
                },
            };
        }

        function updateMetricControls() {
            if (metricSelect) {
                metricSelect.value = activeMetricKey;
            }

            scaleButtons.forEach(function (button) {
                const mode = button.dataset.geoScale || 'absolute';
                const isActive = mode === activeScale;
                button.classList.toggle('is-active', isActive);
                button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });
        }

        const activeTheme = document.documentElement.dataset.theme || 'light';
        const themeChanged = Boolean(card._landingGeoTheme && card._landingGeoTheme !== activeTheme);
        card._landingGeoTheme = activeTheme;

        function updateLegend(kind, renderPayload) {
            const counts = kind === 'world' ? renderPayload.world : renderPayload.indonesia;
            const aliasMap = kind === 'world' ? geoWorldAliases : geoIndonesiaAliases;
            const countsResult = buildGeoCountIndex(counts || {}, aliasMap);
            const maxValue = activeScale === 'share' ? 100 : (countsResult.max || 0);
            const isDark = document.documentElement.dataset.theme === 'dark';
            const toneColor = getAnalyticsToneColor('info');
            const from = isDark ? '#162033' : '#f1f6fd';
            const mid = mixHexColors(from, toneColor, 0.55);

            if (legendMin) {
                legendMin.textContent = activeScale === 'share' ? '0%' : '0';
            }

            if (legendMax) {
                legendMax.textContent = activeScale === 'share'
                    ? '100%'
                    : Number(maxValue).toLocaleString('id-ID');
            }

            if (legendBar) {
                legendBar.style.background = 'linear-gradient(90deg,' + from + ',' + mid + ',' + toneColor + ')';
            }
        }

        function renderSummary(kind, renderPayload) {
            const summaryEl = kind === 'world' ? worldSummary : indonesiaSummary;
            if (!summaryEl) {
                return;
            }

            const absCounts = kind === 'world' ? renderPayload.world_abs : renderPayload.indonesia_abs;
            const unit = renderPayload.unit || 'Data';
            const total = kind === 'world' ? (renderPayload.totals.world_total || 0) : (renderPayload.totals.indonesia_total || 0);
            const items = Object.keys(absCounts || {}).map(function (key) {
                return { key, value: Number(absCounts[key] || 0) };
            }).filter(function (item) {
                return item.value > 0;
            }).sort(function (a, b) {
                return b.value - a.value;
            }).slice(0, 5);

            if (!items.length) {
                summaryEl.textContent = 'Belum ada data untuk diringkas pada peta ini.';
                return;
            }

            const lines = items.map(function (item, index) {
                const share = total > 0 ? Math.round((item.value / total) * 1000) / 10 : 0;
                return (index + 1) + '. ' + item.key + ' — ' + item.value.toLocaleString('id-ID') + ' ' + unit + ' (' + share + '%)';
            });

            summaryEl.innerHTML = '<strong>Ringkasan Top 5</strong><br>' + lines.map(escapeHtml).join('<br>');
        }

        function setActive(kind, options = {}) {
            const forceRender = Boolean(options.forceRender);
            const renderPayload = buildRenderPayload();
            card._landingGeoActiveKind = kind;
            const metric = getActiveMetric();
            const hasIndonesia = Boolean(metric && metric.indonesia && Object.keys(metric.indonesia || {}).length > 0);
            if (hint) {
                hint.hidden = hasIndonesia;
            }
            buttons.forEach(function (button) {
                const isActive = button.dataset.geoToggle === kind;
                button.classList.toggle('is-active', isActive);
                button.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });

            panels.forEach(function (panel) {
                const isTarget = panel.id === (kind === 'world' ? 'geo-panel-world' : 'geo-panel-indonesia');

                if (isTarget) {
                    panel.removeAttribute('hidden');
                    panel.classList.add('is-active');
                } else {
                    panel.setAttribute('hidden', '');
                    panel.classList.remove('is-active');
                }
            });

            updateLegend(kind, renderPayload);
            renderSummary(kind, renderPayload);

            const targetContainer = kind === 'world' ? worldContainer : indonesiaContainer;
            const themeMismatch = Boolean(targetContainer._landingLeafletTheme && targetContainer._landingLeafletTheme !== activeTheme);

            if (forceRender || themeMismatch || !targetContainer._landingLeafletReady) {
                targetContainer._landingLeafletReady = true;
                targetContainer._landingLeafletTheme = activeTheme;
                renderChoroplethMap(targetContainer, kind, renderPayload).catch(function () {
                    // Ignore map rendering errors (e.g. offline / topology fetch failure).
                });
                return;
            }

            if (targetContainer._landingLeafletMap) {
                window.requestAnimationFrame(function () {
                    targetContainer._landingLeafletMap.invalidateSize(true);
                });
            }
        }

        const initialButton = buttons.find(function (button) {
            return button.getAttribute('aria-selected') === 'true' || button.classList.contains('is-active');
        }) || buttons[0];

        const initialKind = initialButton ? initialButton.dataset.geoToggle : 'world';
        const metric = getActiveMetric();
        const hasIndonesia = Boolean(metric && metric.indonesia && Object.keys(metric.indonesia || {}).length > 0);

        if (hint) {
            hint.hidden = hasIndonesia;
        }

        if (!card._landingGeoBound) {
            card._landingGeoBound = true;
            if (metricSelect) {
                metricSelect.addEventListener('change', function () {
                    activeMetricKey = resolveMetricKey(metricSelect.value);
                    card.dataset.geoMetric = activeMetricKey;
                    try {
                        localStorage.setItem(storageMetricKey, activeMetricKey);
                    } catch (error) {
                        // ignore
                    }

                    updateMetricControls();
                    setActive(card._landingGeoActiveKind || initialKind || 'world', { forceRender: true });
                });
            }

            scaleButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const mode = button.dataset.geoScale || 'absolute';
                    if (!['absolute', 'share'].includes(mode) || mode === activeScale) {
                        return;
                    }

                    activeScale = mode;
                    card.dataset.geoScale = activeScale;
                    try {
                        localStorage.setItem(storageScaleKey, activeScale);
                    } catch (error) {
                        // ignore
                    }

                    updateMetricControls();
                    setActive(card._landingGeoActiveKind || initialKind || 'world', { forceRender: true });
                });
            });

            buttons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const kind = button.dataset.geoToggle || 'world';
                    setActive(kind);
                });
            });
        }

        updateMetricControls();
        setActive(initialKind || 'world', { forceRender: themeChanged });
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

        const payload = parseJsonScript(shell, '[data-analytics-payload]', {});
        initLandingGeoChoropleth(shell, payload);

        if (typeof Chart === 'undefined') {
            animateAnalyticsCards(section);
            return;
        }

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
        const geoCountry = parsedUrl.searchParams.get('geo_country') || '';
        const geoProvince = parsedUrl.searchParams.get('geo_province') || '';
        const geoCountryCode = parsedUrl.searchParams.get('geo_country_code') || '';
        const geoProvinceCode = parsedUrl.searchParams.get('geo_province_code') || '';
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

        const geoCountryInput = form.querySelector('input[name="geo_country"]');
        if (geoCountryInput) {
            geoCountryInput.value = geoCountry;
        }

        const geoProvinceInput = form.querySelector('input[name="geo_province"]');
        if (geoProvinceInput) {
            geoProvinceInput.value = geoProvince;
        }

        const geoCountryCodeInput = form.querySelector('input[name="geo_country_code"]');
        if (geoCountryCodeInput) {
            geoCountryCodeInput.value = geoCountryCode;
        }

        const geoProvinceCodeInput = form.querySelector('input[name="geo_province_code"]');
        if (geoProvinceCodeInput) {
            geoProvinceCodeInput.value = geoProvinceCode;
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

        if (!params.get('geo_country')) {
            params.delete('geo_country');
        }

        if (!params.get('geo_province')) {
            params.delete('geo_province');
        }

        if (!params.get('geo_country_code')) {
            params.delete('geo_country_code');
        }

        if (!params.get('geo_province_code')) {
            params.delete('geo_province_code');
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

        if (currentAnalyticsContent) {
            currentAnalyticsContent.querySelectorAll('[data-geo-map]').forEach(function (container) {
                destroyLeafletMap(container);
            });
        }

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

            const geoCountryInput = filterForm.querySelector('input[name="geo_country"]');
            if (geoCountryInput) {
                geoCountryInput.value = '';
            }

            const geoProvinceInput = filterForm.querySelector('input[name="geo_province"]');
            if (geoProvinceInput) {
                geoProvinceInput.value = '';
            }

            const geoCountryCodeInput = filterForm.querySelector('input[name="geo_country_code"]');
            if (geoCountryCodeInput) {
                geoCountryCodeInput.value = '';
            }

            const geoProvinceCodeInput = filterForm.querySelector('input[name="geo_province_code"]');
            if (geoProvinceCodeInput) {
                geoProvinceCodeInput.value = '';
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
