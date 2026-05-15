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
                    borderColor: '#ffffff',
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
