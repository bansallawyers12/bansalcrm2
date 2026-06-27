/**
 * UI Components Module
 * 
 * Initialization and setup for common UI components:
 * - Flatpickr (date/time pickers)
 * - Tom Select (enhanced dropdowns)
 * - Modals
 * 
 * Usage:
 *   UIComponents.initDatepicker()
 *   UIComponents.initTomSelect('.my-select')
 */

'use strict';

// ============================================================================
// ASYNC WRAPPER - Wait for vendor libraries before initialization
// ============================================================================

(async function() {
    // Wait for vendor libraries to be ready
    if (typeof window.vendorLibsReady !== 'undefined') {
        console.log('[ui-components.js] Waiting for vendorLibsReady promise...');
        await window.vendorLibsReady;
        console.log('[ui-components.js] Vendor libraries ready!');
    } else {
        // Fallback: Poll for vendor libraries if vendorLibsReady not available
        console.log('[ui-components.js] vendorLibsReady not found, polling for libraries...');
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined' &&
                    typeof flatpickr !== 'undefined' &&
                    typeof TomSelect !== 'undefined' &&
                    typeof window.initTomSelect === 'function') {
                    console.log('[ui-components.js] All vendor libraries detected!');
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

    // ========================================================================
    // UI Components Definition (now safe to use vendor libraries)
    // ========================================================================

/**
 * UI Components helper object
 */
const UIComponents = {
    /**
     * Initialize Flatpickr date picker
     * @param {string} selector - CSS selector
     * @param {object} options - Flatpickr options
     */
    initFlatpickr: function(selector, options) {
        if (typeof flatpickr === 'undefined') {
            console.warn('Flatpickr is not loaded');
            return;
        }
        
        try {
            flatpickr(selector, options || {});
        } catch (e) {
            console.error('Error initializing Flatpickr:', e);
        }
    },

    /**
     * Initialize Tom Select dropdown
     * @param {string} selector - CSS selector
     * @param {object} options - Tom Select options
     */
    initTomSelect: function(selector, options) {
        if (typeof window.initTomSelect !== 'function') {
            console.warn('[UIComponents] initTomSelect not available');
            return;
        }

        try {
            document.querySelectorAll(selector).forEach(function (el) {
                if (el.classList.contains('tomselect-migrated') || el.tomselect) {
                    return;
                }
                if (!el.classList.contains('tomselect')) {
                    el.classList.add('tomselect');
                }
                window.initTomSelectPreserveValue(el, Object.assign({ width: '100%' }, options || {}));
            });
        } catch (e) {
            console.error('Error initializing Tom Select:', e);
        }
    },

    /**
     * Initialize standard date picker with common options
     */
    initDatepicker: function() {
        this.initFlatpickr('.datepicker', {
            dateFormat: "Y-m-d",
            allowInput: true
        });
    },

    /**
     * Initialize all common UI components
     * Call this on page load
     */
    init: function() {
        // Initialize date pickers
        this.initDatepicker();

        // Legacy select.select2 markup → Tom Select
        if (document.querySelectorAll('select.select2:not(.tomselect-migrated)').length > 0) {
            this.initTomSelect('select.select2:not(.tomselect-migrated):not(.tomselect)', {
                allowClear: true
            });
        }
    }
};

    // Auto-initialize on document ready
    $(document).ready(function() {
        UIComponents.init();
    });

    // Export for use in other modules
    if (typeof window !== 'undefined') {
        window.UIComponents = UIComponents;
    }

})();
