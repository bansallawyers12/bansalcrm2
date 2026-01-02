/**
 * Google Maps Module
 * 
 * Provides Google Maps Autocomplete functionality for address input
 * 
 * Usage:
 *   GoogleMaps.initAutocomplete(mapElementId, inputElementId, options)
 *   GoogleMaps.loadGoogleMaps(apiKey, callback)
 */

'use strict';

/**
 * Google Maps helper object
 */
const GoogleMaps = {
    /**
     * Load Google Maps API script
     * @param {string} apiKey - Google Maps API key
     * @param {string} callback - Callback function name (global function)
     */
    loadGoogleMaps: function(apiKey, callback) {
        // Only load if required elements exist
        if (!document.getElementById("map") || !document.getElementById("pac-input")) {
            console.warn("Google Maps: Required elements (map or pac-input) not found. Maps functionality disabled.");
            return;
        }
        
        // Check if already loaded
        if (typeof google !== 'undefined' && google.maps && google.maps.places) {
            if (callback && typeof window[callback] === 'function') {
                window[callback]();
            }
            return;
        }
        
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&callback=${callback}&libraries=places&v=weekly&loading=async`;
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    },

    /**
     * Initialize Google Maps Autocomplete
     * @param {string} mapElementId - ID of map container element
     * @param {string} inputElementId - ID of address input element
     * @param {object} options - Configuration options
     */
    initAutocomplete: function(mapElementId, inputElementId, options) {
        // Check if required elements exist before initializing
        const mapElement = document.getElementById(mapElementId);
        const input = document.getElementById(inputElementId);
        
        if (!mapElement || !input) {
            console.warn("Google Maps: Required elements (map or pac-input) not found. Maps functionality disabled.");
            return;
        }
        
        // Check if Google Maps API is loaded
        if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
            console.error("Google Maps API not loaded properly.");
            return;
        }
        
        const defaultOptions = {
            center: { lat: -33.8688, lng: 151.2195 }, // Sydney, Australia
            zoom: 13,
            mapTypeId: "roadmap",
            countryRestriction: 'au',
            fields: ['address_components', 'formatted_address', 'geometry', 'name', 'icon'],
            onPlaceChanged: null, // Callback function
            fieldMappings: {
                postalCode: '#postal_code',
                locality: '#locality',
                state: 'select[name="state"]'
            }
        };
        
        const config = Object.assign({}, defaultOptions, options || {});
        
        try {
            const map = new google.maps.Map(mapElement, {
                center: config.center,
                zoom: config.zoom,
                mapTypeId: config.mapTypeId,
            });
            
            // Show the map once initialized
            mapElement.style.display = 'block';
            
            // Create Autocomplete with Australian bias and required fields
            const autocomplete = new google.maps.places.Autocomplete(input, {
                componentRestrictions: { country: config.countryRestriction },
                fields: config.fields,
                types: ['address']
            });

            // Position the input control on the map
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
            
            // Bias Autocomplete results towards current map's viewport
            map.addListener("bounds_changed", () => {
                if (map.getBounds()) {
                    autocomplete.setBounds(map.getBounds());
                }
            });

            let marker = null;

            // Listen for the event fired when the user selects a prediction
            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();

                if (!place.geometry || !place.geometry.location) {
                    console.log("Returned place contains no geometry");
                    return;
                }

                // Clear out the old marker
                if (marker) {
                    marker.setMap(null);
                    marker = null;
                }
                
                // Parse address components and populate form fields
                if (place.address_components) {
                    this.populateAddressFields(place.address_components, config.fieldMappings);
                }

                const icon = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25),
                };

                // Create a marker for the selected place
                marker = new google.maps.Marker({
                    map,
                    icon,
                    title: place.name,
                    position: place.geometry.location,
                });

                // Adjust map viewport to show the selected place
                const bounds = new google.maps.LatLngBounds();
                if (place.geometry.viewport) {
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
                map.fitBounds(bounds);
                
                // Call custom callback if provided
                if (config.onPlaceChanged && typeof config.onPlaceChanged === 'function') {
                    config.onPlaceChanged(place);
                }
            });
        } catch (error) {
            console.error("Error initializing Google Maps:", error);
        }
    },

    /**
     * Populate form fields with address components
     * @param {Array} addressComponents - Address components from Google Places API
     * @param {object} fieldMappings - Mapping of address fields to form selectors
     */
    populateAddressFields: function(addressComponents, fieldMappings) {
        if (!fieldMappings || typeof $ === 'undefined') {
            return;
        }
        
        let postalCode = '';
        let locality = '';
        let state = '';
        let streetNumber = '';
        let route = '';
        
        // Extract address components
        addressComponents.forEach((component) => {
            if (component.types.includes('postal_code')) {
                postalCode = component.long_name;
            }
            if (component.types.includes('locality')) {
                locality = component.long_name;
            }
            // Also check for postal_town if locality is not found
            if (!locality && component.types.includes('postal_town')) {
                locality = component.long_name;
            }
            // Extract state/administrative area
            if (component.types.includes('administrative_area_level_1')) {
                state = component.long_name;
            }
            // Extract street number
            if (component.types.includes('street_number')) {
                streetNumber = component.long_name;
            }
            // Extract route/street name
            if (component.types.includes('route')) {
                route = component.long_name;
            }
        });
        
        // State abbreviation to full name mapping for Australian states
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
        
        // Populate the form fields
        if (postalCode && fieldMappings.postalCode) {
            $(fieldMappings.postalCode).val(postalCode);
        }
        if (locality && fieldMappings.locality) {
            $(fieldMappings.locality).val(locality);
        }
        if (state && fieldMappings.state) {
            // Check if state is an abbreviation and convert to full name
            const fullStateName = stateMapping[state] || state;
            $(fieldMappings.state).val(fullStateName);
        }
    }
};

// Export for use in other modules
if (typeof window !== 'undefined') {
    window.GoogleMaps = GoogleMaps;
}

