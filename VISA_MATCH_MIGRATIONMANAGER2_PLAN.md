# Plan: Align Visa Structure with migrationmanager2 (DO NOT APPLY YET)

This document is the implementation plan only. Do not apply changes until approved.

---

## Objective

1. Add columns to `admins` so visa structure matches migrationmanager2 (one visa per client).
2. On import: match visa type by name or by migrationmanager2’s title/nick_name; if no match, create new record in `visa_types`. Duplicates accepted short-term; fix gradually.

**No renaming:** In migrationmanager2, “matters” is used for a broader purpose (client matters, email templates, visa types, etc.). In bansalcrm2 the reference is only for **visa subclass**, so we keep the table `visa_types` and model `VisaType` — no rename to matters.

---

## Phase 1: Add New Columns (Structure Match)

### 1.1 Migration: Add columns to `admins`

- **New columns:**
  - `visa_type_id` — unsignedBigInteger, nullable, FK to `visa_types.id`. Indexed.
  - `visa_country` — string (e.g. 255), nullable.
  - `visa_grant_date` — date, nullable.
- **Keep unchanged:** `visa_type` (string), `visa_opt`, `visaexpiry` — no data migration in this phase.
- **Migration file:** e.g. `YYYY_MM_DD_HHMMSS_add_visa_columns_to_admins_table.php`.

### 1.2 Model: `App\Models\Admin`

- Add `visa_type_id`, `visa_country`, `visa_grant_date` to `$fillable` (if used).
- Add `visa_grant_date` to `$casts` as `date` (if using Carbon).
- Add relationship: `public function visaType() { return $this->belongsTo(VisaType::class, 'visa_type_id', 'id'); }`
- Ensure existing accessors/attributes for `visaExpiry` / `visa_type` remain.

### 1.3 Optional: Add `nick_name` to `visa_types`

- **Purpose:** Export/import parity with migrationmanager2 (they send `visa_type_matter_title` and `visa_type_matter_nick_name`). Bansalcrm2 can send `visa_type` (id), `visa_type_matter_title` (= `visa_types.name`), and optionally `visa_type_matter_nick_name` (= `visa_types.nick_name` if added).
- **Migration:** Add column `nick_name` (string, nullable) to `visa_types`. No rename of table or of `name`.
- **Model:** Add `nick_name` to `VisaType` fillable/sortable if needed.

### 1.4 Forms and display (optional in Phase 1)

- Client/lead create and edit views: optionally add fields for `visa_country` and `visa_grant_date` (can be deferred).
- Dropdown can keep saving `visa_type` (string) from existing `visa_types` dropdown; Phase 2 can start populating `visa_type_id` on import.

---

## Phase 2: Import “Match or Create” Logic

### 2.1 Service or helper: resolve visa type for import

- **Location:** e.g. `App\Services\VisaTypeResolveService` or method in `ClientImportService`.
- **Input:** From JSON: `visa_type` (int, from migrationmanager2), `visa_type_matter_title`, `visa_type_matter_nick_name`.
- **Logic:**
  1. If `visa_type` is integer and exists in `visa_types.id` → return that id (only if IDs are aligned across systems; otherwise skip or use only string match).
  2. Else try match by string: trim + case-insensitive match on `visa_types.name` for `visa_type_matter_title`, then `visa_type_matter_nick_name`. Return id if found.
  3. If no match: `INSERT` new row into `visa_types` with `name = visa_type_matter_title ?? visa_type_matter_nick_name ?? 'Imported'`, optionally set `nick_name` if column exists. Return new id.
- **Output:** `visa_types.id` (integer).

### 2.2 ClientImportService changes

- When importing visa from `visa_countries` (first or only entry):
  - Call resolve service to get `visa_type_id`.
  - Set `$client->visa_type_id = $resolvedId`.
  - Set `$client->visa_type = $visaTypeModel->name` (sync string for backward compatibility).
  - Set `$client->visa_country = $visaData['visa_country'] ?? null`.
  - Set `$client->visa_grant_date = $this->parseDate($visaData['visa_grant_date'] ?? null)` (column from Phase 1).
  - Keep existing: `visa_opt` ← `visa_description`, `visaexpiry` ← `visa_expiry_date`.

### 2.3 ClientExportService changes

- Export one visa object with: `visa_country`, `visa_type` (id from `visa_type_id`), `visa_type_matter_title` (= `visaType->name` or `visa_type` string), `visa_type_matter_nick_name` (= `visaType->nick_name` if column exists), `visa_description` (= visa_opt), `visa_expiry_date`, `visa_grant_date`.
- Same JSON shape as migrationmanager2 so both systems accept the same file.

---

## Phase 3: Optional Cleanup and Deduplication

- **Later:** Admin tool or script to list `visa_types` with similar `name` (and `nick_name` if present); merge duplicates by updating `admins.visa_type_id` to one canonical id and removing duplicate `visa_types` rows.
- **Matching rule:** Trim + case-insensitive when comparing; consider normalising spaces.

---

## Order of Application (When Approved)

1. **Phase 1** — Migration(s): add columns to `admins`; optionally add `nick_name` to `visa_types`. Update Admin model (relationship, fillable, casts). Deploy; verify existing data unchanged.
2. **Phase 2** — Resolve service + ClientImportService + ClientExportService; test import/export with migrationmanager2 JSON. Verify “match or create” and duplicate handling.
3. **Phase 3** — When ready, run deduplication on `visa_types`.

---

## Rollback (If Needed)

- **Phase 1:** Migration down: drop `visa_type_id`, `visa_country`, `visa_grant_date` from `admins`; optionally drop `nick_name` from `visa_types`.
- **Phase 2:** Revert ClientImportService and ClientExportService; remove resolve service; no schema change.
- **Phase 3:** N/A (data cleanup only).

---

## Files to Touch (Summary)

- **Migrations:** 1 new (add columns to admins); 1 optional (add nick_name to visa_types).
- **Models:** Admin.php (relationship, fillable, casts); optionally VisaType.php (nick_name).
- **Controllers:** No rename; ClientController / LeadController only if you start saving visa_type_id from forms.
- **Services:** ClientImportService, ClientExportService; new VisaTypeResolveService (or inline in ClientImportService).
- **Views:** Optional: add visa_country, visa_grant_date to client/lead forms; no rename of visatype views.
- **Routes:** No change (keep visa-type and VisaTypeController).

Do not apply this plan until explicitly approved.
