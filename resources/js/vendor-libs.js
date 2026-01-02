/**
 * Third-party Vendor Libraries Entry Point
 * 
 * This file imports and exposes commonly used third-party libraries
 * that were previously loaded from public/js or CDN.
 * 
 * Libraries included:
 * - flatpickr (date picker)
 * - select2 (enhanced select dropdowns)
 * - DataTables (tables)
 * - iziToast (notifications)
 * - intl-tel-input (international phone input)
 */

// Import flatpickr
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

// Import select2 (requires jQuery to be available globally)
// jQuery is already loaded via CDN in <head> and jquery-init.js
import 'select2';
import 'select2/dist/css/select2.min.css';

// Import DataTables (requires jQuery to be available globally)
import 'datatables.net';
import 'datatables.net-bs5';
// Note: DataTables CSS is typically loaded separately or via CDN
// If you need it bundled, you may need to import it differently
// For now, keeping the existing CSS file in public/css

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
if (typeof window.$ !== 'undefined') {
    // select2 is already a jQuery plugin, so it's available as $.fn.select2
    // DataTables is already a jQuery plugin, so it's available as $.fn.DataTable
    console.log('Vendor libraries loaded: flatpickr, select2, DataTables, iziToast, intl-tel-input');
    
    // Create a promise that resolves when all plugins are ready
    const waitForPlugins = () => {
        return new Promise((resolve) => {
            const check = () => {
                // Check if all required plugins are available
                if (typeof window.$ !== 'undefined' &&
                    typeof window.$.fn.select2 === 'function' &&
                    typeof window.$.fn.DataTable === 'function' &&
                    typeof window.intlTelInput === 'function' &&
                    typeof window.flatpickr !== 'undefined' &&
                    typeof window.iziToast !== 'undefined') {
                    resolve();
                } else {
                    // Retry every 50ms until plugins are ready
                    setTimeout(check, 50);
                }
            };
            check();
        });
    };
    
    // Expose the promise globally
    window.vendorLibsReady = waitForPlugins();
    
    // Also dispatch event for backward compatibility
    window.vendorLibsReady.then(() => {
        document.dispatchEvent(new Event('VendorLibsLoaded'));
    });
}

