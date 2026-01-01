/**
 * jQuery Initialization Entry Point
 * 
 * This file loads jQuery and exposes it to the global window object.
 * It's loaded as a separate Vite entry point BEFORE the main app.js
 * to ensure jQuery is available for legacy scripts.
 */

import jQuery from 'jquery';

// Expose jQuery to global scope for legacy scripts
window.$ = window.jQuery = jQuery;

// Log confirmation that jQuery is ready
console.log('jQuery ' + jQuery.fn.jquery + ' loaded and ready');

