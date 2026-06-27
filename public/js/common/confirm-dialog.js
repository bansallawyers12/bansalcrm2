/**
 * Bootstrap 5 confirmation modal — replaces window.confirm() in CRM UI.
 *
 * API:
 *   crmConfirm('Delete this record?').then(function (ok) { if (ok) { … } });
 *
 * Markup helpers (no inline confirm() needed):
 *   <form data-crm-confirm="Delete this item?">…</form>
 *   <a href="…" data-crm-confirm="Are you sure?">…</a>
 *   <button type="submit" data-crm-confirm="Proceed?">…</button>
 */
(function (window) {
    'use strict';

    var modalEl = null;
    var bsModal = null;
    var pendingResolve = null;
    var nativeConfirm = window.confirm ? window.confirm.bind(window) : null;

    function ensureModal() {
        if (modalEl) {
            return modalEl;
        }

        var html =
            '<div class="modal fade" id="crmConfirmModal" tabindex="-1" aria-labelledby="crmConfirmModalTitle" aria-hidden="true">' +
            '  <div class="modal-dialog modal-dialog-centered">' +
            '    <div class="modal-content">' +
            '      <div class="modal-header">' +
            '        <h5 class="modal-title" id="crmConfirmModalTitle">Please confirm</h5>' +
            '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
            '      </div>' +
            '      <div class="modal-body"><p id="crmConfirmModalMessage" class="mb-0" style="white-space:pre-wrap"></p></div>' +
            '      <div class="modal-footer">' +
            '        <button type="button" class="btn btn-secondary" id="crmConfirmCancel" data-bs-dismiss="modal">Cancel</button>' +
            '        <button type="button" class="btn btn-primary" id="crmConfirmOk">Confirm</button>' +
            '      </div>' +
            '    </div>' +
            '  </div>' +
            '</div>';

        document.body.insertAdjacentHTML('beforeend', html);
        modalEl = document.getElementById('crmConfirmModal');

        modalEl.querySelector('#crmConfirmOk').addEventListener('click', function () {
            finish(true);
        });

        modalEl.addEventListener('hidden.bs.modal', function () {
            if (pendingResolve) {
                finish(false);
            }
        });

        return modalEl;
    }

    function getBootstrapModal() {
        ensureModal();
        if (!bsModal && window.bootstrap && window.bootstrap.Modal) {
            bsModal = new window.bootstrap.Modal(modalEl);
        }
        return bsModal;
    }

    function finish(result) {
        var resolve = pendingResolve;
        pendingResolve = null;
        if (resolve) {
            resolve(!!result);
        }
        if (bsModal && modalEl && modalEl.classList.contains('show')) {
            bsModal.hide();
        }
    }

    function cancelPending() {
        if (pendingResolve) {
            var resolve = pendingResolve;
            pendingResolve = null;
            resolve(false);
        }
    }

    /**
     * @param {string} message
     * @param {{ title?: string, confirmText?: string, cancelText?: string, confirmClass?: string }} [options]
     * @returns {Promise<boolean>}
     */
    window.crmConfirm = function (message, options) {
        options = options || {};
        cancelPending();

        return new Promise(function (resolve) {
            ensureModal();
            pendingResolve = resolve;

            document.getElementById('crmConfirmModalMessage').textContent =
                message != null ? String(message) : 'Are you sure?';
            document.getElementById('crmConfirmModalTitle').textContent =
                options.title || 'Please confirm';

            var okBtn = document.getElementById('crmConfirmOk');
            okBtn.textContent = options.confirmText || 'Confirm';
            okBtn.className = 'btn ' + (options.confirmClass || 'btn-primary');

            document.getElementById('crmConfirmCancel').textContent =
                options.cancelText || 'Cancel';

            var modal = getBootstrapModal();
            if (modal) {
                modal.show();
                return;
            }

            pendingResolve = null;
            if (nativeConfirm) {
                resolve(nativeConfirm(message));
            } else {
                resolve(false);
            }
        });
    };

    function bindDelegatedHandlers() {
        document.addEventListener('submit', function (event) {
            var form = event.target;
            if (!form || !form.getAttribute) {
                return;
            }
            var message = form.getAttribute('data-crm-confirm');
            if (!message) {
                return;
            }
            if (form.dataset.crmConfirmApproved === '1') {
                delete form.dataset.crmConfirmApproved;
                return;
            }
            event.preventDefault();
            event.stopImmediatePropagation();
            window.crmConfirm(message).then(function (ok) {
                if (!ok) {
                    return;
                }
                form.dataset.crmConfirmApproved = '1';
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.submit();
                }
            });
        }, true);

        document.addEventListener('click', function (event) {
            var target = event.target.closest('[data-crm-confirm]');
            if (!target || target.closest('form[data-crm-confirm]')) {
                return;
            }
            if (target.dataset.crmConfirmBypass === '1') {
                delete target.dataset.crmConfirmBypass;
                return;
            }

            var message = target.getAttribute('data-crm-confirm');
            if (!message) {
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();

            window.crmConfirm(message).then(function (ok) {
                if (!ok) {
                    return;
                }

                if (target.tagName === 'A' && target.href) {
                    window.location.href = target.href;
                    return;
                }

                if (target.type === 'submit' && target.form) {
                    target.form.dataset.crmConfirmApproved = '1';
                    if (typeof target.form.requestSubmit === 'function') {
                        target.form.requestSubmit(target);
                    } else {
                        target.form.submit();
                    }
                    return;
                }

                target.dataset.crmConfirmBypass = '1';
                target.click();
            });
        }, true);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindDelegatedHandlers);
    } else {
        bindDelegatedHandlers();
    }
})(window);
