function initUnitDashboard() {
    const mainContent = document.getElementById('mainContent');
    if (!mainContent || !mainContent.classList.contains('unitdash')) return;

    initUnitChart();
    initJurusanProdiChart();
    initUpaPusatChart();
    initTrendChart();

    const tabs = document.querySelectorAll('[data-filter-tab]');
    const rows = document.querySelectorAll('[data-kerjasama-row]');
    const noResult = document.getElementById('unitDashNoResult');
    const navSearchInput = document.getElementById('navSearchInput');
    let activeDocFilter = 'all';
    let dashboardSearchQuery = (navSearchInput?.value || '').trim().toLowerCase();

    function applyDashboardTableFilter() {
        let visibleCount = 0;

        rows.forEach(function(row) {
            const matchesTab = activeDocFilter === 'all' || row.dataset.docType === activeDocFilter;
            const matchesSearch = !dashboardSearchQuery || row.textContent.toLowerCase().includes(dashboardSearchQuery);
            const isVisible = matchesTab && matchesSearch;

            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount += 1;
        });

        if (noResult) {
            noResult.style.display = visibleCount === 0 && rows.length > 0 ? '' : 'none';
            const empty = noResult.querySelector('.ud-empty');
            if (empty) {
                empty.textContent = dashboardSearchQuery
                    ? 'Tidak ada dokumen yang cocok dengan pencarian ini.'
                    : 'Tidak ada dokumen pada filter ini.';
            }
        }
    }

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            activeDocFilter = tab.dataset.filterTab;

            tabs.forEach(function(item) {
                item.classList.toggle('is-active', item === tab);
            });

            applyDashboardTableFilter();
        });
    });

    window.addEventListener('unit-dashboard-global-search', function(event) {
        dashboardSearchQuery = String(event.detail || '').trim().toLowerCase();
        applyDashboardTableFilter();
    });

    applyDashboardTableFilter();

    document.querySelectorAll('[data-save-document-link]').forEach(function(button) {
        button.addEventListener('click', async function() {
            const editor = button.closest('[data-link-editor]');
            const input = editor.querySelector('[data-document-link-input]');
            const state = editor.parentElement.querySelector('[data-save-state]');
            const icon = button.querySelector('i');
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            button.disabled = true;
            icon.className = 'fas fa-spinner fa-spin';
            state.textContent = 'Menyimpan...';

            try {
                const response = await fetch(button.dataset.updateUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        document_link: input.value.trim()
                    })
                });

                const payload = await response.json().catch(function() {
                    return {};
                });

                if (!response.ok) {
                    throw new Error(payload.message || 'Gagal menyimpan link.');
                }

                state.textContent = input.value.trim() ? 'Link tersimpan' : 'Belum ada link';
                state.style.color = 'var(--success)';
            } catch (error) {
                state.textContent = error.message;
                state.style.color = 'var(--danger)';
            } finally {
                button.disabled = false;
                icon.className = 'fas fa-floppy-disk';
            }
        });
    });
}

function initUnitChart() {
    const canvas = document.getElementById('jenisKerjasamaChart');
    if (!canvas || typeof Chart === 'undefined') return;

    // Hindari duplikasi instance chart jika di-reload via Turbo
    if (window.jenisKerjasamaChartInstance) {
        window.jenisKerjasamaChartInstance.destroy();
    }

    const mou = parseInt(canvas.dataset.mou) || 0;
    const moa = parseInt(canvas.dataset.moa) || 0;
    const ia = parseInt(canvas.dataset.ia) || 0;

    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const textColor = isDark ? '#e2e8f0' : '#475569';

    const data = {
        labels: ['MoU', 'MoA', 'IA'],
        datasets: [{
            data: [mou, moa, ia],
            backgroundColor: [
                '#3b82f6', // blue
                '#f59e0b', // amber
                '#10b981'  // emerald
            ],
            borderWidth: 0,
            hoverOffset: 6
        }]
    };

    const config = {
        type: 'doughnut',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: textColor,
                        font: {
                            family: "'Inter', sans-serif",
                            weight: '650',
                            size: 12
                        },
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: isDark ? 'rgba(30, 41, 59, 0.95)' : 'rgba(15, 23, 42, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#cbd5e1',
                    bodyFont: {
                        weight: '600'
                    },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) label += ': ';
                            if (context.parsed !== null) label += context.parsed + ' Dokumen';
                            return label;
                        }
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 1000,
                easing: 'easeOutQuart'
            }
        }
    };

    window.jenisKerjasamaChartInstance = new Chart(canvas, config);
}

