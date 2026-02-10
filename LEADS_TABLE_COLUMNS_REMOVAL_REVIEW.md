# Leads Table Columns – Usage Review

**Purpose:** Review each column in the `leads` table for usage and recommend whether any can be removed. No changes have been applied; this is for your approval before any migration.

**Context:** The `leads` table stores potential clients before conversion. When a lead is converted, data is copied into the `admins` table (client record with `role = 7` and `lead_id` set). Leads are created via `LeadController@store`, viewed/edited via the client detail flow (`ClientController@leaddetail`), and matched to existing clients in `SearchService`.

---

## Columns with zero data (database query)

**Source:** Query run on `leads` table via `php artisan leads:column-stats`. **Total rows:** 9,980. **Total columns:** 55.

These columns have **0 non-NULL values** across all rows:

| Column | Filled | Null | Notes |
|--------|--------|------|-------|
| **profile_img** | 0 | 9,980 | Keep – Form and lead→client conversion use it. |
| **preferredintake** | 0 | 9,980 | Keep – In create form and conversion. |
| **lead_id** | 0 | 9,980 | **Remove** – Redundant; relationship is `admins.lead_id` → `leads.id`. |
| **verified_at** | 0 | 9,980 | Keep – Part of phone verification feature. |
| **verified_by** | 0 | 9,980 | Keep – Part of verification feature; required for audit. |

**Conclusion:** Drop only **leads.lead_id**. Keep the rest for forms, conversion, or verification.

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
- **Usage:** `Lead` model casts and `needsVerification()`; `PhoneVerificationController::getStatusForLead`; `PhoneVerificationService` sets on verify; migration added these.
- **Recommendation:** **Do not remove.** Used for phone verification.

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
- **Recommendation:** **Keep.** Consider migrating to media storage later (same as admins review).

### 27. **preferredintake** (model: **preferredIntake**)
- **Usage:** `LeadController@store`; `Lead` model accessor/mutator; `convertoClient` (commented) copies to admin.
- **Recommendation:** **Keep** if intake date is used; otherwise review with business.

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
- **Usage:** `LeadController@index`: filter `$request->has('priority')` and `$query->where('priority', @$priority)`.
- **Recommendation:** **Keep** if priority filter is used; if filter is unused, could remove filter and column.

---

## Review or safe to remove

### 35. **name**
- **Usage:** In `Lead` model `$fillable` and `$sortable`. No code writes to `lead->name`; lead create uses first_name/last_name. Index name filter uses `COALESCE(first_name,'') || ' ' || COALESCE(last_name,'')`, not a `name` column.
- **Recommendation:** **Candidate for removal** if the column exists and is redundant with first_name + last_name. Confirm column exists in DB; remove from model fillable/sortable and drop column. Optionally add a virtual/computed “name” accessor if needed.

### 36. **agent_id**
- **Usage:** `Lead` model has `agentdetail()` relationship to User. No found writes in `LeadController@store` or elsewhere to `lead->agent_id`. Client create/edit use `subagent` (agent_id) on **admins**, not on leads.
- **Recommendation:** **Candidate for removal** if no legacy or external system sets it. Remove `agentdetail()` from Lead model and drop column. If “sub-agent” is ever needed on leads, add back with proper form/flow.

### 37. **social_type**, **social_link**
- **Usage:** Commented out in `LeadController@store` (`//$obj->social_type`, `//$obj->social_link`). No other references found.
- **Recommendation:** **Safe to remove** if you confirm no UI or imports use them. Drop columns and delete commented lines.

### 38. **advertisements_name**
- **Usage:** Commented in `LeadController@store` (`//$obj->advertisements_name`). No other references found.
- **Recommendation:** **Safe to remove.** Drop column and remove commented line.

### 39. **latest_comment**
- **Usage:** Only in a **commented-out** block in `resources/views/Admin/leads/index.blade.php` (`{{-- ... @$list->latest_comment ... --}}`). No reads/writes elsewhere.
- **Recommendation:** **Safe to remove** if column exists. Remove from view comment if desired and drop column.

### 40. **start_process**
- **Usage:** Already dropped in migration `2026_02_07_140000_drop_start_process_column.php`. No action needed.
- **Recommendation:** **Already removed.**

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
| **Zero data (DB query)** | **5** | **profile_img**, **preferredintake**, **lead_id**, **verified_at**, **verified_by** – see section above. |

**Suggested next steps (do not apply yet):**
1. Confirm in the database which of the “candidate” and “safe to remove” columns actually exist on `leads`.
2. If you use priority filter in the UI, keep **priority**; otherwise consider dropping it.
3. Remove **social_type**, **social_link**, **advertisements_name**, and **latest_comment** via migration and clean commented code.
4. After confirming **name** is redundant, remove from model and drop column; add accessor if you need `$lead->name` as first_name + last_name.
5. After confirming **agent_id** is unused, remove `agentdetail()` and drop column.
6. Optionally consolidate **age** as derived from **dob** and drop **age** column if you do not need a separate stored value.
