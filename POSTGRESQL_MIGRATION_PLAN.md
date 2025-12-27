# PostgreSQL Migration Plan

This document outlines all the changes needed to migrate the application from MySQL to PostgreSQL.

## Summary of Required Changes

### 1. **MySQL-Specific SQL Functions to Replace**

#### A. GROUP_CONCAT → STRING_AGG (PostgreSQL)
**Issue:** `GROUP_CONCAT()` is MySQL-specific. PostgreSQL uses `STRING_AGG()`.

**Files to Update:**
- `app/Services/SearchService.php` (Lines 132, 446)

**Current Code:**
```php
DB::raw('GROUP_CONCAT(client_phone) as phones')
```

**PostgreSQL Equivalent:**
```php
DB::raw("STRING_AGG(client_phone::text, ',') as phones")
```

**Note:** The `::text` cast is needed if `client_phone` is not already text type.

---

#### B. CONCAT → PostgreSQL CONCAT or || operator
**Issue:** `CONCAT()` works in PostgreSQL but `||` is more standard. However, Laravel's DB::raw will handle CONCAT fine.

**Files to Update:**
- `app/Services/SearchService.php` (Lines 154, 246)
- `app/Http/Controllers/Admin/ClientsController.php` (Lines 1523, 1533, 1562, 5848)
- `app/Http/Controllers/Admin/LeadController.php` (Line 98)
- `app/Http/Controllers/Admin/AdminController.php` (Line 1859)
- `app/Http/Controllers/Agent/ClientsController.php` (Lines 388, 399)

**Current Code:**
```php
DB::raw("CONCAT(admins.first_name, ' ', admins.last_name)")
DB::raw("concat(first_name, ' ', last_name)")
```

**PostgreSQL Equivalent:**
```php
// Option 1: Use CONCAT (works in PostgreSQL)
DB::raw("CONCAT(first_name, ' ', last_name)")

// Option 2: Use || operator (more PostgreSQL-native)
DB::raw("(first_name || ' ' || last_name)")
```

**Recommendation:** Keep using `CONCAT()` as it's more readable and works in both databases. No change needed, but ensure proper syntax.

---

### 2. **PDO Attribute Settings**

**Issue:** `PDO::ATTR_EMULATE_PREPARES` is MySQL-specific optimization. PostgreSQL doesn't need this and it might cause issues.

**Files to Update:**
- `app/Http/Controllers/Admin/ApplicationsController.php` (Lines 1622, 1624)
- `app/Http/Controllers/Admin/ProductsController.php` (Lines 730, 732)

**Current Code:**
```php
DB::connection()->getPdo()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
DB::table('check_applications')->insert($data);
DB::connection()->getPdo()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
```

**PostgreSQL Solution:**
```php
// Remove the PDO attribute setting, PostgreSQL handles prepared statements differently
// Simply use:
DB::table('check_applications')->insert($data);
```

**Alternative (if needed for large inserts):**
```php
// For PostgreSQL, you can chunk large inserts
foreach (array_chunk($data, 500) as $chunk) {
    DB::table('check_applications')->insert($chunk);
}
```

---

### 3. **Database Configuration**

**Status:** ✅ Already completed in `.env` file
- `DB_CONNECTION=pgsql`
- `DB_PORT=5432`
- `DB_DATABASE=bansalcrm_pg`
- `DB_USERNAME=postgres`
- `DB_PASSWORD=admin123`

**Status:** ✅ Already updated in `config/database.php`
- PostgreSQL connection uses `DB_*` environment variables
- MySQL connection uses `DB_MYSQL_*` environment variables (backup)

---

### 4. **Laravel Schema Builder Compatibility**

**Status:** ✅ No changes needed
- Laravel's schema builder is database-agnostic
- Migrations using `Schema::create()`, `$table->string()`, etc. work with PostgreSQL
- Laravel automatically handles differences (e.g., `increments()` becomes `SERIAL` in PostgreSQL)

---

### 5. **Data Type Considerations**

