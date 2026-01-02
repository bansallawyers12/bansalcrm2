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
 * - intl-tel-input (international phone input)
 * 
 * Note: select2 is loaded from CDN (see admin.blade.php) to avoid ES module issues
 */

// Import flatpickr
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

// Note: DataTables is loaded from CDN (see admin.blade.php) to avoid ES module issues
// import 'datatables.net';
// import 'datatables.net-bs5';

// Import iziToast
import iziToast from 'izitoast';
import 'izitoast/dist/css/iziToast.min.css';

// Import intl-tel-input
import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/build/css/intlTelInput.css';

// Expose libraries globally for legacy scripts
window.flatpickr = flatpickr;
window.iziToast = iziToast;
window.intlTelInput = intlTelInput;

// DataTables and select2 are jQuery plugins, so they're automatically available via $
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
            // Note: select2 and DataTables are loaded from CDN, so we check them but don't require them from Vite
            const select2Ready = jQueryAvailable && typeof $.fn.select2 === 'function';
            const dataTableReady = jQueryAvailable && typeof $.fn.DataTable === 'function';
            const intlTelReady = typeof window.intlTelInput === 'function';
            const flatpickrReady = typeof window.flatpickr !== 'undefined';
            const iziToastReady = typeof window.iziToast !== 'undefined';
            
            // Debug logging
            if (attempts % 20 === 0) { // Log every 1 second (20 * 50ms)
                console.log('Checking plugins...', {
                    jQuery: jQueryAvailable,
                    select2: select2Ready,
                    dataTable: dataTableReady,
                    intlTel: intlTelReady,
                    flatpickr: flatpickrReady,
                    iziToast: iziToastReady,
                    attempts: attempts
                });
            }
            
            // Note: select2 and DataTables are loaded from CDN, so we don't require them to be ready from Vite
            // We only check Vite-loaded libraries
            if (intlTelReady && flatpickrReady && iziToastReady) {
                console.log('✅ All vendor libraries loaded: flatpickr, iziToast, intl-tel-input');
                if (select2Ready) {
                    console.log('✅ Select2 available from CDN');
                } else {
                    console.warn('⚠️ Select2 not yet available (loading from CDN)');
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
                if (!intlTelReady) missing.push('intl-tel-input');
                if (!flatpickrReady) missing.push('flatpickr');
                if (!iziToastReady) missing.push('iziToast');
                if (!select2Ready) missing.push('select2 (CDN)');
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

