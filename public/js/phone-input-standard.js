/**
 * Standardized Phone Input Handler
 * Compatible with existing intlTelInput plugin (v0.9.2)
 * Handles normalization without database migration
 * 
 * Preferred Countries: Australia, India, Pakistan, Nepal, UK, Canada
 */
(function($, window) {
    'use strict';
    
    // Prevent double initialization
    if (window.PhoneInputStandard) {
        console.warn('PhoneInputStandard already initialized');
        return;
    }
    
    const PhoneInputStandard = {
        // Configuration (can be overridden via data attributes)
        config: {
            defaultCode: window.DEFAULT_COUNTRY_CODE || '+61',
            defaultCountry: window.DEFAULT_COUNTRY || 'au',
            preferredCountries: (window.PREFERRED_COUNTRIES || 'au,in,pk,np,gb,ca').split(','),
            selector: '.telephone',
            autoInitialize: true,
            debug: false
        },
        
        /**
         * Initialize phone input with intlTelInput
         * @param {string|jQuery} selector - CSS selector or jQuery object
         * @param {object} options - Optional configuration overrides
         */
        init: function(selector, options) {
            selector = selector || this.config.selector;
            const settings = $.extend({}, {
                preferredCountries: this.config.preferredCountries,
                initialCountry: this.config.defaultCountry,
                separateDialCode: false,
                autoFormat: false
            }, options || {});
            
            const self = this;
            
            $(selector).each(function() {
                const $input = $(this);
                
                // Skip if already initialized
                if ($input.data('phone-initialized')) {
                    if (self.config.debug) {
                        console.log('Phone input already initialized:', $input);
                    }
                    return;
                }
                
                // Check if intlTelInput plugin is available
                if (typeof $.fn.intlTelInput !== 'function') {
                    if (self.config.debug) {
                        console.warn('intlTelInput plugin not available yet');
                    }
                    return;
                }
                
                try {
                    // Check if intlTelInput countries data is available
                    if (!window.intlTelInput || !window.intlTelInput.countries || !Array.isArray(window.intlTelInput.countries) || window.intlTelInput.countries.length === 0) {
                        if (self.config.debug) {
                            console.warn('intlTelInput countries not yet available, retrying in 100ms...');
                        }
                        // Retry initialization after a short delay
                        setTimeout(function() {
                            if (!$input.data('phone-initialized')) {
                                self.init(selector, options);
                            }
                        }, 100);
                        return;
                    }
                    
                    // Get existing value or use default
                    let existingValue = $input.val() || '';
                    const isReadonly = $input.prop('readonly') || $input.attr('readonly');
                    
                    // Normalize existing value
                    if (existingValue) {
                        existingValue = self.normalizeCode(existingValue);
                    } else if (!isReadonly) {
                        // Set default only if not readonly and empty
                        existingValue = self.config.defaultCode;
                    }
                    
                    // Initialize intlTelInput with error handling
                    try {
                        $input.intlTelInput(settings);
                    } catch (initError) {
                        if (self.config.debug) {
                            console.warn('intlTelInput initialization error, retrying...', initError);
                        }
                        // Retry once after delay
                        setTimeout(function() {
                            if (!$input.data('phone-initialized')) {
                                try {
                                    $input.intlTelInput(settings);
                                } catch (retryError) {
                                    console.error('intlTelInput initialization failed after retry:', retryError);
                                    return;
                                }
                            }
                        }, 150);
                        return;
                    }
                    
                    // Set normalized value
                    if (existingValue) {
                        $input.val(existingValue);
                    }
                    
                    // Handle country change event
                    $input.on('countrychange', function() {
                        try {
                            const countryData = $input.intlTelInput('getSelectedCountryData');
                            if (countryData && countryData.dialCode) {
                                $input.val('+' + countryData.dialCode);
                            }
                        } catch (e) {
                            if (self.config.debug) {
                                console.warn('Error on countrychange:', e);
                            }
                        }
                    });
                    
                    // Mark as initialized
                    $input.data('phone-initialized', true);
                    
                    if (self.config.debug) {
                        console.log('Phone input initialized:', $input, 'Value:', existingValue);
                    }
                    
                } catch (error) {
                    console.error('Error initializing phone input:', error);
                    // Don't mark as initialized if there was an error, so it can retry
                }
            });
            
            return this;
        },
        
        /**
         * Extract country code from input value
         * @param {string|jQuery} input - Input selector or jQuery object
         * @returns {string} Normalized country code
         */
        extractCode: function(input) {
            const $input = $(input);
            const value = $input.val() || '';
            return this.normalizeCode(value);
        },
        
        /**
         * Normalize country code format to +XX
         * Handles all legacy formats
         * @param {string} code - Country code in any format
         * @returns {string} Normalized format (+XX)
         */
        normalizeCode: function(code) {
            if (!code) {
                return this.config.defaultCode;
            }
            
            // Convert to string and trim
            code = String(code).trim();
            
            if (code === '') {
                return this.config.defaultCode;
            }
            
            // Remove all non-digits except +
            code = code.replace(/[^\d+]/g, '');
            
            // Ensure starts with +
            if (code.charAt(0) !== '+') {
                code = '+' + code;
            }
            
            // Remove duplicate + signs
            code = code.replace(/\++/g, '+');
            
            // Validate format (+ followed by 1-4 digits)
            if (!/^\+\d{1,4}$/.test(code)) {
                return this.config.defaultCode;
            }
            
            return code;
        },
        
        /**
         * Reinitialize all phone inputs (useful for dynamic content)
         * Call this after adding new phone fields dynamically
         */
        refresh: function() {
            if (this.config.debug) {
                console.log('Refreshing phone inputs...');
            }
            this.init();
            return this;
        },
        
        /**
         * Set default country code (for runtime configuration)
         * @param {string} code - Country code (e.g., '+61', '61')
         */
        setDefaultCode: function(code) {
            this.config.defaultCode = this.normalizeCode(code);
            if (this.config.debug) {
                console.log('Default country code set to:', this.config.defaultCode);
            }
            return this;
        },
        
        /**
         * Get the current default country code
         * @returns {string}
         */
        getDefaultCode: function() {
            return this.config.defaultCode;
        },
        
        /**
         * Enable debug mode
         */
        enableDebug: function() {
            this.config.debug = true;
            console.log('PhoneInputStandard debug mode enabled');
            return this;
        },
        
        /**
         * Disable debug mode
         */
        disableDebug: function() {
            this.config.debug = false;
            return this;
        }
    };
    
    // Auto-initialize when DOM is ready
    $(document).ready(function() {
        if (PhoneInputStandard.config.autoInitialize) {
            // Wait for intlTelInput to be available
            function tryInit() {
                if (typeof $.fn.intlTelInput === 'function') {
                    PhoneInputStandard.init();
                    
                    if (PhoneInputStandard.config.debug) {
                        console.log('PhoneInputStandard: Auto-initialization complete');
                    }
                } else {
                    // Retry after a short delay
                    setTimeout(tryInit, 100);
                }
            }
            
            tryInit();
        }
    });
    
    // Re-initialize on modal shown events (for dynamic forms)
    $(document).on('shown.bs.modal', '.modal', function() {
        if (PhoneInputStandard.config.debug) {
            console.log('Modal shown, refreshing phone inputs...');
        }
        
        setTimeout(function() {
            PhoneInputStandard.refresh();
        }, 100);
    });
    
    // Re-initialize when dynamic content is added
    // Listen for common dynamic content events
    if (window.MutationObserver) {
        const observer = new MutationObserver(function(mutations) {
            let shouldRefresh = false;
            
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            const $node = $(node);
                            // Check if added node contains .telephone inputs
                            if ($node.is('.telephone') || $node.find('.telephone').length > 0) {
                                shouldRefresh = true;
                            }
                        }
                    });
                }
            });
            
            if (shouldRefresh) {
                if (PhoneInputStandard.config.debug) {
                    console.log('New phone inputs detected, refreshing...');
                }
                setTimeout(function() {
                    PhoneInputStandard.refresh();
                }, 50);
            }
        });
        
        // Observe document body for changes
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    // Make available globally
    window.PhoneInputStandard = PhoneInputStandard;
    
    if (PhoneInputStandard.config.debug) {
        console.log('PhoneInputStandard loaded with config:', PhoneInputStandard.config);
    }
    
})(jQuery, window);
