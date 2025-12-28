# Unused Composer Dependencies Analysis

This document lists dependencies in `composer.json` that appear to be unused in the codebase.

## Summary

**Total Dependencies Checked:** 32 (24 production + 8 dev)  
**Completely Unused:** 6  
**Minimally Used (can be replaced):** 1  
**Dev-Only Tools:** 1 (laravel/tinker - keep for development)

---

## ❌ Unused Dependencies (Can be Removed)

### 1. **doctrine/dbal** (`^3.0`)
- **Status:** ❌ NOT USED
- **Reason:** No code references found. Doctrine DBAL is typically used for database schema modifications (column type changes, renaming, etc.) but is not being used in this project.
- **Impact:** Safe to remove if not planning to use advanced database schema operations
- **Recommendation:** ✅ **REMOVE**

### 2. **laravel/socialite** (`^5.16`)
- **Status:** ❌ NOT USED
- **Reason:** No code references found. Socialite is used for OAuth authentication (Google, Facebook, etc.) but is not implemented in the codebase.
- **Note:** Configuration exists in `config/services.php` (facebook, google) but no actual implementation found.
- **Recommendation:** ✅ **REMOVE**

### 3. **pear/mail** (`^2.0`)
- **Status:** ❌ NOT USED
- **Reason:** No code references found. The codebase uses Laravel's built-in Mail facade instead.
- **Recommendation:** ✅ **REMOVE**

### 4. **stripe/stripe-php** (`^13.14`)
- **Status:** ❌ NOT USED
- **Reason:** Configuration exists in `config/services.php` but no actual Stripe API calls found in the codebase. Payment processing appears to be handled manually (Cheque, Cash, Credit Card, Bank Transfers) without Stripe integration.
- **Recommendation:** ✅ **REMOVE** (unless planning to integrate Stripe in the future)

### 5. **spatie/laravel-ignition** (duplicate in require-dev)
- **Status:** ❌ DUPLICATE
- **Reason:** Listed in both `require` (line 29) and `require-dev` (line 42). Should only be in `require-dev`.
- **Recommendation:** ✅ **REMOVE from `require`** (keep in `require-dev`)

---

## ❌ Additional Unused Dependencies (Can be Removed)

### 6. **laravel/helpers** (`*`)
- **Status:** ⚠️ **USED (Minimally)**
- **Reason:** Found usage in 3 locations:
  - `app/Http/Controllers/Controller.php` - Uses `str_slug()` in 3 places
  - `app/Exceptions/Handler.php` - Uses `array_get()` once
- **Impact:** Only 2 functions used. Laravel 12 has built-in alternatives (`Str::slug()`, `Arr::get()`)
- **Recommendation:** ⚠️ **CAN BE REMOVED** but requires code changes:
  - Replace `str_slug($text)` with `Illuminate\Support\Str::slug($text)` (3 occurrences)
  - Replace `array_get($array, 0)` with `Illuminate\Support\Arr::get($array, 0)` or `$array[0] ?? null` (1 occurrence)

### 7. **laravel/ui** (`^4.5`)
- **Status:** ❌ NOT USED
- **Reason:** No `php artisan ui` commands found. Laravel UI is used for authentication scaffolding, but this project uses custom authentication controllers (`AdminLoginController`, `AgentLoginController`) and custom views.
- **Recommendation:** ✅ **REMOVE**

---

## ✅ Used Dependencies (Keep)

The following dependencies are actively used and should be kept:

