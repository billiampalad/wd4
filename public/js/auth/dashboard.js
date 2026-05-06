function initUnitDashboard() {
    const mainContent = document.getElementById('mainContent');
    if (!mainContent || !mainContent.classList.contains('unitdash')) return;

    initUnitChart();

    const tabs = document.querySelectorAll('[data-filter-tab]');
    const rows = document.querySelectorAll('[data-kerjasama-row]');
    const noResult = document.getElementById('unitDashNoResult');

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            const filter = tab.dataset.filterTab;
            let visibleCount = 0;

            tabs.forEach(function(item) {
                item.classList.toggle('is-active', item === tab);
            });

            rows.forEach(function(row) {
                const isVisible = filter === 'all' || row.dataset.docType === filter;
                row.style.display = isVisible ? '' : 'none';
                if (isVisible) visibleCount += 1;
            });

            if (noResult) {
                noResult.style.display = visibleCount === 0 ? '' : 'none';
            }
        });
    });

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

// Inisialisasi untuk Unit Dashboard
document.addEventListener('DOMContentLoaded', initUnitDashboard);
document.addEventListener('turbo:load', initUnitDashboard);
