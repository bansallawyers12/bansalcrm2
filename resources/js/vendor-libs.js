/**
 * Third-party Vendor Libraries Entry Point
 *
 * Bundled here (Phase 2b/2c): flatpickr, iziToast, Tom Select, DataTables (Bootstrap 5).
 * jQuery is loaded synchronously in layout <head>; npm imports use jquery-global-shim.js.
 * Page-specific entries: apexcharts-init.js, signature-pad-init.js (Vite).
 */

import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

import iziToast from 'izitoast';
import 'izitoast/dist/css/iziToast.min.css';

import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.min.css';

import DataTable from 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.css';

window.flatpickr = flatpickr;
window.iziToast = iziToast;
window.TomSelect = TomSelect;
window.DataTable = DataTable;

/** Native alert kept for fallbacks when iziToast is unavailable. */
const nativeAlert = window.alert.bind(window);
window.__nativeAlert = nativeAlert;

/**
 * Shared toast helper for legacy scripts (replaces alert()).
 * @param {string} message
 * @param {'success'|'error'|'warning'|'info'} [type]
 */
window.showToast = function (message, type) {
    type = type || 'info';
    var text = (message != null && String(message).trim() !== '') ? String(message).trim() : '';
    if (!text) {
        if (type === 'error') {
            text = 'Something went wrong.';
        } else if (type === 'success') {
            text = 'Done.';
        } else if (type === 'warning') {
            text = 'Please check your input.';
        } else {
            return;
        }
    }
    text = text.replace(/\s*\n+\s*/g, ' — ');
    if (typeof window.iziToast !== 'undefined') {
        var opts = {
            message: text,
            position: 'topRight',
            timeout: type === 'error' ? 8000 : 5000,
        };
        if (type === 'error' && typeof window.iziToast.error === 'function') {
            window.iziToast.error(opts);
        } else if (type === 'success' && typeof window.iziToast.success === 'function') {
            window.iziToast.success(opts);
        } else if (type === 'warning' && typeof window.iziToast.warning === 'function') {
            window.iziToast.warning(opts);
        } else {
            window.iziToast.show(opts);
        }
        return;
    }
    nativeAlert(text);
};
window.showLegacyToast = window.showToast;

/**
 * Route legacy alert() calls to iziToast (non-blocking).
 * showToast / toastMsg fallbacks use nativeAlert to avoid recursion.
 */
window.alert = function (message) {
    if (message == null || String(message).trim() === '') {
        return;
    }
    if (typeof window.showToast === 'function') {
        var text = String(message);
        var type = 'info';
        if (/^error[:\s]/i.test(text)) {
            type = 'error';
        } else if (/^success[:\s]/i.test(text)) {
            type = 'success';
        } else if (/^warning[:\s]/i.test(text)) {
            type = 'warning';
        }
        window.showToast(text, type);
        return;
    }
    nativeAlert(message);
};

// toastMsg lives in public/js/common/utilities.js (loaded from admin layouts)

// Create a promise that resolves when all plugins are ready
const waitForPlugins = () => {
    return new Promise((resolve, reject) => {
        let attempts = 0;
        const maxAttempts = 200; // 10 seconds max (200 * 50ms)
        
        const check = () => {
            attempts++;
            
            // Check if jQuery is available
            const jQueryAvailable = typeof window.$ !== 'undefined' || typeof window.jQuery !== 'undefined';
            const $ = window.$ || window.jQuery;
            
            const dataTableReady = jQueryAvailable && typeof $.fn.DataTable === 'function';
            const flatpickrReady = typeof window.flatpickr !== 'undefined';
            const iziToastReady = typeof window.iziToast !== 'undefined';

            const tomSelectLibReady = typeof window.TomSelect === 'function';
            const initTomSelectReady = typeof window.initTomSelect === 'function';

            if (attempts % 20 === 0) {
                console.log('Checking plugins...', {
                    jQuery: jQueryAvailable,
                    tomSelectLib: tomSelectLibReady,
                    initTomSelect: initTomSelectReady,
                    dataTable: dataTableReady,
                    flatpickr: flatpickrReady,
                    iziToast: iziToastReady,
                    attempts: attempts
                });
            }

            // Resolve once Vite-bundled libs are ready. initTomSelect (public/js) may load later.
            if (flatpickrReady && iziToastReady && tomSelectLibReady && dataTableReady) {
                console.log('✅ Vite vendor libraries loaded: flatpickr, iziToast, Tom Select, DataTables');
                if (initTomSelectReady) {
                    console.log('✅ initTomSelect helpers available');
                }
                resolve();
            } else if (attempts >= maxAttempts) {
                const missing = [];
                if (!jQueryAvailable) missing.push('jQuery');
                if (!flatpickrReady) missing.push('flatpickr');
                if (!iziToastReady) missing.push('iziToast');
                if (!tomSelectLibReady) missing.push('Tom Select (Vite)');
                if (!dataTableReady) missing.push('DataTables (Vite)');
                
                console.warn('⚠️ Vendor libraries timeout. Missing:', missing.join(', '));
                // Resolve anyway to not block the page
                resolve();
            } else {
                // Retry every 50ms until plugins are ready
                setTimeout(check, 50);
            }
        };
        
        // Start checking after a small delay to allow imports to process
        setTimeout(check, 100);
    });
};

// Expose the promise globally
window.vendorLibsReady = waitForPlugins();

// Also dispatch event for backward compatibility
window.vendorLibsReady.then(() => {
    document.dispatchEvent(new Event('VendorLibsLoaded'));
    console.log('📢 VendorLibsLoaded event dispatched');
}).catch((error) => {
    console.error('❌ Error waiting for vendor libraries:', error);
});

