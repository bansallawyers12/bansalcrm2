<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Country Code
    |--------------------------------------------------------------------------
    |
    | The default country code to use when none is provided.
    | Format: +XX (with plus sign)
    | Australia: +61
    |
    */
    'default_country_code' => env('DEFAULT_COUNTRY_CODE', '+61'),
    
    /*
    |--------------------------------------------------------------------------
    | Default Country
    |--------------------------------------------------------------------------
    |
    | The ISO 2-letter country code for default country (lowercase)
    | Used for intlTelInput initialization
    |
    */
    'default_country' => env('DEFAULT_COUNTRY', 'au'),
    
    /*
    |--------------------------------------------------------------------------
    | Preferred Countries (Order matters - shows in this order)
    |--------------------------------------------------------------------------
    |
    | Countries shown at the top of phone input dropdowns
    | Order: Australia, India, Pakistan, Nepal, UK, Canada
    |
    | Phone codes:
    | - Australia (AU): +61
    | - India (IN): +91
    | - Pakistan (PK): +92
    | - Nepal (NP): +977
    | - United Kingdom (GB): +44
    | - Canada (CA): +1
    |
    */
    'preferred_countries' => ['au', 'in', 'pk', 'np', 'gb', 'ca'],
    
    /*
    |--------------------------------------------------------------------------
    | Popular Countries (ISO codes - uppercase)
    |--------------------------------------------------------------------------
    |
    | Used for database queries and dropdown prioritization
    |
    */
    'popular_countries' => ['AU', 'IN', 'PK', 'NP', 'GB', 'CA'],
    
    /*
    |--------------------------------------------------------------------------
    | Display Formatting
    |--------------------------------------------------------------------------
    |
    | Options for how phone numbers are displayed throughout the CRM
    |
    */
    'format' => [
        'display_separator' => ' ',  // Space between country code and number: "+61 412345678"
        'include_plus' => true,      // Always show + sign in display
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    |
    | Enable validation against the countries database table (246 countries)
    |
    */
    'validate_against_db' => true,
    
    /*
    |--------------------------------------------------------------------------
    | Country Code Mapping
    |--------------------------------------------------------------------------
    |
    | Quick reference for preferred countries
    |
    */
    'country_codes' => [
        'AU' => '+61',   // Australia
        'IN' => '+91',   // India
        'PK' => '+92',   // Pakistan
        'NP' => '+977',  // Nepal
        'GB' => '+44',   // United Kingdom
        'CA' => '+1',    // Canada
    ],
];