function initJurusanProdiChart() {
    const jurusanCanvas = document.getElementById('jurusanChart');
    const prodiCanvas = document.getElementById('prodiChart');
    if (!jurusanCanvas || !prodiCanvas || typeof Chart === 'undefined') return;

    if (window.jurusanChartInstance) window.jurusanChartInstance.destroy();
    if (window.prodiChartInstance) window.prodiChartInstance.destroy();

    const jurusans = JSON.parse(jurusanCanvas.dataset.jurusans || '[]');
    const prodis = JSON.parse(jurusanCanvas.dataset.prodis || '[]');

    // ── Warna sesuai spesifikasi Chart.js ──────────────────────────────
    const bgColors = [
        'rgba(255, 99, 132, 0.2)',
        'rgba(255, 159, 64, 0.2)',
        'rgba(255, 205, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(201, 203, 207, 0.2)'
    ];
    const borderColors = [
        'rgb(255, 99, 132)',
        'rgb(255, 159, 64)',
        'rgb(255, 205, 86)',
        'rgb(75, 192, 192)',
        'rgb(54, 162, 235)',
        'rgb(153, 102, 255)',
        'rgb(201, 203, 207)'
    ];

    // ── Jurusan Chart ─────────────────────────────────────────────────
    const jurusanLabels = jurusans.map(j => j.name);
    const jurusanData   = jurusans.map(j => j.count);

    const jurusanDataset = {
        label: 'Jumlah Kerjasama',
        data: jurusanData,
        backgroundColor: jurusanLabels.map((_, i) => bgColors[i % bgColors.length]),
        borderColor:     jurusanLabels.map((_, i) => borderColors[i % borderColors.length]),
        borderWidth: 1
    };

    window.jurusanChartInstance = new Chart(jurusanCanvas, {
        type: 'bar',
        data: { labels: jurusanLabels, datasets: [jurusanDataset] },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            },
            onClick: (e, elements) => {
                if (elements.length > 0) {
                    const idx = elements[0].index;
                    updateProdiChart(jurusans[idx].id, jurusans[idx].name);
                } else {
                    updateProdiChart(null, 'Semua');
                }
            },
            onHover: (e, elements) => {
                e.native.target.style.cursor = elements.length ? 'pointer' : 'default';
            }
        }
    });

    // ── Prodi Chart ───────────────────────────────────────────────────
    window.prodiChartInstance = new Chart(prodiCanvas, {
        type: 'bar',
        data: { labels: [], datasets: [{
            label: 'Jumlah Kerjasama',
            data: [],
            backgroundColor: [],
            borderColor: [],
            borderWidth: 1
        }] },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    function updateProdiChart(jurusanId, jurusanName) {
        const subtitle = document.getElementById('prodiChartSubtitle');
        if (subtitle) {
            subtitle.innerText = jurusanName === 'Semua'
                ? 'Menampilkan Semua Jurusan'
                : `Filter: ${jurusanName}`;
        }

        let filtered = jurusanId !== null
            ? prodis.filter(p => p.jurusan_id === jurusanId)
            : prodis;

        // Tampilkan semua prodi (termasuk yang count=0)
        const labels = filtered.map(p => p.name);
        const data   = filtered.map(p => p.count);

        window.prodiChartInstance.data.labels                         = labels;
        window.prodiChartInstance.data.datasets[0].data              = data;
        window.prodiChartInstance.data.datasets[0].backgroundColor   = labels.map((_, i) => bgColors[i % bgColors.length]);
        window.prodiChartInstance.data.datasets[0].borderColor       = labels.map((_, i) => borderColors[i % borderColors.length]);
        window.prodiChartInstance.update();
    }

    // Inisialisasi awal dengan semua prodi
    updateProdiChart(null, 'Semua');
}

function initUpaPusatChart() {
    const canvas = document.getElementById('upaPusatChart');
    if (!canvas || typeof Chart === 'undefined') return;

    if (window.upaPusatChartInstance) {
        window.upaPusatChartInstance.destroy();
    }

    const items = JSON.parse(canvas.dataset.items || '[]');
    const labels = items.map(item => item.name);
    const data = items.map(item => Number(item.count || 0));
    const types = items.map(item => item.type || '');
    const colors = types.map(type => type === 'Pusat' ? '#7c3aed' : '#0891b2');
    const softColors = types.map(type => type === 'Pusat' ? 'rgba(124, 58, 237, 0.18)' : 'rgba(8, 145, 178, 0.18)');

    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const textColor = isDark ? '#e2e8f0' : '#475569';
    const gridColor = isDark ? 'rgba(148, 163, 184, 0.16)' : 'rgba(148, 163, 184, 0.22)';

    window.upaPusatChartInstance = new Chart(canvas, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Jumlah Kerja Sama',
                data,
                backgroundColor: softColors,
                borderColor: colors,
                borderWidth: 2,
                borderRadius: 8,
                maxBarThickness: 34
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        color: textColor
                    },
                    grid: {
                        color: gridColor
                    }
                },
                y: {
                    ticks: {
                        color: textColor,
                        font: {
                            weight: '650'
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: isDark ? 'rgba(30, 41, 59, 0.95)' : 'rgba(15, 23, 42, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#cbd5e1',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label(context) {
                            const type = types[context.dataIndex] || 'Unit';
                            return `${type}: ${context.parsed.x} Dokumen`;
                        }
                    }
                }
            }
        }
    });
}

