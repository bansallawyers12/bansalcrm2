/**
 * Shared Bootstrap 5 modal helpers — stale instance recovery + safe show.
 */
'use strict';

(function (global) {
    function resolveModalEl(modalElOrId) {
        if (!modalElOrId) {
            return null;
        }
        if (typeof modalElOrId === 'string') {
            return document.getElementById(modalElOrId);
        }
        if (modalElOrId.nodeType === 1) {
            return modalElOrId;
        }
        if (global.jQuery && modalElOrId instanceof global.jQuery && modalElOrId.length) {
            return modalElOrId[0];
        }
        return null;
    }

    function ensureModalCanShow(modalEl) {
        if (!modalEl || typeof global.bootstrap === 'undefined' || !global.bootstrap.Modal) {
            return;
        }
        var instance = global.bootstrap.Modal.getInstance(modalEl);
        if (instance && !modalEl.classList.contains('show')) {
            instance.dispose();
        }
    }

    function showCrmModal(modalElOrId) {
        var modalEl = resolveModalEl(modalElOrId);
        if (!modalEl) {
            console.warn('[showCrmModal] Modal not found:', modalElOrId);
            return false;
        }

        ensureModalCanShow(modalEl);

        try {
            if (typeof global.bootstrap !== 'undefined' && global.bootstrap.Modal) {
                global.bootstrap.Modal.getOrCreateInstance(modalEl).show();
                return true;
            }
            if (global.jQuery && global.jQuery.fn.modal) {
                global.jQuery(modalEl).modal('show');
                return true;
            }
        } catch (err) {
            console.error('[showCrmModal] show error', err);
        }

        return false;
    }

    global.ensureModalCanShow = ensureModalCanShow;
    global.showCrmModal = showCrmModal;
})(typeof window !== 'undefined' ? window : this);
