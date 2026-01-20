/**
 * Admin Client Detail - Blade Inline JavaScript
 * 
 * Contains Blade-specific functionality that uses Laravel Blade variables
 * This file is included after all other modules and contains code that
 * cannot be extracted to standalone JS files due to Blade variable dependencies.
 * 
 * NOTE: This file should remain in sync with detail.blade.php inline scripts
 * 
 * Dependencies:
 *   - jQuery
 *   - Bootstrap 5
 *   - Flatpickr
 *   - All other page modules
 */

'use strict';

// ============================================================================
// TAB URL SYNCHRONIZATION
// ============================================================================

// Keep URL in sync with active tab and honor ?tab= on load
(function() {
    var tabList = document.getElementById('client_tabs');
    if (!tabList) {
        return;
    }

    var tabLinks = tabList.querySelectorAll('[data-bs-toggle="tab"][data-tab]');
    if (!tabLinks.length) {
        return;
    }

    var baseUrl = tabList.getAttribute('data-base-url');
    if (!baseUrl) {
        return;
    }
    var activeTabSlug = tabList.getAttribute('data-active-tab');
    var applicationId = tabList.getAttribute('data-application-id');
    var base = new URL(baseUrl, window.location.origin);
    var basePath = base.pathname.replace(/\/+$/, '');
    var applicationPath = applicationId ? basePath + '/application/' + applicationId : null;

    var params = new URLSearchParams(window.location.search);
    var initialTab = params.get('tab');
    if (initialTab) {
        var normalizedInitialTab = initialTab === 'noteterm' ? 'notestrm' : initialTab;
        var initialTrigger = tabList.querySelector('[data-tab="' + normalizedInitialTab + '"]');
        if (initialTrigger && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
            bootstrap.Tab.getOrCreateInstance(initialTrigger).show();
        }
        var migratedUrl = new URL(window.location.href);
        migratedUrl.searchParams.delete('tab');
        if (normalizedInitialTab === 'application' && applicationPath) {
            migratedUrl.pathname = applicationPath;
        } else {
            migratedUrl.pathname = normalizedInitialTab === 'activities' ? basePath : basePath + '/' + normalizedInitialTab;
        }
        history.replaceState(null, '', migratedUrl.toString());
    } else if (activeTabSlug) {
        var canonicalUrl = new URL(window.location.href);
        canonicalUrl.searchParams.delete('tab');
        if (activeTabSlug === 'application' && applicationPath) {
            canonicalUrl.pathname = applicationPath;
        } else {
            canonicalUrl.pathname = activeTabSlug === 'activities' ? basePath : basePath + '/' + activeTabSlug;
        }
        history.replaceState(null, '', canonicalUrl.toString());
    }

    tabLinks.forEach(function(link) {
        link.addEventListener('shown.bs.tab', function(event) {
            var tabValue = event.target.getAttribute('data-tab');
            if (!tabValue) {
                return;
            }
            var url = new URL(window.location.href);
            var currentApplicationId = tabList.getAttribute('data-application-id');
            var currentApplicationPath = currentApplicationId ? basePath + '/application/' + currentApplicationId : null;
            url.searchParams.delete('tab');
            if (tabValue === 'application' && currentApplicationPath) {
                url.pathname = currentApplicationPath;
            } else {
                url.pathname = tabValue === 'activities' ? basePath : basePath + '/' + tabValue;
            }
            history.replaceState(null, '', url.toString());
        });
    });
})();

// ============================================================================
// BOOTSTRAP DROPDOWN INITIALIZATION
// ============================================================================

