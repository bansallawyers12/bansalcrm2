<?php
namespace App\Helpers;

use App\Models\Country;

class PhoneHelper
{
    /**
     * Normalize country code to standard format: +XX
     * Handles: +61, 61, +61 , "61", null, empty, etc.
     * CRITICAL: This handles legacy data without DB changes
     * 
     * @param mixed $code - Country code in any format
     * @return string - Normalized format: "+XX"
     */
    public static function normalizeCountryCode($code): string
    {
        // Handle null, empty, or whitespace-only values
        if (empty($code) || (!is_string($code) && !is_numeric($code))) {
            return self::getDefaultCountryCode();
        }
        
        // Convert to string and trim whitespace
        $code = trim((string) $code);
        
        // Handle empty string after trim
        if ($code === '') {
            return self::getDefaultCountryCode();
        }
        
        // Remove all whitespace and non-digit characters except +
        $code = preg_replace('/[^\d+]/', '', $code);
        
        // Ensure starts with +
        if (!str_starts_with($code, '+')) {
            $code = '+' . ltrim($code, '+');
        }
        
        // Remove duplicate + signs (handles cases like "++61")
        $code = preg_replace('/\++/', '+', $code);
        
        // Validate format (+ followed by 1-4 digits)
        if (!preg_match('/^\+\d{1,4}$/', $code)) {
            return self::getDefaultCountryCode();
        }
        
        return $code;
    }
    
    /**
     * Format phone number for display: +XX XXXX XXXX
     * Works with both old and new data formats
     * 
     * @param string|null $countryCode
     * @param string|null $phone
     * @param string $separator - Separator between code and number (default: space)
     * @return string - Formatted phone number
     */
    public static function formatPhoneNumber($countryCode, $phone, $separator = ' '): string
    {
        $code = self::normalizeCountryCode($countryCode);
        $phone = trim($phone ?? '');
        
        if (empty($phone)) {
            return $code;
        }
        
        return $code . $separator . $phone;
    }
    
    /**
     * Extract country code from full number string
     * Useful for parsing legacy data
     * 
     * @param string|null $fullNumber - Full phone number with country code
     * @return string - Extracted country code in +XX format
     */
    public static function extractCountryCode($fullNumber): string
    {
        if (empty($fullNumber)) {
            return self::getDefaultCountryCode();
        }
        
        $fullNumber = trim($fullNumber);
        
        // Match + followed by 1-4 digits at start
        if (preg_match('/^\+?(\d{1,4})/', $fullNumber, $matches)) {
            return '+' . $matches[1];
        }
        
        return self::getDefaultCountryCode();
    }
    
    /**
     * Get default country code from config (with fallback)
     * 
     * @return string - Default country code (e.g., "+61")
     */
    public static function getDefaultCountryCode(): string
    {
        return config('phone.default_country_code', '+61');
    }
    
    /**
     * Validate country code format
     * 
     * @param mixed $code
     * @return bool
     */
    public static function isValidFormat($code): bool
    {
        if (empty($code)) {
            return false;
        }
        
        $normalized = self::normalizeCountryCode($code);
        return preg_match('/^\+\d{1,4}$/', $normalized) === 1;
    }
    
    /**
     * Validate against Country model in database (246 countries)
     * 
     * @param mixed $code - Country code to validate
     * @return bool
     */
    public static function isValidCountryCode($code): bool
    {
        if (!self::isValidFormat($code)) {
            return false;
        }
        
        try {
            // Use Country model to validate
            return Country::isValidPhoneCode($code);
        } catch (\Exception $e) {
            // If database query fails (invalid format), return false
            return false;
        }
    }
    
    /**
     * Format for database storage (always normalized)
     * 
     * @param mixed $code
     * @return string
     */
    public static function formatForStorage($code): string
    {
        return self::normalizeCountryCode($code);
    }
    
    /**
     * Format phone number with verification icon (for display in views)
     * 
     * @param string|null $countryCode
     * @param string|null $phone
     * @param bool $isVerified - Whether the number is verified
     * @param string|null $contactType - Type of contact (Personal, Work, etc.)
     * @return string - HTML formatted phone with icons
     */
    public static function formatWithVerification($countryCode, $phone, $isVerified = false, $contactType = null): string
    {
        $formatted = self::formatPhoneNumber($countryCode, $phone);
        
        if ($contactType) {
            $formatted .= ' (' . $contactType . ')';
        }
        
        // Only show verification icons for Personal contact type
        if ($contactType === 'Personal') {
            if ($isVerified) {
                $formatted .= ' <i class="fas fa-check-circle verified-icon fa-lg"></i>';
            } else {
                $formatted .= ' <i class="far fa-circle unverified-icon fa-lg"></i>';
            }
        }
        
        return $formatted;
    }
    
    /**
     * Get country name from phone code
     * 
     * @param mixed $code
     * @return string|null
     */
    public static function getCountryName($code): ?string
    {
        try {
            if (!self::isValidFormat($code)) {
                return null;
            }
            
            $country = Country::getByPhoneCode($code);
            return $country ? $country->name : null;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Get all available country codes from database
     * 
     * @return array - Array of country codes ['AU' => '+61', 'IN' => '+91', ...]
     */
    public static function getAllCountryCodes(): array
    {
        return Country::getPhoneCodesArray();
    }
    
    /**
     * Get preferred countries (Australia, India, Pakistan, Nepal, UK, Canada)
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPreferredCountries()
    {
        return Country::getPreferredCountries();
    }
    
    /**
     * Parse a full phone number into components
     * 
     * @param string $fullNumber - Full phone number (e.g., "+61 412345678")
     * @return array - ['country_code' => '+61', 'phone' => '412345678']
     */
    public static function parsePhoneNumber($fullNumber): array
    {
        if (empty($fullNumber)) {
            return [
                'country_code' => self::getDefaultCountryCode(),
                'phone' => ''
            ];
        }
        
        $fullNumber = trim($fullNumber);
        
        // Extract country code (+ followed by 1-4 digits)
        if (preg_match('/^(\+?\d{1,4})\s*(.*)$/', $fullNumber, $matches)) {
            $countryCode = self::normalizeCountryCode($matches[1]);
            $phone = trim($matches[2]);
            
            return [
                'country_code' => $countryCode,
                'phone' => $phone
            ];
        }
        
        // If no country code found, assume it's just the phone number
        return [
            'country_code' => self::getDefaultCountryCode(),
            'phone' => $fullNumber
        ];
    }
    
    /**
     * Normalize an array of country codes
     * Useful for bulk operations
     * 
     * @param array $codes
     * @return array
     */
    public static function normalizeArray(array $codes): array
    {
        return array_map([self::class, 'normalizeCountryCode'], $codes);
    }
    
    /**
     * Check if a country code is in the preferred list
     * 
     * @param mixed $code
     * @return bool
     */
    public static function isPreferredCountry($code): bool
    {
        $normalized = self::normalizeCountryCode($code);
        $preferredCodes = config('phone.country_codes', []);
        
        return in_array($normalized, $preferredCodes);
    }
}
