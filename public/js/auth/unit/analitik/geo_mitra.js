(function () {
    console.log("geo_mitra.js: Script initialized.");

    function parseGeoJson(id, fallback) {
        const node = document.getElementById(id);
        if (!node) {
            console.warn("geo_mitra.js: JSON node not found: " + id);
            return fallback;
        }

        try {
            const data = JSON.parse(node.textContent || '');
            console.log("geo_mitra.js: Parsed data for " + id + ":", data);
            return data;
        } catch (error) {
            console.error("geo_mitra.js: Failed to parse JSON for " + id + ":", error);
            return fallback;
        }
    }

    function statusKerjasamaGridColor() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        return isDark ? 'rgba(226, 232, 240, .16)' : 'rgba(120, 120, 120, .32)';
    }

    function statusKerjasamaTextColor() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        return isDark ? '#cbd5e1' : '#666';
    }

    function statusKerjasamaSurfaceColor() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        return isDark ? '#181c27' : '#ffffff';
    }

    function statusKerjasamaTooltipColors() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';

        return {
            background: isDark ? '#0f172a' : '#ffffff',
            border: isDark ? 'rgba(148, 163, 184, .28)' : 'rgba(15, 23, 42, .12)',
            title: isDark ? '#f8fafc' : '#0f172a',
            body: isDark ? '#cbd5e1' : '#475569'
        };
    }

    function prepareCanvasContext(canvas) {
        const rect = canvas.getBoundingClientRect();
        const width = Math.max(Math.round(rect.width), 1);
        const height = Math.max(Math.round(rect.height), 1);
        const ratio = window.devicePixelRatio || 1;

        canvas.width = width * ratio;
        canvas.height = height * ratio;

        const ctx = canvas.getContext('2d');
        ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
        ctx.clearRect(0, 0, width, height);

        return {
            ctx: ctx,
            width: width,
            height: height
        };
    }

    function normalizePositiveChartData(chartData, emptyLabel) {
        const labels = Array.isArray(chartData.labels) ? chartData.labels : [];
        const data = Array.isArray(chartData.data) ? chartData.data : [];
        const colors = Array.isArray(chartData.colors) ? chartData.colors : [];
        const normalized = {
            labels: [],
            data: [],
            colors: []
        };

        labels.forEach(function (label, index) {
            const value = Number(data[index] || 0);
            if (value > 0) {
                normalized.labels.push(label);
                normalized.data.push(value);
                normalized.colors.push(colors[index] || '#6366f1');
            }
        });

        if (normalized.data.length === 0) {
            normalized.labels.push(emptyLabel);
            normalized.data.push(1);
            normalized.colors.push('#cbd5e1');
        }

        return normalized;
    }

    function drawFallbackDoughnut(canvas, chartData) {
        const prepared = prepareCanvasContext(canvas);
        const ctx = prepared.ctx;
        const width = prepared.width;
        const height = prepared.height;
        const data = normalizePositiveChartData(chartData, 'Tidak Ada Data');
        const total = data.data.reduce(function (sum, value) {
            return sum + Number(value || 0);
        }, 0);
        const radius = Math.max(Math.min(width, height) / 2 - 16, 20);
        const innerRadius = radius * 0.6;
        const centerX = width / 2;
        const centerY = height / 2;
        let startAngle = -Math.PI / 2;

        data.data.forEach(function (value, index) {
            const angle = total ? (Number(value || 0) / total) * Math.PI * 2 : 0;
            ctx.beginPath();
            ctx.arc(centerX, centerY, radius, startAngle, startAngle + angle);
            ctx.arc(centerX, centerY, innerRadius, startAngle + angle, startAngle, true);
            ctx.closePath();
            ctx.fillStyle = data.colors[index] || '#6366f1';
            ctx.fill();
            startAngle += angle;
        });

        ctx.beginPath();
        ctx.arc(centerX, centerY, innerRadius - 1, 0, Math.PI * 2);
        ctx.fillStyle = statusKerjasamaSurfaceColor();
        ctx.fill();

        if (data.labels[0] === 'Tidak Ada Data') {
            ctx.fillStyle = statusKerjasamaTextColor();
            ctx.font = '600 12px Plus Jakarta Sans, Arial, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('Tidak Ada Data', centerX, centerY);
        }
    }

    function drawRoundedRect(ctx, x, y, width, height, radius) {
        const safeRadius = Math.min(radius, Math.abs(width) / 2, Math.abs(height) / 2);

        if (typeof ctx.roundRect === 'function') {
            ctx.roundRect(x, y, width, height, safeRadius);
            return;
        }

        ctx.moveTo(x + safeRadius, y);
        ctx.lineTo(x + width - safeRadius, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + safeRadius);
        ctx.lineTo(x + width, y + height - safeRadius);
        ctx.quadraticCurveTo(x + width, y + height, x + width - safeRadius, y + height);
        ctx.lineTo(x + safeRadius, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height - safeRadius);
        ctx.lineTo(x, y + safeRadius);
        ctx.quadraticCurveTo(x, y, x + safeRadius, y);
    }

    function drawFallbackBar(canvas, chartData) {
        const prepared = prepareCanvasContext(canvas);
        const ctx = prepared.ctx;
        const width = prepared.width;
        const height = prepared.height;
        const data = normalizePositiveChartData(chartData, 'Tidak Ada Data');
        const maxValue = Math.max.apply(null, data.data);
        const textColor = statusKerjasamaTextColor();
        const gridColor = statusKerjasamaGridColor();
        const left = 34;
        const right = 12;
        const top = 18;
        const bottom = 46;
        const chartWidth = Math.max(width - left - right, 1);
        const chartHeight = Math.max(height - top - bottom, 1);
        const slotWidth = chartWidth / data.data.length;
        const barWidth = Math.min(slotWidth * 0.56, 42);

        ctx.strokeStyle = gridColor;
        ctx.lineWidth = 1;
        ctx.fillStyle = textColor;
        ctx.font = '600 10px Plus Jakarta Sans, Arial, sans-serif';
        ctx.textAlign = 'right';
        ctx.textBaseline = 'middle';

        for (let step = 0; step <= 4; step++) {
            const y = top + chartHeight - (chartHeight * step / 4);
            const value = Math.round(maxValue * step / 4);
            ctx.beginPath();
            ctx.moveTo(left, y);
            ctx.lineTo(width - right, y);
            ctx.stroke();
            ctx.fillText(String(value), left - 8, y);
        }

        data.data.forEach(function (value, index) {
            const barHeight = maxValue ? (Number(value || 0) / maxValue) * chartHeight : 0;
            const x = left + slotWidth * index + (slotWidth - barWidth) / 2;
            const y = top + chartHeight - barHeight;

            ctx.fillStyle = data.colors[index] || '#6366f1';
            ctx.beginPath();
            drawRoundedRect(ctx, x, y, barWidth, barHeight, 6);
            ctx.fill();

            ctx.fillStyle = textColor;
            ctx.font = '700 10px Plus Jakarta Sans, Arial, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText(String(value), x + barWidth / 2, y - 8);

            ctx.save();
            ctx.translate(x + barWidth / 2, height - 10);
            ctx.rotate(-Math.PI / 8);
            ctx.font = '600 10px Plus Jakarta Sans, Arial, sans-serif';
            ctx.fillText(String(data.labels[index]).slice(0, 14), 0, 0);
            ctx.restore();
        });
    }

    function createGeoFallbackCharts() {
        const kategoriCanvas = document.getElementById('geoKategoriChart');
        const negaraCanvas = document.getElementById('geoNegaraChart');

        if (kategoriCanvas) {
            drawFallbackDoughnut(kategoriCanvas, parseGeoJson('geoKategoriChartData', {
                labels: [],
                data: [],
                colors: []
            }));
        }

        if (negaraCanvas) {
            drawFallbackBar(negaraCanvas, parseGeoJson('geoNegaraChartData', {
                labels: [],
                data: [],
                colors: []
            }));
        }
    }

    function runGeoChartRender() {
        try {
            createGeoCharts();
        } catch (error) {
            console.error("geo_mitra.js: Chart render failed. Rendering fallback charts.", error);
            createGeoFallbackCharts();
        }
    }

    function applyThemeToGeoCharts() {
        console.log("geo_mitra.js: Applying theme colors to charts.");
        const surfaceColor = statusKerjasamaSurfaceColor();
        const tooltipColors = statusKerjasamaTooltipColors();
        const textColor = statusKerjasamaTextColor();
        const gridColor = statusKerjasamaGridColor();

        if (window.geoKategoriChart) {
            const donut = window.geoKategoriChart;
            if (donut.data && donut.data.datasets) {
                donut.data.datasets.forEach(function (dataset) {
                    dataset.borderColor = surfaceColor;
                });
            }
            if (donut.options && donut.options.plugins && donut.options.plugins.tooltip) {
                donut.options.plugins.tooltip.backgroundColor = tooltipColors.background;
                donut.options.plugins.tooltip.borderColor = tooltipColors.border;
                donut.options.plugins.tooltip.borderWidth = 1;
                donut.options.plugins.tooltip.titleColor = tooltipColors.title;
                donut.options.plugins.tooltip.bodyColor = tooltipColors.body;
            }
            donut.update('none');
        }

        if (window.geoNegaraChart) {
            const bar = window.geoNegaraChart;
            if (bar.options && bar.options.plugins && bar.options.plugins.tooltip) {
                bar.options.plugins.tooltip.backgroundColor = tooltipColors.background;
                bar.options.plugins.tooltip.borderColor = tooltipColors.border;
                bar.options.plugins.tooltip.borderWidth = 1;
                bar.options.plugins.tooltip.titleColor = tooltipColors.title;
                bar.options.plugins.tooltip.bodyColor = tooltipColors.body;
            }

            if (bar.options && bar.options.scales) {
                if (bar.options.scales.x) {
                    if (bar.options.scales.x.grid) bar.options.scales.x.grid.color = gridColor;
                    if (bar.options.scales.x.border) bar.options.scales.x.border.color = gridColor;
                    if (bar.options.scales.x.ticks) bar.options.scales.x.ticks.color = textColor;
                }
                if (bar.options.scales.y) {
                    if (bar.options.scales.y.grid) bar.options.scales.y.grid.color = gridColor;
                    if (bar.options.scales.y.border) bar.options.scales.y.border.color = gridColor;
                    if (bar.options.scales.y.ticks) bar.options.scales.y.ticks.color = textColor;
                }
            }
            bar.update('none');
        }
    }

    function createGeoCharts() {
        console.log("geo_mitra.js: Creating geo charts.");
        const page = document.getElementById('mainContent');
        if (!page) {
            console.warn("geo_mitra.js: mainContent not found.");
            return;
        }

        if (typeof Chart === 'undefined') {
            console.warn("geo_mitra.js: Chart.js is undefined. Rendering fallback charts.");
            createGeoFallbackCharts();
            return;
        }

        const kategoriCanvas = document.getElementById('geoKategoriChart');
        const negaraCanvas = document.getElementById('geoNegaraChart');

        if (kategoriCanvas) {
            const kategoriData = parseGeoJson('geoKategoriChartData', {
                labels: [],
                data: [],
                colors: []
            });

            if (window.geoKategoriChart) {
                window.geoKategoriChart.destroy();
            }

            const tooltipColors = statusKerjasamaTooltipColors();

            const kategoriLabels = Array.isArray(kategoriData.labels) ? kategoriData.labels : [];
            const kategoriValues = Array.isArray(kategoriData.data) ? kategoriData.data : [];
            const kategoriColors = Array.isArray(kategoriData.colors) ? kategoriData.colors : [];
            const totalKategori = kategoriValues.reduce(function (sum, value) {
                return sum + Number(value || 0);
            }, 0);

            if (totalKategori === 0) {
                kategoriLabels.splice(0, kategoriLabels.length, 'Tidak Ada Data');
                kategoriValues.splice(0, kategoriValues.length, 1);
                kategoriColors.splice(0, kategoriColors.length, '#cbd5e1');
            }

            window.geoKategoriChart = new Chart(kategoriCanvas, {
                type: 'doughnut',
                data: {
                    labels: kategoriLabels,
                    datasets: [{
                        data: kategoriValues,
                        backgroundColor: kategoriColors,
                        borderColor: statusKerjasamaSurfaceColor(),
                        borderWidth: 2,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: 10
                    },
                    cutout: '60%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: tooltipColors.background,
                            borderColor: tooltipColors.border,
                            borderWidth: 1,
                            titleColor: tooltipColors.title,
                            bodyColor: tooltipColors.body,
                            callbacks: {
                                label: function (context) {
                                    const total = context.dataset.data.reduce(function (sum, value) {
                                        return sum + Number(value || 0);
                                    }, 0);
                                    const value = Number(context.raw || 0);
                                    const percent = total ? Math.round((value / total) * 100) : 0;
                                    return context.label + ': ' + value + ' Mitra (' + percent + '%)';
                                }
                            }
                        }
                    }
                }
            });
            console.log("geo_mitra.js: Kategori chart created successfully.");
        }

        if (negaraCanvas) {
            const negaraData = parseGeoJson('geoNegaraChartData', {
                labels: [],
                data: [],
                colors: []
            });

            if (window.geoNegaraChart) {
                window.geoNegaraChart.destroy();
            }

            const labels = Array.isArray(negaraData.labels) ? negaraData.labels : [];
            const data = Array.isArray(negaraData.data) ? negaraData.data : [];
            const colors = Array.isArray(negaraData.colors) ? negaraData.colors : [];

            const filteredLabels = [];
            const filteredData = [];
            const filteredColors = [];

            labels.forEach((label, index) => {
                const count = Number(data[index] || 0);
                if (count > 0) {
                    filteredLabels.push(label);
                    filteredData.push(count);
                    filteredColors.push(colors[index] || '#4f46e5');
                }
            });

            if (filteredData.length === 0) {
                filteredLabels.push('Tidak Ada Data');
                filteredData.push(1);
                filteredColors.push('#cbd5e1');
            }

            const tooltipColors = statusKerjasamaTooltipColors();
            const gridColor = statusKerjasamaGridColor();
            const textColor = statusKerjasamaTextColor();

            window.geoNegaraChart = new Chart(negaraCanvas, {
                type: 'bar',
                data: {
                    labels: filteredLabels,
                    datasets: [{
                        label: 'Jumlah Mitra',
                        data: filteredData,
                        backgroundColor: filteredColors,
                        borderRadius: 6,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: tooltipColors.background,
                            borderColor: tooltipColors.border,
                            borderWidth: 1,
                            titleColor: tooltipColors.title,
                            bodyColor: tooltipColors.body
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            border: {
                                color: gridColor
                            },
                            ticks: {
                                color: textColor,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: gridColor,
                                lineWidth: 1.4
                            },
                            border: {
                                color: gridColor
                            },
                            ticks: {
                                color: textColor,
                                precision: 0,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
            console.log("geo_mitra.js: Negara chart created successfully.");
        }
    }

    function initGeoMitraPage() {
        console.log("geo_mitra.js: Page init triggered.");
        const page = document.getElementById('mainContent');
        if (!page || !document.getElementById('geoKategoriChartData')) return;

        if (page.dataset.geoInitialized === 'true') return;
        page.dataset.geoInitialized = 'true';

        if (typeof Chart === 'undefined') {
            let attempts = 0;
            const interval = setInterval(() => {
                attempts++;
                if (typeof Chart !== 'undefined') {
                    clearInterval(interval);
                    runGeoChartRender();
                } else if (attempts > 30) {
                    clearInterval(interval);
                    console.error("geo_mitra.js: Chart.js failed to load after 3 seconds.");
                    createGeoFallbackCharts();
                }
            }, 100);
        } else {
            runGeoChartRender();
        }
    }

    // Bind event listeners
    if (!window.geoMitraEventsRegistered) {
        document.addEventListener('DOMContentLoaded', initGeoMitraPage);
        document.addEventListener('turbo:load', initGeoMitraPage);
        window.geoMitraEventsRegistered = true;
    }

    // Immediate run in case page is loaded dynamically or already parsed
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        console.log("geo_mitra.js: DOM ready, triggering immediate init.");
        initGeoMitraPage();
    }

    // Set up theme observer
    if (!window.geoMitraThemeObserver) {
        window.geoMitraThemeObserver = new MutationObserver(function (mutations) {
            const themeChanged = mutations.some(function (mutation) {
                return mutation.attributeName === 'data-theme';
            });

            if (themeChanged) {
                applyThemeToGeoCharts();
            }
        });

        window.geoMitraThemeObserver.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-theme']
        });
        console.log("geo_mitra.js: Theme observer registered.");
    }
})();
