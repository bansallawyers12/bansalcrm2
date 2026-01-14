<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Helpers\PhoneHelper;

echo "=== PhoneHelper Test ===\n\n";

// Test 1: Normalization
echo "1. Country Code Normalization:\n";
$testCodes = [
    '+61',        // Already normalized
    '61',         // Missing +
    '+61 ',       // Extra space
    '  +61  ',    // Multiple spaces
    '++61',       // Double +
    '91',         // India without +
    '',           // Empty
    null,         // Null
];

foreach ($testCodes as $code) {
    $normalized = PhoneHelper::normalizeCountryCode($code);
    $display = $code === null ? 'null' : "'" . $code . "'";
    echo sprintf("   %-15s → %s\n", $display, $normalized);
}
echo "\n";

// Test 2: Format Phone Number
echo "2. Phone Number Formatting:\n";
$formatTests = [
    ['+61', '412345678'],
    ['61', '412345678'],
    ['+91', '9876543210'],
    ['', '412345678'],
];

foreach ($formatTests as $test) {
    $formatted = PhoneHelper::formatPhoneNumber($test[0], $test[1]);
    echo sprintf("   Code: %-8s Phone: %-12s → %s\n", "'{$test[0]}'", $test[1], $formatted);
}
echo "\n";

// Test 3: Parse Phone Number
echo "3. Parse Full Phone Number:\n";
$parseTests = [
    '+61 412345678',
    '+91 9876543210',
    '412345678',
    '+44 20 7123 4567',
];

foreach ($parseTests as $fullNumber) {
    $parsed = PhoneHelper::parsePhoneNumber($fullNumber);
    echo sprintf("   %-20s → Code: %s, Phone: %s\n", 
        $fullNumber, 
        $parsed['country_code'], 
        $parsed['phone']
    );
}
echo "\n";

// Test 4: Validation
echo "4. Country Code Validation:\n";
$validationTests = ['+61', '+91', '+92', '+977', '+44', '+1', '+999', 'invalid'];
foreach ($validationTests as $code) {
    $isValid = PhoneHelper::isValidCountryCode($code);
    $country = PhoneHelper::getCountryName($code);
    echo sprintf("   %-10s → %s %s\n", 
        $code, 
        $isValid ? '✓ Valid' : '✗ Invalid',
        $country ? "({$country})" : ''
    );
}
echo "\n";

// Test 5: Preferred Countries
echo "5. Preferred Countries:\n";
$preferred = PhoneHelper::getPreferredCountries();
foreach ($preferred as $country) {
    $code = '+' . $country->phonecode;
    $isPreferred = PhoneHelper::isPreferredCountry($code);
    echo sprintf("   %s: %-20s %s %s\n", 
        $country->sortname,
        $country->name,
        $code,
        $isPreferred ? '⭐ Preferred' : ''
    );
}
echo "\n";

// Test 6: Default Country Code
echo "6. Default Country Code: " . PhoneHelper::getDefaultCountryCode() . "\n\n";

// Test 7: Format with Verification
echo "7. Format with Verification Icons:\n";
$verificationTests = [
    ['+61', '412345678', true, 'Personal'],
    ['+61', '412345678', false, 'Personal'],
    ['+91', '9876543210', true, 'Work'],
];

foreach ($verificationTests as $test) {
    $formatted = PhoneHelper::formatWithVerification($test[0], $test[1], $test[2], $test[3]);
    echo "   " . strip_tags($formatted) . "\n";
    echo "   HTML: " . htmlspecialchars($formatted) . "\n\n";
}

echo "=== All Tests Complete ===\n";
