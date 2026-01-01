/**
 * jQuery Initialization Entry Point
 * 
 * This file loads jQuery and exposes it to the global window object.
 * It's loaded as a separate Vite entry point BEFORE the main app.js
 * to ensure jQuery is available for legacy scripts.
 * 
 * Note: If jQuery is already loaded from CDN (in <head>), this will
 * skip loading to avoid duplication and use the CDN version.
 */

import jQuery from 'jquery';

// Only expose jQuery if it's not already loaded (e.g., from CDN)
if (typeof window.jQuery === 'undefined') {
	// Expose jQuery to global scope for legacy scripts
	window.$ = window.jQuery = jQuery;
	
	// Log confirmation that jQuery is ready
	console.log('jQuery ' + jQuery.fn.jquery + ' loaded via Vite');
} else {
	// jQuery already loaded (probably from CDN in <head>)
	console.log('jQuery ' + window.jQuery.fn.jquery + ' already loaded from CDN');
}
