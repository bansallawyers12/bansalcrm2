# Leads Table Columns – Usage Review

**Purpose:** Review each column in the `leads` table for usage and recommend whether any can be removed. No changes have been applied; this is for your approval before any migration.

**Context:** The `leads` table stores potential clients before conversion. When a lead is converted, data is copied into the `admins` table (client record with `role = 7` and `lead_id` set). Leads are created via `LeadController@store`, viewed/edited via the client detail flow (`ClientController@leaddetail`), and matched to existing clients in `SearchService`.

---

## Column existence (database query)

**Source:** `Schema::getColumnListing('leads')`. **Total columns in DB:** 55.

### Columns in this review but NOT in database

| Column | Status |
|--------|--------|
| **name** | Does not exist – remove from Lead model fillable/sortable only. |
| **agent_id** | Does not exist – remove `agentdetail()` from Lead model only. |
| **priority** | Does not exist – remove filter code from LeadController@index. |
| **latest_comment** | Does not exist – no column to drop. |
| **start_process** | Already removed (migration applied). |

### Columns in database (55 total)

`id`, `user_id`, `first_name`, `last_name`, `gender`, `dob`, `marital_status`, `passport_no`, `visa_type`, `visa_expiry_date`, `tags_label`, `contact_type`, `country_code`, `phone`, `email_type`, `email`, `social_type`, `social_link`, `service`, `assign_to`, `status`, `lead_quality`, `lead_source`, `advertisements_name`, `comments_note`, `converted`, `converted_date`, `related_files`, `created_at`, `updated_at`, `att_phone`, `att_email`, `att_country_code`, `profile_img`, `age`, `preferredintake`, `country_passport`, `address`, `city`, `state`, `zip`, `country`, `nomi_occupation`, `skill_assessment`, `high_quali_aus`, `high_quali_overseas`, `relevant_work_exp_aus`, `relevant_work_exp_over`, `naati_py`, `married_partner`, `total_points`, `lead_id`, `is_verified`, `verified_at`, `verified_by`

---

## Columns with zero data (database query)

**Source:** Query run on `leads` table via `php artisan leads:column-stats`. **Total rows:** 9,980. **Total columns:** 55.

These columns have **0 non-NULL values** across all rows.

| Column | Filled | Null | Status |
|--------|--------|------|--------|
| **profile_img** | 0 | 9,980 | **Mark for deletion** – Zero data; commented in code already. |
| **preferredintake** | 0 | 9,980 | **Mark for deletion** – Zero data; remove from model accessor. |
| **lead_id** | 0 | 9,980 | **Mark for deletion** – Redundant; relationship is `admins.lead_id` → `leads.id`. |
| **verified_at** | 0 | 9,980 | **KEEP** – Active phone verification feature; set by PhoneVerificationService. |
| **verified_by** | 0 | 9,980 | **KEEP** – Active phone verification feature; set by PhoneVerificationService. |

**Conclusion:** Drop **profile_img**, **preferredintake**, **lead_id** (3 columns). **Keep verified_at and verified_by** – they're part of the active phone verification feature (routes exist, service sets them on verification).

---

## Do not remove (critical / actively used)

### 1. **id**
- **Usage:** Primary key; used everywhere (routes, detail links, filters, `Admin.lead_id`, Followup.lead_id, PhoneVerification.lead_id).
- **Recommendation:** **Do not remove.**

### 2. **user_id**
- **Usage:** `LeadController@index`: base query `whereNotNull('user_id')`; `assign()` checks `where('user_id', Auth::user()->id)`. Likely “created by” staff.
- **Recommendation:** **Do not remove.** Required for lead listing and assignment checks.

### 3. **first_name**, **last_name**
- **Usage:** `LeadController@store` (required); lead list view; filter by name (COALESCE first_name, '') || ' ' || COALESCE(last_name, ''); `ClientController` copies to admin on first lead detail load; `convertoClient` (commented block) copies to admin.
- **Recommendation:** **Do not remove.** Core identity fields.

### 4. **email**
- **Usage:** `LeadController`: store, unique check, filter; `Lead::where('email', $email)` in `is_email_unique`; `SearchService` matches leads to clients by email/att_email; lead list view; copied to admin on lead detail.
- **Recommendation:** **Do not remove.**

### 5. **phone**
- **Usage:** `LeadController`: store, unique check, filter; `is_contactno_unique`; lead list view; copied to admin; `PhoneVerificationService` (send OTP, Australian number check).
- **Recommendation:** **Do not remove.**

### 6. **country_code**
- **Usage:** `LeadController@store` (via PhoneHelper::normalizeCountryCode); `Lead` model mutator/accessor and `formatted_phone`; copied to admin; `PhoneVerificationService` (country code for SMS).
- **Recommendation:** **Do not remove.**

### 7. **assign_to**
- **Usage:** `LeadController`: index counts (not_contacted, followup, etc.), filter, assign action; `Lead::staffuser()` relationship; `ClientController` uses for office_id and staffuser relation; `users/view.blade.php` (leads assigned to user, converted counts).
- **Recommendation:** **Do not remove.** Core for assignment and reporting.

### 8. **status**
- **Usage:** `LeadController`: index counts by status (0, 1, 11–15), filter; store; lead list (via followup type); `Lead` model fillable/sortable.
- **Recommendation:** **Do not remove.** Core for lead pipeline.

### 9. **converted**
- **Usage:** `LeadController`: index base query `where('converted', 0)`; store sets 0; `convertoClient` sets 1; `SearchService` only considers `where('converted', 0)`; `users/view` converted-leads count.
- **Recommendation:** **Do not remove.**

### 10. **converted_date**
- **Usage:** `LeadController@convertoClient`: `$o->converted_date = date('Y-m-d')`; `users/view.blade.php`: “Today Converted” uses `whereDate('converted_date', date('Y-m-d'))`.
- **Recommendation:** **Do not remove.** Needed for conversion reporting.

### 11. **service**
- **Usage:** `LeadController@store` (required); lead list view; copied in `convertoClient` (commented) to admin; create form.
- **Recommendation:** **Do not remove.**

### 12. **lead_source**
- **Usage:** `LeadController@store` (source); `convertoClient` copies to admin as `source`; create form.
- **Recommendation:** **Do not remove.**

### 13. **lead_quality**
- **Usage:** `LeadController@store` (required); lead list view (display); create form.
- **Recommendation:** **Do not remove.**

### 14. **att_email**, **att_phone**, **att_country_code**
- **Usage:** `LeadController@store`; `ClientController` copies to admin; `Lead` model has `formatted_att_phone` and att_country_code mutator/accessor; `SearchService` matches leads to clients via `leads.att_email` with admins email/att_email.
- **Recommendation:** **Do not remove.** Standard attendant contact and duplicate detection.

### 15. **created_at**, **updated_at**
- **Usage:** Laravel timestamps; lead list display and filter (from/to date); `ClientController` copies to admin; sortable in model.
- **Recommendation:** **Do not remove.**

### 16. **is_verified**, **verified_at**, **verified_by**
- **Usage:** `Lead` model casts and `needsVerification()`; `PhoneVerificationController::getStatusForLead`; `PhoneVerificationService` sets all three on verify (line 181: `Lead::where('id', $leadId)->update(['is_verified' => true, 'verified_at' => now(), 'verified_by' => Auth::id()]);`). Active routes for lead phone verification exist.
- **Recommendation:** **Keep all three.** Active feature with routes and service; zero data means no leads verified yet, not unused columns.

---

## Used in forms and lead → client copy (keep unless you drop features)

### 17. **gender**
- **Usage:** `LeadController@store` (required); copied to admin on lead detail.
- **Recommendation:** **Keep.** Required on create and for client copy.

### 18. **dob**
- **Usage:** `LeadController@store`; copied to admin; age calculated from DOB in `ClientController`.
- **Recommendation:** **Keep.**

### 19. **age**
- **Usage:** `LeadController@store` (parsed from request); not copied to admin (age derived from dob there).
- **Recommendation:** **Keep** if you display/filter by age on leads; otherwise could be derived from `dob` and column removed later.

### 20. **marital_status**
- **Usage:** `LeadController@store`; copied to admin; migration renamed from martial_status and normalized values.
- **Recommendation:** **Keep.**

### 21. **passport_no**
- **Usage:** `LeadController@store`; in `ClientController` lead→admin copy the line is commented (`//$obj->passport_no = $lead->passport_no`); in `convertoClient` (commented block) copied as `passport_number`.
- **Recommendation:** **Keep** for now (form and data). Uncomment and use in client copy if you need passport on client.

### 22. **visa_type**, **visa_expiry_date**
- **Usage:** `LeadController@store`; `ClientController` copies visa_type (visa_expiry_date copy commented); create form.
- **Recommendation:** **Keep.** Uncomment visa_expiry_date copy in ClientController if needed on client.

### 23. **contact_type**, **email_type**
- **Usage:** `LeadController@store` (required); `convertoClient` (commented) copies to admin.
- **Recommendation:** **Keep.** Required on create.

### 24. **tags_label**
- **Usage:** `LeadController@store` (array to comma-separated); create form (tagname).
- **Recommendation:** **Keep** if tags are used; otherwise consider removing form and column together.

### 25. **comments_note**
- **Usage:** `LeadController@store`; `convertoClient` (commented) copies to admin.
- **Recommendation:** **Keep.**

### 26. **profile_img**
- **Usage:** `LeadController@store` (upload); `ClientController` copies to admin.
- **Recommendation:** **Mark for deletion** – Zero data. Remove from store/client copy; drop column.

### 27. **preferredintake** (model: **preferredIntake**)
- **Usage:** `LeadController@store`; `Lead` model accessor/mutator; `convertoClient` (commented) copies to admin.
- **Recommendation:** **Mark for deletion** – Zero data. Remove from store/model; drop column.

### 28. **country_passport**
- **Usage:** `LeadController@store`; migration converts sortname → name; `convertoClient` (commented) copies; create form.
- **Recommendation:** **Keep.**

### 29. **address**, **city**, **state**, **zip**, **country**
- **Usage:** `LeadController@store`; `ClientController` copies all to admin; create form.
- **Recommendation:** **Keep.**

### 30. **nomi_occupation**
- **Usage:** `LeadController@store`; `ClientController` copies to admin; migration/import context.
- **Recommendation:** **Keep.**

### 31. **skill_assessment**, **high_quali_aus**, **high_quali_overseas**, **relevant_work_exp_aus**, **relevant_work_exp_over**
- **Usage:** `LeadController@store`; `convertoClient` (commented) copies to admin; leads create form.
- **Recommendation:** **Keep** if migration/assessment data is required; otherwise can be removed with form and client copy.

### 32. **naati_py**, **married_partner**, **total_points**
- **Usage:** `LeadController@store` (naati_py as comma-separated); `convertoClient` (commented) copies to admin; create form.
- **Recommendation:** **Keep** if used for points/eligibility; otherwise review.

### 33. **related_files**
- **Usage:** `LeadController@store` (comma-separated list); `convertoClient` (commented) copies to admin; create form.
- **Recommendation:** **Keep** if “related files” (e.g. related clients) is used; otherwise consider removal with form.

### 34. **priority**
- **Exists in DB:** No.
- **Usage:** `LeadController@index`: filter `$request->has('priority')` and `$query->where('priority', @$priority)`. Column does not exist – filter is dead code.
- **Recommendation:** **Remove** filter code from LeadController; no column to drop.

---

## Review or safe to remove

### 35. **name**
- **Exists in DB:** No.
- **Usage:** In `Lead` model `$fillable` and `$sortable`. No code writes to `lead->name`; lead create uses first_name/last_name. Index name filter uses `COALESCE(first_name,'') || ' ' || COALESCE(last_name,'')`, not a `name` column.
- **Recommendation:** **Remove** from Lead model `$fillable` and `$sortable`; add computed accessor if `$lead->name` is needed.

### 36. **agent_id**
- **Exists in DB:** No.
- **Usage:** `Lead` model has `agentdetail()` relationship to User. No found writes in `LeadController@store` or elsewhere. Client create/edit use `subagent` (agent_id) on **admins**, not on leads.
- **Recommendation:** **Remove** `agentdetail()` from Lead model; no column to drop.

### 37. **social_type**, **social_link**
- **Exists in DB:** Yes.
- **Usage:** Commented out in `LeadController@store` (`//$obj->social_type`, `//$obj->social_link`). No other references found.
- **Recommendation:** **Safe to remove.** Drop columns and delete commented lines.

### 38. **advertisements_name**
- **Exists in DB:** Yes.
- **Usage:** Commented in `LeadController@store` (`//$obj->advertisements_name`). No other references found.
- **Recommendation:** **Safe to remove.** Drop column and remove commented line.

### 39. **latest_comment**
- **Exists in DB:** No.
- **Usage:** Only in a **commented-out** block in `resources/views/Admin/leads/index.blade.php` (`{{-- ... @$list->latest_comment ... --}}`). No reads/writes elsewhere.
- **Recommendation:** No column to drop. Remove from view comment if desired.

### 40. **start_process**
- **Exists in DB:** No (dropped in migration `2026_02_07_140000_drop_start_process_column.php`).
- **Recommendation:** **Already removed.** No action needed.

---

## Summary

| Category | Count | Action |
|----------|--------|--------|
| Do not remove | 16 | Keep; core for leads and client copy. |
| Keep (forms / copy) | 18 | Keep unless you drop the feature. |
| Review / optional | 1 | **priority** – keep if filter used. |
| Candidate for removal | 2 | **name**, **agent_id** (verify no usage). |
| Safe to remove | 4 | **social_type**, **social_link**, **advertisements_name**, **latest_comment**. |
| Already removed | 1 | **start_process**. |
| **Zero data – drop (3)** | **3** | **profile_img**, **preferredintake**, **lead_id** – drop via migration. |
| **Zero data – keep (2)** | **2** | **verified_at**, **verified_by** – part of active phone verification feature. |
| **Not in DB (code cleanup)** | **5** | **name**, **agent_id**, **priority**, **latest_comment**, **start_process** – see Column existence section. |

---

## Action plan

### Part 1: Code cleanup (non-existent columns)

**These columns are referenced in code but do NOT exist in the database. Remove code references only.**

#### 1. Remove from `app\Models\Lead.php`

**File:** `app\Models\Lead.php`

**Action:** Remove the following from `$fillable` array:
- `'name'`

**Action:** Remove the following from `$sortable` array:
- `'name'`

**Action:** Remove the `agentdetail()` relationship method:

```php
public function agentdetail()
{
    return $this->belongsTo('App\Models\User','agent_id','id');
}
```

**Optional:** Add a computed accessor for `name` if needed elsewhere:

```php
public function getNameAttribute()
{
    return trim($this->first_name . ' ' . $this->last_name);
}
```

#### 2. Remove priority filter from `app\Http\Controllers\Admin\LeadController.php`

**File:** `app\Http\Controllers\Admin\LeadController.php`

**Action:** Remove the priority filter block (around lines 145–152):

```php
if ($request->has('priority')) 
{
    $priority = $request->input('priority'); 
    if(trim($priority) != '')
    {
        $query->where('priority', '=', @$priority);
    }
}
```

**Action:** Update the condition on line 153 to remove `|| $request->has('priority')`:

Before:
```php
if ($request->has('type') || $request->has('lead_id') || $request->has('email')|| $request->has('name') || $request->has('phone') || $request->has('status')|| $request->has('followupdate') || $request->has('priority'))
```

After:
```php
if ($request->has('type') || $request->has('lead_id') || $request->has('email')|| $request->has('name') || $request->has('phone') || $request->has('status')|| $request->has('followupdate'))
```

#### 3. Clean up commented code in views (optional)

**File:** `resources\views\Admin\leads\index.blade.php`

**Action:** Remove or clean up any commented-out `latest_comment` references if desired (no functional impact).

---

### Part 2: Migration – drop columns from database

**These columns exist in the database but are unused or have zero data. Create a migration to drop them.**

#### Columns to drop via migration (7 total)

**Zero-data columns to drop (3):**

| Column | Reason | Zero data? |
|--------|--------|------------|
| **profile_img** | Zero data; already commented out in code | Yes (0/9,980) |
| **preferredintake** | Zero data; model accessor to remove | Yes (0/9,980) |
| **lead_id** | Not used; redundant with `admins.lead_id` → `leads.id` | Yes (0/9,980) |

**Other unused columns (4):**

| Column | Reason | Zero data? |
|--------|--------|------------|
| **social_type** | Commented out in code; never written | No (12 rows) |
| **social_link** | Commented out in code; never written | No (14 rows) |
| **advertisements_name** | Commented in code; never written | No (2,482 rows) |

#### Migration template

**File:** Create `database\migrations\YYYY_MM_DD_HHMMSS_drop_unused_columns_from_leads_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUnusedColumnsFromLeadsTable extends Migration
{
    public function up()
    {
        $columnsToDrop = [
            'profile_img',        // Zero data; already commented out in code
            'preferredintake',    // Zero data; accessor to remove
            'lead_id',            // Zero data; redundant column
            'social_type',        // Commented out; never written
            'social_link',        // Commented out; never written
            'advertisements_name', // Commented out; never written
        ];

        Schema::table('leads', function (Blueprint $table) use ($columnsToDrop) {
            foreach ($columnsToDrop as $col) {
                if (Schema::hasColumn('leads', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('profile_img')->nullable();
            $table->date('preferredintake')->nullable();
            $table->bigInteger('lead_id')->nullable();
            $table->string('social_type')->nullable();
            $table->text('social_link')->nullable();
            $table->string('advertisements_name')->nullable();
        });
    }
}
```

#### Code cleanup for dropped columns

**File:** `app\Models\Lead.php`
- Remove `getPreferredIntakeAttribute` and `setPreferredIntakeAttribute` accessor/mutator methods (lines 51-68; preferredintake column dropped).
- **KEEP** `verified_at` in `$casts` (active feature).
- **KEEP** `verified_at`, `verified_by` in `$fillable` if present (active feature).

**File:** `app\Http\Controllers\Admin\LeadController.php`
- Profile_img already commented out (line 310: `// profile_img column removed from admins table`).
- PreferredIntake already commented out (line 409: `// preferredIntake column removed`).
- Remove commented-out lines: `//$obj->social_type`, `//$obj->social_link`, `//$obj->advertisements_name` (if present in store method).

**File:** `app\Http\Controllers\Admin\Client\ClientController.php`
- Profile_img already commented out (line 253: `// profile_img column removed from admins table`).
- No other cleanup needed for preferredintake or lead_id.

**Important:** Do NOT touch `verified_at` or `verified_by` in PhoneVerificationService – active feature setting these on line 181.

---

### Part 3: Optional – consolidate age column

**Column:** `age` (exists in DB, 66 filled / 9,914 null)

**Current behavior:** Age is saved when creating a lead but could be derived from `dob`.

**Option 1 (keep column):** Leave as-is for performance (no computation needed).

**Option 2 (remove column):** Drop `age` column and compute from `dob` using an accessor:

```php
// In Lead model
public function getAgeAttribute()
{
    if (!$this->dob) return null;
    return \Carbon\Carbon::parse($this->dob)->age;
}
```

Then remove from `LeadController@store` and drop column via migration.

---

## Summary of actions

**Code cleanup (no migration):**
1. Remove `name` from Lead model `$fillable` and `$sortable`.
2. Remove `agentdetail()` from Lead model.
3. Remove priority filter from LeadController@index.
4. Optionally add computed accessor for `name`.

**Migration (drop 7 columns):**
1. Drop zero-data: `profile_img`, `preferredintake`, `lead_id` (3).
2. Drop unused: `social_type`, `social_link`, `advertisements_name` (4).
3. **KEEP** `verified_at`, `verified_by` (active phone verification feature).
4. Clean up: Remove preferredIntake accessor/mutator from Lead model; other code already commented.

**Optional:**
- Consolidate `age` as computed from `dob` and drop column.
