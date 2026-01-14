# Phone Configuration Setup Guide

## Environment Variables to Add

Add these lines to your `.env` file:

```env
# Phone Configuration
DEFAULT_COUNTRY_CODE=+61
DEFAULT_COUNTRY=au
PREFERRED_COUNTRIES=au,in,pk,np,gb,ca
```

## Preferred Countries

The following countries will appear at the top of country selection dropdowns in this order:

1. 🇦🇺 **Australia (AU)** - +61
2. 🇮🇳 **India (IN)** - +91
3. 🇵🇰 **Pakistan (PK)** - +92
4. 🇳🇵 **Nepal (NP)** - +977
5. 🇬🇧 **United Kingdom (GB)** - +44
6. 🇨🇦 **Canada (CA)** - +1

## Configuration File Created

✅ `config/phone.php` has been created with these preferred countries.

## Next Steps

1. **Add to .env file:**
   ```bash
   # Open your .env file and add the lines above
   notepad .env
   ```

2. **Clear config cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

3. **Verify configuration:**
   ```bash
   php artisan tinker
   # Then run: config('phone.preferred_countries')
   # Should output: ["au", "in", "pk", "np", "gb", "ca"]
   ```

## Usage in Code

### Get preferred countries:
```php
$preferredCountries = config('phone.preferred_countries');
// Returns: ['au', 'in', 'pk', 'np', 'gb', 'ca']
```

### Get country codes:
```php
$countryCodes = config('phone.country_codes');
// Returns: ['AU' => '+61', 'IN' => '+91', 'PK' => '+92', ...]
```

### Get default country code:
```php
$defaultCode = config('phone.default_country_code');
// Returns: '+61'
```

## JavaScript Usage

In your blade templates, the configuration will be available via:

```javascript
window.DEFAULT_COUNTRY_CODE    // '+61'
window.DEFAULT_COUNTRY         // 'au'
window.PREFERRED_COUNTRIES     // 'au,in,pk,np,gb,ca'
```

## Database Verification

All preferred countries exist in the `countries` table:

| Country        | ISO Code | Phone Code |
|----------------|----------|------------|
| Australia      | AU       | +61        |
| India          | IN       | +91        |
| Pakistan       | PK       | +92        |
| Nepal          | NP       | +977        |
| United Kingdom | GB       | +44        |
| Canada         | CA       | +1         |

---

**Note:** Australia (+61) is set as the default country since this is an Australian-based CRM (Bansal Immigration).
