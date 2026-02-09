# Leads Table – Column Review

**Purpose:** Document all columns in the `leads` table, their data usage, and recommendations (Keep / Remove / Zero or no data).  
**Source:** Live schema and row counts (total leads: **9,980**).  
**Total columns:** 55.

---

## Summary

| Category | Count | Action |
|----------|--------|--------|
| **Keep** | 45 | Required for app logic, forms, conversion, or search |
| **Remove (candidates)** | 4 | Zero data or unused / commented out in code |
| **Zero or no data** | 5 | All rows NULL; keep only if required by feature/structure |

---

## 1. Zero or no data (all rows NULL)

These columns have **0 non-NULL values** across 9,980 rows:

| Column | Filled | Null | Recommendation |
|--------|--------|------|----------------|
| **profile_img** | 0 | 9,980 | **Keep** – Form and lead→client conversion use it; structure needed. |
| **preferredintake** | 0 | 9,980 | **Keep** – In create form and conversion; used when converting lead to client. |
| **lead_id** | 0 | 9,980 | **Remove** – Not used on `leads` table. Link is `admins.lead_id` → `leads.id`. Redundant. |
| **verified_at** | 0 | 9,980 | **Keep** – Part of phone verification (Lead model, `needsVerification()`); required for feature. |
| **verified_by** | 0 | 9,980 | **Keep** – Part of verification feature; required for audit. |

**Conclusion:** Drop only **leads.lead_id**. Keep the rest for forms, conversion, or verification.

---

## 2. Columns to keep (critical or actively used)

### Core identity & assignment
- **id** – Primary key.
- **user_id** – Creator; used in LeadController index filter.
- **first_name**, **last_name** – Name; used in forms, list, search, conversion, followup templates.
- **assign_to** – Assigned staff; used in LeadController (counts, filters), ClientController (lead→client), FollowupController.
- **status** – Lead status; filtering and workflow (e.g. not_contacted, followup, won, lost).
- **converted** – 0/1; used in LeadController and SearchService to exclude converted leads.
- **converted_date** – When lead was converted (9,613 filled).

### Contact (required / search)
- **contact_type**, **country_code**, **phone**, **email_type**, **email** – All 9,980 filled; used in create, search (SearchService), conversion.
- **att_phone**, **att_email**, **att_country_code** – Used in create, conversion, and SearchService (email/phone matching for duplicate detection).

### Demographics & visa
- **gender**, **dob**, **marital_status** – Forms and conversion.
- **visa_type**, **visa_expiry_date** – Forms and conversion (5,731 / 5,496 filled).
- **country_passport**, **country** – Forms and conversion (5,109 filled each).

### Lead metadata
- **lead_quality**, **lead_source** – Forms and conversion.
- **service** – All filled; forms and conversion.
- **comments_note** – Forms and conversion (9,752 filled).
- **created_at**, **updated_at** – Timestamps; filtering and display.
- **is_verified** – Phone verification flag (boolean); Lead model and verification flow.

### Address (optional)
- **address**, **city**, **state**, **zip** – Low fill (261–1,002) but in forms and conversion; keep.

### Qualifications / migration (optional, low fill)
- **nomi_occupation**, **skill_assessment**, **high_quali_aus**, **high_quali_overseas**, **relevant_work_exp_aus**, **relevant_work_exp_over** – In create form and conversion; keep.
- **naati_py** (7 filled), **married_partner** (54), **total_points** (45) – In form and conversion; keep.

### Other
- **tags_label** (110 filled) – Tags in create form; keep.
- **advertisements_name** (2,482 filled) – In form; keep.
- **related_files** (8 filled) – In create and conversion (commented block); keep for compatibility.
- **age** (66 filled) – Can be derived from DOB but still saved in form; keep.

---

## 3. Columns to remove (recommended)

| Column | Filled | Reason |
|--------|--------|--------|
| **lead_id** | 0 | Not used on `leads`. Relationship is `admins.lead_id` → `leads.id`. Safe to drop from `leads`. |
| **social_type** | 12 | Commented out in LeadController create (`//$obj->social_type`). Effectively unused. |
| **social_link** | 14 | Commented out in LeadController create (`//$obj->social_link`). Effectively unused. |

**Optional (low value):**  
- **related_files** – Only 8 rows; could be dropped if you confirm no reliance in conversion or reporting. Currently **keep** in this review.

---

## 4. Full column listing (with counts and recommendation)

