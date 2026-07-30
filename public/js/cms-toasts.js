/**
 * Global CMS toast API: CmsToast(message, type = 'info', opts?)
 * Types: success | error | warning | info
 */
(function (global) {
    'use strict';
    if (global.CmsToast) return;

    var HOST_ID = 'cms-toast-host';
    var ICONS = {
        success: '<svg class="cms-toast__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>',
        error: '<svg class="cms-toast__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M15 9 9 15M9 9l6 6"/></svg>',
        warning: '<svg class="cms-toast__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M12 3 2 21h20L12 3z"/><path d="M12 9v5M12 17h.01"/></svg>',
        info: '<svg class="cms-toast__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8h.01M11 12h1v5h1"/></svg>'
    };

    function host() {
        var el = document.getElementById(HOST_ID);
        if (el) return el;
        el = document.createElement('div');
        el.id = HOST_ID;
        el.className = 'cms-toast-host';
        el.setAttribute('aria-live', 'polite');
        el.setAttribute('aria-relevant', 'additions');
        document.body.appendChild(el);
        return el;
    }

    function normalizeType(type) {
        type = String(type || 'info').toLowerCase();
        if (type === 'danger' || type === 'fail' || type === 'failed') return 'error';
        if (type === 'ok' || type === 'done') return 'success';
        if (['success', 'error', 'warning', 'info'].indexOf(type) === -1) return 'info';
        return type;
    }

    function CmsToast(message, type, opts) {
        message = String(message == null ? '' : message).trim();
        if (!message) return null;
        type = normalizeType(type);
        opts = opts || {};
        var ttl = typeof opts.duration === 'number' ? opts.duration : (type === 'error' ? 7000 : 4500);

        var node = document.createElement('div');
        node.className = 'cms-toast cms-toast--' + type;
        node.setAttribute('role', type === 'error' ? 'alert' : 'status');
        node.innerHTML =
            (ICONS[type] || ICONS.info) +
            '<p class="cms-toast__msg"></p>' +
            '<button type="button" class="cms-toast__x" aria-label="Close">×</button>';
        node.querySelector('.cms-toast__msg').textContent = message;

        var closed = false;
        function close() {
            if (closed) return;
            closed = true;
            node.classList.remove('is-in');
            node.classList.add('is-out');
            setTimeout(function () {
                if (node.parentNode) node.parentNode.removeChild(node);
            }, 220);
        }

        node.querySelector('.cms-toast__x').addEventListener('click', close);
        host().appendChild(node);
        requestAnimationFrame(function () { node.classList.add('is-in'); });
        if (ttl > 0) setTimeout(close, ttl);
        return { close: close, el: node };
    }

    CmsToast.success = function (m, o) { return CmsToast(m, 'success', o); };
    CmsToast.error = function (m, o) { return CmsToast(m, 'error', o); };
    CmsToast.warning = function (m, o) { return CmsToast(m, 'warning', o); };
    CmsToast.info = function (m, o) { return CmsToast(m, 'info', o); };

    CmsToast.fromQueue = function (items) {
        if (!items || !items.length) return;
        items.forEach(function (item, i) {
            setTimeout(function () {
                CmsToast(item.message, item.type || 'info', item);
            }, i * 120);
        });
    };

    global.CmsToast = CmsToast;
})(typeof window !== 'undefined' ? window : this);
