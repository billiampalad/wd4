/**
 * Institusi – Interactive Filter
 *
 * Clicking a stat/jenis card filters the table rows.
 * Clicking the same card again (or "Jumlah Institusi") resets to show all.
 */
(function () {
    function initInstitusiFilter() {
        const page = document.getElementById('mainContent');
        if (!page) return;

        const cards = page.querySelectorAll('[data-filter]');
        const table = page.querySelector('.dk-table');
        if (!cards.length || !table) return;

        const rows = Array.from(table.querySelectorAll('tbody tr[data-type]'));
        const emptyRow = table.querySelector('tbody tr[data-empty]');

        // Prevent double-binding if Turbo re-renders
        if (table.dataset.filterBound) return;
        table.dataset.filterBound = 'true';

        let activeFilter = null;

        cards.forEach(function (card) {
            card.addEventListener('click', function () {
                const filter = this.getAttribute('data-filter');

                // Toggle: click same card again → reset
                if (activeFilter === filter || filter === 'all') {
                    activeFilter = null;
                    cards.forEach(function (c) { c.classList.remove('active'); });
                    showAll();
                    return;
                }

                activeFilter = filter;
                cards.forEach(function (c) { c.classList.remove('active'); });
                this.classList.add('active');
                applyFilter(filter);
            });
        });

        function showAll() {
            var idx = 0;
            rows.forEach(function (row) {
                row.style.display = '';
                idx++;
                renumber(row, idx);
            });
            if (emptyRow) emptyRow.style.display = rows.length === 0 ? '' : 'none';
        }

        function applyFilter(filter) {
            var idx = 0;

            rows.forEach(function (row) {
                var visible = matchFilter(row, filter);
                row.style.display = visible ? '' : 'none';
                if (visible) {
                    idx++;
                    renumber(row, idx);
                }
            });

            if (emptyRow) emptyRow.style.display = idx === 0 ? '' : 'none';
        }

        function matchFilter(row, filter) {
            var type = row.getAttribute('data-type') || '';
            var mou  = parseInt(row.getAttribute('data-mou') || '0', 10);
            var moa  = parseInt(row.getAttribute('data-moa') || '0', 10);
            var ia   = parseInt(row.getAttribute('data-ia')  || '0', 10);

            switch (filter) {
                case 'type-jurusan': return type === 'jurusan';
                case 'type-upa':     return type === 'upa';
                case 'type-pusat':   return type === 'pusat';
                case 'doc-mou':      return mou > 0;
                case 'doc-moa':      return moa > 0;
                case 'doc-ia':       return ia  > 0;
                default:             return true;
            }
        }

        function renumber(row, n) {
            var span = row.querySelector('.dk-num');
            if (span) span.textContent = String(n).padStart(2, '0');
        }
    }

    // Run immediately (DOM is already built when a partial <script> executes)
    initInstitusiFilter();

    // Also run on Turbo navigations
    document.addEventListener('turbo:load', initInstitusiFilter);
})();
