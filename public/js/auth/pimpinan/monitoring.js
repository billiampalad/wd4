document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)';
    const textColor = isDark ? '#8b92a8' : '#6b7280';

    // Financial Trend Chart
    const finCtx = document.getElementById('financialTrendChart');
    if (finCtx) {
        const raw = JSON.parse(finCtx.dataset.trend || '[]');
        const bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const labels = raw.map(i => bulan[i.bulan] + ' ' + i.tahun);
        const data = raw.map(i => i.total_kontrak);

        new Chart(finCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nilai Kontrak (Rp)',
                    data: data,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245,158,11,0.08)',
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#f59e0b',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 7,
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
                            callback: v => 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(v)
                        }
                    },
                    x: { grid: { display: false }, ticks: { color: textColor, maxRotation: 45 } }
                }
            }
        });
    }
});
