# Migration Plan: Leads Table → Admins Table (type=lead)

## Overview

Migrate all lead records from the `leads` table into the `admins` table with `type = 'lead'`. Some leads already have corresponding rows in `admins` (linked via `admins.lead_id` or matching email). This plan covers strategy, steps, and considerations—**no implementation**.

---

## 1. Current State

### Tables Involved

| Table | Purpose |
|-------|---------|
| `leads` | Lead records (pre-clients) |
| `admins` | Staff (role ≠ 7) and clients/leads (role = 7). Has `type` ('lead' vs client) and `lead_id` linking to `leads.id` |
| `followups` | `lead_id` → `leads.id` |
| `phone_verifications` | `lead_id` → `leads.id` |

### Matching Criteria (from SearchService, ClientController, LeadController)

A lead is considered **already in admins** if:
- `admins.lead_id` = `leads.id`, OR
- `admins.email` = `leads.email` (both non-null, admins.role = 7)

### Constraints

- `admins.email` is **unique**
- Leads have **no password**; admins require a password for auth

---

## 2. Pre-Migration Checklist

| # | Action |
|---|--------|
| 1 | Backup database (e.g. `php artisan schema:dump` or DB backup) |
| 2 | Verify `admins` table has `type` column; add if missing (nullable string) |
| 3 | Run `SELECT COUNT(*) FROM leads` and `SELECT COUNT(*) FROM admins WHERE role = 7` to know volumes |
| 4 | Check for duplicate emails in leads: `SELECT email, COUNT(*) FROM leads GROUP BY email HAVING COUNT(*) > 1` |
| 5 | Find leads matched to admins by email only (no lead_id): `SELECT l.id, l.email FROM leads l INNER JOIN admins a ON a.email = l.email AND a.role = 7 WHERE a.lead_id IS NULL OR a.lead_id != l.id` |
| 6 | Confirm `user_roles` has role 7 (client/lead) |

---

## 3. Matching & Handling Rules

### 3.1 Matching Priority

1. **Primary:** `admins.lead_id = leads.id`
2. **Secondary:** `admins.email = leads.email` (both non-null) and `admins.role = 7`

### 3.2 For Leads That Already Have an Admin Row

Choose one strategy:
- **Option A (Full sync):** UPDATE the admin row with all lead data, ensure `type = 'lead'`
- **Option B (Link only):** UPDATE `admins.lead_id` (and `type`) where missing; leave other admin data as-is
- **Option C (Skip):** Do nothing for matching rows

### 3.3 For Leads Without a Matching Admin

INSERT new row into `admins` with:
- `type = 'lead'`
- `role = 7`
- `lead_id = leads.id`
- Mapped columns from leads

---

## 4. Column Mapping (Leads → Admins)

Only map columns that exist on both tables (use `Schema::hasColumn` at runtime).

| Leads (source) | Admins (target) | Notes |
|----------------|-----------------|-------|
| id | lead_id | FK back to lead |
| first_name | first_name | |
| last_name | last_name | |
| email | email | Must be unique; handle duplicates |
| phone | phone | |
| country_code | country_code | |
| gender | gender | |
| dob | dob | |
| marital_status | marital_status | |
| visa_type / visa_type_id | visa_type / visa_type_id | Match column names |
| address | address | |
| city | city | |
| state | state | |
| country | country | |
| zip | zip | |
| assign_to | office_id (via staff) | Resolve staff.office_id if needed |
| created_at | created_at | |
| updated_at | updated_at | |
| — | password | Use placeholder (e.g. bcrypt random) for leads |
| — | role | 7 |
| — | type | 'lead' |
| — | status | 1 (active) |
| — | verified | 0 |
| — | is_archived | 0 |
| — | show_dashboard_per | 0 |

Include other shared columns (e.g. `country_passport`, `passport_no`) as schema dictates.

---

## 5. Migration Steps (High-Level)

1. **Ensure schema:** Add `type` column to `admins` if not present.
2. **Fetch leads:** Load all leads (filter by `converted = 0` if applicable).
3. **Identify existing:** For each lead, check if admin exists via `lead_id` or email.
4. **Process each lead:**
   - **Matched by lead_id:** Apply chosen UPDATE strategy (A/B/C).
   - **Matched by email only:** Same; also set `lead_id` if empty.
   - **Not matched:** INSERT into admins with mapped data, `type = 'lead'`.
5. **Handle email uniqueness:** If leads have duplicate emails, decide: migrate first, skip, or use alternate value.
6. **Handle password:** For new admins, set placeholder (e.g. `bcrypt(Str::random(32))`) or no-login marker.
7. **Optional:** Add `is_migrated` flag on leads to avoid re-processing on re-runs.
8. **PostgreSQL:** After bulk inserts, run `SELECT setval(pg_get_serial_sequence('admins', 'id'), (SELECT MAX(id) FROM admins))`.

---

## 6. Edge Cases & Risks

| Scenario | Mitigation |
|----------|------------|
| Lead email exists in admins for different lead | Decide: skip, update, or use alternate email. |
| Null/empty lead email | Skip or use placeholder; document. |
| Duplicate emails in leads | Migrate one per email; log others. |
| FK orphans (assign_to, etc.) | Null out if referenced staff/record missing. |
| PostgreSQL sequences | Update admins.id sequence after bulk inserts. |

---

## 7. Post-Migration Validation

| # | Action |
|---|--------|
| 1 | Count: `leads` vs `admins WHERE type = 'lead'` or `lead_id IS NOT NULL` |
| 2 | Spot-check sample rows (names, emails, phones) |
| 3 | Verify `followups.lead_id` and `phone_verifications.lead_id` still resolve correctly (leads table intact) |
| 4 | If leads will be deprecated: plan migration of `followups` and `phone_verifications` to reference `admins` |

---

## 8. Rollback

- If using migration: Implement `down()` to remove migrated rows (e.g. `DELETE FROM admins WHERE type = 'lead' AND lead_id IN (SELECT id FROM leads)`), or restore from backup.
- If using script: Restore from DB backup if needed.

---

## 9. Implementation Options

- **Laravel migration:** New migration class with logic in `up()` / `down()`.
- **Artisan command:** e.g. `php artisan leads:migrate-to-admins` with `--dry-run` flag.
- **Standalone script:** One-off PHP script using app DB connection; wrap in transaction.

---

## 10. Decisions Required Before Implementation

| # | Decision | Options |
|---|----------|---------|
| 1 | Existing-row strategy | A: Full sync / B: Link only / C: Skip |
| 2 | Duplicate lead emails | First wins / Skip / Modify / Error |
| 3 | Converted leads | Include / Exclude |
| 4 | Leads table after migration | Keep for history / Deprecate later |
| 5 | Batch size | All at once / Chunked (e.g. 1000) |

---

*Document created as planning reference. No code implementation.*
