(function () {
    'use strict';

    function create(options) {
        const settings = Object.assign({ text: 'Memuat...', size: 50, inline: false }, options || {});
        const source = document.getElementById('app-loading-template');
        if (!source) return null;

        const fragment = source.content.cloneNode(true);
        const indicator = fragment.querySelector('.app-loading-indicator');
        const spinner = fragment.querySelector('.app-loading-spinner');
        const text = fragment.querySelector('.app-loading-text');

        if (settings.inline) indicator.classList.add('is-inline');
        spinner.style.setProperty('--uib-size', Number(settings.size) + 'px');
        if (settings.text) text.textContent = settings.text;
        else text.remove();

        return indicator;
    }

    function html(options) {
        const node = create(options);
        return node ? node.outerHTML : '';
    }

    function tableRow(colspan, text) {
        return '<tr data-loading-row><td colspan="' + Number(colspan || 1) +
            '" class="app-loading-cell">' + html({ text: text || 'Memuat data...', size: 42 }) +
            '</td></tr>';
    }

    function setButton(button, text) {
        if (!button) return;
        if (!button.dataset.loadingOriginal) button.dataset.loadingOriginal = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="app-loading-button">' +
            html({ text: '', size: 18, inline: true }) +
            '<span>' + (text || 'Memproses...') + '</span></span>';
    }

    function resetButton(button) {
        if (!button) return;
        button.disabled = false;
        if (button.dataset.loadingOriginal) {
            button.innerHTML = button.dataset.loadingOriginal;
            delete button.dataset.loadingOriginal;
        }
    }

    function swal(title, text) {
        if (!window.Swal) return;
        window.Swal.fire({
            title: title || 'Memproses Data',
            html: html({ text: text || 'Mohon tunggu...', size: 46 }),
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false
        });
    }

    function showPage(text) {
        const element = document.getElementById('app-loading-overlay');
        if (!element) return;
        const label = element.querySelector('.app-loading-text');
        if (label && text) label.textContent = text;
        element.classList.add('is-visible');
        element.setAttribute('aria-hidden', 'false');
    }

    function hidePage() {
        const element = document.getElementById('app-loading-overlay');
        if (!element) return;
        element.classList.remove('is-visible');
        element.setAttribute('aria-hidden', 'true');
    }

    function shouldShowForLink(link, event) {
        if (!link || event.defaultPrevented || event.button !== 0) return false;
        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return false;
        if (link.target === '_blank' || link.hasAttribute('download')) return false;
        if (link.dataset.noLoading !== undefined || !link.href || link.href.startsWith('javascript:')) return false;

        const destination = new URL(link.href, window.location.href);
        if (destination.origin !== window.location.origin) return false;
        return !(destination.pathname === window.location.pathname &&
            destination.search === window.location.search && destination.hash);
    }

    function bindGlobalEvents() {
        document.addEventListener('click', function (event) {
            const link = event.target.closest('a[href]');
            if (shouldShowForLink(link, event)) showPage('Membuka halaman...');
        });

        document.addEventListener('submit', function (event) {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) return;
            if (form.dataset.noLoading !== undefined || form.target === '_blank') return;
            window.setTimeout(function () {
                if (!event.defaultPrevented) {
                    showPage(form.dataset.loadingText || 'Memproses data...');
                }
            }, 0);
        });

        window.addEventListener('pageshow', hidePage);
        window.addEventListener('load', hidePage);
        document.addEventListener('turbo:load', hidePage);
        document.addEventListener('turbo:before-cache', hidePage);
    }

    window.AppLoading = {
        create: create,
        html: html,
        tableRow: tableRow,
        setButton: setButton,
        resetButton: resetButton,
        swal: swal,
        showPage: showPage,
        hidePage: hidePage
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindGlobalEvents, { once: true });
    } else {
        bindGlobalEvents();
    }
})();
