function openModal(id, nama, mitra, nomou, periode, statusLabel, statusClass) {
    document.getElementById('modal-title').textContent = nama;
    document.getElementById('modal-mitra').textContent = mitra;
    document.getElementById('modal-nomou').textContent = nomou;
    document.getElementById('modal-periode').textContent = periode;

    const badge = document.getElementById('modal-status-badge');
    badge.textContent = statusLabel;
    badge.className = 'status-pill ' + statusClass;

    document.getElementById('detailModal').classList.add('open');
}

function closeModal(event) {
    if (event.target === document.getElementById('detailModal')) {
        document.getElementById('detailModal').classList.remove('open');
    }
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.getElementById('detailModal').classList.remove('open');
    }
});

(function () {
    const themeToggle = document.querySelector('[data-theme-toggle]');
    const themeLabel = themeToggle ? themeToggle.querySelector('[data-theme-toggle-label]') : null;
    const storageKey = 'welcome-theme';

    function applyTheme(theme) {
        const isDark = theme === 'dark';
        document.documentElement.dataset.theme = isDark ? 'dark' : 'light';

        if (themeToggle) {
            themeToggle.setAttribute('aria-pressed', String(isDark));
            themeToggle.setAttribute('aria-label', isDark ? 'Ubah ke mode terang' : 'Ubah ke mode gelap');
        }

        if (themeLabel) {
            themeLabel.textContent = isDark ? 'Mode Terang' : 'Mode Gelap';
        }
    }

    function getInitialTheme() {
        const datasetTheme = document.documentElement.dataset.theme;

        if (datasetTheme === 'dark' || datasetTheme === 'light') {
            return datasetTheme;
        }

        try {
            const savedTheme = localStorage.getItem(storageKey);

            if (savedTheme === 'dark' || savedTheme === 'light') {
                return savedTheme;
            }
        } catch (error) {
            return 'light';
        }

        return 'light';
    }

    applyTheme(getInitialTheme());

    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';

            try {
                localStorage.setItem(storageKey, nextTheme);
            } catch (error) {
                // Ignore storage failures and still apply the visual theme.
            }

            applyTheme(nextTheme);
        });
    }
})();

(function () {
    const mainSection = document.getElementById('data-kerjasama');

    if (!mainSection) {
        return;
    }

    let activeController = null;

    function buildRequestUrl(form) {
        const url = new URL(form.action || window.location.href, window.location.origin);
        const params = new URLSearchParams(new FormData(form));

        if (!params.get('search')) {
            params.delete('search');
        }

        url.search = params.toString();
        return url.toString();
    }

    async function loadKerjasamaSection(url, options = {}) {
        const { pushState = true } = options;

        if (activeController) {
            activeController.abort();
        }

        const controller = new AbortController();
        activeController = controller;
        mainSection.classList.add('is-loading');
        mainSection.setAttribute('aria-busy', 'true');

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: controller.signal,
            });

            if (!response.ok) {
                throw new Error('Failed to load landing data');
            }

            const html = await response.text();
            const parsed = new DOMParser().parseFromString(html, 'text/html');
            const nextMainSection = parsed.getElementById('data-kerjasama');

            if (!nextMainSection) {
                throw new Error('Landing section not found in response');
            }

            mainSection.innerHTML = nextMainSection.innerHTML;

            if (pushState) {
                window.history.pushState({ landingAsync: true }, '', url);
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                window.location.href = url;
            }
        } finally {
            if (activeController === controller) {
                activeController = null;
                mainSection.classList.remove('is-loading');
                mainSection.removeAttribute('aria-busy');
            }
        }
    }

    document.addEventListener('change', function (event) {
        const filterInput = event.target.closest('[data-landing-filter] input[name="kategori_mitra"]');

        if (!filterInput) {
            return;
        }

        loadKerjasamaSection(buildRequestUrl(filterInput.form));
    });

    document.addEventListener('submit', function (event) {
        const filterForm = event.target.closest('form[data-landing-filter]');

        if (!filterForm) {
            return;
        }

        event.preventDefault();
        loadKerjasamaSection(buildRequestUrl(filterForm));
    });

    document.addEventListener('click', function (event) {
        const paginationLink = event.target.closest('#data-kerjasama .pagination-wrap a');

        if (!paginationLink || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        event.preventDefault();
        loadKerjasamaSection(paginationLink.href);
    });

    window.addEventListener('popstate', function () {
        loadKerjasamaSection(window.location.href, { pushState: false });
    });
})();