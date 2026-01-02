/**
 * UI Components Module
 * 
 * Initialization and setup for common UI components:
 * - Flatpickr (date/time pickers)
 * - Select2 (enhanced dropdowns)
 * - Modals
 * 
 * Usage:
 *   UIComponents.initDatepicker()
 *   UIComponents.initSelect2('.my-select')
 */

'use strict';

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
     * Initialize Select2 dropdown
     * @param {string} selector - CSS selector
     * @param {object} options - Select2 options
     */
    initSelect2: function(selector, options) {
        if (typeof $.fn.select2 === 'undefined') {
            console.warn('Select2 is not loaded');
            return;
        }
        
        try {
            $(selector).select2(options || {});
        } catch (e) {
            console.error('Error initializing Select2:', e);
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
        
        // Initialize Select2 dropdowns (if any exist)
        if ($('.select2').length > 0) {
            this.initSelect2('.select2');
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

