document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)';
    const textColor = isDark ? '#8b92a8' : '#6b7280';
    const surfaceColor = isDark ? '#1a1d2e' : '#ffffff';

    // 1. Tren Tahunan (Line Chart)
    const trenCtx = document.getElementById('trenTahunanChart');
    if (trenCtx) {
        const rawTren = JSON.parse(trenCtx.getAttribute('data-tren') || '[]');
        const labels = rawTren.map(item => item.tahun);
        const data = rawTren.map(item => item.total);

        new Chart(trenCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Produktivitas Kerjasama',
                    data: data,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79,70,229,0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: gridColor }, ticks: { stepSize: 1, color: textColor } },
                    x: { grid: { display: false }, ticks: { color: textColor } }
                }
            }
        });
    }

    // 2. Distribusi Jenis (Pie Chart)
    const jenisCtx = document.getElementById('distribusiJenisChart');
    if (jenisCtx) {
        const rawJenis = JSON.parse(jenisCtx.getAttribute('data-jenis') || '[]');
        const labels = rawJenis.map(item => item.jenis);
        const data = rawJenis.map(item => item.total);
        const colors = ['#ec4899', '#8b5cf6', '#3b82f6', '#10b981', '#f59e0b'];

        new Chart(jenisCtx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, data.length),
                    borderWidth: 2,
                    borderColor: surfaceColor
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: textColor, padding: 20, usePointStyle: true } }
                }
            }
        });
    }

    // 3. Top 5 Jurusan (Bar Chart)
    const jurusanCtx = document.getElementById('topJurusanChart');
    if (jurusanCtx) {
        const rawJurusan = JSON.parse(jurusanCtx.getAttribute('data-jurusan') || '[]');
        const labels = rawJurusan.map(item => item.nama_jurusan);
        const data = rawJurusan.map(item => item.total);

        new Chart(jurusanCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Kerjasama',
                    data: data,
                    backgroundColor: 'rgba(99,102,241,0.8)',
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { color: gridColor }, ticks: { stepSize: 1, color: textColor } },
                    y: { grid: { display: false }, ticks: { color: textColor, font: { size: 10 } } }
                }
            }
        });
    }

    // 4. Klasifikasi Mitra (Doughnut Chart)
    const klasifikasiCtx = document.getElementById('klasifikasiMitraChart');
    if (klasifikasiCtx) {
        const rawKlasifikasi = JSON.parse(klasifikasiCtx.getAttribute('data-klasifikasi') || '[]');
        const labels = rawKlasifikasi.map(item => item.klasifikasi);
        const data = rawKlasifikasi.map(item => item.total);
        const colors = ['#f59e0b', '#10b981', '#3b82f6', '#ec4899', '#8b5cf6', '#6366f1'];

        new Chart(klasifikasiCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, data.length),
                    borderWidth: 2,
                    borderColor: surfaceColor,
                    cutout: '70%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { color: textColor, font: { size: 10 }, usePointStyle: true } }
                }
            }
        });
    }

});