| # | Column | Filled | Null | Recommendation |
|---|--------|--------|------|----------------|
| 1 | id | 9,980 | 0 | Keep |
| 2 | user_id | 9,980 | 0 | Keep |
| 3 | first_name | 9,980 | 0 | Keep |
| 4 | last_name | 9,980 | 0 | Keep |
| 5 | gender | 9,980 | 0 | Keep |
| 6 | dob | 7,243 | 2,737 | Keep |
| 7 | marital_status | 4,459 | 5,521 | Keep |
| 8 | passport_no | 18 | 9,962 | Keep (form + conversion) |
| 9 | visa_type | 5,731 | 4,249 | Keep |
| 10 | visa_expiry_date | 5,496 | 4,484 | Keep |
| 11 | tags_label | 110 | 9,870 | Keep |
| 12 | contact_type | 9,980 | 0 | Keep |
| 13 | country_code | 9,980 | 0 | Keep |
| 14 | phone | 9,980 | 0 | Keep |
| 15 | email_type | 9,980 | 0 | Keep |
| 16 | email | 9,980 | 0 | Keep |
| 17 | social_type | 12 | 9,968 | **Remove** (commented in code) |
| 18 | social_link | 14 | 9,966 | **Remove** (commented in code) |
| 19 | service | 9,980 | 0 | Keep |
| 20 | assign_to | 9,980 | 0 | Keep |
| 21 | status | 9,783 | 197 | Keep |
| 22 | lead_quality | 9,980 | 0 | Keep |
| 23 | lead_source | 9,980 | 0 | Keep |
| 24 | advertisements_name | 2,482 | 7,498 | Keep |
| 25 | comments_note | 9,752 | 228 | Keep |
| 26 | converted | 9,980 | 0 | Keep |
| 27 | converted_date | 9,613 | 367 | Keep |
| 28 | related_files | 8 | 9,972 | Keep (or remove if not used) |
| 29 | created_at | 9,980 | 0 | Keep |
| 30 | updated_at | 9,980 | 0 | Keep |
| 31 | att_phone | 49 | 9,931 | Keep |
| 32 | att_email | 23 | 9,957 | Keep |
| 33 | att_country_code | 5,478 | 4,502 | Keep |
| 34 | profile_img | 0 | 9,980 | Zero data – Keep (form/conversion) |
| 35 | age | 66 | 9,914 | Keep |
| 36 | preferredintake | 0 | 9,980 | Zero data – Keep (form/conversion) |
| 37 | country_passport | 5,109 | 4,871 | Keep |
| 38 | address | 261 | 9,719 | Keep |
| 39 | city | 265 | 9,715 | Keep |
| 40 | state | 290 | 9,690 | Keep |
| 41 | zip | 1,002 | 8,978 | Keep |
| 42 | country | 5,109 | 4,871 | Keep |
| 43 | nomi_occupation | 231 | 9,749 | Keep |
| 44 | skill_assessment | 187 | 9,793 | Keep |
| 45 | high_quali_aus | 658 | 9,322 | Keep |
| 46 | high_quali_overseas | 703 | 9,277 | Keep |
| 47 | relevant_work_exp_aus | 424 | 9,556 | Keep |
| 48 | relevant_work_exp_over | 331 | 9,649 | Keep |
| 49 | naati_py | 7 | 9,973 | Keep |
| 50 | married_partner | 54 | 9,926 | Keep |
| 51 | total_points | 45 | 9,935 | Keep |
| 52 | lead_id | 0 | 9,980 | **Zero data – Remove** (redundant) |
| 53 | is_verified | 9,980 | 0 | Keep |
| 54 | verified_at | 0 | 9,980 | Zero data – Keep (verification feature) |
| 55 | verified_by | 0 | 9,980 | Zero data – Keep (verification feature) |

---

## 5. Usage notes (where columns are used)

- **LeadController:** create (store), index (filters: assign_to, status, user_id, email, name, phone, created_at, followup_date, priority), conversion block (commented).
- **ClientController::leaddetail:** Loads lead, syncs to Admin when opening lead detail; uses first_name, last_name, email, phone, country_code, gender, dob, visa_type, type, profile_img, att_email, att_phone, marital_status, address, city, state, zip, country, nomi_occupation, assign_to, staffuser.
- **SearchService:** Lead search uses converted, email, att_email, first_name, last_name, phone.
- **Lead model:** preferredIntake accessor/mutator; country_code / att_country_code normalisation; formatted_phone, formatted_att_phone; is_verified, verified_at, verified_by; needsVerification(), isPlaceholderNumber().
- **FollowupController:** lead_id (from leads.id), first_name, assign_to for compose.

---

## 6. Recommended migrations

1. **Drop from `leads` table:**
   - `lead_id` (unused; relationship is on `admins.lead_id`).
2. **Optional – drop if you confirm no use in conversion/reports:**
   - `social_type`
   - `social_link`

Before dropping any column, run a quick code search for the column name and update the Lead model’s `$fillable` and any accessors/mutators if present.
