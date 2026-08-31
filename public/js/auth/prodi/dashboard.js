/**
 * Prodi Dashboard Charts & Interaction
 * Fully compatible with Hotwire Turbo lifecycle
 */

function initProdiDashboard() {
    const dataContainer = document.getElementById('prodiDashboardData');
    const donutCanvas = document.getElementById('chartStatusDonut');
    const barCanvas = document.getElementById('chartTrendBar');

    // If elements don't exist on the current page, exit cleanly
    if (!dataContainer || !donutCanvas || !barCanvas) {
        return;
    }

    if (typeof Chart === 'undefined') {
        console.warn('Chart.js is not loaded.');
        return;
    }

    // Safely destroy existing chart instances before recreating
    const existingDonut = Chart.getChart(donutCanvas);
    if (existingDonut) {
        existingDonut.destroy();
    }

    const existingBar = Chart.getChart(barCanvas);
    if (existingBar) {
        existingBar.destroy();
    }

    // Parse data from container
    let statusData = {};
    let trendData = { labels: [], data: [] };

    try {
        statusData = JSON.parse(dataContainer.getAttribute('data-status') || '{}');
        trendData = JSON.parse(dataContainer.getAttribute('data-trend') || '{"labels":[],"data":[]}');
    } catch (e) {
        console.error('Failed to parse prodi dashboard chart data:', e);
    }

    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
    const textColor = isDark ? '#94a3b8' : '#64748b';

    // ─── 1. Donut Chart (Distribusi Status Mahasiswa) ─────────────
    const statusLabels = Object.keys(statusData);
    const statusValues = Object.values(statusData);

    new Chart(donutCanvas, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusValues,
                backgroundColor: ['#6366f1', '#10b981', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 8,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: textColor,
                        font: { family: 'Plus Jakarta Sans', size: 12, weight: 600 },
                        padding: 16,
                        usePointStyle: true,
                        pointStyleWidth: 10,
                    }
                },
                tooltip: {
                    backgroundColor: isDark ? '#1e293b' : '#fff',
                    titleColor: isDark ? '#e2e8f0' : '#1e293b',
                    bodyColor: isDark ? '#94a3b8' : '#475569',
                    borderWidth: 1,
                    borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.08)',
                    cornerRadius: 12,
                    padding: 12,
                    titleFont: { family: 'Plus Jakarta Sans', size: 13, weight: 700 },
                    bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                    callbacks: {
                        label: function(ctx) {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                            return ' ' + ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });

    // ─── 2. Bar Chart (Trend Penempatan per Tahun) ─────────────────
    new Chart(barCanvas, {
        type: 'bar',
        data: {
            labels: trendData.labels || [],
            datasets: [{
                label: 'Penempatan',
                data: trendData.data || [],
                backgroundColor: 'rgba(16,185,129,0.2)',
                borderColor: '#10b981',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
                maxBarThickness: 44,
                hoverBackgroundColor: 'rgba(16,185,129,0.4)',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: { intersect: false, mode: 'index' },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: textColor,
                        font: { family: 'Plus Jakarta Sans', size: 12, weight: 600 }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor, drawBorder: false },
                    ticks: {
                        color: textColor,
                        font: { family: 'Plus Jakarta Sans', size: 12 },
                        stepSize: 1,
                    }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? '#1e293b' : '#fff',
                    titleColor: isDark ? '#e2e8f0' : '#1e293b',
                    bodyColor: isDark ? '#94a3b8' : '#475569',
                    borderWidth: 1,
                    borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.08)',
                    cornerRadius: 12,
                    padding: 12,
                    titleFont: { family: 'Plus Jakarta Sans', size: 13, weight: 700 },
                    bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                }
            }
        }
    });
}

// Listen to Turbo and standard DOM load events
document.addEventListener('turbo:load', initProdiDashboard);
document.addEventListener('DOMContentLoaded', initProdiDashboard);

// Re-render charts on dark mode toggle
window.addEventListener('theme-changed', function() {
    initProdiDashboard();
});
