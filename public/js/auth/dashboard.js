function initUnitDashboard() {
    const mainContent = document.getElementById('mainContent');
    if (!mainContent || !mainContent.classList.contains('unitdash')) return;

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

// Inisialisasi untuk Unit Dashboard
document.addEventListener('DOMContentLoaded', initUnitDashboard);
document.addEventListener('turbo:load', initUnitDashboard);
