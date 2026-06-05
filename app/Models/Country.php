<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Country extends Model
{
	protected $fillable = [
        'id', 'sortname', 'name', 'phonecode', 'created_at', 'updated_at'
    ];

    /** Attributes stored in cache (arrays only — avoids incomplete serialized Collections/Models). */
    private static function cacheableCountryAttributes(): array
    {
        return ['id', 'sortname', 'name', 'phonecode'];
    }

    /**
     * Get all countries with phone codes (cached for 24 hours)
     */
    public static function getAllWithPhoneCodes()
    {
        $rows = Cache::remember('countries_with_phonecodes_v2', 86400, function () {
            return self::orderBy('name')
                ->get(self::cacheableCountryAttributes())
                ->map->only(self::cacheableCountryAttributes())
                ->values()
                ->all();
        });

        return self::hydrate($rows);
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
        
        $row = Cache::remember('country_by_phonecode_v2_' . $cleanCode, 86400, function () use ($cleanCode) {
            $model = self::where('phonecode', $cleanCode)->first(self::cacheableCountryAttributes());

            return $model ? $model->only(self::cacheableCountryAttributes()) : null;
        });

        return $row !== null ? self::hydrate([$row])->first() : null;
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
        
        $rows = Cache::remember('preferred_countries_v2', 86400, function () use ($preferredCodes) {
            $countries = self::whereIn('sortname', $preferredCodes)->get(self::cacheableCountryAttributes());

            return $countries->sortBy(function ($country) use ($preferredCodes) {
                return array_search($country->sortname, $preferredCodes);
            })
                ->values()
                ->map->only(self::cacheableCountryAttributes())
                ->all();
        });

        return self::hydrate($rows);
    }
    
    /**
     * Clear country cache (useful after database updates)
     */
    public static function clearCache()
    {
        Cache::forget('countries_with_phonecodes');
        Cache::forget('countries_with_phonecodes_v2');
        Cache::forget('phonecodes_array');
        Cache::forget('preferred_countries');
        Cache::forget('preferred_countries_v2');

        // Clear individual country caches (you might need to clear all if many exist)
        foreach (self::pluck('phonecode') as $code) {
            Cache::forget('country_by_phonecode_' . $code);
            Cache::forget('country_by_phonecode_v2_' . $code);
            Cache::forget('valid_phonecode_' . $code);
        }
    }
}

