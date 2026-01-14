<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Phone Configuration Test ===\n\n";

// Test 1: Config values
echo "1. Configuration Values:\n";
echo "   Default Country Code: " . config('phone.default_country_code') . "\n";
echo "   Default Country: " . config('phone.default_country') . "\n";
echo "   Preferred Countries: " . implode(', ', config('phone.preferred_countries')) . "\n\n";

// Test 2: Country model - Preferred countries
echo "2. Preferred Countries from Database:\n";
$preferred = App\Models\Country::getPreferredCountries();
foreach ($preferred as $country) {
    echo sprintf("   %s: %s (+%s)\n", $country->sortname, $country->name, $country->phonecode);
}
echo "\n";

// Test 3: Total countries available
echo "3. Total Countries Available: " . App\Models\Country::count() . "\n\n";

// Test 4: Validate phone codes
echo "4. Validating Preferred Country Codes:\n";
$codes = ['+61', '+91', '+92', '+977', '+44', '+1'];
foreach ($codes as $code) {
    $valid = App\Models\Country::isValidPhoneCode($code);
    $country = App\Models\Country::getByPhoneCode($code);
    echo sprintf("   %s: %s (%s)\n", 
        $code, 
        $valid ? '✓ Valid' : '✗ Invalid',
        $country ? $country->name : 'N/A'
    );
}

echo "\n=== Test Complete ===\n";
