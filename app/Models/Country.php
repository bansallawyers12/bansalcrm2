<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Country extends Model
{
	protected $fillable = [
        'id', 'sortname', 'name', 'phonecode', 'created_at', 'updated_at'
    ];
    
    /**
     * Get all countries with phone codes (cached for 24 hours)
     */
    public static function getAllWithPhoneCodes()
    {
        return Cache::remember('countries_with_phonecodes', 86400, function() {
            return self::orderBy('name')->get();
        });
    }
    
    /**
     * Get country by phone code
     * @param string $phoneCode - Can be "+61" or "61"
     */
    public static function getByPhoneCode($phoneCode)
    {
        // Remove + sign if present
        $cleanCode = ltrim($phoneCode, '+');
        
        // Check if it's numeric (prevent SQL errors)
        if (!is_numeric($cleanCode)) {
            return null;
        }
        
        return Cache::remember('country_by_phonecode_' . $cleanCode, 86400, function() use ($cleanCode) {
            return self::where('phonecode', $cleanCode)->first();
        });
    }
    
    /**
     * Validate if phone code exists in database
     * @param string $phoneCode - Can be "+61" or "61"
     * @return bool
     */
    public static function isValidPhoneCode($phoneCode)
    {
        // Remove + sign if present and validate it's numeric
        $cleanCode = ltrim($phoneCode, '+');
        
        // Check if it's numeric (prevent SQL errors)
        if (!is_numeric($cleanCode)) {
            return false;
        }
        
        return Cache::remember('valid_phonecode_' . $cleanCode, 86400, function() use ($cleanCode) {
            return self::where('phonecode', $cleanCode)->exists();
        });
    }
    
    /**
     * Get phone code for a country by ISO code
     * @param string $isoCode - 2-letter ISO code (e.g., "AU", "IN")
     * @return string|null - Returns "+61" format or null
     */
    public static function getPhoneCodeByISO($isoCode)
    {
        $country = self::where('sortname', strtoupper($isoCode))->first();
        return $country ? '+' . $country->phonecode : null;
    }
    
    /**
     * Get all phone codes as array [sortname => +phonecode]
     * @return array - ['AU' => '+61', 'IN' => '+91', ...]
     */
    public static function getPhoneCodesArray()
    {
        return Cache::remember('phonecodes_array', 86400, function() {
            return self::pluck('phonecode', 'sortname')
                ->map(fn($code) => '+' . $code)
                ->toArray();
        });
    }
    
    /**
     * Get preferred countries (Australia, India, Pakistan, Nepal, UK, Canada)
     * Returns countries in the configured order
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPreferredCountries()
    {
        $preferredCodes = config('phone.popular_countries', ['AU', 'IN', 'PK', 'NP', 'GB', 'CA']);
        
        return Cache::remember('preferred_countries', 86400, function() use ($preferredCodes) {
            // Get countries and maintain the order from config
            $countries = self::whereIn('sortname', $preferredCodes)->get();
            
            // Sort by the order in config array
            return $countries->sortBy(function($country) use ($preferredCodes) {
                return array_search($country->sortname, $preferredCodes);
            })->values();
        });
    }
    
    /**
     * Clear country cache (useful after database updates)
     */
    public static function clearCache()
    {
        Cache::forget('countries_with_phonecodes');
        Cache::forget('phonecodes_array');
        Cache::forget('preferred_countries');
        
        // Clear individual country caches (you might need to clear all if many exist)
        foreach (self::pluck('phonecode') as $code) {
            Cache::forget('country_by_phonecode_' . $code);
            Cache::forget('valid_phonecode_' . $code);
        }
    }
}

