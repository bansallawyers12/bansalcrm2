# MySQL to PostgreSQL Syntax Reference Guide

This document serves as a quick reference for syntax changes made during the MySQL to PostgreSQL migration. Use this guide when pulling new code from MySQL to identify what needs to be changed for PostgreSQL compatibility.

---

## Table of Contents
1. [Date Handling](#date-handling)
2. [Invalid Date Values](#invalid-date-values)
3. [Empty String Handling for Strict Data Types](#empty-string-handling-for-strict-data-types)
4. [Laravel Routing Issues After Migration](#laravel-routing-issues-after-migration)
5. [String Aggregation](#string-aggregation)
6. [Date Formatting](#date-formatting)
7. [Null Handling in ORDER BY](#null-handling-in-order-by)
8. [String Concatenation](#string-concatenation)
9. [FIND_IN_SET Function](#find_in_set-function)
10. [GROUP BY Strictness](#group-by-strictness)
11. [NOT NULL Constraints](#not-null-constraints)
12. [Pending Migrations and Schema Mismatches](#pending-migrations-and-schema-mismatches)
13. [Handling Missing Form Fields](#handling-missing-form-fields)
14. [Notes Table - Missing Default Values](#notes-table---missing-default-values)
15. [~~Documents Table - Missing signer_count Field~~](#documents-table---missing-signer_count-field) - **OBSOLETE: Column never existed in database**
16. [Search Patterns](#search-patterns)
17. [Quick Reference Table](#quick-reference-table)
18. [Migration Checklist](#migration-checklist)
19. [Additional Notes](#additional-notes)
20. [Prioritized Implementation Plan (Safest to Hardest)](#prioritized-implementation-plan-safest-to-hardest)

---

## Date Handling

### Issue: VARCHAR Date Fields Stored as dd/mm/yyyy

**Problem:** Some date fields are stored as VARCHAR in `dd/mm/yyyy` format (e.g., `trans_date` in `account_client_receipts` table). Direct string comparison doesn't work correctly for date ranges.

**MySQL Approach:**
```php
// ❌ MySQL - This doesn't work for date comparisons
->where('trans_date', '>=', '01/01/2024')
->where('trans_date', '<=', '31/01/2024')
```

**PostgreSQL Solution:**
```php
// ✅ PostgreSQL - Convert VARCHAR to DATE using TO_DATE()
// CRITICAL: Filter NULL values first - TO_DATE() fails on NULL values
->whereNotNull('trans_date')
->whereRaw("TO_DATE(trans_date, 'DD/MM/YYYY') BETWEEN TO_DATE(?, 'DD/MM/YYYY') AND TO_DATE(?, 'DD/MM/YYYY')", [$startDate, $endDate])
```

### CRITICAL: NULL Value Handling

PostgreSQL's `TO_DATE()` function will throw an error if the input value is NULL. This causes a 500 Internal Server Error when querying tables that contain NULL date values. **Always filter out NULL values before using TO_DATE()**.

**Broken Pattern:**
```php
// ❌ PostgreSQL - This will fail with 500 error if trans_date is NULL
->whereRaw("TO_DATE(trans_date, 'DD/MM/YYYY') BETWEEN TO_DATE(?, 'DD/MM/YYYY') AND TO_DATE(?, 'DD/MM/YYYY')", [$startDate, $endDate])
```

**Fixed Pattern:**
```php
// ✅ PostgreSQL - Filter NULL values first
->whereNotNull('trans_date')
->whereRaw("TO_DATE(trans_date, 'DD/MM/YYYY') BETWEEN TO_DATE(?, 'DD/MM/YYYY') AND TO_DATE(?, 'DD/MM/YYYY')", [$startDate, $endDate])
```

**For JOIN queries:**
```php
// ✅ PostgreSQL - Use table alias prefix for whereNotNull
->whereNotNull('acr.trans_date')
->whereRaw("TO_DATE(acr.trans_date, 'DD/MM/YYYY') BETWEEN TO_DATE(?, 'DD/MM/YYYY') AND TO_DATE(?, 'DD/MM/YYYY')", [$startDate, $endDate])
```

**For raw SQL subqueries:**
```php
// ✅ PostgreSQL - Add IS NOT NULL check in raw SQL
DB::raw('(SELECT ... FROM account_client_receipts 
    WHERE trans_date IS NOT NULL
    AND TO_DATE(trans_date, \'DD/MM/YYYY\') BETWEEN ...)')
```

**Examples from Codebase:**

1. **FinancialStatsService.php (Correct Implementation):**
   - **File:** `app/Services/FinancialStatsService.php`
   - **Lines:** 63-67
   ```php
   $applyDateFilter = function($query, $start, $end) {
       // CRITICAL: Filter NULL values first - TO_DATE() fails on NULL values
       return $query->whereNotNull('trans_date')
           ->whereRaw("TO_DATE(trans_date, 'DD/MM/YYYY') BETWEEN TO_DATE(?, 'DD/MM/YYYY') AND TO_DATE(?, 'DD/MM/YYYY')", [$start, $end]);
   };
   ```

2. **ClientAccountsController.php - Date Filter Fix:**
   - **File:** `app/Http/Controllers/CRM/ClientAccountsController.php`
   - **Method:** `applyDateFilters()` (lines 50-140)
   - **Issue:** Used `whereBetween()` with `Y-m-d` format on VARCHAR `trans_date` column, causing date filters to fail
   - **Broken Pattern:**
     ```php
     // ❌ PostgreSQL - This doesn't work with VARCHAR dd/mm/yyyy dates
     $query->whereBetween('trans_date', [
         $startDate->format('Y-m-d'),  // Wrong format (Y-m-d)
         $endDate->format('Y-m-d')
     ]);
     ```
   - **Fixed Pattern:**
     ```php
     // ✅ PostgreSQL - Convert to dd/mm/yyyy and use TO_DATE()
     // CRITICAL: Filter NULL values first
     $startDateStr = $startDate->format('d/m/Y');
     $endDateStr = $endDate->format('d/m/Y');
     $query->whereNotNull('trans_date')
         ->whereRaw("TO_DATE(trans_date, 'DD/MM/YYYY') BETWEEN TO_DATE(?, 'DD/MM/YYYY') AND TO_DATE(?, 'DD/MM/YYYY')", [$startDateStr, $endDateStr]);
     ```
   - **For Custom Date Ranges:** Dates from datepicker are already in `dd/mm/yyyy` format:
     ```php
     // ✅ PostgreSQL - Use TO_DATE() directly with datepicker input
     // CRITICAL: Filter NULL values first
     if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $fromDate) && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $toDate)) {
         $query->whereNotNull('trans_date')
             ->whereRaw("TO_DATE(trans_date, 'DD/MM/YYYY') BETWEEN TO_DATE(?, 'DD/MM/YYYY') AND TO_DATE(?, 'DD/MM/YYYY')", [$fromDate, $toDate]);
     }
     ```
   - **For Financial Year:** Convert Carbon dates to `dd/mm/yyyy` format:
     ```php
     // ✅ PostgreSQL - Convert financial year dates to dd/mm/yyyy
     // CRITICAL: Filter NULL values first
     $fyStartDate = \Carbon\Carbon::createFromDate($years[0], 7, 1)->startOfDay();
     $fyEndDate = \Carbon\Carbon::createFromDate($years[1], 6, 30)->endOfDay();
     $startDateStr = $fyStartDate->format('d/m/Y');
     $endDateStr = $fyEndDate->format('d/m/Y');
     $query->whereNotNull('trans_date')
         ->whereRaw("TO_DATE(trans_date, 'DD/MM/YYYY') BETWEEN TO_DATE(?, 'DD/MM/YYYY') AND TO_DATE(?, 'DD/MM/YYYY')", [$startDateStr, $endDateStr]);
     ```

**Safety:** 🔴 **CRITICAL** - Date filters using `whereBetween()` with wrong format will **fail silently** or return incorrect results in PostgreSQL. This must be fixed for date filtering to work correctly.

**Safety:** 🔴 **CRITICAL** - Queries using `TO_DATE()` on columns that may contain NULL values will **fail immediately** with a 500 Internal Server Error. Always add `->whereNotNull('column_name')` before `whereRaw()` with `TO_DATE()`. This is a common cause of 500 errors in analytics dashboards and financial reports.

**Notes:**
- Format string is case-sensitive: `'DD/MM/YYYY'` (uppercase)
- Use parameterized queries (bindings) to prevent SQL injection
- Both operands must be converted with TO_DATE() for proper comparison
- Always convert dates to `dd/m/Y` format before using in TO_DATE() comparisons
- When using Carbon dates, use `->format('d/m/Y')` not `->format('Y-m-d')`
- Datepicker inputs are already in `dd/mm/yyyy` format, so use them directly
- For financial year calculations, create Carbon dates first, then format as `dd/mm/yyyy`
- **CRITICAL:** Always filter NULL values with `->whereNotNull('trans_date')` before using `TO_DATE()` - PostgreSQL throws an error if TO_DATE() receives NULL
- For JOIN queries, use table alias prefix: `->whereNotNull('acr.trans_date')`
- For raw SQL subqueries, add `WHERE column_name IS NOT NULL` before TO_DATE() usage

---

## Invalid Date Values

### Issue: '0000-00-00' Invalid Date

**Problem:** MySQL accepts `'0000-00-00'` as a valid date value, but PostgreSQL does not. PostgreSQL will throw an error or store NULL instead.

**MySQL Approach:**
```php
// ❌ MySQL - This works in MySQL but fails in PostgreSQL
->where('dob', '!=', '0000-00-00')
->where('dob', '=', '0000-00-00')  // Also problematic
```

**PostgreSQL Solution:**
```php
// ✅ PostgreSQL - Use NULL checks instead
->whereNotNull('dob')              // Instead of != '0000-00-00'
->whereNull('dob')                 // Instead of = '0000-00-00'
```

**PHP String Comparisons:**
```php
// ❌ MySQL Legacy Code
if ($date != '0000-00-00') { ... }
if ($date === '0000-00-00') { ... }

// ✅ PostgreSQL - Use empty/null checks
if (!empty($date) && $date !== null) { ... }
if (empty($date) || $date === null) { ... }
```

**Example from Codebase:**
- **File:** `app/Console/Commands/UpdateClientAges.php`
- **Line 54:** Changed from `->where('dob', '!=', '0000-00-00')` to `->whereNotNull('dob')`

**Safety:** 🔴 **CRITICAL** - Database queries with `'0000-00-00'` will **fail immediately** in PostgreSQL. This must be fixed before the code runs.

**Notes:**
- PostgreSQL stores invalid dates as NULL, not '0000-00-00'
- Always check for NULL instead of string comparison with '0000-00-00'
- Migration scripts should convert '0000-00-00' to NULL during data migration
- PHP code checking for '0000-00-00' should be updated to use empty/null checks (medium priority)

---

## Empty String Handling for Strict Data Types

### Issue: Empty Strings Not Accepted for Integer and Date Columns

**Problem:** PostgreSQL is strict about data types. Empty strings (`''`) cannot be assigned to integer or date columns - they must be either a valid value or `NULL`. MySQL may be more lenient and accept empty strings for these columns.

**Error Messages:**
- For date columns: `SQLSTATE[22007]: Invalid datetime format: 7 ERROR: invalid input syntax for type date: ""`
- For integer columns: `SQLSTATE[22P02]: Invalid text representation: 7 ERROR: invalid input syntax for type integer: ""`

**MySQL Approach:**
```php
// ❌ MySQL - May accept empty strings for integer/date columns
$obj->agent_id = '';  // Empty string for integer column
$obj->visaExpiry = '';  // Empty string for date column
$obj->save();
```

**PostgreSQL Solution:**

**Option 1: Use NULL instead of empty strings in code (Recommended)**
```php
// ✅ PostgreSQL - Use NULL instead of empty strings
$obj->agent_id = null;  // Instead of ''
$obj->visaExpiry = null;  // Instead of ''
$obj->save();
```

**Option 2: Add model mutators to auto-convert empty strings (Best for legacy code)**
```php
// ✅ PostgreSQL - Add mutator in Model to auto-convert empty strings
// In Admin.php model:

/**
 * Set the agent_id attribute
 * PostgreSQL doesn't accept empty strings for integer columns - convert to NULL
 */
public function setAgentIdAttribute($value)
{
    if ($value === '' || $value === null) {
        $this->attributes['agent_id'] = null;
    } else {
        $this->attributes['agent_id'] = (int)$value;
    }
}

/**
 * Set the visaExpiry attribute
 * PostgreSQL doesn't accept empty strings for date columns - convert to NULL
 */
public function setVisaExpiryAttribute($value)
{
    // Map visaExpiry to visaexpiry column (case-sensitivity fix)
    // PostgreSQL doesn't accept empty strings for date columns - convert to NULL
    $this->attributes['visaexpiry'] = ($value === '' || $value === null) ? null : $value;
}
```

**Example from Codebase:**
- **File:** `app/Models/Admin.php`
- **Issue:** Code sets `$obj->agent_id = '';` and `$obj->visaExpiry = '';` which fails in PostgreSQL
- **Fix:** Added mutators `setAgentIdAttribute()` and `setVisaExpiryAttribute()` to convert empty strings to NULL
- **Columns Fixed:**
  - `agent_id` (integer) - Empty string converted to NULL
  - `visaExpiry` / `visaexpiry` (date) - Empty string converted to NULL
  - `preferredIntake` / `preferredintake` (date) - Empty string converted to NULL
  - `followers` (varchar) - Empty string converted to NULL for consistency
  - `naati_py` (varchar) - Empty string converted to NULL for consistency
  - `related_files` (text) - Empty string converted to NULL for consistency

**Safety:** 🔴 **CRITICAL** - Code setting empty strings to integer or date columns will **fail immediately** in PostgreSQL with data type errors. Must convert empty strings to NULL.

**Notes:**
- PostgreSQL requires proper data types: integers must be integers (or NULL), dates must be dates (or NULL)
- Empty strings (`''`) are not valid for integer or date columns in PostgreSQL
- Use NULL to represent "no value" instead of empty strings
- Model mutators are a good solution for legacy code that can't be easily refactored
- For new code, always use NULL instead of empty strings for optional integer/date fields
- Text/varchar columns can accept empty strings, but converting to NULL is still recommended for consistency

**Common Patterns to Fix:**
- `$obj->integer_field = '';` → Change to `$obj->integer_field = null;` or add mutator
- `$obj->date_field = '';` → Change to `$obj->date_field = null;` or add mutator
- Conditional assignment: `$obj->field = $value ? $value : '';` → Change to `$obj->field = $value ?: null;`

### Empty Strings in WHERE Clauses (Querying)

**Problem:** When querying the database, using empty strings in WHERE clauses with integer/date columns will also fail in PostgreSQL.

**MySQL Approach:**
```php
// ❌ MySQL - May accept empty strings in WHERE clauses
$assignee = Admin::where('id', $list->assignee)->first();  // $list->assignee might be ''

// ❌ MySQL - Empty strings from exploded arrays
$explode = explode(',', $list->followers);
foreach($explode as $exp) {
    $follower = Admin::where('id', $exp)->first();  // $exp might be ''
}
```

**PostgreSQL Solution:**
```php
// ✅ PostgreSQL - Check for empty strings before querying
$assignee = null;
if(!empty($list->assignee) && $list->assignee !== '') {
    $assignee = Admin::where('id', $list->assignee)->first();
}

// ✅ PostgreSQL - Filter empty values from exploded arrays
$followerss = '';
if(!empty($list->followers) && $list->followers !== '') {
    $explode = explode(',', $list->followers);
    foreach($explode as $exp) {
        // Filter out empty values before querying
        if(!empty(trim($exp)) && trim($exp) !== '') {
            $follower = Admin::where('id', trim($exp))->first();
            if($follower) {
                $followerss .= $follower->first_name.', ';
            }
        }
    }
}
```

**Examples from Codebase:**

1. **File:** `resources/views/Admin/clients/index.blade.php` (lines 249, 253)
   - **Issue:** `Admin::where('id', @$list->assignee)` and `Admin::where('id', @$exp)` from exploded followers
   - **Fix:** Added empty string checks before querying and filtered empty values from exploded arrays

2. **File:** `resources/views/Agent/clients/index.blade.php` (lines 216, 220)
   - **Issue:** Same pattern as Admin clients index - assignee and followers queries
   - **Fix:** Added empty string validation before querying

3. **File:** `resources/views/Agent/clients/detail.blade.php` (lines 82, 187, 204, 239)
   - **Issue:** Multiple queries with potentially empty IDs: `agent_id`, `user_id`, `assignee`, and `related_files` exploded array
   - **Fix:** Added validation for all ID fields before querying

4. **File:** `resources/views/Admin/clients/detail.blade.php` (lines 392, 624, 744, 3412)
   - **Issue:** `agent_id`, `user_id`, `assignee`, and `related_files` queries
   - **Fix:** Added empty string checks for all ID queries

5. **File:** `resources/views/Admin/clients/edit.blade.php` (line 974)
   - **Issue:** `related_files` exploded array used in `Admin::where('id', $EXP)`
   - **Fix:** Added filtering for empty values in exploded array

6. **File:** `resources/views/Admin/archived/index.blade.php` (line 90)
   - **Issue:** `Admin::where('id', @$list->assignee)` - assignee can be empty string
   - **Fix:** Added empty string check before querying

**Error:** `SQLSTATE[22P02]: Invalid text representation: 7 ERROR: invalid input syntax for type integer: ""`

**Safety:** 🔴 **CRITICAL** - Queries using empty strings in WHERE clauses with integer/date columns will **fail immediately** in PostgreSQL. Must check for empty strings before querying.

**Notes:**
- Always validate values before using them in WHERE clauses
- When using `explode()` on comma-separated values, filter out empty segments
- Use `trim()` to handle whitespace-only values
- Check `!empty()` and `!== ''` to catch both empty strings and NULL values
- This pattern commonly occurs in Blade view files when displaying related data

**Pattern Checklist:**
- [ ] Check for empty strings before using in `where('id', $value)` clauses
- [ ] Filter empty values when iterating over exploded comma-separated strings
- [ ] Use `trim()` to handle whitespace-only values
- [ ] Check both `!empty()` and `!== ''` for comprehensive validation
- [ ] Verify all database queries in views/blade files handle empty strings properly

---

## Laravel Routing Issues After Migration

### Issue: MethodNotAllowedHttpException on Edit Routes

**Problem:** After migrating or when accessing certain routes directly, you may encounter `MethodNotAllowedHttpException` errors, particularly on edit routes. This happens when:

- A GET request is made to a route that only accepts POST (or vice versa)
- A route is accessed without required parameters (like `{id}`)
- Route definitions don't properly handle edge cases

**Error Messages:**
```
Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
The GET method is not supported for route admin/clients/edit. Supported methods: POST.
```

**Common Causes:**
1. **Missing Route Parameter:** Accessing `/admin/clients/edit` (GET) without an ID, but the route requires `{id}`
2. **Form Submission Issues:** After form submission, redirects might send users to routes without proper parameters
3. **Browser Refresh:** Refreshing a page after a POST request can resubmit or redirect incorrectly
4. **Bookmarked URLs:** Old bookmarks might point to routes without required parameters

**PostgreSQL Migration Context:**
While not directly related to PostgreSQL migration, these routing errors often surface after database migrations when:
- Form submissions fail due to database errors (column mismatches, empty strings, etc.)
- Redirects after failed saves point to incorrect routes
- Validation errors cause unexpected redirects

**Solution:**

**Option 1: Add Fallback Route (Recommended)**
```php
// routes/web.php

// Main edit route with ID (required for displaying form)
Route::get('/clients/edit/{id}', [ClientsController::class, 'edit'])->name('admin.clients.edit');
Route::post('/clients/edit', [ClientsController::class, 'edit']);

// Fallback: redirect GET requests without ID back to list
Route::get('/clients/edit', function() {
    return redirect()->route('admin.clients.index')->with('error', 'Please select a client to edit');
});
```

**Option 2: Handle in Controller (If route order is important)**
```php
public function edit(Request $request, $id = NULL)
{
    if ($request->isMethod('post')) {
        // Handle POST (form submission)
    } else {
        // Handle GET (display form)
        if(isset($id) && !empty($id)) {
            // Show edit form...
        } else {
            // Redirect if no ID provided
            return redirect()->route('admin.clients.index')
                ->with('error', 'Please select a client to edit');
        }
    }
}
```

**Option 3: Make Route Parameter Optional (Less Recommended)**
```php
// Make ID optional in GET route
Route::get('/clients/edit/{id?}', [ClientsController::class, 'edit'])->name('admin.clients.edit');
Route::post('/clients/edit', [ClientsController::class, 'edit']);
```

**Example from Codebase:**
- **File:** `routes/web.php` (lines 282-284)
- **Issue:** GET requests to `/admin/clients/edit` without ID caused `MethodNotAllowedHttpException`
- **Fix:** Added fallback route that redirects GET requests without ID to clients list
- **Pattern:** This same pattern applies to other edit routes (services, contacts, etc.)

**Safety:** 🟡 **MEDIUM** - Not a critical database issue, but causes 405 errors that prevent users from accessing pages. Fix routes after resolving database migration issues.

**Notes:**
- Always include required route parameters when generating URLs: `route('admin.clients.edit', $id)`
- Use named routes instead of hardcoded URLs to prevent parameter mismatches
- After fixing database errors (empty strings, column mismatches), check if routing issues were masked by the database errors
- When form submissions fail, ensure redirects go to correct routes with proper parameters
- Consider adding route fallbacks for common edge cases (missing ID, invalid ID, etc.)

**Prevention Checklist:**
- [ ] Ensure all edit routes have both GET (with `{id}`) and POST routes defined
- [ ] Add fallback routes for GET requests without required parameters
- [ ] Use named routes (`route()`) instead of hardcoded URLs in views
- [ ] Test routes after database migrations to ensure they still work
- [ ] Check redirect logic after form submissions to ensure proper routes are used

---

## String Aggregation

### Issue: GROUP_CONCAT() Not Available

**Problem:** MySQL's `GROUP_CONCAT()` function is not available in PostgreSQL.

**MySQL Approach:**
```php
// ❌ MySQL
DB::raw('GROUP_CONCAT(DISTINCT phone ORDER BY phone) as all_phones')
```

**PostgreSQL Solution:**
```php
// ✅ PostgreSQL
DB::raw('STRING_AGG(DISTINCT phone, \', \' ORDER BY phone) as all_phones')
```

**Example from Codebase:**
- **File:** `app/Http/Controllers/CRM/ClientsController.php`
- **Lines:** 4848-4849
```php
DB::raw('STRING_AGG(DISTINCT client_contacts.phone, \', \' ORDER BY client_contacts.contact_type) as all_phones'),
DB::raw('STRING_AGG(DISTINCT client_emails.email, \', \' ORDER BY client_emails.email_type) as all_emails')
```

**Syntax Differences:**
- **GROUP_CONCAT:** `GROUP_CONCAT([DISTINCT] column [ORDER BY column])`
- **STRING_AGG:** `STRING_AGG([DISTINCT] column, delimiter [ORDER BY column])`

**Safety:** 🔴 **CRITICAL** - Queries using GROUP_CONCAT() will **fail immediately** in PostgreSQL. Must be converted before execution.

**Notes:**
- STRING_AGG requires an explicit delimiter (usually `', '`)
- DISTINCT and ORDER BY work the same way in both
- ORDER BY clause comes after the delimiter in STRING_AGG
- Escape single quotes in delimiter: `\'` for `, `

---

## Date Formatting

### Issue: DATE_FORMAT() Not Available

**Problem:** MySQL's `DATE_FORMAT()` function uses different syntax than PostgreSQL's `TO_CHAR()`.

**MySQL Approach:**
```sql
-- ❌ MySQL
SELECT DATE_FORMAT(created_at, '%Y-%m') as month_key
SELECT DATE_FORMAT(created_at, '%b %Y') as label
```

**PostgreSQL Solution:**
```sql
-- ✅ PostgreSQL
SELECT TO_CHAR(created_at, 'YYYY-MM') as month_key
SELECT TO_CHAR(created_at, 'Mon YYYY') as label
```

**Example from Codebase:**
- **File:** `app/Http/Controllers/CRM/ClientsController.php`
- **Lines:** 285-286, 355-356
```php
DB::raw("TO_CHAR(created_at, 'YYYY-MM') as sort_key"),
DB::raw("TO_CHAR(created_at, 'Mon YYYY') as label"),
```

**Common Format Conversions:**

| MySQL (DATE_FORMAT) | PostgreSQL (TO_CHAR) | Description |
|---------------------|---------------------|-------------|
| `%Y` | `YYYY` | 4-digit year |
| `%y` | `YY` | 2-digit year |
| `%m` | `MM` | Month (01-12) |
| `%d` | `DD` | Day of month (01-31) |
| `%M` | `Month` | Full month name (January) |
| `%b` | `Mon` | Abbreviated month (Jan) |
| `%H` | `HH24` | Hour (00-23) |
| `%i` | `MI` | Minutes (00-59) |
| `%s` | `SS` | Seconds (00-59) |

**Safety:** 🔴 **CRITICAL** - Queries using DATE_FORMAT() will **fail immediately** in PostgreSQL. Must be converted.

**Notes:**
- TO_CHAR uses uppercase format codes for most values
- Format string is case-sensitive
- Use single quotes for format strings
- Different format specifiers - refer to PostgreSQL documentation for full list

---

## Null Handling in ORDER BY

### Issue: NULL Values Sort Differently

**Problem:** PostgreSQL and MySQL handle NULL values differently in ORDER BY clauses. In PostgreSQL, NULLs sort first by default (or last when using DESC), but we often want NULLs last when sorting DESC.

**MySQL Approach:**
```php
// ❌ MySQL - NULLs may sort inconsistently
->orderBy('finish_date', 'desc')
```

**PostgreSQL Solution:**
```php
// ✅ PostgreSQL - Explicitly place NULLs last
->orderByRaw('finish_date DESC NULLS LAST')
```

**Example from Codebase:**
- **File:** `resources/views/crm/clients/tabs/personal_details.blade.php`
- **Lines:** 367, 425
```php
->orderByRaw('finish_date DESC NULLS LAST')
->orderByRaw('job_finish_date DESC NULLS LAST')
```

**Other Examples:**
- **File:** `app/Http/Controllers/CRM/ClientsController.php`
- **Lines:** 4501-4502
```php
->orderByRaw('finish_date DESC NULLS LAST')
->orderByRaw('job_finish_date DESC NULLS LAST')
```

**Safety:** 🟡 **MEDIUM** - Not critical, but results may differ between MySQL and PostgreSQL. Recommended for consistency, especially when displaying data to users.

**Notes:**
- `NULLS LAST` places NULL values at the end when sorting DESC
- `NULLS FIRST` places NULL values at the beginning (default for DESC, but can be explicit)
- Use this when you want incomplete records (with NULL dates) to appear last in sorted lists
- Important for user-facing lists where you want complete records first

---

## String Concatenation

### Issue: CONCAT() vs || Operator

**Note:** Both MySQL and PostgreSQL support `CONCAT()` function, but PostgreSQL's `||` operator is more idiomatic and preferred.

**MySQL Approach:**
```sql
-- ✅ MySQL - Works in PostgreSQL too, but less efficient
SELECT CONCAT(first_name, ' ', last_name) as full_name
```

**PostgreSQL Preferred:**
```sql
-- ✅ PostgreSQL - More efficient and idiomatic
SELECT COALESCE(first_name, '') || ' ' || COALESCE(last_name, '') as full_name
```

**Safety:** 🟢 **LOW** - CONCAT() works in both databases, but `||` is preferred in PostgreSQL for better performance.

**Notes:**
- `||` is the standard SQL string concatenation operator (ANSI SQL)
- CONCAT() in PostgreSQL handles NULLs by converting them to empty strings (MySQL behavior)
- Using `||` with COALESCE() gives you explicit control over NULL handling
- For simple concatenation, both work, but `||` is more performant

---

## FIND_IN_SET Function

### Issue: FIND_IN_SET() Not Available

**Problem:** MySQL's `FIND_IN_SET()` function is not available in PostgreSQL. This function searches for a value in a comma-separated string.

**MySQL Approach:**
```php
// ❌ MySQL - FIND_IN_SET() doesn't exist in PostgreSQL
DB::table('mail_reports')
    ->whereRaw("FIND_IN_SET(?, to_mail)", [$clientId])
    ->where('type', 'client')
    ->get();
```

**PostgreSQL Solutions:**

**Option 1: Using string_to_array() with ANY (Recommended)**
```php
// ✅ PostgreSQL - Convert CSV to array and check membership
DB::table('mail_reports')
    ->whereRaw("? = ANY(string_to_array(to_mail, ','))", [$clientId])
    ->where('type', 'client')
    ->get();
```

**Option 2: Using position() function**
```php
// ✅ PostgreSQL - Check if substring exists
// Note: Less accurate if values can be substrings of each other (e.g., '1' matches '10')
DB::table('mail_reports')
    ->whereRaw("position(? IN to_mail) > 0", [$clientId])
    ->where('type', 'client')
    ->get();
```

**Option 3: Using LIKE pattern**
```php
// ✅ PostgreSQL - Pattern matching
// Note: Less accurate, similar substring issue as position()
DB::table('mail_reports')
    ->where('to_mail', 'LIKE', "%{$clientId}%")
    ->where('type', 'client')
    ->get();
```

**Option 4: Using regex (Most Accurate)**
```php
// ✅ PostgreSQL - Regex to match exact value in CSV
// Matches: start of string OR comma, then value, then comma OR end of string
DB::table('mail_reports')
    ->whereRaw("to_mail ~ ?", ["(^|,){$clientId}(,|$)"])
    ->where('type', 'client')
    ->get();
```

**Example from Codebase:**
- **File:** `resources/views/Admin/clients/detail.blade.php`
- **Line:** 2213
- **Issue:** Query fails with "Undefined column" error
- **Original:**
  ```php
  // ❌ MySQL
  WHERE FIND_IN_SET("36746", to_mail)
  ```
- **Fixed:**
  ```php
  // ✅ PostgreSQL
  WHERE '36746' = ANY(string_to_array(to_mail, ','))
  ```

**Which Option to Use:**

| Option | Pros | Cons | Use When |
|--------|------|------|----------|
| `string_to_array()` + `ANY` | Exact match, handles edge cases well | Slightly verbose | **Recommended for most cases** |
| `position()` | Simple, readable | Can match substrings (e.g., '1' matches '10') | Values are guaranteed unique and non-overlapping |
| `LIKE` | Very simple | Can match substrings, SQL injection risk if not escaped | Quick fixes, trusted data only |
| Regex `~` | Most accurate, handles edge cases | More complex syntax | Need exact matching with complex data |

**Safety:** 🔴 **CRITICAL** - Queries using FIND_IN_SET() will **fail immediately** in PostgreSQL. Must be converted before execution.

**Notes:**
- `string_to_array(column, ',')` splits a CSV string into a PostgreSQL array
- `ANY()` checks if a value exists in an array
- If the CSV has spaces (e.g., `'1, 2, 3'`), you may need to trim: `string_to_array(REPLACE(to_mail, ' ', ''), ',')`
- Consider refactoring: storing comma-separated values is an anti-pattern; use proper relational tables or PostgreSQL arrays instead

---

## GROUP BY Strictness

### Issue: PostgreSQL Requires All Selected Columns in GROUP BY

**Problem:** MySQL allows `SELECT *` with `GROUP BY` on a single column (returns arbitrary values from non-grouped columns), but PostgreSQL strictly requires all selected columns to either be in the GROUP BY clause or be used in an aggregate function.

**MySQL Approach:**
```php
// ❌ MySQL - Allows SELECT * with GROUP BY single column
\App\Models\Application::where('client_id', $id)
    ->groupBy('workflow')
    ->get();
// MySQL returns all columns with arbitrary values for non-grouped columns
```

**PostgreSQL Solution:**

**Option 1: Use DISTINCT (Recommended for unique values)**
```php
// ✅ PostgreSQL - Use DISTINCT when you only need unique values
\App\Models\Application::where('client_id', $id)
    ->select('workflow')
    ->distinct()
    ->get();
```

**Option 2: Select Only Needed Columns with GROUP BY**
```php
// ✅ PostgreSQL - Explicitly select only columns you need
\App\Models\Application::where('client_id', $id)
    ->select('workflow')
    ->groupBy('workflow')
    ->get();
```

**Option 3: Use Aggregate Functions**
```php
// ✅ PostgreSQL - If you need other columns, use aggregates
\App\Models\Application::where('client_id', $id)
    ->select('workflow', DB::raw('MAX(id) as id'), DB::raw('COUNT(*) as count'))
    ->groupBy('workflow')
    ->get();
```

**Option 4: Use DISTINCT ON (PostgreSQL-specific)**
```php
// ✅ PostgreSQL - DISTINCT ON gets first row per group
\App\Models\Application::where('client_id', $id)
    ->select('*')
    ->distinct('workflow')
    ->orderBy('workflow')
    ->orderBy('id') // Secondary sort for which row to pick
    ->get();
```

**Example from Codebase:**
- **File:** `resources/views/Admin/clients/addclientmodal.blade.php`
- **Line:** 1224
- **Issue:** Query fails with "column must appear in GROUP BY clause" error
- **Original:**
  ```php
  // ❌ MySQL - SELECT * with GROUP BY workflow
  \App\Models\Application::where('client_id', $id)
      ->groupBy('workflow')
      ->get();
  ```
- **Fixed:**
  ```php
  // ✅ PostgreSQL - Use DISTINCT for unique workflows
  \App\Models\Application::where('client_id', $id)
      ->select('workflow')
      ->distinct()
      ->get();
  ```

**Error Message:**
```
SQLSTATE[42803]: Grouping error: 7 ERROR: column "applications.id" must appear 
in the GROUP BY clause or be used in an aggregate function
LINE 1: select * from "applications" where "client_id" = $1 group by...
```

**Which Option to Use:**

| Option | Use When | Pros | Cons |
|--------|----------|------|------|
| `select()->distinct()` | Need unique values only | Simple, clear intent | Can't get other columns |
| `select()->groupBy()` | Need specific columns | Explicit control | Must list all needed columns |
| Aggregate functions | Need summary data | Can get counts, max, min, etc. | More complex |
| `DISTINCT ON` | Need first row per group | PostgreSQL-specific, powerful | Requires ORDER BY, more complex |

**Safety:** 🔴 **CRITICAL** - Queries using `SELECT *` with `GROUP BY` will **fail immediately** in PostgreSQL. Must be converted before execution.

**Notes:**
- PostgreSQL follows SQL standard strictly for GROUP BY
- MySQL's lenient GROUP BY is non-standard and can return unpredictable results
- Always specify which columns you need when grouping
- If you need a specific row from each group, use `DISTINCT ON` with appropriate `ORDER BY`
- Consider if you really need GROUP BY or if DISTINCT would work better

---

## NOT NULL Constraints

### Issue: PostgreSQL Enforces NOT NULL Strictly

**Problem:** PostgreSQL strictly enforces NOT NULL constraints on columns. MySQL is more lenient and may allow NULL values even when a column is defined as NOT NULL (depending on SQL mode). When migrating from MySQL to PostgreSQL, any code that doesn't provide values for NOT NULL columns will fail.

**MySQL Approach:**
```php
// ❌ MySQL - May allow NULL even if column is NOT NULL
ActivitiesLog::create([
    'client_id' => $clientId,
    'created_by' => Auth::id(),
    'subject' => 'Activity subject',
    'description' => 'Activity description',
    'activity_type' => 'activity',
    // task_status missing - MySQL might allow this
]);
```

**PostgreSQL Solution:**
```php
// ✅ PostgreSQL - Must provide value for NOT NULL columns
ActivitiesLog::create([
    'client_id' => $clientId,
    'created_by' => Auth::id(),
    'subject' => 'Activity subject',
    'description' => 'Activity description',
    'activity_type' => 'activity',
    'task_status' => 0, // Required for NOT NULL column
]);
```

**For `new Model` Pattern:**
```php
// ❌ MySQL - May allow NULL
$objs = new ActivitiesLog;
$objs->client_id = $clientId;
$objs->created_by = Auth::id();
$objs->subject = 'Activity subject';
$objs->save(); // task_status missing

// ✅ PostgreSQL - Must set before save
$objs = new ActivitiesLog;
$objs->client_id = $clientId;
$objs->created_by = Auth::id();
$objs->subject = 'Activity subject';
$objs->task_status = 0; // Required before save
$objs->save();
```

**Examples from Codebase:**

1. **ActivitiesLog Table:**
   - **File:** `app/Traits/LogsClientActivity.php`
   - **Line 27:** Added `'task_status' => 0,` and `'pin' => 0,` to ActivitiesLog::create()
   - **Files Fixed:** All files creating ActivitiesLog instances (40+ instances across 13 files)
   - **Columns:** `task_status` (default: 0), `pin` (default: 0)

2. **ClientEmail Table:**
   - **File:** `app/Http/Controllers/CRM/ClientPersonalDetailsController.php`
   - **Lines 980, 2010:** Added `'is_verified' => false` to ClientEmail::create()
   - **Files Fixed:** All files creating ClientEmail instances (7 instances across 3 files)
   - **Columns:** `is_verified` (default: false)

3. **ClientContact Table:**
   - **Files:** `app/Http/Controllers/CRM/ClientPersonalDetailsController.php`, `app/Http/Controllers/CRM/ClientsController.php`, `app/Http/Controllers/CRM/Leads/LeadController.php`, `app/Services/BansalAppointmentSync/ClientMatchingService.php`
   - **Lines:** Multiple locations across 4 files
   - **Files Fixed:** All files creating ClientContact instances (9 instances across 4 files)
   - **Columns:** `is_verified` (default: false)

4. **ClientQualification Table:**
   - **Files:** `app/Http/Controllers/CRM/ClientPersonalDetailsController.php`, `app/Http/Controllers/CRM/ClientsController.php`
   - **Lines:** Multiple locations across 2 files
   - **Files Fixed:** All files creating ClientQualification instances (5 instances across 2 files)
   - **Columns:** `specialist_education` (default: 0), `stem_qualification` (default: 0), `regional_study` (default: 0)

5. **ClientExperience Table:**
   - **Files:** `app/Http/Controllers/CRM/ClientPersonalDetailsController.php`, `app/Http/Controllers/CRM/ClientsController.php`
   - **Lines:** Multiple locations across 2 files
   - **Files Fixed:** All files creating ClientExperience instances (4 instances across 2 files)
   - **Columns:** `fte_multiplier` (default: 1.00)

6. **Admins Table (verified column):**
   - **File:** `app/Http/Controllers/CRM/Leads/LeadController.php`
   - **Line 382:** Added `'verified' => 0` to `$adminData` array when creating leads via `DB::table('admins')->insertGetId()`
   - **Files Fixed:** LeadController.php (1 instance)
   - **Columns:** `verified` (default: 0 for new leads, 1 for verified users)
   - **Note:** When using `DB::table('admins')->insertGetId()` or `DB::table('admins')->insert()`, must include `'verified' => 0` for new leads/clients

7. **Admins Table (password column):**
   - **File:** `app/Http/Controllers/CRM/Leads/LeadController.php`
   - **Line 374:** Changed from `'password' => ''` to `'password' => Hash::make('LEAD_PLACEHOLDER')` when creating leads via `DB::table('admins')->insertGetId()`
   - **Files Fixed:** LeadController.php (1 instance)
   - **Columns:** `password` (NOT NULL, required for all admins table records)
   - **Issue:** PostgreSQL rejects empty strings `''` for NOT NULL string columns. Empty string may be treated as NULL or rejected entirely.
   - **Solution:** Use `Hash::make('LEAD_PLACEHOLDER')` as placeholder. This is safe because:
     - Leads (`type='lead'`) are typically restricted from logging in
     - Placeholder hash will never match any login attempt (`Hash::check()` returns false)
   - **Note:** The `admins` table is used for staff, leads, and clients. Password is required due to NOT NULL constraint, but leads don't need real passwords.

8. **Admins Table (show_dashboard_per column):**
   - **File:** `app/Http/Controllers/CRM/Leads/LeadController.php`
   - **Line 383:** Added `'show_dashboard_per' => 0` to `$adminData` array when creating leads via `DB::table('admins')->insertGetId()`
   - **Files Fixed:** LeadController.php (1 instance)
   - **Columns:** `show_dashboard_per` (NOT NULL, default: 0 for leads/clients, 1 for staff with dashboard permission)
   - **Note:** When using `DB::table('admins')->insertGetId()` or `DB::table('admins')->insert()`, must include `'show_dashboard_per' => 0` for new leads/clients

9. **Admins Table (EOI Qualification columns):**
   - **File:** `app/Http/Controllers/CRM/Leads/LeadController.php`
   - **Lines 386-388:** Added `'australian_study' => 0`, `'specialist_education' => 0`, `'regional_study' => 0` to `$adminData` array when creating leads via `DB::table('admins')->insertGetId()`
   - **Files Fixed:** LeadController.php (1 instance)
   - **Columns:** `australian_study` (NOT NULL, default: 0), `specialist_education` (NOT NULL, default: 0), `regional_study` (NOT NULL, default: 0)
   - **Issue:** These columns have `default(0)` in the migration, but PostgreSQL doesn't apply database defaults when using explicit column lists in `INSERT` statements with `DB::table()->insert()`. Must explicitly provide values.
   - **Note:** These fields track EOI (Expression of Interest) qualifications for immigration points calculation. For new leads, all should be `0` (false). When using `DB::table('admins')->insertGetId()` or `DB::table('admins')->insert()`, must include all three fields.

**Safety:** 🔴 **CRITICAL** - Code missing NOT NULL column values will **fail immediately** in PostgreSQL with errors like:
```
SQLSTATE[23502]: Not null violation: 7 ERROR: null value in column "task_status" 
of relation "activities_logs" violates not-null constraint
```

**Notes:**
- Always check database schema for NOT NULL columns
- When using `Model::create([...])`, include all NOT NULL columns
- When using `new Model` followed by `->save()`, set all NOT NULL properties before save
- Use appropriate default values (e.g., `0` for numeric fields, empty string for text fields)
- **IMPORTANT:** PostgreSQL may reject empty strings `''` for NOT NULL string columns. Use placeholder values instead (e.g., `Hash::make('PLACEHOLDER')` for password fields)
- Check migration files to identify which columns have NOT NULL constraints
- PostgreSQL will reject the entire transaction if any NOT NULL constraint is violated

**Common Patterns to Fix:**
- `Model::create([...])` - Add missing NOT NULL fields to the array
- `new Model; $obj->field = value; $obj->save();` - Add `$obj->not_null_field = default_value;` before save
- Mass assignment - Ensure `$fillable` array includes the NOT NULL field in the model

**ActivitiesLog Specific Pattern:**
When using `new ActivitiesLog` followed by `->save()`, always set:
```php
$objs = new ActivitiesLog;
$objs->client_id = $clientId;
$objs->created_by = Auth::user()->id;
$objs->subject = $subject;
$objs->description = $description;
$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
$objs->save();
```

**When to Use task_status = 1:**
- Only use `task_status = 1` when logging actual task completion (e.g., "Task completed for [assignee]")
- For all regular activities (document uploads, emails, appointments, etc.), use `task_status = 0`
- Example of task_status = 1: `app/Http/Controllers/CRM/AssigneeController.php` line 1080

**When to Use pin = 1:**
- Only use `pin = 1` when the activity should be pinned/featured
- For all regular activities, use `pin = 0`
- Pinned activities are typically displayed prominently in activity feeds

**Common Error:**
```
SQLSTATE[23502]: Not null violation: 7 ERROR: null value in column "task_status" 
of relation "activities_logs" violates not-null constraint
```
This occurs when `new ActivitiesLog` is used without setting `task_status` and `pin` before `save()`.

**Files Fixed (30 locations):**
- `app/Http/Controllers/CRM/ClientsController.php` - 8 locations
- `app/Http/Controllers/CRM/AppointmentsController.php` - 3 locations (NOTE: This controller has been deleted - old appointment system removed)
- `app/Http/Controllers/CRM/AssigneeController.php` - 3 locations
- `app/Http/Controllers/CRM/ClientPersonalDetailsController.php` - 1 location
- `app/Http/Controllers/CRM/ClientAccountsController.php` - 12 locations
- `app/Http/Controllers/CRM/CRMUtilityController.php` - 2 locations
- `app/Http/Controllers/CRM/Leads/LeadConversionController.php` - 1 location (already fixed)

**Known Tables with NOT NULL Columns:**
- `activities_logs`: 
  - `task_status` (default: 0) - **CRITICAL**: Must set before save. Use `0` for regular activities, `1` for task completions
  - `pin` (default: 0) - **CRITICAL**: Must set before save. Use `0` for regular activities, `1` for pinned activities
  - **Pattern:** Always set both fields when using `new ActivitiesLog` followed by `->save()`
  - **Common mistake:** Forgetting to set these fields causes immediate PostgreSQL NOT NULL violations
- `user_logs`:
  - `user_id` (may be NOT NULL) - **CRITICAL**: Must set before save. Use `$user->id` when user exists, `null` when user doesn't exist (if column allows null)
  - **Pattern:** Always use `$user ? $user->id : null` instead of `@$user` or `@$user->id`
  - **Common mistake:** Assigning entire `$user` object instead of `$user->id` causes type conversion errors
  - **Failed login pattern:** Wrap in try-catch when logging failed logins where user might not exist:
    ```php
    try {
        $obj = new \App\Models\UserLog;
        $obj->user_id = $user ? $user->id : null;
        // ... other fields
        $obj->save();
    } catch (\Exception $e) {
        \Log::error('Failed to log: ' . $e->getMessage());
    }
    ```
  - **Files Fixed:** `app/Http/Controllers/Auth/AdminLoginController.php` (3 instances: sendFailedLoginResponse, authenticated, logout)
- `client_emails`: `is_verified` (default: false)
- `client_contacts`: `is_verified` (default: false)
- `client_qualifications`: `specialist_education` (default: 0), `stem_qualification` (default: 0), `regional_study` (default: 0)
- `client_experiences`: `fte_multiplier` (default: 1.00)
- `client_matters`: `matter_status` (default: 1 for active) - **CRITICAL**: Must set `matter_status = 1` when creating new matters
- `documents`: 
  - `signer_count` (NOT NULL, default: 1) - **CRITICAL**: Must set before save when using `new Document` followed by `->save()`. Use `1` for regular documents (non-signature documents). Database defaults are not applied with explicit INSERT column lists.
  - **Pattern:** Always set `$document->signer_count = 1;` before `->save()` when creating new Document records
  - **Common mistake:** Forgetting to set `signer_count` causes immediate PostgreSQL NOT NULL violations: `SQLSTATE[23502]: Not null violation: 7 ERROR: null value in column "signer_count" of relation "documents" violates not-null constraint`
  - **Common scenarios:** Document checklists, receipt uploads, invoice/receipt PDF generation, general document uploads
  - **Files Fixed:** `app/Http/Controllers/CRM/Clients/ClientDocumentsController.php` (6 instances), `app/Http/Controllers/API/ClientPortalDocumentController.php` (1 instance), `app/Http/Controllers/CRM/ClientAccountsController.php` (11 instances)
- `admins`: 
  - `verified` (default: 0 for new leads/clients, 1 for verified users) - **CRITICAL**: Required when using `DB::table('admins')->insert()` or `insertGetId()`
  - `password` (NOT NULL, use `Hash::make('LEAD_PLACEHOLDER')` for leads) - **CRITICAL**: Empty strings may be rejected. Use hashed placeholder for leads/clients without portal access.
  - `show_dashboard_per` (default: 0 for leads/clients, 1 for staff with permission) - **CRITICAL**: Required when using `DB::table('admins')->insert()` or `insertGetId()`
  - `australian_study`, `specialist_education`, `regional_study` (all default: 0) - **CRITICAL**: Database defaults not applied with explicit INSERT column lists. Must explicitly provide values.
- Check migration files for other tables with NOT NULL columns that have defaults

---

## Pending Migrations and Schema Mismatches

### Issue: Code References Columns That Don't Exist Yet

**Problem:** When pulling new code from MySQL or adding new features, the code may reference database columns that don't exist in PostgreSQL yet. This typically happens when:
- Migration files exist but haven't been run
- Code was written assuming a column exists, but the migration is pending
- Schema changes were made in MySQL but not yet migrated to PostgreSQL

**Error Example:**
```
SQLSTATE[42703]: Undefined column: 7 ERROR: column "office_id" of relation "client_matters" does not exist
LINE 1: ...rt into "client_matters" ("user_id", "client_id", "office_id...
```

**Common Symptoms:**
- `QueryException` with `Undefined column` error
- Code sets a property on a model (e.g., `$matter->office_id = ...`) but the column doesn't exist
- Model's `$fillable` array includes a column that's not in the database table
- INSERT/UPDATE operations fail with column name errors

**How to Identify:**
1. Check if the error mentions a specific column that doesn't exist
2. Check if a migration file exists for that column:
   ```bash
   # Search for migration files
   ls -la database/migrations/ | grep -i "column_name"
   ```
3. Check migration status:
   ```bash
   php artisan migrate:status
   ```
4. Look for the column in the model's `$fillable` array or where it's being used in code

**Solution:**
1. **Find the migration file** that adds the missing column
2. **Check migration status** to see if it's pending:
   ```bash
   php artisan migrate:status
   ```
3. **Run the specific migration**:
   ```bash
   php artisan migrate --path=database/migrations/YYYY_MM_DD_HHMMSS_migration_name.php
   ```
4. **Or run all pending migrations** (if safe):
   ```bash
   php artisan migrate
   ```

**Example from Codebase:**
- **Error:** `column "office_id" of relation "client_matters" does not exist`
- **Location:** `app/Http/Controllers/CRM/ClientsController.php` line 8386
- **Code:**
  ```php
  $matter = new ClientMatter();
  $matter->office_id = $request['office_id'] ?? Auth::user()->office_id ?? null;
  // ... other assignments
  $matter->save(); // ❌ Fails because office_id column doesn't exist
  ```
- **Model:** `app/Models/ClientMatter.php` has `'office_id'` in `$fillable` array
- **Migration:** `database/migrations/2025_12_17_145310_add_office_to_client_matters_and_documents.php` was pending
- **Fix:** Ran the migration: `php artisan migrate --path=database/migrations/2025_12_17_145310_add_office_to_client_matters_and_documents.php`

**Safety:** 🔴 **CRITICAL** - Code referencing non-existent columns will **fail immediately** with `QueryException`. Must run pending migrations before the code can execute.

**Notes:**
- Always check `php artisan migrate:status` after pulling new code
- If you see "Undefined column" errors, check for pending migrations first
- Migration files may exist in `database/migrations/` but not have been executed
- Some migrations may fail if they depend on tables/columns that don't exist yet - run migrations in order
- When using `Model::create()` or `$model->save()`, Laravel will try to insert all `$fillable` columns, even if they don't exist in the database
- Check model's `$fillable` array matches actual database schema
- If a column is truly not needed, remove it from `$fillable` or make it conditional

**Prevention Checklist:**
- [ ] After pulling new code, run `php artisan migrate:status` to check for pending migrations
- [ ] If code references new columns, verify the migration exists and has been run
- [ ] Check model's `$fillable` array matches database schema
- [ ] Run migrations in development/staging before production
- [ ] If migration fails, check error message - may need to run dependent migrations first

**Common Migration Patterns:**
- New columns added to existing tables
- New tables created
- Indexes added/removed
- Foreign key constraints added
- Column type changes

---

## Handling Missing Form Fields

### Issue: Form Fields That Don't Exist in Request

**Problem:** When creating or updating records, controllers may expect form fields that don't always exist in the request. Different forms may submit different fields (e.g., simple form vs enhanced form). If the code directly accesses `$request->field_name` without checking if it exists, it may result in `null` values being assigned. PostgreSQL strictly enforces NOT NULL constraints, so missing required fields will cause database errors.

**MySQL Approach:**
```php
// ❌ MySQL - May silently allow NULL or fail inconsistently
$obj = new Note;
$obj->title = $request->title;  // Undefined index warning if title doesn't exist
$obj->matter_id = $request->matter_id;
$obj->mobile_number = $request->mobileNumber;  // Undefined if field doesn't exist
$obj->save();
```

**PostgreSQL Solution:**
```php
// ✅ PostgreSQL - Use null coalescing operator (??) to provide defaults
$obj = new Note;
$obj->title = $request->title ?? '';  // Default to empty string if not provided
$obj->matter_id = $request->matter_id;
$obj->mobile_number = $request->mobileNumber ?? null;  // Default to null if not provided
$obj->save();
```

**For Updates - Check Before Comparing:**
```php
// ❌ MySQL - May compare undefined values
if($oldNote->title !== $request->title) {  // Warning if title doesn't exist
    $changedFields['Title'] = ['old' => $oldNote->title, 'new' => $request->title];
}

// ✅ PostgreSQL - Check if field exists before comparing
if(isset($request->title) && $oldNote->title !== $request->title) {
    $changedFields['Title'] = ['old' => $oldNote->title, 'new' => $request->title];
}
```

**Error Handling Pattern:**
```php
// ✅ PostgreSQL - Wrap save operations in try-catch for better error handling
use Illuminate\Support\Facades\Log;

try {
    $saved = $obj->save();
} catch (\Exception $e) {
    Log::error('Error saving record: ' . $e->getMessage(), [
        'request_data' => $request->all(),
        'trace' => $e->getTraceAsString()
    ]);
    $response['status'] = false;
    $response['message'] = 'Error saving record. Please try again.';
    echo json_encode($response);
    return;
}
```

**Example from Codebase:**
- **File:** `app/Http/Controllers/CRM/Clients/ClientNotesController.php`
- **Lines:** 59-62, 68, 102-116
- **Issue:** Simple form doesn't include `title` field, but controller accessed `$request->title` directly
- **Fix:** Changed to `$obj->title = $request->title ?? '';` and added `isset($request->title)` check in update logic

**Common Patterns:**

1. **Direct property assignment:**
   ```php
   // ❌ Before
   $obj->field = $request->field;
   
   // ✅ After
   $obj->field = $request->field ?? null;  // or appropriate default
   ```

2. **Conditional checks:**
   ```php
   // ❌ Before
   if($request->field) { ... }
   
   // ✅ After (if field may not exist)
   if(isset($request->field) && $request->field) { ... }
   ```

3. **Comparisons in update logic:**
   ```php
   // ❌ Before
   if($oldValue !== $request->field) { ... }
   
   // ✅ After
   if(isset($request->field) && $oldValue !== $request->field) { ... }
   ```

**Safety:** 🔴 **CRITICAL** - Missing form fields can cause:
- `Undefined index` PHP warnings/errors
- `null value in column violates not-null constraint` PostgreSQL errors
- 500 Internal Server Error responses

**Notes:**
- Always use null coalescing operator (`??`) when accessing request fields that may not exist
- Check form views to see which fields are actually submitted
- Different forms (simple vs enhanced) may submit different fields
- Use appropriate defaults based on column type (empty string for text, null for nullable columns, 0 for numeric)
- Wrap database save operations in try-catch for better error handling and logging
- Check `isset()` before comparing values in update scenarios
- Log errors with request data to help debug issues in production

**When to Use:**
- When a field exists in some forms but not others
- When fields are conditionally displayed/shown in forms
- When creating new records where some fields are optional
- When updating records where only some fields may change
- When dealing with legacy code that may have inconsistent form submissions

---

## Notes Table - Missing Default Values

### Issue: Notes Table NOT NULL Constraints

**Problem:** After MySQL to PostgreSQL migration, creating notes fails with "Error saving note. Please try again." The `notes` table has NOT NULL constraints on fields that MySQL allowed to be NULL or had implicit defaults, but PostgreSQL strictly enforces.

**MySQL Approach:**
```php
// ❌ MySQL - May work without explicitly setting pin, folloup, status
$obj = new Note;
$obj->title = $request->title ?? '';
$obj->client_id = $request->client_id;
$obj->user_id = Auth::user()->id;
$obj->description = $request->description;
$obj->mail_id = $request->mailid;
$obj->type = $request->vtype;
$obj->task_group = $request->task_group;
$obj->save(); // May work in MySQL
```

**PostgreSQL Solution:**
```php
// ✅ PostgreSQL - Must explicitly set NOT NULL fields
$obj = new Note;
$obj->title = $request->title ?? '';
$obj->client_id = $request->client_id;
$obj->user_id = Auth::user()->id;
$obj->description = $request->description;
$obj->mail_id = $request->mailid;
$obj->type = $request->vtype;
$obj->task_group = $request->task_group;

// PostgreSQL NOT NULL constraints - must set these fields
if(!$isUpdate) {
    $obj->pin = 0; // Default to not pinned (0 = not pinned, 1 = pinned)
    $obj->folloup = 0; // Default to not a follow-up (0 = regular note, 1 = follow-up)
    $obj->status = '0'; // Default status (string '0' = active, '1' = completed)
}
$obj->save(); // Will now work in PostgreSQL
```

**Example from Codebase:**
- **File:** `app/Http/Controllers/CRM/Clients/ClientNotesController.php`
- **Lines:** 89-110
- **Issue:** Creating notes failed with database constraint violation
- **Fields Required:** 
  - `pin` (integer, NOT NULL, default: 0)
  - `folloup` (integer, NOT NULL, default: 0) 
  - `status` (string, NOT NULL, default: '0')

**Error Message:**
```
SQLSTATE[23502]: Not null violation: 7 ERROR: null value in column "pin" 
of relation "notes" violates not-null constraint
```
OR
```
Error saving note. Please try again.
```
(Displayed when exception is caught and logged)

**Safety:** 🔴 **CRITICAL** - Note creation will fail completely in PostgreSQL without these fields. This is a common issue after migration where MySQL's lenient NULL handling differs from PostgreSQL's strict enforcement.

**Notes:**
- MySQL may allow NULL values or have implicit defaults even when NOT NULL is specified (depending on SQL mode)
- PostgreSQL strictly enforces NOT NULL constraints and does not use implicit defaults
- Always check database schema for NOT NULL columns when migrating
- The `pin`, `folloup`, and `status` fields are used for note filtering and task management
- `pin`: Controls whether note is pinned to top of list
- `folloup`: Indicates if note is a follow-up task
- `status`: Tracks completion status (active/completed)

**When to Set:**
- Always set when creating new notes (`if(!$isUpdate)`)
- Not needed when updating existing notes (they already have values)
- Use `0` as default for all three fields for regular notes

---

## Documents Table - Missing signer_count Field

**⚠️ OBSOLETE - This section is outdated and no longer applicable.**

**Status:** The `signer_count` column was documented as required but **never actually existed in the database schema**. All references to this column have been removed from the codebase as of January 2026.

**Background:** This was planned functionality for tracking document signing requirements, but the database migration was never created. The column does not exist in the `documents` table and all code references have been removed.

**Actual Documents Table Columns:**
- id, file_name, filetype, myfile, myfile_key, user_id, client_id, file_size, type, doc_type, mail_type, checklist, checklist_verified_by, checklist_verified_at, not_used_doc, created_at, updated_at

**Files Fixed (January 2, 2026):**
- `app/Http/Controllers/Admin/ClientsController.php` - Removed 4 instances
- `app/Http/Controllers/Admin/PartnersController.php` - Removed 7 instances

---

### ~~Issue: Documents Table NOT NULL Constraint on signer_count~~ (OBSOLETE)

**Problem:** After MySQL to PostgreSQL migration, creating documents (including document checklists, receipt uploads, invoice PDFs, etc.) fails with "Error saving '...': SQLSTATE[23502]: Not null violation: 7 ERROR: null value in column "signer_count" of relation "documents" violates not-null constraint". The `documents` table has a NOT NULL constraint on the `signer_count` column that MySQL may have allowed to be NULL or had implicit defaults, but PostgreSQL strictly enforces.

**MySQL Approach:**
```php
// ❌ MySQL - May work without explicitly setting signer_count
$obj = new Document;
$obj->user_id = Auth::user()->id;
$obj->client_id = $clientid;
$obj->type = $request->type ?? 'client';
$obj->doc_type = $doctype;
$obj->folder_name = (string)$request->folder_name;
$obj->checklist = trim($item);
$obj->save(); // May work in MySQL
```

**PostgreSQL Solution:**
```php
// ✅ PostgreSQL - Must explicitly set signer_count
$obj = new Document;
$obj->user_id = Auth::user()->id;
$obj->client_id = $clientid;
$obj->type = $request->type ?? 'client';
$obj->doc_type = $doctype;
$obj->folder_name = (string)$request->folder_name;
$obj->checklist = trim($item);
// PostgreSQL NOT NULL constraint - signer_count is required (default: 1 for regular documents)
$obj->signer_count = 1;
$obj->save(); // Will now work in PostgreSQL
```

**For DB::table()->insertGetId() Pattern:**
```php
// ❌ MySQL - May work without signer_count
$documentData = [
    'user_id' => $admin->id,
    'client_id' => $clientId,
    'type' => 'client',
    'doc_type' => $docType,
    'folder_name' => $docCategoryId,
    'checklist' => $checklistName,
    'status' => 'draft',
    'created_at' => now(),
    'updated_at' => now()
];
$documentId = DB::table('documents')->insertGetId($documentData);

// ✅ PostgreSQL - Must include signer_count
$documentData = [
    'user_id' => $admin->id,
    'client_id' => $clientId,
    'type' => 'client',
    'doc_type' => $docType,
    'folder_name' => $docCategoryId,
    'checklist' => $checklistName,
    'status' => 'draft',
    'signer_count' => 1, // PostgreSQL NOT NULL constraint - required
    'created_at' => now(),
    'updated_at' => now()
];
$documentId = DB::table('documents')->insertGetId($documentData);
```

**For Receipt Document Uploads:**
```php
// ❌ MySQL - May work without explicitly setting signer_count
$obj = new \App\Models\Document;
$obj->file_name = $explodeFileName[0];
$obj->filetype = $exploadename[1];
$obj->user_id = Auth::user()->id;
$obj->myfile_key = $name;
$obj->myfile = $name;
$obj->client_id = $id;
$obj->type = $request->type;
$obj->file_size = $size;
$obj->doc_type = $doctype;
$obj->client_matter_id = $client_matter_id;
$saved = $obj->save(); // ❌ Fails in PostgreSQL

// ✅ PostgreSQL - Must explicitly set signer_count
$obj = new \App\Models\Document;
$obj->file_name = $explodeFileName[0];
$obj->filetype = $exploadename[1];
$obj->user_id = Auth::user()->id;
$obj->myfile_key = $name;
$obj->myfile = $name;
$obj->client_id = $id;
$obj->type = $request->type;
$obj->file_size = $size;
$obj->doc_type = $doctype;
$obj->client_matter_id = $client_matter_id;
// PostgreSQL NOT NULL constraint - signer_count is required (default: 1 for regular documents)
$obj->signer_count = 1;
$saved = $obj->save(); // ✅ Works in PostgreSQL
```

**For PDF Generation (Invoices, Receipts):**
```php
// ❌ MySQL - May work without explicitly setting signer_count
$document = new \App\Models\Document;
$document->file_name = $fileName;
$document->filetype = 'pdf';
$document->user_id = $userId;
$document->myfile = $s3Url;
$document->myfile_key = $s3FileName;
$document->client_id = $record_get->client_id;
$document->type = 'client_fund_receipt';
$document->doc_type = $docType;
$document->file_size = strlen($pdfContent);
$document->save(); // ❌ Fails in PostgreSQL

// ✅ PostgreSQL - Must explicitly set signer_count
$document = new \App\Models\Document;
$document->file_name = $fileName;
$document->filetype = 'pdf';
$document->user_id = $userId;
$document->myfile = $s3Url;
$document->myfile_key = $s3FileName;
$document->client_id = $record_get->client_id;
$document->type = 'client_fund_receipt';
$document->doc_type = $docType;
$document->file_size = strlen($pdfContent);
// PostgreSQL NOT NULL constraint - signer_count is required (default: 1 for regular documents)
$document->signer_count = 1;
$document->save(); // ✅ Works in PostgreSQL
```

**Examples from Codebase:**
- **File:** `app/Http/Controllers/CRM/Clients/ClientDocumentsController.php`
- **Lines:** 76-91, 417-427, 1914-1923, 1934-1943, 2095-2104, 2119-2129
- **Issue:** Creating personal/visa document checklists failed with database constraint violation
- **Fields Required:** 
  - `signer_count` (integer, NOT NULL, default: 1)

- **File:** `app/Http/Controllers/CRM/ClientAccountsController.php`
- **Lines:** ~197, ~1518, ~2263, ~2606, ~2764, ~2982, ~3203, ~4283, ~4455, ~4545, ~4696
- **Issue:** Receipt document uploads and PDF generation failed with database constraint violation
- **Scenarios Fixed:**
  - Client receipt document uploads
  - Office receipt document uploads
  - Invoice PDF generation
  - Client fund receipt PDF generation
  - Office receipt PDF generation
  - General document uploads
- **Fields Required:**
  - `signer_count` (integer, NOT NULL, default: 1)

**Error Message:**
```
Error saving 'National Identity Card': SQLSTATE[23502]: Not null violation: 7 ERROR: 
null value in column "signer_count" of relation "documents" violates not-null constraint
```

OR (for receipt uploads):
```
Failed to upload document: SQLSTATE[23502]: Not null violation: 7 ERROR: null value in column "signer_count" of relation "documents" violates not-null constraint
DETAIL: Failing row contains (75647, null, null, null, null, null, null, null, null, null, null, null, null, null, agreement_PUNE2500911_1766045909, pdf, 1766278990_agreement_PUNE2500911_1766045909.pdf, 1766278990_agreement_PUNE2500911_1766045909.pdf, 1, 44034, 681748, client, receipt_uploads, null, null, 3541, null, null, null, null, null, null, null, null, null, null, null, null, null, 2025-12-21 12:03:13, 2025-12-21 12:03:13, null).
```

**Safety:** 🔴 **CRITICAL** - Document creation (checklists, uploads, PDFs) will fail completely in PostgreSQL without this field. This is a common issue after migration where MySQL's lenient NULL handling differs from PostgreSQL's strict enforcement.

**Notes:**
- MySQL may allow NULL values or have implicit defaults even when NOT NULL is specified (depending on SQL mode)
- PostgreSQL strictly enforces NOT NULL constraints and does not use implicit defaults
- The migration defines `default(1)` for `signer_count`, but PostgreSQL doesn't apply database defaults when using explicit column lists in INSERT statements (which Laravel/Eloquent uses)
- Always check database schema for NOT NULL columns when migrating
- The `signer_count` field tracks how many signers are required for signature documents. For regular documents (non-signature), use `1` as the default value
- When using `new Document` followed by `->save()`, always set `signer_count` before save
- When using `DB::table('documents')->insertGetId()` or `insert()`, always include `'signer_count' => 1` in the data array

**When to Set:**
- Always set when creating new Document records (`new Document` or `DB::table('documents')->insert()`)
- Use `1` as default for regular documents (non-signature documents)
- For signature documents, set the actual number of required signers

**Files Fixed:**
- `app/Http/Controllers/CRM/Clients/ClientDocumentsController.php` - 6 instances (personal checklist, visa checklist, bulk upload)
- `app/Http/Controllers/API/ClientPortalDocumentController.php` - 1 instance (API endpoint)
- `app/Http/Controllers/CRM/ClientAccountsController.php` - 11 instances (receipt uploads, invoice PDFs, receipt PDFs, general document uploads)

---

## Search Patterns

### When Pulling Code from MySQL, Search For:

Use these patterns to find code that needs to be converted:

```bash
# Date format function
grep -r "DATE_FORMAT" app/
grep -r "STR_TO_DATE" app/

# String aggregation
grep -r "GROUP_CONCAT" app/

# String search functions
grep -r "FIND_IN_SET" app/

# GROUP BY issues
grep -r "groupBy\|groupby\|GROUP BY" app/ | grep -v "groupByRaw"

# Invalid date comparisons
grep -r "0000-00-00" app/
grep -r "'0000-00-00'" app/
grep -r '"0000-00-00"' app/

# Date functions
grep -r "UNIX_TIMESTAMP" app/
grep -r "FROM_UNIXTIME" app/
grep -r "TIMESTAMPDIFF" app/
grep -r "DATEDIFF" app/

# Raw SQL queries that might need review
grep -r "DB::raw" app/ | grep -i "date"
grep -r "whereRaw" app/

# NOT NULL constraint violations
# Check for Model::create or new Model patterns that might miss required fields
grep -r "::create([" app/ | grep -v "task_status"
grep -r "new ActivitiesLog" app/
grep -r "new.*Log" app/ | grep -v "task_status"
grep -r "ClientEmail::create" app/
grep -r "ClientContact::create" app/
grep -r "ClientQualification::create" app/
grep -r "ClientExperience::create" app/
grep -r "ActivitiesLog::create" app/

# Check for pending migrations
php artisan migrate:status | grep Pending

# Check for ActivitiesLog missing task_status/pin
grep -r "new ActivitiesLog" app/Http/Controllers/ | grep -v "task_status"
grep -r "new.*ActivitiesLog" app/Http/Controllers/ | grep -v "task_status"

# Check for Note creation missing pin/folloup/status
grep -r "new Note" app/Http/Controllers/ | grep -v "pin"
grep -r "\$.*= new Note;" app/Http/Controllers/

# Check for Document creation missing signer_count
grep -r "new Document" app/Http/Controllers/ | grep -v "signer_count"
grep -r "\$.*= new Document" app/Http/Controllers/ | grep -v "signer_count"
grep -r "DB::table('documents')->insert" app/ | grep -v "signer_count"

# Check for direct request field access (may need null coalescing)
grep -r "\$request->[a-zA-Z_]*;" app/Http/Controllers/ | grep -v "??"
grep -r "->[a-zA-Z_]* = \$request->" app/Http/Controllers/

# Check for comparison without isset check
grep -r "!== \$request->" app/Http/Controllers/
grep -r "== \$request->" app/Http/Controllers/ | grep -v "isset"

# Check for VARCHAR date field comparisons using wrong format (whereBetween with Y-m-d)
grep -r "whereBetween.*trans_date" app/Http/Controllers/
grep -r "whereBetween.*'trans_date'" app/Http/Controllers/
grep -r "format('Y-m-d')" app/Http/Controllers/ | grep -i "date"
# Should use TO_DATE() with dd/mm/yyyy format instead

# Check for TO_DATE() usage without whereNotNull (NULL handling issue)
grep -r "TO_DATE.*trans_date" app/ | grep -v "whereNotNull"
grep -r "whereRaw.*TO_DATE.*trans_date" app/ | grep -v "whereNotNull"
# Should have ->whereNotNull('trans_date') before whereRaw() with TO_DATE()
```

---

## Quick Reference Table

| MySQL Syntax | PostgreSQL Syntax | Safety Level | Notes |
|-------------|-------------------|--------------|-------|
| `DATE_FORMAT(date, '%Y-%m')` | `TO_CHAR(date, 'YYYY-MM')` | 🔴 Critical | Must convert format codes |
| `STR_TO_DATE(str, '%d/%m/%Y')` | `TO_DATE(str, 'DD/MM/YYYY')` | 🔴 Critical | Different format syntax |
| `whereBetween('trans_date', ['Y-m-d', 'Y-m-d'])` on VARCHAR dd/mm/yyyy | `whereNotNull('trans_date')->whereRaw("TO_DATE(trans_date, 'DD/MM/YYYY') BETWEEN TO_DATE(?, 'DD/MM/YYYY') AND TO_DATE(?, 'DD/MM/YYYY')", ['d/m/Y', 'd/m/Y'])` | 🔴 Critical | VARCHAR dates stored as dd/mm/yyyy must use TO_DATE() with correct format. **CRITICAL:** Must filter NULL values first with `whereNotNull()` |
| `GROUP_CONCAT(col)` | `STRING_AGG(col, ', ')` | 🔴 Critical | Requires delimiter |
| `FIND_IN_SET(val, col)` | `val = ANY(string_to_array(col, ','))` | 🔴 Critical | PostgreSQL uses array functions |
| `SELECT * ... GROUP BY col` | `SELECT col ... GROUP BY col` or `SELECT col ... DISTINCT` | 🔴 Critical | PostgreSQL requires all selected columns in GROUP BY |
| `column != '0000-00-00'` | `column IS NOT NULL` | 🔴 Critical | PostgreSQL rejects invalid dates |
| `column = '0000-00-00'` | `column IS NULL` | 🔴 Critical | Same as above |
| `ORDER BY col DESC` | `ORDER BY col DESC NULLS LAST` | 🟡 Medium | For consistency with NULL dates |
| `IFNULL(expr, default)` | `COALESCE(expr, default)` | 🟢 Low | COALESCE works in both |
| `CONCAT(a, b)` | `a \|\| b` | 🟢 Low | Both work, `\|\|` preferred |
| `Model::create([...])` missing NOT NULL fields | Add all NOT NULL fields to array | 🔴 Critical | PostgreSQL rejects NULL in NOT NULL columns |
| `new Model; $obj->save()` missing NOT NULL | Set `$obj->not_null_field = value;` before save | 🔴 Critical | Same as above |
| `ActivitiesLog::create()` missing `task_status`/`pin` | Add `'task_status' => 0, 'pin' => 0` | 🔴 Critical | activities_logs table |
| `ClientEmail::create()` missing `is_verified` | Add `'is_verified' => false` | 🔴 Critical | client_emails table |
| `ClientContact::create()` missing `is_verified` | Add `'is_verified' => false` | 🔴 Critical | client_contacts table |
| `ClientQualification::create()` missing `specialist_education`/`stem_qualification`/`regional_study` | Add `'specialist_education' => 0, 'stem_qualification' => 0, 'regional_study' => 0` | 🔴 Critical | client_qualifications table |
| `ClientExperience::create()` missing `fte_multiplier` | Add `'fte_multiplier' => 1.00` | 🔴 Critical | client_experiences table |
| `ClientMatter` creation missing `matter_status` | Add `$matter->matter_status = 1;` before save (1 = active) | 🔴 Critical | client_matters table |
| `new ActivitiesLog` missing `task_status`/`pin` | Add `$objs->task_status = 0; $objs->pin = 0;` before save | 🔴 Critical | activities_logs table |
| `DB::table('admins')->insert()` missing `verified` | Add `'verified' => 0` (for new leads/clients) | 🔴 Critical | admins table |
| `DB::table('admins')->insert()` password empty string | Use `'password' => Hash::make('LEAD_PLACEHOLDER')` | 🔴 Critical | admins table - PostgreSQL rejects empty strings for NOT NULL |
| `DB::table('admins')->insert()` missing `show_dashboard_per` | Add `'show_dashboard_per' => 0` (for new leads/clients) | 🔴 Critical | admins table |
| `DB::table('admins')->insert()` missing EOI fields | Add `'australian_study' => 0, 'specialist_education' => 0, 'regional_study' => 0` | 🔴 Critical | admins table - Database defaults not applied with explicit column lists |
| Code references column that doesn't exist | Run pending migration: `php artisan migrate --path=database/migrations/YYYY_MM_DD_HHMMSS_name.php` | 🔴 Critical | Check `php artisan migrate:status` for pending migrations |
| Missing form field accessed directly | Use null coalescing: `$obj->field = $request->field ?? default_value;` | 🔴 Critical | Prevents undefined index warnings and NULL constraint violations |
| Update logic comparing undefined field | Check `isset($request->field)` before comparing | 🔴 Critical | Prevents undefined index warnings in change tracking |
| Database save without error handling | Wrap in try-catch and log errors | 🟡 Medium | Improves debugging and provides better error messages |
| `new Note` missing `pin`/`folloup`/`status` | Add `$obj->pin = 0; $obj->folloup = 0; $obj->status = '0';` before save | 🔴 Critical | notes table - PostgreSQL NOT NULL constraints |
| `new Document` missing `signer_count` | Add `$document->signer_count = 1;` before save | 🔴 Critical | documents table - PostgreSQL NOT NULL constraint (default: 1 for regular documents) |
| `DB::table('documents')->insertGetId()` missing `signer_count` | Add `'signer_count' => 1` to array | 🔴 Critical | documents table - Database defaults not applied with explicit INSERT column lists |

---

## Migration Checklist

When pulling new code from MySQL, check for:

- [ ] Any date comparisons with `'0000-00-00'` → Change to NULL checks
- [ ] **Empty strings for integer/date columns:** Check for `$obj->integer_field = ''` or `$obj->date_field = ''` → Change to `null` or add model mutators to auto-convert
- [ ] `DATE_FORMAT()` → Change to `TO_CHAR()` with updated format codes
- [ ] `GROUP_CONCAT()` → Change to `STRING_AGG()` with delimiter
- [ ] `FIND_IN_SET()` → Change to `ANY(string_to_array())` or appropriate alternative
- [ ] `SELECT * ... GROUP BY` → Change to `SELECT specific_columns ... GROUP BY` or use `DISTINCT`
- [ ] VARCHAR date comparisons → Use `TO_DATE()` for proper comparison
- [ ] **NULL handling for TO_DATE():** Always add `->whereNotNull('trans_date')` before `whereRaw()` with `TO_DATE()` - PostgreSQL throws error if TO_DATE() receives NULL
- [ ] **VARCHAR date field filtering:** Check for `whereBetween('trans_date', [...])` or similar with `Y-m-d` format → Must use `whereRaw("TO_DATE(trans_date, 'DD/MM/YYYY') BETWEEN TO_DATE(?, 'DD/MM/YYYY') AND TO_DATE(?, 'DD/MM/YYYY')", ['d/m/Y', 'd/m/Y'])` for VARCHAR fields stored as dd/mm/yyyy
- [ ] **Date format in filters:** When filtering VARCHAR dates (like `trans_date`), ensure dates are formatted as `d/m/Y` (not `Y-m-d`) before using in TO_DATE() queries
- [ ] `ORDER BY` with date columns → Consider adding `NULLS LAST`
- [ ] `IFNULL()` → Consider `COALESCE()` (both work, but COALESCE is standard)
- [ ] Any raw SQL queries using MySQL-specific functions
- [ ] `Model::create([...])` calls → Verify all NOT NULL columns are included
- [ ] `new Model` followed by `->save()` → Verify all NOT NULL properties are set before save
- [ ] `ActivitiesLog::create()` → Verify `task_status` and `pin` are included
- [ ] `new ActivitiesLog` followed by `->save()` → Verify `task_status` and `pin` are set before save
- [ ] `ClientEmail::create()` → Verify `is_verified` is included
- [ ] `ClientContact::create()` → Verify `is_verified` is included
- [ ] `ClientQualification::create()` → Verify `specialist_education`, `stem_qualification`, and `regional_study` are included
- [ ] `ClientExperience::create()` → Verify `fte_multiplier` is included
- [ ] `ClientMatter` creation → Verify `matter_status` is set to `1` (active) before save
- [ ] `new ActivitiesLog` followed by `->save()` → Verify `task_status` and `pin` are set (both default to `0`)
- [ ] `DB::table('admins')->insert()` or `insertGetId()` → Verify `verified` is included (use `0` for new leads/clients)
- [ ] `DB::table('admins')->insert()` or `insertGetId()` → Verify `password` is included (use `Hash::make('LEAD_PLACEHOLDER')` for leads, not empty string)
- [ ] `DB::table('admins')->insert()` or `insertGetId()` → Verify `show_dashboard_per` is included (use `0` for new leads/clients)
- [ ] `DB::table('admins')->insert()` or `insertGetId()` → Verify EOI qualification fields are included (`'australian_study' => 0, 'specialist_education' => 0, 'regional_study' => 0`)
- [ ] Check other models for NOT NULL columns with defaults that need explicit values
- [ ] **IMPORTANT:** Database defaults (`default()` in migrations) are NOT applied when using `DB::table()->insert()` with explicit column lists. Always provide explicit values for NOT NULL columns.
- [ ] Check for empty strings `''` being used for NOT NULL string columns - PostgreSQL may reject them
- [ ] **Check for pending migrations:** Run `php artisan migrate:status` after pulling new code
- [ ] If you see "Undefined column" errors, check for pending migrations that add those columns
- [ ] Verify model's `$fillable` array matches actual database schema
- [ ] Run specific pending migrations if needed: `php artisan migrate --path=database/migrations/YYYY_MM_DD_HHMMSS_name.php`
- [ ] **Check for missing form fields:** Use null coalescing operator (`??`) when accessing `$request->field` that may not exist
- [ ] **Update logic:** Check `isset($request->field)` before comparing values in change tracking
- [ ] **Error handling:** Wrap database save operations in try-catch blocks for better error handling
- [ ] **Form verification:** Check form views to see which fields are actually submitted (different forms may submit different fields)
- [ ] **ActivitiesLog creation:** Verify `task_status` and `pin` are set before `save()` when using `new ActivitiesLog`
- [ ] **ActivitiesLog pattern:** Use `task_status = 0` and `pin = 0` for regular activities, `task_status = 1` only for task completions
- [ ] **Note creation:** Verify `pin`, `folloup`, and `status` are set before `save()` when using `new Note`
- [ ] **Note pattern:** Use `pin = 0`, `folloup = 0`, `status = '0'` for new regular notes (only when `!$isUpdate`)
- [ ] **Document creation:** Verify `signer_count` is set before `save()` when using `new Document`
- [ ] **Document pattern:** Use `signer_count = 1` for regular documents (non-signature documents) when creating new Document records
- [ ] **Document API:** When using `DB::table('documents')->insertGetId()`, include `'signer_count' => 1` in the data array

---

## Additional Notes

### Date Storage Format
- Many date fields in this codebase are stored as VARCHAR in `dd/mm/yyyy` format
- Always use `TO_DATE()` when comparing these fields in WHERE clauses
- When inserting/updating, convert from `dd/mm/yyyy` (user input) to `Y-m-d` (database format) in PHP before saving

### NULL vs Empty String
- PostgreSQL treats NULL and empty string differently
- Use `IS NULL` / `IS NOT NULL` for NULL checks
- Use `= ''` / `!= ''` for empty string checks
- Use `COALESCE()` to handle NULL values in expressions

### Performance Considerations
- `TO_DATE()` in WHERE clauses can prevent index usage - consider storing dates as DATE type if possible
- `STRING_AGG()` with DISTINCT may be slower than GROUP_CONCAT on large datasets
- Consider creating functional indexes for frequently used TO_DATE() conversions

### Testing
- Always test date range queries with edge cases (NULL dates, invalid formats)
- Test string aggregation with NULL values
- Verify ORDER BY behavior with NULL values matches expected user experience

---

## Prioritized Implementation Plan (Safest to Hardest)

This section organizes changes by safety level and implementation difficulty, from easiest/safest to most complex.

### ✅ Completion Status Summary (Last Verified: January 2025)

- **🟢 Tier 1:** ✅ **COMPLETE** - All optimization and standardization changes done
- **🟡 Tier 2:** ✅ **COMPLETE** - All recommended improvements implemented
- **🟠 Tier 3:** ✅ **COMPLETE** - All critical MySQL→PostgreSQL function conversions done
- **🔴 Tier 4:** ✅ **MOSTLY COMPLETE** - Date filtering patterns verified (no TO_DATE found in current codebase, may indicate complete migration or different approach)
- **🔴🔴 Tier 5:** ⚠️ **NEARLY COMPLETE** - Most NOT NULL constraints fixed, 1 remaining issue found

**Remaining Issues:**
1. ⚠️ **DB::table('documents')->insert()** in `app/Http/Controllers/Admin/ClientsController.php` line 4042 - Missing `signer_count` field when merging documents
2. ⚠️ **3 Pending Migrations** - Run `php artisan migrate:status` to review:
   - `2025_12_27_170820_drop_bkk_tables`
   - `2025_12_27_172119_drop_unused_frontend_and_legacy_tables`
   - `2025_12_27_180929_drop_cashbacks_table`
   - `2025_12_28_092458_add_primary_keys_to_remaining_tables`

**Next Steps:** 
1. Fix missing `signer_count` in document merge functionality
2. Review and run pending migrations if safe
3. Continue monitoring for new code from MySQL that may introduce issues

### 📊 Overview Summary

| Tier | Risk Level | Effort | Status | Examples |
|------|-----------|--------|---------|----------|
| 🟢 **Tier 1** | Very Low | Low | ✅ **COMPLETE** | Pending migrations check (ongoing), CONCAT→\|\| ✅, IFNULL→COALESCE ✅ |
| 🟡 **Tier 2** | Low-Medium | Medium | ✅ **COMPLETE** | ORDER BY NULLS LAST ✅, null coalescing ✅, isset checks ✅ |
| 🟠 **Tier 3** | Medium | Medium | ✅ **COMPLETE** | '0000-00-00'→NULL ✅, GROUP_CONCAT→STRING_AGG ✅, DATE_FORMAT→TO_CHAR ✅, FIND_IN_SET→string_to_array ✅, GROUP BY strictness ✅ |
| 🔴 **Tier 4** | High | High | ⚠️ **IN PROGRESS** | VARCHAR date filtering, TO_DATE NULL handling |
| 🔴🔴 **Tier 5** | Very High | Very High | ⚠️ **IN PROGRESS** | NOT NULL constraints (ActivitiesLog, Document, Note, etc.) |

**Legend:**
- **Optional:** Performance/quality improvements, won't break if skipped
- **Recommended:** Prevents warnings and improves UX, low risk to implement
- **Required:** Will fail immediately if encountered in new code
- **Critical:** Will cause 500 errors or data corruption if not fixed

### 🎯 How to Use This Plan

**When pulling NEW code from MySQL:**
1. Run the search patterns from Tier 3, 4, and 5 (critical issues)
2. Fix any findings before deploying
3. Test thoroughly, especially date filtering and model creation

**When improving EXISTING code:**
1. ✅ Tier 1 is COMPLETE
2. ✅ Tier 2 is COMPLETE
3. ✅ Tier 3 is COMPLETE
4. ⚠️ Focus on Tier 4 and Tier 5 for critical fixes

**Emergency/Production Issues:**
- Jump directly to the relevant tier based on error message
- Focus on Tier 4-5 if seeing 500 errors or constraint violations
- Check Tier 3 if seeing function not found errors

---

### 🟢 TIER 1: SAFEST - Low Risk, High Confidence Changes ✅ **COMPLETE**

These changes are straightforward, easy to verify, and have minimal risk of breaking functionality.

**Status:** ✅ **COMPLETE** - All Tier 1 items have been implemented or are ongoing maintenance tasks.

#### 1.1. Check for Pending Migrations ✅
- **Risk Level:** Very Low
- **Effort:** 5 minutes
- **Action:** Run `php artisan migrate:status` to identify pending migrations
- **Why Safe:** Just checking status, no code changes
- **Status:** ✅ **COMPLETE** (Ongoing maintenance task - run before deploying new code)
- **Search Pattern:** 
  ```bash
  php artisan migrate:status | grep Pending
  ```

#### 1.2. String Concatenation Optimization (CONCAT to ||) ✅
- **Risk Level:** Very Low
- **Effort:** Low (can be done incrementally)
- **Change:** Replace `CONCAT(a, b)` with `a || b` in PostgreSQL
- **Why Safe:** Both work, but `||` is preferred. Low risk since CONCAT works in both.
- **Status:** ✅ **COMPLETE** - Codebase uses `||` operator (verified: no CONCAT() found in app/)
- **Search Pattern:**
  ```bash
  grep -r "CONCAT(" app/ | grep -i "DB::raw\|whereRaw"
  ```
- **Notes:** ✅ Completed - Codebase uses `COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')` pattern

#### 1.3. IFNULL to COALESCE (Standardization) ✅
- **Risk Level:** Very Low
- **Effort:** Low
- **Change:** Replace `IFNULL(expr, default)` with `COALESCE(expr, default)`
- **Why Safe:** COALESCE works in both databases, but is standard SQL
- **Status:** ✅ **COMPLETE** - Codebase uses COALESCE (verified: no IFNULL found in app/)
- **Search Pattern:**
  ```bash
  grep -r "IFNULL" app/
  ```
- **Notes:** ✅ Completed - Codebase uses COALESCE throughout

---

### 🟡 TIER 2: SAFE - Medium Risk, Well-Defined Patterns ✅ **COMPLETE**

These changes have clear patterns and are safe when following the documented examples.

**Status:** ✅ **COMPLETE** - All Tier 2 items have been implemented. Core patterns are in place.

#### 2.1. ORDER BY with NULLS LAST (Code Quality) ✅
- **Risk Level:** Low-Medium
- **Effort:** Low-Medium
- **Change:** Add `NULLS LAST` to ORDER BY clauses for date columns
- **Pattern:**
  ```php
  // Before
  ->orderBy('finish_date', 'desc')
  
  // After
  ->orderByRaw('finish_date DESC NULLS LAST')
  ```
- **Why Safe:** Only affects sort order, doesn't break functionality. Improves UX consistency.
- **Status:** ✅ **COMPLETE** - All `orderBy('created_at', 'desc')` instances have been updated to `orderByRaw('created_at DESC NULLS LAST')` in AssigneeController, ClientsController, and PartnersController.
- **Search Pattern:**
  ```bash
  grep -r "orderBy.*date.*desc" app/Http/Controllers/
  grep -r "orderBy.*finish_date" app/
  ```
- **Priority:** Medium - Recommended for user-facing lists

#### 2.2. Missing Form Field Handling (Null Coalescing) ✅
- **Risk Level:** Low-Medium
- **Effort:** Medium (requires reviewing each case)
- **Change:** Add null coalescing operator (`??`) for optional form fields
- **Pattern:**
  ```php
  // Before
  $obj->title = $request->title;
  
  // After
  $obj->title = $request->title ?? '';
  ```
- **Why Safe:** Prevents undefined index warnings and NULL constraint violations
- **Status:** ✅ **COMPLETE** - Codebase uses appropriate patterns: null coalescing (`??`) where needed (e.g., ClientNotesController), `isset()` checks in update logic, and `@$requestData['field']` pattern in legacy code. Critical cases are handled.
- **Search Pattern:**
  ```bash
  grep -r "->[a-zA-Z_]* = \$request->" app/Http/Controllers/ | grep -v "??"
  ```
- **Priority:** Medium-High - Prevents runtime errors

#### 2.3. Update Logic with isset Checks ✅
- **Risk Level:** Low-Medium
- **Effort:** Medium (requires reviewing each case)
- **Change:** Add `isset()` checks before comparing request values
- **Pattern:**
  ```php
  // Before
  if($oldValue !== $request->field) { ... }
  
  // After
  if(isset($request->field) && $oldValue !== $request->field) { ... }
  ```
- **Why Safe:** Prevents undefined index warnings in change tracking
- **Status:** ✅ **COMPLETE** - isset checks are used in update logic where needed (e.g., ClientNotesController). No instances of `!== $request->` without isset found in codebase.
- **Search Pattern:**
  ```bash
  grep -r "!== \$request->" app/Http/Controllers/ | grep -v "isset"
  ```
- **Priority:** Medium - Prevents PHP warnings in logs

---

### 🟠 TIER 3: MODERATE RISK - Critical Fixes with Clear Patterns ✅ **COMPLETE**

These are critical issues that will fail, but have well-documented patterns to follow.

**Status:** ✅ **COMPLETE** - All Tier 3 critical fixes have been implemented. No MySQL-specific functions found in codebase.

#### 3.1. Invalid Date Comparisons ('0000-00-00') ✅
- **Risk Level:** Medium (will fail if not fixed)
- **Effort:** Low-Medium
- **Change:** Replace `'0000-00-00'` comparisons with NULL checks
- **Pattern:**
  ```php
  // Before
  ->where('dob', '!=', '0000-00-00')
  
  // After
  ->whereNotNull('dob')
  ```
- **Why Safe:** Clear pattern, easy to identify and fix
- **Status:** ✅ **COMPLETE** - No '0000-00-00' comparisons found (only found in comment/documentation)
- **Search Pattern:**
  ```bash
  grep -r "0000-00-00" app/
  ```
- **Priority:** High - Will fail immediately if not fixed

#### 3.2. GROUP_CONCAT to STRING_AGG ✅
- **Risk Level:** Medium (will fail if not fixed)
- **Effort:** Low-Medium
- **Change:** Convert MySQL GROUP_CONCAT to PostgreSQL STRING_AGG
- **Pattern:**
  ```php
  // Before
  DB::raw('GROUP_CONCAT(DISTINCT phone ORDER BY phone) as all_phones')
  
  // After
  DB::raw('STRING_AGG(DISTINCT phone, \', \' ORDER BY phone) as all_phones')
  ```
- **Why Safe:** Well-defined syntax conversion
- **Status:** ✅ **COMPLETE** - Codebase uses STRING_AGG (verified in SearchService.php)
- **Search Pattern:**
  ```bash
  grep -r "GROUP_CONCAT" app/
  ```
- **Priority:** High - Will fail immediately if not fixed

#### 3.3. DATE_FORMAT to TO_CHAR ✅
- **Risk Level:** Medium (will fail if not fixed)
- **Effort:** Medium (requires format code conversion)
- **Change:** Convert MySQL DATE_FORMAT to PostgreSQL TO_CHAR with format codes
- **Pattern:**
  ```php
  // Before
  DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_key")
  
  // After
  DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month_key")
  ```
- **Why Safe:** Clear conversion table available in reference guide
- **Status:** ✅ **COMPLETE** - No DATE_FORMAT found in codebase
- **Search Pattern:**
  ```bash
  grep -r "DATE_FORMAT" app/
  ```
- **Priority:** High - Will fail immediately if not fixed

#### 3.4. FIND_IN_SET to string_to_array ✅
- **Risk Level:** Medium (will fail if not fixed)
- **Effort:** Low-Medium
- **Change:** Convert MySQL FIND_IN_SET to PostgreSQL array functions
- **Pattern:**
  ```php
  // Before
  ->whereRaw("FIND_IN_SET(?, to_mail)", [$clientId])
  
  // After
  ->whereRaw("? = ANY(string_to_array(to_mail, ','))", [$clientId])
  ```
- **Why Safe:** Well-defined conversion, multiple solution options
- **Status:** ✅ **COMPLETE** - Codebase uses string_to_array with ANY (verified in AdminController.php)
- **Search Pattern:**
  ```bash
  grep -r "FIND_IN_SET" app/
  ```
- **Priority:** High - Will fail immediately if not fixed

#### 3.5. GROUP BY Strictness ✅
- **Risk Level:** Medium (will fail if not fixed)
- **Effort:** Low-Medium
- **Change:** Fix SELECT * with GROUP BY to select only needed columns
- **Pattern:**
  ```php
  // Before
  ->groupBy('workflow')->get()
  
  // After (if only need unique values)
  ->select('workflow')->distinct()->get()
  
  // OR (if need specific columns)
  ->select('workflow', 'id')->groupBy('workflow', 'id')->get()
  ```
- **Why Safe:** Clear pattern, usually just need to add select() or use distinct()
- **Status:** ✅ **COMPLETE** - All GROUP BY statements found use ->select() before ->groupBy() (verified in PartnersController, ApplicationsController, ReportController)
- **Search Pattern:**
  ```bash
  grep -r "groupBy\|groupby" app/ | grep -v "groupByRaw"
  ```
- **Priority:** High - Will fail immediately if not fixed

---

### 🔴 TIER 4: HIGH RISK - Complex Critical Fixes

These are critical issues that require careful implementation and testing.

#### 4.1. VARCHAR Date Field Filtering (whereBetween to TO_DATE)
- **Risk Level:** High (silent failures possible)
- **Effort:** High (requires understanding date formats)
- **Change:** Convert VARCHAR date filters from whereBetween to TO_DATE with proper format
- **Pattern:**
  ```php
  // Before
  $query->whereBetween('trans_date', [
      $startDate->format('Y-m-d'),
      $endDate->format('Y-m-d')
  ]);
  
  // After
  $startDateStr = $startDate->format('d/m/Y');
  $endDateStr = $endDate->format('d/m/Y');
  $query->whereNotNull('trans_date')
      ->whereRaw("TO_DATE(trans_date, 'DD/MM/YYYY') BETWEEN TO_DATE(?, 'DD/MM/YYYY') AND TO_DATE(?, 'DD/MM/YYYY')", [$startDateStr, $endDateStr]);
  ```
- **Status:** ✅ **VERIFIED** - No `whereBetween.*trans_date` or `TO_DATE.*trans_date` patterns found in current codebase
- **Why Risky:** 
  - Requires understanding date format (dd/mm/yyyy vs Y-m-d)
  - Must filter NULL values first (TO_DATE fails on NULL)
  - Easy to make mistakes with format strings
- **Search Pattern:**
  ```bash
  grep -r "whereBetween.*trans_date" app/Http/Controllers/
  grep -r "TO_DATE.*trans_date" app/
  ```
- **Priority:** Critical - Date filters will fail or return wrong results
- **Testing Required:** Test with various date ranges, NULL dates, edge cases
- **Note:** No instances found in current codebase. May indicate:
  - Migration already complete
  - Date filtering handled differently
  - Services/utilities handle date filtering (e.g., FinancialStatsService, ClientAccountsController)

#### 4.2. TO_DATE NULL Handling (Missing whereNotNull)
- **Risk Level:** High (500 errors on NULL values)
- **Effort:** Medium-High
- **Change:** Add `->whereNotNull('column')` before `whereRaw()` with `TO_DATE()`
- **Pattern:**
  ```php
  // Before (WILL FAIL)
  ->whereRaw("TO_DATE(trans_date, 'DD/MM/YYYY') BETWEEN ...")
  
  // After
  ->whereNotNull('trans_date')
  ->whereRaw("TO_DATE(trans_date, 'DD/MM/YYYY') BETWEEN ...")
  ```
- **Why Risky:** 
  - Easy to forget the whereNotNull check
  - Causes 500 errors when NULL dates exist
  - Requires understanding which columns may be NULL
- **Search Pattern:**
  ```bash
  grep -r "TO_DATE.*trans_date" app/ | grep -v "whereNotNull"
  ```
- **Priority:** Critical - Causes 500 Internal Server Errors
- **Testing Required:** Test with datasets containing NULL dates

---

### 🔴🔴 TIER 5: HIGHEST RISK - NOT NULL Constraint Fixes

These require understanding the data model and business logic. Highest risk if done incorrectly.

#### 5.1. ActivitiesLog NOT NULL Fields (task_status, pin)
- **Risk Level:** Very High (requires understanding business logic)
- **Effort:** High (many locations, need to understand when to use 0 vs 1)
- **Change:** Add `task_status` and `pin` fields when creating ActivitiesLog
- **Pattern:**
  ```php
  // Before
  $objs = new ActivitiesLog;
  $objs->client_id = $clientId;
  $objs->save();
  
  // After
  $objs = new ActivitiesLog;
  $objs->client_id = $clientId;
  $objs->task_status = 0; // 0 = activity, 1 = task completion
  $objs->pin = 0; // 0 = not pinned, 1 = pinned
  $objs->save();
  ```
- **Status:** ✅ **COMPLETE** - All 53 instances of `new ActivitiesLog` verified to have `task_status` and `pin` set
- **Why Risky:**
  - Many locations across codebase (53 instances found)
  - Need to understand when task_status should be 0 vs 1
  - Need to understand when pin should be 0 vs 1
  - Wrong values could affect business logic
- **Search Pattern:**
  ```bash
  grep -r "new ActivitiesLog" app/Http/Controllers/ | grep -v "task_status"
  ```
- **Priority:** Critical - Will fail immediately
- **Testing Required:** 
  - Verify activities are created correctly
  - Verify task completions work correctly
  - Verify pinned activities work correctly
- **Verified Files:**
  - `app/Http/Controllers/Agent/ClientsController.php` - 11 instances ✅
  - `app/Http/Controllers/Admin/ActionController.php` - 4 instances ✅
  - `app/Http/Controllers/Admin/AdminController.php` - 3 instances ✅
  - `app/Http/Controllers/Admin/ClientsController.php` - 33 instances ✅
  - `app/Http/Controllers/Admin/PartnersController.php` - 3 instances ✅
  - `app/Http/Controllers/HomeController.php` - 1 instance ✅

#### 5.2. Document NOT NULL Field (signer_count)
- **Risk Level:** Very High (requires understanding document types)
- **Effort:** High (many locations, different document types)
- **Change:** Add `signer_count` field when creating Document records
- **Pattern:**
  ```php
  // Before
  $document = new Document;
  $document->file_name = $fileName;
  $document->save();
  
  // After
  $document = new Document;
  $document->file_name = $fileName;
  $document->signer_count = 1; // 1 for regular documents
  $document->save();
  ```
- **Why Risky:**
  - Many locations (18+ instances mentioned)
  - Different document types may have different signer counts
  - Need to understand when to use 1 vs actual signer count
- **Status:** ⚠️ **NEARLY COMPLETE** - Most instances fixed, 1 remaining issue found
- **Remaining Issue:**
  - **File:** `app/Http/Controllers/Admin/ClientsController.php`
  - **Line:** 4042
  - **Issue:** `DB::table('documents')->insert()` when merging clients - missing `signer_count` field
  - **Fix Required:** Add `'signer_count' => $docval->signer_count ?? 1` to the insert array
- **Search Pattern:**
  ```bash
  grep -r "new Document" app/Http/Controllers/ | grep -v "signer_count"
  grep -r "DB::table('documents')->insert" app/ | grep -v "signer_count"
  ```
- **Priority:** Critical - Will fail immediately
- **Testing Required:**
  - Test document uploads
  - Test document checklists
  - Test PDF generation
  - Test signature documents (if applicable)
  - **Test client merge functionality** (where remaining issue exists)

#### 5.3. Note NOT NULL Fields (pin, folloup, status)
- **Risk Level:** Very High
- **Effort:** Medium-High
- **Change:** Add `pin`, `folloup`, and `status` when creating Notes
- **Pattern:**
  ```php
  // Before
  $obj = new Note;
  $obj->title = $request->title ?? '';
  $obj->save();
  
  // After (only when creating new notes)
  $obj = new Note;
  $obj->title = $request->title ?? '';
  if(!$isUpdate) {
      $obj->pin = 0;
      $obj->folloup = 0;
      $obj->status = '0';
  }
  $obj->save();
  ```
- **Why Risky:**
  - Need to distinguish between create and update operations
  - Wrong status values could affect note filtering
- **Search Pattern:**
  ```bash
  grep -r "new Note" app/Http/Controllers/ | grep -v "pin"
  ```
- **Priority:** Critical - Will fail immediately
- **Testing Required:**
  - Test note creation
  - Test note updates
  - Verify note filtering works correctly

#### 5.4. Other Model NOT NULL Fields
- **Risk Level:** Very High (varies by model)
- **Effort:** High (requires understanding each model)
- **Models:**
  - ClientEmail: `is_verified`
  - ClientContact: `is_verified`
  - ClientQualification: `specialist_education`, `stem_qualification`, `regional_study`
  - ClientExperience: `fte_multiplier`
  - ClientMatter: `matter_status`
  - Admins (various fields): `verified`, `password`, `show_dashboard_per`, EOI fields (`australian_study`, `specialist_education`, `regional_study`)
- **Why Risky:**
  - Each model has different business rules
  - Wrong default values could affect functionality
  - Admins table is especially complex (used for staff, leads, clients)
- **Priority:** Critical - Will fail immediately
- **Testing Required:** Comprehensive testing for each model

---

### 📋 Recommended Implementation Order

1. **Start with Tier 1 (Safest):**
   - Check for pending migrations
   - String concatenation (if time permits)
   - IFNULL to COALESCE (if time permits)

2. **Move to Tier 2 (Safe):**
   - ORDER BY NULLS LAST (for improved UX)
   - Missing form field handling (preventive)

3. **Address Tier 3 (Moderate Risk):**
   - Invalid date comparisons
   - GROUP_CONCAT conversions
   - DATE_FORMAT conversions

4. **Carefully handle Tier 4 (High Risk):**
   - VARCHAR date filtering (test thoroughly)
   - TO_DATE NULL handling (test with NULL data)

5. **Last: Tier 5 (Highest Risk):**
   - NOT NULL constraint fixes (requires deep understanding)
   - Test each change thoroughly
   - Consider code review before deploying

---

### 🎯 Quick Wins (Do These First)

These provide immediate value with minimal risk. Run these searches on ANY new code before deployment:

1. ✅ **Check pending migrations** (30 seconds)
   ```bash
   php artisan migrate:status
   ```

2. ✅ **Search for '0000-00-00' comparisons** (easy to find and fix)
   ```bash
   grep -r "0000-00-00" app/ --include="*.php"
   ```
   Fix: Replace with `->whereNotNull()` or `->whereNull()`

3. ✅ **Search for GROUP_CONCAT** (easy to find and convert)
   ```bash
   grep -r "GROUP_CONCAT" app/ --include="*.php"
   ```
   Fix: Convert to `STRING_AGG(column, ', ')`

4. ✅ **Search for DATE_FORMAT** (clear conversion pattern)
   ```bash
   grep -r "DATE_FORMAT" app/ --include="*.php"
   ```
   Fix: Convert to `TO_CHAR()` with format codes (see conversion table)

5. ✅ **Search for FIND_IN_SET** (easy to find and convert)
   ```bash
   grep -r "FIND_IN_SET" app/ --include="*.php"
   ```
   Fix: Convert to `val = ANY(string_to_array(column, ','))`

6. ✅ **Search for GROUP BY issues** (check for SELECT * with GROUP BY)
   ```bash
   grep -r "groupBy\|groupby" app/ --include="*.php" | grep -v "groupByRaw"
   ```
   Fix: Add `->select('column')` or use `->distinct()` instead of `->groupBy()`

7. ✅ **Search for new ActivitiesLog without task_status**
   ```bash
   grep -r "new ActivitiesLog" app/Http/Controllers/ --include="*.php" | grep -v "task_status"
   ```
   Fix: Add `$obj->task_status = 0; $obj->pin = 0;` before save

8. ✅ **Search for new Document without signer_count**
   ```bash
   grep -r "new Document" app/Http/Controllers/ --include="*.php" | grep -v "signer_count"
   ```
   Fix: Add `$obj->signer_count = 1;` before save

9. ✅ **Search for TO_DATE without whereNotNull**
   ```bash
   grep -r "TO_DATE.*trans_date" app/ --include="*.php" | grep -v "whereNotNull"
   ```
   Fix: Add `->whereNotNull('trans_date')` before `whereRaw()` with TO_DATE

### ⚡ Pre-Deployment Checklist

Before deploying ANY new code from MySQL, run this quick audit:

```bash
# Critical Issues (Will cause immediate failures)
echo "=== Checking for Critical MySQL Syntax ==="
grep -r "GROUP_CONCAT\|DATE_FORMAT\|FIND_IN_SET\|0000-00-00" app/ --include="*.php" | wc -l

# GROUP BY issues
echo "=== Checking for GROUP BY issues ==="
grep -r "groupBy\|groupby" app/ --include="*.php" | grep -v "groupByRaw" | wc -l

# NOT NULL Constraint Issues
echo "=== Checking for potential NOT NULL violations ==="
grep -r "new ActivitiesLog" app/Http/Controllers/ --include="*.php" | grep -v "task_status" | wc -l
grep -r "new Document" app/Http/Controllers/ --include="*.php" | grep -v "signer_count" | wc -l
grep -r "new Note" app/Http/Controllers/ --include="*.php" | grep -v "pin" | wc -l

# Date Handling Issues
echo "=== Checking for TO_DATE without NULL handling ==="
grep -r "TO_DATE.*trans_date" app/ --include="*.php" | grep -v "whereNotNull" | wc -l

echo "=== Audit Complete. Any non-zero counts need attention! ==="
```

If all counts are **zero**, you're good to deploy. Otherwise, fix the findings first.

---

### 🔧 Common Error Messages & Solutions

Quick reference for troubleshooting production errors:

| Error Message | Tier | Root Cause | Immediate Fix |
|--------------|------|------------|---------------|
| `function group_concat(...) does not exist` | 3 | MySQL function not in PostgreSQL | Replace with `STRING_AGG(col, ', ')` |
| `function date_format(...) does not exist` | 3 | MySQL function not in PostgreSQL | Replace with `TO_CHAR(col, 'format')` |
| `function find_in_set(...) does not exist` OR `Undefined column` with FIND_IN_SET | 3 | MySQL function not in PostgreSQL | Replace with `val = ANY(string_to_array(col, ','))` |
| `Grouping error: column "..." must appear in GROUP BY clause` | 3 | PostgreSQL strict GROUP BY | Add `->select('column')` or use `->distinct()` instead of `->groupBy()` |
| `invalid input syntax for type date: "0000-00-00"` | 3 | Invalid date value | Replace `where('col', '!=', '0000-00-00')` with `whereNotNull('col')` |
| `null value in column "task_status" violates not-null constraint` | 5 | Missing NOT NULL field | Add `$obj->task_status = 0; $obj->pin = 0;` before save |
| `null value in column "signer_count" violates not-null constraint` | 5 | Missing NOT NULL field | Add `$obj->signer_count = 1;` before save |
| `null value in column "pin" of relation "notes" violates not-null constraint` | 5 | Missing NOT NULL field | Add `$obj->pin = 0; $obj->folloup = 0; $obj->status = '0';` before save |
| `date/time field value out of range` (with TO_DATE) | 4 | TO_DATE() received NULL | Add `->whereNotNull('trans_date')` before whereRaw |
| 500 error on date filtering | 4 | Wrong date format or NULL handling | Use TO_DATE with dd/mm/yyyy format + whereNotNull |
| `column "..." does not exist` | 8 | Pending migration | Run `php artisan migrate:status` and apply pending migrations |

**How to Use:**
1. Find your error message in the table
2. Check the Tier number for detailed explanation
3. Apply the immediate fix
4. Test thoroughly

---

**Last Updated:** January 2025 - Comprehensive codebase audit completed
**Status:** Reference guide for ongoing code pulls from MySQL source

---

## 📋 Current Status Summary (January 2025 Audit)

### ✅ Completed Items

1. **MySQL Function Conversions (Tier 3):**
   - ✅ `GROUP_CONCAT` → `STRING_AGG` - No instances found (complete)
   - ✅ `DATE_FORMAT` → `TO_CHAR` - No instances found (complete)
   - ✅ `FIND_IN_SET` → `string_to_array` - No instances found (complete)
   - ✅ `'0000-00-00'` comparisons → NULL checks - Only found in comments (complete)
   - ✅ `GROUP BY` strictness - All use `select()` before `groupBy()` (complete)

2. **Standardization (Tier 1-2):**
   - ✅ `CONCAT` → `||` operator - Using `||` throughout (complete)
   - ✅ `IFNULL` → `COALESCE` - Using `COALESCE` throughout (complete)
   - ✅ ORDER BY NULLS LAST - Implemented where needed (complete)
   - ✅ Null coalescing for form fields - Implemented (complete)

3. **NOT NULL Constraints (Tier 5):**
   - ✅ ActivitiesLog `task_status`/`pin` - All 53 instances verified (complete)
   - ✅ Document `signer_count` - Most instances fixed (12 found with signer_count)
   - ✅ Note `pin`/`folloup`/`status` - No instances found in controllers (complete or handled elsewhere)

### ⚠️ Remaining Issues

1. **Document signer_count (1 instance):**
   - **File:** `app/Http/Controllers/Admin/ClientsController.php`
   - **Line:** 4042
   - **Issue:** `DB::table('documents')->insert()` missing `signer_count` when merging clients
   - **Fix:** Add `'signer_count' => $docval->signer_count ?? 1` to insert array

2. **Pending Migrations:**
   - ✅ **ALL RESOLVED** - All migrations have been recorded in the migrations table (January 2025)
   - Previously pending migrations were verified to have been run (tables don't exist) and have been recorded:
     - `2025_12_27_170820_drop_bkk_tables` - ✅ Recorded (batch 10)
     - `2025_12_27_172119_drop_unused_frontend_and_legacy_tables` - ✅ Recorded (batch 10)
     - `2025_12_27_180929_drop_cashbacks_table` - ✅ Recorded (batch 10)
     - `2025_12_28_092458_add_primary_keys_to_remaining_tables` - ✅ Recorded (batch 10, empty placeholder)
   - **Status:** All migrations are now properly recorded. Run `php artisan migrate:status` to verify.

### 📊 Statistics

- **Total ActivitiesLog instances:** 53 (all have task_status/pin ✅)
- **Document instances with signer_count:** 12 found
- **Document instances missing signer_count:** 1 found (line 4042)
- **MySQL-specific functions found:** 0
- **Pending migrations:** 0 ✅ (All migrations recorded - January 2025)

### 🎯 Next Actions

1. Fix missing `signer_count` in client merge functionality
2. ✅ **COMPLETE:** All pending migrations have been verified and recorded (January 2025)
3. Continue monitoring new code from MySQL for these patterns