1. ✅ **aws/aws-sdk-php** - Used indirectly through `league/flysystem-aws-s3-v3` for S3 storage
2. ✅ **barryvdh/laravel-dompdf** - Used extensively for PDF generation (invoices, receipts, applications)
3. ✅ **guzzlehttp/guzzle** - Used in `ClientsController` and `SmsService` for HTTP requests
4. ✅ **hfig/mapi** - Used in `PartnersController` for parsing Outlook .msg files
5. ✅ **illuminate/http** - Core Laravel component
6. ✅ **illuminate/support** - Core Laravel component
7. ✅ **kyslik/column-sortable** - Used extensively across 80+ models for table sorting
8. ✅ **laravel/framework** - Core framework
9. ✅ **laravel/sanctum** - Used for API authentication (`HasApiTokens` in Admin model, middleware in Kernel)
10. ✅ **laravel/tinker** - Development tool (keep for `php artisan tinker` command)
11. ✅ **league/flysystem** - Core Laravel filesystem component
12. ✅ **league/flysystem-aws-s3-v3** - Used for S3 file storage (`Storage::disk('s3')`)
13. ✅ **maatwebsite/excel** - Used in `ImportUser` and `ImportPartner` classes
14. ✅ **phpoffice/phpspreadsheet** - Used in multiple controllers for Excel file processing
15. ✅ **spatie/laravel-html** - Used in `app/Helpers/Form.php` for HTML generation
16. ✅ **twilio/sdk** - Used in `TwilioService` and `Helper.php` for SMS functionality
17. ✅ **yajra/laravel-datatables-oracle** - Used in `ClientsController` and `AssigneeController` for DataTables

---

## 📋 Removal Recommendations

### Safe to Remove Immediately (No Code Changes Needed):
```json
{
  "require": {
    // Remove these (no code uses them):
    "doctrine/dbal": "^3.0",
    "laravel/socialite": "^5.16",
    "laravel/ui": "^4.5",
    "pear/mail": "^2.0",
    "stripe/stripe-php": "^13.14",
    "spatie/laravel-ignition": "^2.8"  // Remove from require, keep in require-dev
  }
}
```

### Remove After Code Updates:
**laravel/helpers** - Used in 4 places, easily replaceable:
1. Open `app/Http/Controllers/Controller.php`
   - Line 330, 363, 509: Replace `str_slug($title)` with `Str::slug($title)`
   - Add `use Illuminate\Support\Str;` at top
2. Open `app/Exceptions/Handler.php`
   - Line 82: Replace `array_get($exception->guards(), 0)` with `Arr::get($exception->guards(), 0)` or `$exception->guards()[0] ?? null`
   - Add `use Illuminate\Support\Arr;` at top (if using Arr::get)
3. Then remove `laravel/helpers` from composer.json

---

## 🔧 How to Remove

1. Edit `composer.json` and remove the unused dependencies
2. Run `composer update` to update the lock file
3. Test the application to ensure nothing breaks
4. Commit the changes

**Example command for immediate removal:**
```bash
composer remove doctrine/dbal laravel/socialite laravel/ui pear/mail stripe/stripe-php
```

**After fixing the 4 helper function usages:**
```bash
composer remove laravel/helpers
```

**Note:** For `spatie/laravel-ignition`, manually edit `composer.json` to remove it from `require` (keep in `require-dev`), then run `composer update`.

**Note:** For `spatie/laravel-ignition`, only remove it from the `require` section, not from `require-dev`.

---

## 📝 Additional Notes

- **AWS S3**: While `aws/aws-sdk-php` is not directly used in code, it's required by `league/flysystem-aws-s3-v3`, so keep it.
- **Stripe**: The configuration suggests it was planned but never implemented. Can be removed unless future integration is planned.
- **Socialite**: OAuth configuration exists but no implementation found. Can be removed unless OAuth login is needed.
- **PEAR Mail**: Laravel's Mail system is used instead, making PEAR Mail redundant.

---

---

## ✅ Laravel Passport Status

**Laravel Passport is NOT in composer.json** - It was already removed.

- **Status:** ✅ Not present in dependencies
- **Reason:** The project uses **Laravel Sanctum** for API authentication instead
- **Evidence:**
  - API uses `HasApiTokens` trait from `Laravel\Sanctum` (not Passport)
  - `AuthController` uses `$user->createToken()` (Sanctum method)
  - OAuth tables (`oauth_access_tokens`, `oauth_clients`, etc.) were already dropped via migration
  - Sanctum middleware is configured in `Kernel.php`

**Conclusion:** Laravel Passport is not installed, so no action needed.

---

**Last Updated:** Based on codebase analysis on current date  
**Files Scanned:** All PHP files in `app/`, `config/`, `routes/`, and related directories

