/**
 * Google Places Address Autocomplete
 * Features:
 * - Address suggestions as you type
 * - Automatic field population
 * - Unit number support (format: Unit/Street)
 * - Postcode extraction with multiple fallbacks
 * - Single address field that combines address_line_1 and address_line_2
 */

(function() {
    'use strict';
    
    // Wait for jQuery to be available
    function initWhenReady() {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            // Retry after a short delay if jQuery isn't loaded yet
            setTimeout(initWhenReady, 100);
            return;
        }
        
        $(document).ready(function() {
            console.log('Address autocomplete: Initializing...');
            initAddressAutocomplete();
        });
    }
    
    // Start initialization
    initWhenReady();
    
    function initAddressAutocomplete() {
        const config = getConfig();
        
        if (!config.isValid) {
            console.error('Address autocomplete configuration missing!', config);
            return;
        }
        
        console.log('Address autocomplete: Configuration loaded', config);
        bindAddressSearch(config);
        bindAddressSelection(config);
        bindClickOutside();
    }
    
    /**
     * Get configuration from DOM
     */
    function getConfig() {
        const container = document.getElementById('addressAutocomplete');
        
        if (!container) {
            return {
                searchRoute: '',
                detailsRoute: '',
                csrfToken: '',
                isValid: false
            };
        }
        
        return {
            searchRoute: container.dataset.searchRoute || '',
            detailsRoute: container.dataset.detailsRoute || '',
            csrfToken: container.dataset.csrfToken || '',
            isValid: !!(container.dataset.searchRoute && container.dataset.detailsRoute)
        };
    }
    
    /**
     * Bind address search functionality
     */
    function bindAddressSearch(config) {
        $(document).on('input', '.address-search-input', function() {
            const query = $(this).val();
            const $wrapper = $(this).closest('.address-wrapper');
            
            if (query.length < 3) {
                $wrapper.find('.autocomplete-suggestions').remove();
                return;
            }
            
            $.ajax({
                url: config.searchRoute,
                method: 'POST',
                timeout: 35000,
                data: { 
                    query: query,
                    _token: config.csrfToken
                },
                success: function(response) {
                    if (response.predictions && response.predictions.length > 0) {
                        renderSuggestions($wrapper, response.predictions);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Address search error:', error);
                }
            });
        });
    }
    
    /**
     * Render autocomplete suggestions
     */
    function renderSuggestions($wrapper, predictions) {
        let html = '<div class="autocomplete-suggestions">';
        predictions.forEach(function(prediction) {
            html += `<div class="autocomplete-suggestion" data-place-id="${prediction.place_id}" data-description="${prediction.description.replace(/"/g, '&quot;')}">
                ${prediction.description}
            </div>`;
        });
        html += '</div>';
        
        $wrapper.find('.autocomplete-suggestions').remove();
        $wrapper.find('.address-search-container').append(html);
    }
    
    /**
     * Bind address selection handler
     */
    function bindAddressSelection(config) {
        $(document).on('click', '.autocomplete-suggestion', function() {
            const placeId = $(this).data('place-id');
            const description = $(this).data('description') || $(this).text();
            const $wrapper = $(this).closest('.address-wrapper');
            
            $wrapper.find('.address-search-input').val(description);
            $wrapper.find('.autocomplete-suggestions').remove();
            
            fetchPlaceDetails(config, placeId, $wrapper, description);
        });
    }
    
    /**
     * Fetch and populate address details
     */
    function fetchPlaceDetails(config, placeId, $wrapper, description) {
        $.ajax({
            url: config.detailsRoute,
            method: 'POST',
            timeout: 35000,
            data: { 
                place_id: placeId,
                description: description,
                _token: config.csrfToken
            },
            success: function(response) {
                if (response.result && response.result.address_components) {
                    populateAddressFields($wrapper, response.result);
                }
            },
            error: function(xhr, status, error) {
                console.error('Place details error:', error);
            }
        });
    }
    
    /**
     * Populate address fields from Google Places response
     * Combines address_line_1 and address_line_2 into single address field
     */
    function populateAddressFields($wrapper, result) {
        const components = result.address_components;
        
        let unitNumber = '';
        let streetNumber = '';
        let streetName = '';
        let addressLine1 = '';
        let addressLine2 = '';
        let suburb = '';
        let state = '';
        let postcode = '';
        let country = 'Australia';
        
        // Extract components
        components.forEach(function(component) {
            // Unit/Apartment number
            if (component.types.includes('subpremise')) {
                unitNumber = component.long_name;
            }
            
            // Street number
            if (component.types.includes('street_number')) {
                streetNumber = component.long_name;
            }
            
            // Street name
            if (component.types.includes('route')) {
                streetName = component.long_name;
            }
            
            // Suburb
            if (component.types.includes('locality')) {
                suburb = component.long_name;
            }
            
            // State
            if (component.types.includes('administrative_area_level_1')) {
                state = component.short_name || component.long_name;
            }
            
            // Postcode
            if (component.types.includes('postal_code')) {
                postcode = component.long_name;
            }
            
            // Country
            if (component.types.includes('country')) {
                country = component.long_name;
            }
        });
        
        // Build complete address combining address_line_1 and address_line_2
        // Format: Unit/StreetNumber StreetName, AdditionalInfo
        let fullAddress = '';
        
        if (unitNumber && streetNumber && streetName) {
            // Unit number format: 8/278 Collins Street
            fullAddress = unitNumber + '/' + streetNumber + ' ' + streetName;
        } else if (streetNumber && streetName) {
            // No unit: 278 Collins Street
            fullAddress = streetNumber + ' ' + streetName;
        } else if (streetName) {
            // No street number: Collins Street
            fullAddress = streetName;
        }
        
        // Use formatted_address as fallback if we don't have components
        if (!fullAddress && result.formatted_address) {
            // Remove suburb, state, postcode, country from formatted address
            let formatted = result.formatted_address;
            if (suburb) formatted = formatted.replace(', ' + suburb, '');
            if (state) formatted = formatted.replace(', ' + state, '');
            if (postcode) formatted = formatted.replace(' ' + postcode, '');
            if (country) formatted = formatted.replace(', ' + country, '');
            fullAddress = formatted.trim();
        }
        
        // Fallback postcode extraction from formatted_address
        if (!postcode && result.formatted_address) {
            const postcodeMatch = result.formatted_address.match(/\b(\d{4})\b/);
            if (postcodeMatch) {
                postcode = postcodeMatch[1];
            }
        }
        
        // State mapping for Australian states (convert abbreviation to full name)
        const stateMapping = {
            'NSW': 'New South Wales',
            'VIC': 'Victoria',
            'QLD': 'Queensland',
            'SA': 'South Australia',
            'WA': 'Western Australia',
            'TAS': 'Tasmania',
            'NT': 'Northern Territory',
            'ACT': 'Australian Capital Territory'
        };
        
        const fullStateName = stateMapping[state] || state;
        
        // Populate form fields
        // Single address field (combines address_line_1 and address_line_2)
        $wrapper.find('input[name="address"]').val(fullAddress);
        
        // City/Suburb
        $wrapper.find('input[name="city"], input#locality').val(suburb);
        
        // Postcode
        $wrapper.find('input[name="zip"], input#postal_code').val(postcode);
        
        // State (try to match in dropdown)
        const $stateSelect = $wrapper.find('select[name="state"]');
        if ($stateSelect.length && fullStateName) {
            // Try to find matching option
            $stateSelect.find('option').each(function() {
                if ($(this).text().includes(fullStateName) || $(this).val() === fullStateName) {
                    $stateSelect.val($(this).val());
                    return false;
                }
            });
        }
        
        // Country
        const $countrySelect = $wrapper.find('select[name="country"], select#country_select');
        if ($countrySelect.length && country) {
            // Try to find matching option by country name or code
            $countrySelect.find('option').each(function() {
                const optionText = $(this).text().toLowerCase();
                const optionValue = $(this).val().toLowerCase();
                const countryLower = country.toLowerCase();
                
                if (optionText.includes(countryLower) || optionValue === countryLower || 
                    (country === 'Australia' && (optionText.includes('australia') || optionValue === 'au'))) {
                    $countrySelect.val($(this).val()).trigger('change');
                    return false;
                }
            });
        }
        
        console.log('Address populated:', {
            address: fullAddress,
            suburb: suburb,
            state: fullStateName,
            postcode: postcode,
            country: country
        });
    }
    
    /**
     * Close suggestions when clicking outside
     */
    function bindClickOutside() {
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.address-search-container').length) {
                $('.autocomplete-suggestions').remove();
            }
        });
    }
    
})();
