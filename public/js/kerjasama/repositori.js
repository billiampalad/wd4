(function () {
    function setDetailHeight(detailRow, isOpen) {
        if (!detailRow) return;

        var content = detailRow.querySelector('.dk-detail-content');
        if (!content) return;

        if (isOpen) {
            detailRow.classList.add('is-open');
            detailRow.setAttribute('aria-hidden', 'false');
            content.style.maxHeight = content.scrollHeight + 'px';
            return;
        }

        content.style.maxHeight = content.scrollHeight + 'px';
        window.requestAnimationFrame(function () {
            detailRow.classList.remove('is-open');
            detailRow.setAttribute('aria-hidden', 'true');
            content.style.maxHeight = '0px';
        });
    }

    function closeOpenRows(scope) {
        (scope || document).querySelectorAll('.dk-row.is-expanded').forEach(function (row) {
            var button = row.querySelector('.dk-expand-toggle');
            var detailRow = row.nextElementSibling && row.nextElementSibling.classList.contains('dk-row-detail')
                ? row.nextElementSibling
                : null;

            row.classList.remove('is-expanded');
            if (button) button.setAttribute('aria-expanded', 'false');
            setDetailHeight(detailRow, false);
        });
    }

    function toggleRow(button) {
        var row = button.closest('.dk-row');
        if (!row) return;

        var detailRow = row.nextElementSibling && row.nextElementSibling.classList.contains('dk-row-detail')
            ? row.nextElementSibling
            : null;

        if (!detailRow) return;

        var isOpen = row.classList.toggle('is-expanded');
        button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        setDetailHeight(detailRow, isOpen);
    }

    function initRepositoriExpansion() {
        if (document.documentElement.dataset.repositoriExpansionBound === 'true') return;
        document.documentElement.dataset.repositoriExpansionBound = 'true';

        document.addEventListener('click', function (event) {
            var button = event.target.closest('.dk-expand-toggle');
            if (!button) return;

            event.preventDefault();
            toggleRow(button);
        });

        window.addEventListener('resize', function () {
            document.querySelectorAll('.dk-row-detail.is-open .dk-detail-content').forEach(function (content) {
                content.style.maxHeight = content.scrollHeight + 'px';
            });
        });

        var previewBody = document.getElementById('previewBody');
        if (previewBody && window.MutationObserver) {
            new MutationObserver(function () {
                closeOpenRows(previewBody);
            }).observe(previewBody, { childList: true });
        }
    }

    document.addEventListener('DOMContentLoaded', initRepositoriExpansion);
    document.addEventListener('turbo:load', initRepositoriExpansion);
    if (document.readyState !== 'loading') {
        initRepositoriExpansion();
    }
})();
