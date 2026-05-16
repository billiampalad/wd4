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

function statusKerjasamaHexToRgba(hex, alpha) {
    const cleanHex = String(hex || '').replace('#', '');
    const value = cleanHex.length === 3
        ? cleanHex.split('').map(function (char) { return char + char; }).join('')
        : cleanHex;

    if (value.length !== 6) return 'rgba(148, 163, 184, ' + alpha + ')';

    const red = parseInt(value.slice(0, 2), 16);
    const green = parseInt(value.slice(2, 4), 16);
    const blue = parseInt(value.slice(4, 6), 16);

    return 'rgba(' + red + ', ' + green + ', ' + blue + ', ' + alpha + ')';
}

function statusKerjasamaApplyThemeToCharts() {
    const textColor = statusKerjasamaTextColor();
    const gridColor = statusKerjasamaGridColor();
    const surfaceColor = statusKerjasamaSurfaceColor();
    const tooltipColors = statusKerjasamaTooltipColors();

    if (window.statusKerjasamaDonutChart) {
        const donut = window.statusKerjasamaDonutChart;

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

    if (window.mouVsMoaIaChartInstance && typeof window.mouVsMoaIaChartInstance.update === 'function') {
        const chart = window.mouVsMoaIaChartInstance;

        chart.data.datasets.forEach(function (dataset) {
            dataset.borderColor = surfaceColor;
        });

        chart.options.plugins.tooltip.backgroundColor = tooltipColors.background;
        chart.options.plugins.tooltip.borderColor = tooltipColors.border;
        chart.options.plugins.tooltip.borderWidth = 1;
        chart.options.plugins.tooltip.titleColor = tooltipColors.title;
        chart.options.plugins.tooltip.bodyColor = tooltipColors.body;

        if (chart.options.scales && chart.options.scales.r) {
            chart.options.scales.r.grid.color = gridColor;
            chart.options.scales.r.ticks.color = textColor;
            var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            chart.options.scales.r.ticks.backdropColor = statusKerjasamaHexToRgba(
                isDark ? '#181c27' : '#ffffff', .85
            );
        }

        chart.options.plugins.legend.labels.color = textColor;

        chart.update('none');
    }

    if (window.sebaranDokumenChartInstance && typeof window.sebaranDokumenChartInstance.update === 'function') {
        const sebaran = window.sebaranDokumenChartInstance;

        sebaran.options.plugins.tooltip.backgroundColor = tooltipColors.background;
        sebaran.options.plugins.tooltip.borderColor = tooltipColors.border;
        sebaran.options.plugins.tooltip.borderWidth = 1;
        sebaran.options.plugins.tooltip.titleColor = tooltipColors.title;
        sebaran.options.plugins.tooltip.bodyColor = tooltipColors.body;

        if (sebaran.options.scales.x) {
            sebaran.options.scales.x.grid.color = gridColor;
            sebaran.options.scales.x.border.color = gridColor;
            sebaran.options.scales.x.ticks.color = textColor;
        }
        if (sebaran.options.scales.y) {
            sebaran.options.scales.y.border.color = gridColor;
            sebaran.options.scales.y.ticks.color = textColor;
            sebaran.options.scales.y.title.color = textColor;
        }

        sebaran.update('none');
    }
}

function createStatusKerjasamaCharts() {
    const page = document.getElementById('mainContent');
    if (!page || !page.classList.contains('sk-page') || typeof Chart === 'undefined') return;

    const statusCanvas = document.getElementById('statusKerjasamaChart');
    const growthCanvas = document.getElementById('pertumbuhanKerjasamaChart');
    const mouVsMoaIaCanvas = document.getElementById('mouVsMoaIaChart');
    const sebaranCanvas = document.getElementById('sebaranDokumenChart');
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
    const mouVsMoaIaData = parseStatusKerjasamaJson('mouVsMoaIaData', {
        labels: [],
        colors: [],
        mou: [],
        moa_ia: []
    });
    const sebaranDokumenData = parseStatusKerjasamaJson('sebaranDokumenData', {
        labels: [],
        aktif: [],
        dalam_perpanjangan: [],
        kadaluarsa: []
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
                            label: function (context) {
                                const total = context.dataset.data.reduce(function (sum, value) {
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
                            label: function (context) {
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

    if (mouVsMoaIaCanvas) {
        if (window.mouVsMoaIaChartInstance && typeof window.mouVsMoaIaChartInstance.destroy === 'function') {
            window.mouVsMoaIaChartInstance.destroy();
        }

        const statusLabels = Array.isArray(mouVsMoaIaData.labels) ? mouVsMoaIaData.labels : [];
        const statusColors = Array.isArray(mouVsMoaIaData.colors) ? mouVsMoaIaData.colors : [];
        const mouValues = Array.isArray(mouVsMoaIaData.mou) ? mouVsMoaIaData.mou : [];
        const moaIaValues = Array.isArray(mouVsMoaIaData.moa_ia) ? mouVsMoaIaData.moa_ia : [];
        const totalValues = statusLabels.map(function (label, index) {
            return Number(mouValues[index] || 0) + Number(moaIaValues[index] || 0);
        });
        const grandTotal = totalValues.reduce(function (sum, value) {
            return sum + Number(value || 0);
        }, 0);

        const bgColors = statusColors.map(function (color) {
            return statusKerjasamaHexToRgba(color, .72);
        });
        const borderColors = statusColors.map(function (color) {
            return statusKerjasamaHexToRgba(color, 1);
        });

        const gridColor = statusKerjasamaGridColor();
        const textColor = statusKerjasamaTextColor();
        const surfaceColor = statusKerjasamaSurfaceColor();

        window.mouVsMoaIaChartInstance = new Chart(mouVsMoaIaCanvas, {
            type: 'polarArea',
            data: {
                labels: statusLabels,
                datasets: [{
                    label: 'MoU vs MoA/IA',
                    data: grandTotal ? totalValues : statusLabels.map(function () { return 1; }),
                    backgroundColor: grandTotal ? bgColors : statusColors.map(function (color) {
                        return statusKerjasamaHexToRgba(color, .22);
                    }),
                    borderColor: grandTotal ? borderColors : statusColors.map(function (color) {
                        return statusKerjasamaHexToRgba(color, .35);
                    }),
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            boxWidth: 14,
                            boxHeight: 14,
                            padding: 12,
                            color: textColor,
                            font: {
                                size: 11,
                                weight: '600',
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
                            title: function (contexts) {
                                var context = contexts && contexts.length ? contexts[0] : null;
                                return context ? context.label : '';
                            },
                            label: function (context) {
                                var index = context.dataIndex;
                                var mou = Number(mouValues[index] || 0);
                                var moaIa = Number(moaIaValues[index] || 0);
                                var total = mou + moaIa;
                                return [
                                    'MoU: ' + mou + (total ? ' (' + Math.round((mou / total) * 100) + '%)' : ''),
                                    'MoA/IA: ' + moaIa + (total ? ' (' + Math.round((moaIa / total) * 100) + '%)' : ''),
                                    'Total: ' + total
                                ];
                            },
                            labelColor: function (context) {
                                var color = statusColors[context.dataIndex] || '#94a3b8';
                                return {
                                    borderColor: color,
                                    backgroundColor: color
                                };
                            }
                        }
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        z: 1,
                        grid: {
                            color: gridColor
                        },
                        ticks: {
                            color: textColor,
                            backdropColor: statusKerjasamaHexToRgba(
                                surfaceColor.indexOf('#') === 0 ? surfaceColor : (
                                    document.documentElement.getAttribute('data-theme') === 'dark' ? '#181c27' : '#ffffff'
                                ), .85
                            ),
                            backdropPadding: 4,
                            precision: 0,
                            font: {
                                size: 11,
                                weight: '700'
                            }
                        }
                    }
                }
            }
        });
    }

    if (sebaranCanvas) {
        if (window.sebaranDokumenChartInstance && typeof window.sebaranDokumenChartInstance.destroy === 'function') {
            window.sebaranDokumenChartInstance.destroy();
        }

        const gridColor = statusKerjasamaGridColor();
        const textColor = statusKerjasamaTextColor();
        const sebaranLabels = Array.isArray(sebaranDokumenData.labels) ? sebaranDokumenData.labels : [];
        const sebaranAktif = Array.isArray(sebaranDokumenData.aktif) ? sebaranDokumenData.aktif : [];
        const sebaranDalamPerpanjangan = Array.isArray(sebaranDokumenData.dalam_perpanjangan)
            ? sebaranDokumenData.dalam_perpanjangan
            : [];
        const sebaranKadaluarsa = Array.isArray(sebaranDokumenData.kadaluarsa)
            ? sebaranDokumenData.kadaluarsa
            : [];

        window.sebaranDokumenChartInstance = new Chart(sebaranCanvas, {
            type: 'bar',
            data: {
                labels: sebaranLabels,
                datasets: [
                    {
                        label: 'Aktif',
                        data: sebaranAktif,
                        backgroundColor: '#10b981',
                        borderRadius: { topRight: 999, bottomRight: 999 },
                        barPercentage: 0.85,
                        categoryPercentage: 0.8
                    },
                    {
                        label: 'Dalam Perpanjangan',
                        data: sebaranDalamPerpanjangan,
                        backgroundColor: '#f59e0b',
                        borderRadius: { topRight: 999, bottomRight: 999 },
                        barPercentage: 0.85,
                        categoryPercentage: 0.8
                    },
                    {
                        label: 'Kadaluarsa',
                        data: sebaranKadaluarsa,
                        backgroundColor: '#ef4444',
                        borderRadius: { topRight: 999, bottomRight: 999 },
                        barPercentage: 0.85,
                        categoryPercentage: 0.8
                    }
                ]
            },
            options: {
                indexAxis: 'y',
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
                        bodyColor: tooltipColors.body,
                        callbacks: {
                            title: function (context) {
                                var label = context[0].label || '';
                                if (label === 'MoU') return 'Memorandum of Understanding (MoU)';
                                if (label === 'MoA') return 'Memorandum of Agreement (MoA)';
                                if (label === 'IA') return 'Implementation Agreement (IA)';
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
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
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        border: {
                            color: gridColor
                        },
                        ticks: {
                            color: textColor,
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        },
                        title: {
                            display: true,
                            text: 'Dokumen',
                            color: textColor,
                            font: {
                                size: 13,
                                weight: '700'
                            }
                        }
                    }
                }
            }
        });
    }
}

function initDueDateContributionGraph() {
    const cells = document.querySelectorAll('.sk-due-weeks button.sk-due-cell:not(.sk-due-cell-empty)');
    const yearSelect = document.getElementById('dueYearSelect');

    if (yearSelect && yearSelect.dataset.dueYearBound !== '1') {
        yearSelect.dataset.dueYearBound = '1';
        yearSelect.addEventListener('change', function () {
            yearSelect.form.submit();
        });
    }

    if (!cells.length) return;

    let tooltip = document.querySelector('.sk-due-tooltip');

    if (!tooltip) {
        tooltip = document.createElement('div');
        tooltip.className = 'sk-due-tooltip';
        document.body.appendChild(tooltip);
    }

    function showTooltip(cell) {
        const count = Number(cell.dataset.count || 0);
        const rect = cell.getBoundingClientRect();

        tooltip.textContent = 'Pada ' + (cell.dataset.date || '-') + (count ? ' : ' + count : '');
        tooltip.style.left = rect.left + rect.width / 2 + 'px';
        tooltip.style.top = rect.top - 10 + 'px';
        tooltip.classList.add('is-visible');
    }

    function hideTooltip() {
        tooltip.classList.remove('is-visible');
    }

    cells.forEach(function (cell) {
        if (cell.dataset.dueTooltipBound === '1') return;

        cell.dataset.dueTooltipBound = '1';
        cell.addEventListener('mouseenter', function () {
            showTooltip(cell);
        });
        cell.addEventListener('focus', function () {
            showTooltip(cell);
        });
        cell.addEventListener('mouseleave', hideTooltip);
        cell.addEventListener('blur', hideTooltip);
    });
}

function initStatusKerjasamaPage() {
    createStatusKerjasamaCharts();
    initDueDateContributionGraph();
}

document.addEventListener('DOMContentLoaded', initStatusKerjasamaPage);
document.addEventListener('turbo:load', initStatusKerjasamaPage);

if (!window.statusKerjasamaThemeObserver) {
    window.statusKerjasamaThemeObserver = new MutationObserver(function (mutations) {
        const themeChanged = mutations.some(function (mutation) {
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