function initTrendChart() {
    const canvas = document.getElementById('trendChart');
    if (!canvas || typeof Chart === 'undefined') return;

    if (window.trendChartInstance) window.trendChartInstance.destroy();

    const rawData = JSON.parse(canvas.dataset.trends || '{}');
    if (!rawData.monthly) return;

    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const textColor = isDark ? '#e2e8f0' : '#475569';
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
    
    // Create gradient for the line chart fill
    const ctx = canvas.getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 350);
    gradient.addColorStop(0, isDark ? 'rgba(99, 102, 241, 0.4)' : 'rgba(79, 70, 229, 0.3)');
    gradient.addColorStop(1, isDark ? 'rgba(99, 102, 241, 0.0)' : 'rgba(79, 70, 229, 0.0)');

    const config = {
        type: 'line',
        data: {
            labels: rawData.monthly.labels,
            datasets: [{
                label: 'Jumlah Kerjasama',
                data: rawData.monthly.data,
                borderColor: isDark ? '#818cf8' : '#4f46e5',
                backgroundColor: gradient,
                borderWidth: 3,
                pointBackgroundColor: isDark ? '#1e293b' : '#fff',
                pointBorderColor: isDark ? '#818cf8' : '#4f46e5',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4 // Smooth curve
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? 'rgba(30, 41, 59, 0.95)' : 'rgba(15, 23, 42, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#cbd5e1',
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        title: function(context) {
                            return 'Periode: ' + context[0].label;
                        },
                        label: function(context) {
                            return `${context.parsed.y} Dokumen Kerjasama`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, color: textColor, font: { family: "'Inter', sans-serif" } },
                    grid: { color: gridColor, drawBorder: false },
                    border: { display: false }
                },
                x: {
                    ticks: { color: textColor, font: { family: "'Inter', sans-serif" } },
                    grid: { display: false },
                    border: { display: false }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            animation: {
                duration: 800,
                easing: 'easeOutQuart'
            }
        }
    };

    window.trendChartInstance = new Chart(canvas, config);

    // Buttons logic
    const buttons = document.querySelectorAll('.ud-trend-btn');
    buttons.forEach(btn => {
        btn.addEventListener('click', function() {
            buttons.forEach(b => b.classList.remove('is-active'));
            this.classList.add('is-active');

            const trend = this.dataset.trend;
            if (rawData[trend]) {
                window.trendChartInstance.data.labels = rawData[trend].labels;
                window.trendChartInstance.data.datasets[0].data = rawData[trend].data;
                window.trendChartInstance.update();
            }
        });
    });
    // Auto-wrap tables with dashboard-table-wrapper
    document.querySelectorAll('table').forEach(function(table) {
        var existingWrapper = table.closest('.dashboard-table-wrapper, .table-scroll-area, .table-responsive');

        if (existingWrapper) {
            existingWrapper.classList.add('dashboard-table-wrapper');
            return;
        }

        var wrapper = document.createElement('div');
        wrapper.className = 'dashboard-table-wrapper';

        table.parentNode.insertBefore(wrapper, table);
        wrapper.appendChild(table);
    });
}

// Inisialisasi untuk Unit Dashboard
document.addEventListener('DOMContentLoaded', initUnitDashboard);
document.addEventListener('turbo:load', initUnitDashboard);
(() => {
    const scrollableClass = 'dashboard-scrollbar-target';
    const overflowPattern = /(auto|scroll|overlay)/;

    const isScrollable = (element) => {
        const style = window.getComputedStyle(element);
        const canScrollY = overflowPattern.test(style.overflowY) && element.scrollHeight > element.clientHeight;
        const canScrollX = overflowPattern.test(style.overflowX) && element.scrollWidth > element.clientWidth;

        return canScrollY || canScrollX;
    };

    const markScrollableElements = () => {
        document.documentElement.classList.add(scrollableClass);
        document.body.classList.add(scrollableClass);

        document.querySelectorAll('body *').forEach((element) => {
            element.classList.toggle(scrollableClass, isScrollable(element));
        });
    };

    const scheduleMarking = () => window.requestAnimationFrame(markScrollableElements);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', markScrollableElements, { once: true });
    } else {
        markScrollableElements();
    }

    window.addEventListener('resize', scheduleMarking);

    new MutationObserver(scheduleMarking).observe(document.documentElement, {
        childList: true,
        subtree: true,
    });
})();
document.addEventListener('DOMContentLoaded', () => {
    const chart = document.querySelector('[data-cooperation-chart]');

    if (!chart) {
        return;
    }

    const layout = chart.closest('.dashboard-cooperation-layout') || document;
    const table = Array.from(layout.querySelectorAll('table')).find((item) => !chart.contains(item));
    const body = chart.querySelector('[data-cooperation-chart-body]');
    const empty = chart.querySelector('[data-cooperation-chart-empty]');
    const meta = chart.querySelector('[data-cooperation-chart-meta]');
    const resetButton = chart.querySelector('[data-cooperation-chart-reset]');
    const tooltip = chart.querySelector('[data-cooperation-chart-tooltip]');
    let rows = [];
    let selectedRow = null;

    const numberFormatter = new Intl.NumberFormat('id-ID');

    const parseNumber = (value) => {
        const normalized = String(value || '')
            .replace(/[^\d,.-]/g, '')
            .replace(/\.(?=\d{3}(\D|$))/g, '')
            .replace(',', '.');
        const parsed = Number.parseFloat(normalized);

        return Number.isFinite(parsed) ? parsed : null;
    };

    const getCells = (row) => Array.from(row.querySelectorAll('td, th'));

    const getLabel = (cells) => {
        const preferredCell = cells.find((cell) => {
            const text = cell.textContent.trim();

            return text.length > 0 && parseNumber(text) === null;
        });

        return (preferredCell || cells[0])?.textContent.trim() || 'Tanpa label';
    };

    const getValue = (cells) => {
        const numericCells = cells
            .map((cell) => parseNumber(cell.textContent))
            .filter((value) => value !== null);

        if (numericCells.length === 0) {
            return null;
        }

        return numericCells[numericCells.length - 1];
    };

    const readTableData = () => {
        if (!table) {
            return [];
        }

        return Array.from(table.querySelectorAll('tbody tr'))
            .map((row, index) => {
                const cells = getCells(row);
                const label = getLabel(cells);
                const value = getValue(cells);

                return {
                    index,
                    row,
                    label,
                    value,
                    detail: cells.map((cell) => cell.textContent.trim()).filter(Boolean).join(' | '),
                };
            })
            .filter((item) => item.value !== null);
    };

    const showTooltip = (event, item) => {
        if (!tooltip) {
            return;
        }

        const rect = event.currentTarget.getBoundingClientRect();
        const left = Number.isFinite(event.clientX) && event.clientX > 0 ? event.clientX : rect.right;
        const top = Number.isFinite(event.clientY) && event.clientY > 0 ? event.clientY : rect.top + rect.height / 2;

        tooltip.innerHTML = `<strong>${item.label}</strong><span>${numberFormatter.format(item.value)} data</span>`;
        tooltip.style.display = 'block';
        tooltip.style.left = `${left}px`;
        tooltip.style.top = `${top}px`;
    };

    const moveTooltip = (event) => {
        if (!tooltip || tooltip.style.display !== 'block') {
            return;
        }

        tooltip.style.left = `${event.clientX}px`;
        tooltip.style.top = `${event.clientY}px`;
    };

    const hideTooltip = () => {
        if (tooltip) {
            tooltip.style.display = 'none';
        }
    };

    const renderChart = (items = rows) => {
        const visibleItems = items.filter((item) => item.value !== null);
        const maxValue = Math.max(...visibleItems.map((item) => item.value), 0);

        chart.classList.toggle('is-empty', visibleItems.length === 0);
        body.innerHTML = '';

        if (meta) {
            meta.textContent = selectedRow
                ? `Menampilkan data terpilih: ${selectedRow.label}`
                : `Menampilkan ${visibleItems.length} item dari tabel. Klik baris tabel untuk filter.`;
        }

        if (visibleItems.length === 0) {
            return;
        }

        const fragment = document.createDocumentFragment();

        visibleItems.forEach((item) => {
            const percentage = maxValue > 0 ? Math.max((item.value / maxValue) * 100, 4) : 0;
            const row = document.createElement('div');
            const label = document.createElement('div');
            const track = document.createElement('div');
            const bar = document.createElement('div');
            const value = document.createElement('div');

            row.className = 'dashboard-cooperation-chart__row';
            row.setAttribute('tabindex', '0');
            row.setAttribute('aria-label', `${item.label}: ${numberFormatter.format(item.value)}`);

            label.className = 'dashboard-cooperation-chart__label';
            label.textContent = item.label;
            label.title = item.detail || item.label;

            track.className = 'dashboard-cooperation-chart__track';
            bar.className = 'dashboard-cooperation-chart__bar';
            track.appendChild(bar);

            value.className = 'dashboard-cooperation-chart__value';
            value.textContent = numberFormatter.format(item.value);

            row.append(label, track, value);
            row.addEventListener('mouseenter', (event) => showTooltip(event, item));
            row.addEventListener('mousemove', moveTooltip);
            row.addEventListener('mouseleave', hideTooltip);
            row.addEventListener('focus', (event) => showTooltip(event, item));
            row.addEventListener('blur', hideTooltip);

            fragment.appendChild(row);
            window.requestAnimationFrame(() => {
                bar.style.width = `${percentage}%`;
            });
        });

        body.appendChild(fragment);
    };

    const clearSelection = () => {
        selectedRow = null;
        rows.forEach((item) => item.row.classList.remove('is-chart-selected'));
        renderChart(rows);
    };

    const selectTableRow = (item) => {
        selectedRow = item;
        rows.forEach((rowItem) => {
            rowItem.row.classList.toggle('is-chart-selected', rowItem.index === item.index);
        });
        renderChart([item]);
    };

    const activateTableRow = (row) => {
        const item = rows.find((rowItem) => rowItem.row === row);

        if (item) {
            selectTableRow(item);
        }
    };

    const bindTableRows = () => {
        rows.forEach((item) => {
            if (item.row.dataset.cooperationChartBound === 'true') {
                return;
            }

            item.row.dataset.cooperationChartBound = 'true';
            item.row.addEventListener('click', () => activateTableRow(item.row));
            item.row.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    activateTableRow(item.row);
                }
            });

            if (!item.row.hasAttribute('tabindex')) {
                item.row.setAttribute('tabindex', '0');
            }
        });
    };

    const refreshChart = () => {
        rows = readTableData();
        selectedRow = null;
        bindTableRows();
        renderChart(rows);
    };

    if (resetButton) {
        resetButton.addEventListener('click', clearSelection);
    }

    refreshChart();

    if (table) {
        const observer = new MutationObserver(refreshChart);
        observer.observe(table, {
            childList: true,
            subtree: true,
            characterData: true,
        });
    }
});
