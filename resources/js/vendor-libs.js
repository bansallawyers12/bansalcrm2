/**
 * Third-party Vendor Libraries Entry Point
 * 
 * This file imports and exposes commonly used third-party libraries
 * that were previously loaded from public/js or CDN.
 * 
 * Libraries included:
 * - flatpickr (date picker)
 * - DataTables (tables)
 * - iziToast (notifications)
 * 
 * Note: Tom Select is loaded from CDN (see admin.blade.php)
 */

// Import flatpickr
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

// DataTables is loaded from CDN (see admin.blade.php / adminconsole.blade.php).
// The npm packages datatables.net + datatables.net-bs5 were removed from package.json
// because they are never imported — CDN-only is the intentional loading strategy.

// Import iziToast
import iziToast from 'izitoast';
import 'izitoast/dist/css/iziToast.min.css';

// Expose libraries globally for legacy scripts
window.flatpickr = flatpickr;
window.iziToast = iziToast;

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
    alert(text);
};
window.showLegacyToast = window.showToast;

// But we can also expose them explicitly if needed

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
            
            // Check if all required plugins are available
            const tomSelectReady = typeof window.TomSelect !== 'undefined' && typeof window.initTomSelect === 'function';
            const dataTableReady = jQueryAvailable && typeof $.fn.DataTable === 'function';
            const flatpickrReady = typeof window.flatpickr !== 'undefined';
            const iziToastReady = typeof window.iziToast !== 'undefined';
            
            // Debug logging
            if (attempts % 20 === 0) { // Log every 1 second (20 * 50ms)
                console.log('Checking plugins...', {
                    jQuery: jQueryAvailable,
                    tomSelect: tomSelectReady,
                    dataTable: dataTableReady,
                    flatpickr: flatpickrReady,
                    iziToast: iziToastReady,
                    attempts: attempts
                });
            }
            
            // Note: DataTables are loaded from CDN/public, so we don't require them to be ready from Vite
            // We only check Vite-loaded libraries (flatpickr, iziToast)
            if (flatpickrReady && iziToastReady) {
                console.log('✅ All Vite vendor libraries loaded: flatpickr, iziToast');
                if (tomSelectReady) {
                    console.log('✅ Tom Select available from CDN');
                } else {
                    console.warn('⚠️ Tom Select not yet available (loading from CDN)');
                }
                if (dataTableReady) {
                    console.log('✅ DataTables available from CDN');
                } else {
                    console.warn('⚠️ DataTables not yet available (loading from CDN)');
                }
                resolve();
            } else if (attempts >= maxAttempts) {
                // Timeout - log what's missing
                const missing = [];
                if (!jQueryAvailable) missing.push('jQuery');
                if (!flatpickrReady) missing.push('flatpickr');
                if (!iziToastReady) missing.push('iziToast');
                if (!tomSelectReady) missing.push('Tom Select (CDN)');
                if (!dataTableReady) missing.push('DataTables (CDN)');
                
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

