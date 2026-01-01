/**
 * jQuery is now loaded via jquery-init.js as a separate entry point
 * This ensures it's available before any legacy scripts execute
 */

import _ from 'lodash';
import * as Popper from '@popperjs/core';
// Bootstrap 5 from Vite - NOTE: Legacy bootstrap.bundle.min.js is loaded separately for jQuery plugin compatibility
import * as bootstrap from 'bootstrap';

window._ = _;
window.Popper = Popper;
// Only expose modern Bootstrap if legacy one isn't already loaded
if (typeof window.bootstrap === 'undefined') {
    window.bootstrap = bootstrap;
}

/**
 * Bootstrap 5 jQuery Bridge for Popovers
 * This adds jQuery-style API for Bootstrap 5 popovers to maintain compatibility
 * with legacy code that uses $(element).popover()
 */
if (typeof window.$ !== 'undefined' && window.bootstrap && window.bootstrap.Popover) {
    const Popover = window.bootstrap.Popover;
    
    // Add jQuery plugin method for popovers
    window.$.fn.popover = function(options) {
        const args = Array.prototype.slice.call(arguments, 1);
        const method = typeof options === 'string' ? options : null;
        
        return this.each(function() {
            const $el = window.$(this);
            let popoverInstance = window.bootstrap.Popover.getInstance(this);
            
            // Handle method calls (show, hide, toggle, dispose, etc.)
            if (method) {
                if (popoverInstance) {
                    if (typeof popoverInstance[method] === 'function') {
                        popoverInstance[method]();
                    }
                }
                return;
            }
            
            // Handle initialization
            if (!popoverInstance) {
                // Convert jQuery-style options to Bootstrap 5 options
                // Only include options that are actually provided to avoid type errors
                const bsOptions = {};
                
                // Set html option (boolean)
                if (options?.html !== undefined) {
                    bsOptions.html = options.html !== false;
                } else {
                    bsOptions.html = true; // Default for legacy compatibility
                }
                
                // Set sanitize option (boolean)
                if (options?.sanitize !== undefined) {
                    bsOptions.sanitize = options.sanitize !== false;
                } else {
                    bsOptions.sanitize = false; // Default for legacy compatibility
                }
                
                // Set placement (string)
                bsOptions.placement = options?.placement || 'auto';
                
                // Set trigger (string)
                bsOptions.trigger = options?.trigger || 'click';
                
                // Set container (string or false, but Bootstrap 5 prefers string or element)
                if (options?.container !== undefined && options.container !== false) {
                    bsOptions.container = options.container;
                }
                // Don't set container if it's false or undefined
                
                // Set template (string only, or don't include it)
                if (options?.template && typeof options.template === 'string') {
                    bsOptions.template = options.template;
                }
                // Don't set template if not provided or not a string
                
                // Set content (string)
                bsOptions.content = options?.content || $el.attr('data-content') || '';
                
                // Set title (string)
                bsOptions.title = options?.title || $el.attr('data-original-title') || $el.attr('title') || '';
                
                // Set delay (number or object)
                if (options?.delay !== undefined) {
                    bsOptions.delay = options.delay;
                }
                
                // Set animation (boolean)
                if (options?.animation !== undefined) {
                    bsOptions.animation = options.animation !== false;
                }
                
                // Handle data-* attributes (support both data-toggle and data-bs-toggle)
                if ($el.attr('data-bs-toggle') === 'popover' || $el.attr('data-toggle') === 'popover' || $el.attr('data-role') === 'popover') {
                    if ($el.attr('data-bs-placement') || $el.attr('data-placement')) {
                        bsOptions.placement = $el.attr('data-bs-placement') || $el.attr('data-placement');
                    }
                    if ($el.attr('data-bs-content') || $el.attr('data-content')) {
                        bsOptions.content = $el.attr('data-bs-content') || $el.attr('data-content');
                    }
                    if ($el.attr('data-bs-html') || $el.attr('data-html')) {
                        bsOptions.html = ($el.attr('data-bs-html') || $el.attr('data-html')) === 'true';
                    }
                    if ($el.attr('data-bs-container') || $el.attr('data-container')) {
                        const container = $el.attr('data-bs-container') || $el.attr('data-container');
                        if (container && container !== 'false') {
                            bsOptions.container = container;
                        }
                    }
                }
                
                popoverInstance = new Popover(this, bsOptions);
                
                // Store instance in jQuery data for compatibility
                $el.data('bs.popover', popoverInstance);
                
                // Add event listeners for compatibility
                this.addEventListener('shown.bs.popover', function() {
                    $el.trigger('shown.bs.popover');
                });
                
                this.addEventListener('hidden.bs.popover', function() {
                    $el.trigger('hidden.bs.popover');
                });
            }
        });
    };
    
    // Add static method for getting instance
    window.$.fn.popover.Constructor = Popover;
    
    console.log('Bootstrap 5 jQuery bridge for Popovers initialized');
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });
