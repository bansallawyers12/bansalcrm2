# Plan: Remove Columns from `admins` Table

This document is the implementation plan for dropping the columns listed in **ADMIN_TABLE_COLUMNS_REMOVAL_REVIEW.md**. It covers code changes and migration in order.

**Reference:** `ADMIN_TABLE_COLUMNS_REMOVAL_REVIEW.md`

---

## 1. Columns to Remove (Summary)

### Group A – Marked for deletion (code changes required first)

| Column(s) | Notes |
|-----------|--------|
| default_email_id | UserController, user views, Admin model |
| primary_email | my_profile form, email templates |
| profile_img | Migrate images first; many references |
| rating | ClientController, users/view |
| preferredintake | Admin accessor/mutator, Export/Import |
| applications | Replace with applications()->count(); ClientController, Export/Import |
| followers | ClientController, Export/Import, Admin mutator, report views |
| is_greview_mail_sent | ClientMessagingController, detail view, communications.js |
| gst_no, gstin, gst_date, is_business_gst | returnsetting form and controller |

### Group B – Safe to drop (no or negligible code usage)

| Column(s) |
|-----------|
| lead_status |
| followup_date |
| decrypt_password |
| latitude, longitude |
| wp_customer_id |
| is_greview_post |
| prev_visa |
| smtp_host, smtp_port, smtp_enc, smtp_username, smtp_password |

---

## 2. Code Changes by Column

### 2.1 default_email_id

| File | Change |
|------|--------|
| `app/Http/Controllers/Admin/UserController.php` | Remove validation rule `'default_email_id' => 'nullable\|exists:emails,id'` (store + edit). Remove `$obj->default_email_id = $request->input('default_email_id') ?: null;`. In edit, change `Admin::with(['office', 'defaultEmail'])` to `Admin::with(['office'])`. |
| `resources/views/Admin/users/create.blade.php` | Remove the "Default email (from Admin Console > Email)" dropdown block (label, `<select name="default_email_id">`, options loop). |
| `resources/views/Admin/users/edit.blade.php` | Same: remove default_email_id dropdown block. |
| `app/Models/Admin.php` | Remove `'default_email_id'` from `$fillable`. Remove `defaultEmail()` relationship method. |

---

### 2.2 primary_email

| File | Change |
|------|--------|
| `resources/views/Admin/my_profile.blade.php` | Remove the Primary Email form group (label, input `name="primary_email"`). |
| `resources/views/emails/invoice.blade.php` | Replace `$email = @$admin->primary_email;` with e.g. `$email = @$admin->email;` or another source. |
| `resources/views/emails/reciept.blade.php` | Replace `{{ $admin->primary_email }}` with `{{ $admin->email }}` or equivalent. |
| `resources/views/emails/application.blade.php` | Replace `{{ $admin->primary_email }}` with `{{ $admin->email }}` or equivalent. |

---

### 2.3 profile_img

**Prerequisite:** Migrate existing profile images to a media store (e.g. `storage/app/public/profile_imgs` or a `media` table) and update code to read from there before dropping the column.

| File | Change |
|------|--------|
| `app/Http/Controllers/Admin/Client/ClientController.php` | Remove all reads/writes of `profile_img` (create, update, lead conversion). Use new media store for uploads. |
| `app/Http/Controllers/Admin/LeadController.php` | Remove profile_img handling. |
| `app/Http/Controllers/Admin/AdminController.php` | In myProfile, remove `profile_img` upload and `$obj->profile_img`. |
| `app/Http/Controllers/Admin/AgentController.php` | Remove profile_img handling. |
| `app/Http/Controllers/Admin/PartnersController.php` | Remove profile_img handling. |
| `app/Http/Controllers/Admin/StaffController.php` | Remove profile_img handling. |
| `app/Http/Controllers/Admin/Client/ClientReceiptController.php` | Update receipt generation to use new media source. |
| `app/Http/Controllers/AdminConsole/ProfileController.php` | Remove profile_img handling. |
| `app/Services/ClientExportService.php` | Remove or replace `profile_img` in export payload. |
| `app/Services/ClientImportService.php` | Remove or replace `profile_img` import mapping. |
| `app/Models/Admin.php` | Remove `'profile_img'` from `$fillable`. |
| `app/Traits/ClientHelpers.php` | Remove profile_img handling if present. |
| `resources/views/Admin/clients/create.blade.php` | Update profile image upload to use new media. |
| `resources/views/Admin/clients/edit.blade.php` | Update profile image display/upload to use new media. |
| `resources/views/Admin/clients/detail.blade.php` | Update profile image display. |
| `resources/views/Admin/leads/create.blade.php` | Update profile image upload. |
| `resources/views/Admin/agents/create.blade.php`, `edit.blade.php` | Update profile image handling. |
| `resources/views/Admin/partners/create.blade.php`, `edit.blade.php` | Update profile image handling. |
| `resources/views/Admin/staff/create.blade.php`, `edit.blade.php` | Update profile image handling. |
| `resources/views/Admin/my_profile.blade.php` | Update admin profile image. |
| `resources/views/AdminConsole/profile/create.blade.php`, `edit.blade.php` | Update profile image handling. |
| `resources/views/Elements/Admin/header.blade.php` | Update header avatar/profile image. |
| `resources/views/layouts/adminconsole.blade.php` | Update sidebar/header profile image. |
| `resources/views/emails/invoice.blade.php`, `studentinvoice.blade.php`, `printpreview.blade.php` | Update profile image in email templates. |
| `config/constants.php` | Update profile_imgs path constant if present. |

**Files found with profile_img:** 32+ files. Run `grep -r "profile_img" --include="*.php" .` to find all references before making changes.

---

### 2.4 rating

| File | Change |
|------|--------|
| `app/Http/Controllers/Admin/Client/ClientController.php` | Remove any line that sets `$obj->rating` (search for `rating`). Remove from activity log if logged there. |
| `resources/views/Admin/users/view.blade.php` | Remove display of client rating (search for `rating`). |
| `app/Models/Admin.php` | Remove `'rating'` from `$fillable` if present. |

---

### 2.5 preferredintake

| File | Change |
|------|--------|
| `app/Models/Admin.php` | Remove `getPreferredIntakeAttribute` and `setPreferredIntakeAttribute`. Remove `'preferredintake'` from `$fillable` if present. |
| `app/Services/ClientExportService.php` | Remove `preferredintake` (or equivalent key) from client export array. |
| `app/Services/ClientImportService.php` | Remove assignment `$client->preferredintake = ...`. |
| Any blade/view that shows preferred intake | Remove or replace with static text / another source. |

---

### 2.6 applications (column)

| File | Change |
|------|--------|
| `app/Http/Controllers/Admin/Client/ClientController.php` | Remove `$obj->applications = @$requestData['applications'];`. Anywhere the column is read, use `$client->applications()->count()` (or relationship) instead. |
| `app/Services/ClientExportService.php` | If exporting "applications" count, use `$client->applications()->count()` instead of `$client->applications`. |
| `app/Services/ClientImportService.php` | Remove import of `applications` column. |
| Raw SQL / reports | Search for `admins.applications`; replace with application count via relationship. |

---

### 2.7 followers

| File | Change |
|------|--------|
| `app/Http/Controllers/Admin/Client/ClientController.php` | Remove the block that builds `$followers` from `$requestData['followers']` and sets `$obj->followers`. |
| `app/Models/Admin.php` | Remove `setFollowersAttribute` mutator. Remove `'followers'` from `$fillable` if present. |
| `app/Services/ClientExportService.php` | Remove `'followers'` from export array. |
| `app/Services/ClientImportService.php` | Remove `$client->followers = ...`. |
| `app/Traits/ClientHelpers.php` | Remove `processFollowers()` (or leave unused; optional). |
| `resources/views/Admin/reports/application.blade.php` | Remove column "Client Followers" and `{{ @$clientdetail->followers }}`. |
| `resources/views/Admin/reports/saleforecast-application.blade.php` | Remove cell `{{ @$clientdetail->followers }}`. |
| `resources/views/Admin/reports/client.blade.php` | Remove "Followers" column and `{{ @$list->followers }}`. |

---

### 2.8 is_greview_mail_sent

| File | Change |
|------|--------|
| `app/Http/Controllers/Admin/Client/ClientMessagingController.php` | Remove method `isgreviewmailsent()` (line ~224) or refactor to not read/update `is_greview_mail_sent`. Remove any DB update of this column. |
| `routes/clients.php` | Remove route: `Route::post('/is_greview_mail_sent', ...)` (line ~128). |
| `resources/views/Admin/clients/detail.blade.php` | Remove Google Review button logic that uses `data-is_greview_mail_sent` and any reference to `is_greview_mail_sent`. Search for "greview" in the file. |
| `public/js/.../communications.js` (or related JS files) | Remove references to `is_greview_mail_sent`. Search for "greview" in JS files. |
| `app/Models/Admin.php` | Remove from `$fillable` if present. |

---

### 2.9 gst_no, gstin, gst_date, is_business_gst

| File | Change |
|------|--------|
| `app/Http/Controllers/Admin/AdminController.php` | In `returnsetting()` (or equivalent), remove reads/writes of `gst_no`, `gstin`, `gst_date`, `is_business_gst`. |
| `resources/views/Admin/settings/returnsetting.blade.php` | Remove form fields for is_business_gst, gstin, gst_date. Remove any gst_no field if present. |
| `resources/views/Admin/my_profile.blade.php` | Remove or leave commented any gst_no field. |
| `app/Models/Admin.php` | Remove these from `$fillable` if present. |

---

### 2.10 Group B columns (no/minimal code usage)

| Column(s) | Files to touch |
|-----------|----------------|
| decrypt_password | `app/Models/Admin.php` – remove from `$fillable`. `app/Services/ClientImportService.php` – remove `$client->decrypt_password = null`. Search codebase for `decrypt_password` and remove. |
| lead_status | Search for `lead_status`; remove any reference. |
| followup_date (admins) | Confirm no code references `admins.followup_date`; remove if any. |
| latitude, longitude | Remove from `$fillable` if present; no other usage expected. |
| wp_customer_id | Search and remove references. |
| is_greview_post | Search and remove references. |
| prev_visa | Search and remove references. |
| smtp_host, smtp_port, smtp_enc, smtp_username, smtp_password | Remove from Admin model and any admin profile/settings that write these. Sending uses `emails` table. |

---

## 3. Admin Model `$fillable` (final cleanup)

After all code changes, ensure `app/Models/Admin.php` `$fillable` does **not** contain:

- default_email_id  
- profile_img  
- rating  
- preferredintake (if it was there)  
- followers (if it was there)  
- is_greview_mail_sent (if present)  
- gst_no, gstin, gst_date, is_business_gst (if present)  
- decrypt_password  
- latitude, longitude (if present)  
- lead_status, followup_date, wp_customer_id, is_greview_post, prev_visa  
- smtp_host, smtp_port, smtp_enc, smtp_username, smtp_password  

(telephone was already removed from fillable per review.)

---

## 4. Migration File

Create **one** migration to drop all columns in a single transaction. Only drop columns that **exist** on the table (use `Schema::hasColumn('admins', $col)`).

**Suggested filename:** `database/migrations/YYYY_MM_DD_HHMMSS_drop_marked_columns_from_admins_table.php`

### 4.1 Columns to drop (in one migration)

```php
$columnsToDrop = [
    'default_email_id',
    'primary_email',
    'profile_img',
    'rating',
    'preferredintake',
    'applications',
    'followers',
    'is_greview_mail_sent',
    'gst_no',
    'gstin',
    'gst_date',
    'is_business_gst',
    'lead_status',
    'followup_date',
    'decrypt_password',
    'latitude',
    'longitude',
    'wp_customer_id',
    'is_greview_post',
    'prev_visa',
    'smtp_host',
    'smtp_port',
    'smtp_enc',
    'smtp_username',
    'smtp_password',
];
```

### 4.2 Migration structure (outline)

1. **up()**
   - Drop foreign key for `default_email_id` if it exists (e.g. `$table->dropForeign(['default_email_id']);`) before dropping the column.
   - For each column in `$columnsToDrop`, if `Schema::hasColumn('admins', $col)` then `$table->dropColumn($col)`.
   - Dropping multiple columns in one `Schema::table()` is allowed: `$table->dropColumn(['col1', 'col2']);`.

2. **down()**
   - Re-add columns with appropriate types (nullable where needed) if you need rollback. Optional; many teams do not implement down() for drops.

### 4.3 Example migration (skeleton)

```php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropMarkedColumnsFromAdminsTable extends Migration
{
    protected $columnsToDrop = [
        'default_email_id', 'primary_email', 'profile_img', 'rating',
        'preferredintake', 'applications', 'followers', 'is_greview_mail_sent',
        'gst_no', 'gstin', 'gst_date', 'is_business_gst',
        'lead_status', 'followup_date', 'decrypt_password',
        'latitude', 'longitude', 'wp_customer_id', 'is_greview_post', 'prev_visa',
        'smtp_host', 'smtp_port', 'smtp_enc', 'smtp_username', 'smtp_password',
    ];

    public function up()
    {
        if (Schema::hasColumn('admins', 'default_email_id')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropForeign(['default_email_id']);
            });
        }
        $existing = array_filter($this->columnsToDrop, fn($c) => Schema::hasColumn('admins', $c));
        if (!empty($existing)) {
            Schema::table('admins', function (Blueprint $table) use ($existing) {
                $table->dropColumn($existing);
            });
        }
    }

    public function down()
    {
        // Optional: re-add columns with same types as before for rollback.
    }
}
```

Build the list of existing columns **before** calling `Schema::table()`, then pass that list to `dropColumn()` so only existing columns are dropped.

---

## 5. Additional Checks and Considerations

Before proceeding with code changes, verify the following:

### 5.1 Database Schema Check
- Run `php artisan migrate:status` to see current migration state.
- Check actual DB schema for column names (some may be camelCase vs snake_case).
- Check for **indexes** or **unique constraints** on these columns – drop them first if present.
- Verify column types if implementing down() migration for rollback.

### 5.2 Raw SQL Queries
- Search codebase for raw SQL queries using `DB::raw()`, `DB::select()`, `whereRaw()` that reference these columns.
- Check for any stored procedures (unlikely in Laravel, but verify).

### 5.3 API and JSON Responses
- Check API controllers (if any) that might return these columns in JSON responses.
- Review API documentation/tests that reference these fields.
- Update API versioning if columns are part of public API.

### 5.4 Observers, Events, Listeners
- Check `app/Observers` for any AdminObserver that might reference these columns.
- Search for `Event::listen` or model events (creating, updating, etc.) that access these columns.

### 5.5 Tests
- Search test files for references to these columns:
  - `tests/Unit/Traits/ClientHelpersTest.php` – has `processFollowers()` test (can be removed or updated).
  - Run `grep -r "profile_img\|rating\|preferredintake\|followers\|is_greview_mail_sent" tests/` to find all test references.
- Update or remove failing tests after code changes.

### 5.6 Third-party Integrations
- Check if any external systems or integrations (e.g. APIs, webhooks) read/expect these columns.
- Update integration documentation.

### 5.7 Scheduled Commands
- Check `app/Console/Commands` for any commands that query or update these columns.
- Example: `BirthDate.php` uses `profile_img` – update it.

---

## 6. Order of Operations

1. **Confirm column list** against actual `admins` schema.
2. **Run additional checks** (section 5) – verify indexes, raw SQL, tests, etc.
3. **Code changes** – Complete all file changes in sections 2.1–2.10 and section 3.
4. **Update tests** – Fix or remove tests that reference dropped columns.
5. **Run tests** – `php artisan test` or `phpunit` to ensure no failures.
6. **Deploy** code to staging (so the app no longer reads/writes these columns).
7. **Test staging** – Verify all functionality works without these columns.
8. **Run migration** – Execute the drop migration on staging, then production.
9. **Verify** – No references to dropped columns remain; app and tests pass in all environments.
10. **Monitor** – Check logs for any errors referencing dropped columns for 24-48 hours.

---

## 7. Testing Checklist

After code changes and before deployment, test the following:

### 7.1 User Management
- [ ] Create new user – form works without default_email_id dropdown.
- [ ] Edit existing user – form works without default_email_id dropdown.
- [ ] View user profile – displays correctly.

### 7.2 Profile Management
- [ ] Edit admin profile (my_profile) – form works without primary_email, gst_no fields.
- [ ] Profile displays correctly without removed fields.

### 7.3 Client Management
- [ ] Create new client – works without rating, applications, followers, profile_img (or with new media).
- [ ] Edit existing client – form works without removed fields.
- [ ] View client detail – displays correctly; Google Review button removed or updated.
- [ ] Client export/import – works without removed columns.

### 7.4 Reports
- [ ] Application report – displays without followers column.
- [ ] Sale forecast report – displays without followers column.
- [ ] Client report – displays without followers column.

### 7.5 Email Templates
- [ ] Invoice email – uses correct email field (not primary_email).
- [ ] Receipt email – uses correct email field.
- [ ] Application email – uses correct email field.

### 7.6 GST/Returns
- [ ] Return settings page – works without gst_no, gstin, gst_date, is_business_gst.

### 7.7 API and Integrations
- [ ] API responses – do not include removed columns (or handle gracefully).
- [ ] External integrations – continue to work.

---

## 8. Optional: Phased Approach

If you prefer to remove in phases:

- **Phase 1:** Group B only (safe columns) + migration to drop them.
- **Phase 2:** default_email_id, primary_email, rating, preferredintake, applications, followers, is_greview_mail_sent (code + migration).
- **Phase 3:** profile_img (after media migration) + gst_* (after returnsetting changes).
- **Phase 4:** Any remaining columns.

Use the same code-change and migration steps above, limited to the columns in each phase.

---

## 9. Rollback Plan

If issues arise after dropping columns:

1. **Immediate rollback** – If migration has `down()` implemented, run: `php artisan migrate:rollback --step=1`.
2. **Re-add columns manually** – If down() not implemented, create a new migration to re-add columns with same types.
3. **Restore code** – Deploy previous code version that references the columns.
4. **Data loss** – Dropped column data cannot be recovered without a separate backup (if any exists).

**Prevention:** Test thoroughly in staging before production deployment.

---

## 10. Documentation Updates

After successful removal:

- [ ] Update **ADMIN_TABLE_COLUMNS_REMOVAL_REVIEW.md** – mark as "Completed" with removal date.
- [ ] Update **database/schema.md** or schema documentation if exists.
- [ ] Update **API documentation** if these columns were exposed.
- [ ] Update **onboarding/training docs** if they reference removed features (e.g. Google Review mail tracking).
- [ ] Create **CHANGELOG.md** entry for this change.
