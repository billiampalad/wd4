(function () {
    let trendChartInstance = null;

    function initFinancialTrendChart() {
        const finCtx = document.getElementById('financialTrendChart');
        if (!finCtx || typeof Chart === 'undefined') return;

        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)';
        const textColor = isDark ? '#8b92a8' : '#6b7280';
        const viewportWidth = window.innerWidth || document.documentElement.clientWidth;
        const isMobile = viewportWidth <= 767;

        if (trendChartInstance) {
            trendChartInstance.destroy();
            trendChartInstance = null;
        }

        const raw = JSON.parse(finCtx.dataset.trend || '[]');
        const bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const labels = raw.map(i => bulan[i.bulan] + ' ' + i.tahun);
        const data = raw.map(i => i.total_kontrak);

        trendChartInstance = new Chart(finCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nilai Kontrak (Rp)',
                    data: data,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245,158,11,0.08)',
                    borderWidth: isMobile ? 2 : 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#f59e0b',
                    pointBorderWidth: 2,
                    pointRadius: isMobile ? 3 : 4,
                    pointHoverRadius: isMobile ? 5 : 7,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y)
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: {
                            color: textColor,
                            font: { size: isMobile ? 10 : 12 },
                            callback: v => 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(v)
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: textColor,
                            autoSkip: true,
                            maxRotation: isMobile ? 0 : 45,
                            maxTicksLimit: isMobile ? 4 : 8,
                            font: { size: isMobile ? 10 : 12 }
                        }
                    }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', initFinancialTrendChart);
    document.addEventListener('turbo:load', initFinancialTrendChart);
    if (document.readyState !== 'loading') {
        initFinancialTrendChart();
    }
})();