**Note:** PostgreSQL has stricter type handling than MySQL:
- Ensure all boolean fields use proper boolean types (0/1 vs true/false)
- Date/timestamp fields should be handled consistently
- String comparisons are case-sensitive by default (use `ILIKE` instead of `LIKE` for case-insensitive, but Laravel's `LIKE` should work fine)

**Status:** ✅ Should work as-is, but monitor for issues

---

## Detailed File Changes

### File 1: `app/Services/SearchService.php`

**Change 1 (Line 132):**
```php
// BEFORE:
->select('client_id', DB::raw('GROUP_CONCAT(client_phone) as phones'))

// AFTER:
->select('client_id', DB::raw("STRING_AGG(client_phone::text, ',') as phones"))
```

**Change 2 (Line 446):**
```php
// BEFORE:
->select('client_id', DB::raw('GROUP_CONCAT(client_phone) as phones'))

// AFTER:
->select('client_id', DB::raw("STRING_AGG(client_phone::text, ',') as phones"))
```

**Note on CONCAT:** Lines 154 and 246 use `CONCAT()` which works in PostgreSQL, so no change needed unless you prefer `||` operator.

---

### File 2: `app/Http/Controllers/Admin/ApplicationsController.php`

**Change (Lines 1622-1624):**
```php
// BEFORE:
DB::connection()->getPdo()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
DB::table('check_applications')->insert($data);
DB::connection()->getPdo()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

// AFTER:
// Remove PDO attribute setting
if (!empty($data)) {
    foreach (array_chunk($data, 500) as $chunk) {
        DB::table('check_applications')->insert($chunk);
    }
}
```

---

### File 3: `app/Http/Controllers/Admin/ProductsController.php`

**Change (Lines 730-732):**
```php
// BEFORE:
DB::connection()->getPdo()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
DB::table('check_products')->insert($data);
DB::connection()->getPdo()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

// AFTER:
// Remove PDO attribute setting
if (!empty($data)) {
    foreach (array_chunk($data, 500) as $chunk) {
        DB::table('check_products')->insert($chunk);
    }
}
```

---

### Files 4-8: CONCAT usage (Optional - works as-is in PostgreSQL)

These files use `CONCAT()` which works fine in PostgreSQL, but you can optionally update for consistency:

- `app/Services/SearchService.php` (Lines 154, 246)
- `app/Http/Controllers/Admin/ClientsController.php` (Lines 1523, 1533, 1562, 5848)
- `app/Http/Controllers/Admin/LeadController.php` (Line 98)
- `app/Http/Controllers/Admin/AdminController.php` (Line 1859)
- `app/Http/Controllers/Agent/ClientsController.php` (Lines 388, 399)

**Note:** No change required as `CONCAT()` is supported in PostgreSQL. If you want PostgreSQL-native syntax, use `||` operator instead.

---

## Testing Checklist

After making changes, test the following:

1. ✅ Search functionality (uses GROUP_CONCAT/STRING_AGG)
2. ✅ Client search with name concatenation
3. ✅ Lead search with name concatenation
4. ✅ Bulk data import (ApplicationsController)
5. ✅ Bulk product import (ProductsController)
6. ✅ All database operations use PostgreSQL connection
7. ✅ Verify no MySQL-specific errors in logs

---

## Rollback Plan

If issues occur, you can:
1. Change `DB_CONNECTION=mysql` in `.env`
2. Revert code changes
3. MySQL connection is still configured as backup using `DB_MYSQL_*` variables

---

## Summary of Required Changes

**Critical (Must Change):**
1. Replace `GROUP_CONCAT` with `STRING_AGG` in `SearchService.php` (2 occurrences)
2. Remove `PDO::ATTR_EMULATE_PREPARES` settings in 2 controller files

**Optional (Works as-is but can optimize):**
3. Consider using `||` instead of `CONCAT` for PostgreSQL-native syntax (8+ files)

**Total Files to Modify:** 4 files (2 critical, 2 optional)
