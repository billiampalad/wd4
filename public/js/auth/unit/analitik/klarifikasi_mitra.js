function parseKlasifikasiJson(id, fallback) {
    const node = document.getElementById(id);
    if (!node) return fallback;

    try {
        return JSON.parse(node.textContent || '');
    } catch (error) {
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

function klasifikasiMitraChartCutout() {
    return window.matchMedia('(max-width: 767px)').matches ? '58%' : '60%';
}

function resizeKlasifikasiMitraChart() {
    if (!window.klasifikasiMitraDonutChart) return;

    window.klasifikasiMitraDonutChart.options.cutout = klasifikasiMitraChartCutout();
    window.klasifikasiMitraDonutChart.resize();
    window.klasifikasiMitraDonutChart.update('none');
}

function applyThemeToKlasifikasiChart() {
    const surfaceColor = statusKerjasamaSurfaceColor();
    const tooltipColors = statusKerjasamaTooltipColors();

    if (window.klasifikasiMitraDonutChart) {
        const donut = window.klasifikasiMitraDonutChart;

        donut.data.datasets.forEach(function (dataset) {
            dataset.borderColor = surfaceColor;
        });

        donut.options.plugins.tooltip.backgroundColor = tooltipColors.background;
        donut.options.plugins.tooltip.borderColor = tooltipColors.border;
        donut.options.plugins.tooltip.borderWidth = 1;
        donut.options.plugins.tooltip.titleColor = tooltipColors.title;
        donut.options.plugins.tooltip.bodyColor = tooltipColors.body;
        donut.update('none');
    }
}

function createKlasifikasiMitraChart() {
    const page = document.getElementById('mainContent');
    if (!page || typeof Chart === 'undefined') return;

    const chartCanvas = document.getElementById('klasifikasiMitraChart');
    if (!chartCanvas) return;

    const chartData = parseKlasifikasiJson('klasifikasiMitraChartData', {
        labels: [],
        data: [],
        colors: []
    });

    const tooltipColors = statusKerjasamaTooltipColors();

    if (window.klasifikasiMitraDonutChart) {
        window.klasifikasiMitraDonutChart.destroy();
    }

    const filteredLabels = [];
    const filteredData = [];
    const filteredColors = [];

    chartData.labels.forEach((label, index) => {
        const count = chartData.data[index];
        if (count > 0) {
            filteredLabels.push(label);
            filteredData.push(count);
            filteredColors.push(chartData.colors[index]);
        }
    });

    if (filteredData.length === 0) {
        filteredLabels.push('Tidak Ada Data');
        filteredData.push(1);
        filteredColors.push('#cbd5e1');
    }

    window.klasifikasiMitraDonutChart = new Chart(chartCanvas, {
        type: 'doughnut',
        data: {
            labels: filteredLabels,
            datasets: [{
                data: filteredData,
                backgroundColor: filteredColors,
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
            cutout: klasifikasiMitraChartCutout(),
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
}

function initKlasifikasiMitraPage() {
    createKlasifikasiMitraChart();
    resizeKlasifikasiMitraChart();
}

document.addEventListener('DOMContentLoaded', initKlasifikasiMitraPage);
document.addEventListener('turbo:load', initKlasifikasiMitraPage);
window.addEventListener('resize', resizeKlasifikasiMitraChart);
window.addEventListener('orientationchange', resizeKlasifikasiMitraChart);

if (!window.klasifikasiMitraThemeObserver) {
    window.klasifikasiMitraThemeObserver = new MutationObserver(function (mutations) {
        const themeChanged = mutations.some(function (mutation) {
            return mutation.attributeName === 'data-theme';
        });

        if (themeChanged) {
            applyThemeToKlasifikasiChart();
        }
    });

    window.klasifikasiMitraThemeObserver.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['data-theme']
    });
}
