/**
 * UI Enhancement Libraries Entry Point
 * 
 * This file imports UI enhancement libraries that were previously
 * loaded from CDN.
 * 
 * Libraries included:
 * - feather-icons (icon library)
 * - jquery.nicescroll (custom scrollbar)
 */

// Import feather-icons
import feather from 'feather-icons';

// Import jquery.nicescroll (requires jQuery to be available globally)
// jQuery is already loaded via CDN in <head> and jquery-init.js
import 'jquery.nicescroll';

// Expose feather-icons globally
window.feather = feather;

// Initialize feather icons (replace icons with SVG)
if (typeof document !== 'undefined') {
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            feather.replace();
        });
    } else {
        feather.replace();
    }
}

console.log('UI libraries loaded: feather-icons, jquery.nicescroll');

