function parseStatusKerjasamaJson(id, fallback) {
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

function statusKerjasamaApplyThemeToCharts() {
    const textColor = statusKerjasamaTextColor();
    const gridColor = statusKerjasamaGridColor();
    const surfaceColor = statusKerjasamaSurfaceColor();
    const tooltipColors = statusKerjasamaTooltipColors();

    if (window.statusKerjasamaDonutChart) {
        const donut = window.statusKerjasamaDonutChart;

        donut.data.datasets.forEach(function(dataset) {
            dataset.borderColor = surfaceColor;
        });

        donut.options.plugins.tooltip.backgroundColor = tooltipColors.background;
        donut.options.plugins.tooltip.borderColor = tooltipColors.border;
        donut.options.plugins.tooltip.borderWidth = 1;
        donut.options.plugins.tooltip.titleColor = tooltipColors.title;
        donut.options.plugins.tooltip.bodyColor = tooltipColors.body;
        donut.update('none');
    }

    if (window.pertumbuhanKerjasamaLineChart) {
        const line = window.pertumbuhanKerjasamaLineChart;

        line.options.plugins.legend.labels.color = textColor;
        line.options.plugins.tooltip.backgroundColor = tooltipColors.background;
        line.options.plugins.tooltip.borderColor = tooltipColors.border;
        line.options.plugins.tooltip.borderWidth = 1;
        line.options.plugins.tooltip.titleColor = tooltipColors.title;
        line.options.plugins.tooltip.bodyColor = tooltipColors.body;
        line.options.scales.x.grid.color = gridColor;
        line.options.scales.x.ticks.color = textColor;
        line.options.scales.y.grid.color = gridColor;
        line.options.scales.y.border.color = gridColor;
        line.options.scales.y.ticks.color = textColor;
        line.update('none');
    }
}

function createStatusKerjasamaCharts() {
    const page = document.getElementById('mainContent');
    if (!page || !page.classList.contains('sk-page') || typeof Chart === 'undefined') return;

    const statusCanvas = document.getElementById('statusKerjasamaChart');
    const growthCanvas = document.getElementById('pertumbuhanKerjasamaChart');
    const statusData = parseStatusKerjasamaJson('statusKerjasamaData', {
        labels: [],
        data: [],
        colors: []
    });
    const growthData = parseStatusKerjasamaJson('pertumbuhanKerjasamaData', {
        labels: [],
        mou: [],
        moa: [],
        ia: []
    });
    const tooltipColors = statusKerjasamaTooltipColors();

    if (statusCanvas) {
        if (window.statusKerjasamaDonutChart) {
            window.statusKerjasamaDonutChart.destroy();
        }

        window.statusKerjasamaDonutChart = new Chart(statusCanvas, {
            type: 'doughnut',
            data: {
                labels: statusData.labels,
                datasets: [{
                    data: statusData.data,
                    backgroundColor: statusData.colors,
                    borderColor: statusKerjasamaSurfaceColor(),
                    borderWidth: 2,
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: 10
                },
                cutout: '50%',
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
                            label: function(context) {
                                const total = context.dataset.data.reduce(function(sum, value) {
                                    return sum + Number(value || 0);
                                }, 0);
                                const value = Number(context.raw || 0);
                                const percent = total ? Math.round((value / total) * 100) : 0;
                                return context.label + ': ' + value + ' (' + percent + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    if (growthCanvas) {
        if (window.pertumbuhanKerjasamaLineChart) {
            window.pertumbuhanKerjasamaLineChart.destroy();
        }

        window.pertumbuhanKerjasamaLineChart = new Chart(growthCanvas, {
            type: 'line',
            data: {
                labels: growthData.labels,
                datasets: [{
                    label: 'MoU',
                    data: growthData.mou,
                    borderColor: '#1688cf',
                    backgroundColor: 'rgba(22, 136, 207, .08)',
                    pointRadius: 0,
                    borderWidth: 3,
                    tension: 0
                }, {
                    label: 'MoA',
                    data: growthData.moa,
                    borderColor: '#f58200',
                    backgroundColor: 'rgba(245, 130, 0, .08)',
                    pointRadius: 0,
                    borderWidth: 3,
                    tension: 0
                }, {
                    label: 'IA',
                    data: growthData.ia,
                    borderColor: '#00a878',
                    backgroundColor: 'rgba(0, 168, 120, .08)',
                    pointRadius: 0,
                    borderWidth: 3,
                    tension: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        align: 'start',
                        labels: {
                            boxWidth: 11,
                            boxHeight: 11,
                            color: statusKerjasamaTextColor(),
                            font: {
                                size: 11,
                                family: "'Plus Jakarta Sans', sans-serif"
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: tooltipColors.background,
                        borderColor: tooltipColors.border,
                        borderWidth: 1,
                        titleColor: tooltipColors.title,
                        bodyColor: tooltipColors.body,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.formattedValue;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: statusKerjasamaGridColor(),
                            lineWidth: 1.4
                        },
                        ticks: {
                            color: statusKerjasamaTextColor(),
                            font: {
                                size: 10
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: statusKerjasamaGridColor(),
                            lineWidth: 1.4
                        },
                        border: {
                            color: statusKerjasamaGridColor()
                        },
                        ticks: {
                            color: statusKerjasamaTextColor(),
                            precision: 0,
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', createStatusKerjasamaCharts);
document.addEventListener('turbo:load', createStatusKerjasamaCharts);

if (!window.statusKerjasamaThemeObserver) {
    window.statusKerjasamaThemeObserver = new MutationObserver(function(mutations) {
        const themeChanged = mutations.some(function(mutation) {
            return mutation.attributeName === 'data-theme';
        });

        if (themeChanged) {
            statusKerjasamaApplyThemeToCharts();
        }
    });

    window.statusKerjasamaThemeObserver.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['data-theme']
    });
}