// Initialize Bootstrap 5 dropdowns for Action buttons
(function() {
    var dropdownInitAttempts = 0;
    var maxAttempts = 50; // 5 seconds max wait
    
    function initDropdowns() {
        dropdownInitAttempts++;
        
        // Check if Bootstrap is available
        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
            // Initialize all dropdown toggles that aren't already initialized
            var dropdownToggles = document.querySelectorAll('[data-bs-toggle="dropdown"]');
            var initializedCount = 0;
            
            dropdownToggles.forEach(function(element) {
                // Check if dropdown is already initialized
                if (!bootstrap.Dropdown.getInstance(element)) {
                    try {
                        new bootstrap.Dropdown(element);
                        initializedCount++;
                    } catch (e) {
                        console.warn('Failed to initialize dropdown:', e, element);
                    }
                }
            });
            
            if (initializedCount > 0) {
                console.log('Initialized ' + initializedCount + ' Bootstrap dropdown(s)');
            }
            
            // Setup mutation observer for dynamically added dropdowns
            if (!window.dropdownObserverSetup) {
                window.dropdownObserverSetup = true;
                
                var observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.addedNodes.length > 0) {
                            mutation.addedNodes.forEach(function(node) {
                                if (node.nodeType === 1) { // Element node
                                    // Check for dropdown toggles in the added node
                                    var dropdowns = node.querySelectorAll ? node.querySelectorAll('[data-bs-toggle="dropdown"]') : [];
                                    dropdowns.forEach(function(element) {
                                        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown && !bootstrap.Dropdown.getInstance(element)) {
                                            try {
                                                new bootstrap.Dropdown(element);
                                            } catch (e) {
                                                console.warn('Failed to initialize dynamic dropdown:', e);
                                            }
                                        }
                                    });
                                    
                                    // Also check if the node itself is a dropdown toggle
                                    if (node.hasAttribute && node.hasAttribute('data-bs-toggle') && node.getAttribute('data-bs-toggle') === 'dropdown') {
                                        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown && !bootstrap.Dropdown.getInstance(node)) {
                                            try {
                                                new bootstrap.Dropdown(node);
                                            } catch (e) {
                                                console.warn('Failed to initialize dynamic dropdown:', e);
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    });
                });
                
                // Observe the document body for changes
                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            }
        } else if (dropdownInitAttempts < maxAttempts) {
            // Retry if Bootstrap isn't loaded yet
            setTimeout(initDropdowns, 100);
        } else {
            console.error('Bootstrap Dropdown not available after ' + maxAttempts + ' attempts');
        }
    }
    
    // Start initialization when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDropdowns);
    } else {
        // DOM is already ready
        initDropdowns();
    }
    
    // Also try after window load as a fallback
    window.addEventListener('load', function() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
            initDropdowns();
        }
    });
})();

// ============================================================================
// ACTIVITIES FILTER FUNCTIONALITY
// ============================================================================

jQuery(document).ready(function($) {
    // Activity Type Button Click Handler (main buttons)
    $('.activity-type-btn:not(.dropdown-toggle)').on('click', function() {
        var type = $(this).data('type');
        
        // Remove active class from all buttons and dropdown items
        $('.activity-type-btn').removeClass('active');
        $('.activity-type-dropdown-item').removeClass('active');
        
        // Add active class to clicked button
        $(this).addClass('active');
        
        // Reset dropdown button text
        $('.activity-type-btn.dropdown-toggle').text('More...').removeClass('active');
        
        // Update hidden input
        $('#activity_type_input').val(type);
    });

    // Activity Type Dropdown Item Click Handler
    $(document).on('click', '.activity-type-dropdown-item', function(e) {
        e.preventDefault();
        var type = $(this).data('type');
        var label = $(this).text();
        
        // Remove active class from all buttons and dropdown items
        $('.activity-type-btn').removeClass('active');
        $('.activity-type-dropdown-item').removeClass('active');
        
        // Add active class to clicked dropdown item
        $(this).addClass('active');
        
        // Update dropdown button
        var $dropdownBtn = $('.activity-type-btn.dropdown-toggle');
        $dropdownBtn.text(label).addClass('active');
        
        // Update hidden input
        $('#activity_type_input').val(type);
    });

    // Initialize Date Pickers with Flatpickr
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.date-filter', {
            dateFormat: 'Y-m-d',
            allowInput: true,
            altInput: false
        });
    } else {
        console.warn('Flatpickr is not available. Please ensure vendor-libs.js is loaded.');
    }

    // Auto-submit form on Enter key in search box
    $('#activity_search').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#activitiesFilterForm').submit();
        }
    });
});

console.log('[blade-inline.js] Blade-specific handlers initialized');

// ============================================================================
// NOTE: Additional Blade-specific handlers should be added below
// ============================================================================
// - Add Interested Services Modal handlers (Blade URLs required)
// - Bulk Upload functionality (Blade routes and CSRF required)
// - Client Receipt handlers (Blade routes required)
// These are kept in detail.blade.php inline scripts due to Blade variable dependencies
