/**
 * UI Enhancement Libraries Entry Point
 *
 * Libraries included:
 * - feather-icons (icon library)
 */

// Import feather-icons
import feather from 'feather-icons';

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

console.log('UI libraries loaded: feather-icons');

