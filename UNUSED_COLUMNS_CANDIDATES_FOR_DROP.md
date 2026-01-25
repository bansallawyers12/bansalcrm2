# Unused Database Columns – Candidates for Drop (Do Not Apply Yet)

This document lists database columns that appear **not to be used** anywhere in the application code (models, controllers, services, views, JS, migrations). The list was produced by comparing the current PostgreSQL schema with codebase usage. **None of these have been dropped;** they are candidates only.

---

## ⚠️ Corrections from Deep Verification

**Removed from list (found to be in use):**
1. ~~`checkin_logs.is_archived`~~ - **USED** in `OfficeVisitController::archived()` method and multiple view templates. Do not drop.
2. ~~`document_categories.status`~~ - Was mistakenly confused with `categories.status`. `document_categories.status` is **USED** and should NOT be dropped.

**Still candidates (verified as unused):**
- Total reduced from 14 to **13 columns**

---

## Summary

| # | Table | Column | Confidence | Notes |
|---|--------|--------|------------|-------|
| 1 | `documents` | `office_id` | High | In model fillable only; never read/written in app |
| 2 | `documents` | `folder_name` | High | In model fillable only; never read/written in app |
| 3 | `mail_reports` | `last_accessed_at` | High | In model fillable/casts only; never read/written |
| 4 | `workflow_stages` | `width` | High | No usage in app (only `w_id` is used) |
| 5 | `contacts` | `subject` | Medium | Not in model fillable; not selected or written in controllers |
| 6 | `contacts` | `message` | Medium | Same as above |
| 7 | `contacts` | `image` | Medium | Same as above |
| 8 | `contacts` | `ip_address` | Medium | Same as above |
| 9 | `countries` | `status` | High | No references in app |
| 10 | `categories` | `status` | High | No references in app; unused by MasterCategoryController |
| 11 | `application_document_lists` | `allow_client` | High | Only in schema; `make_mandatory` used, `allow_client` not |
| 12 | `agents` | `password` | Medium | No code references; verify agent auth before drop |
| 13 | `agents` | `remember_token` | Medium | Same as above |

---

## Detailed Findings

### 1. `documents.office_id`
- **Model:** In `Document::$fillable`.
- **Usage:** No controller, service, or view reads or writes this column. `office_id` is used on `admins` and `branches` only.
- **Recommendation:** Safe to consider dropping after removing from `Document::$fillable`.

### 2. `documents.folder_name`
- **Model:** In `Document::$fillable`.
- **Usage:** No references in app code.
- **Recommendation:** Safe to consider dropping after removing from `Document::$fillable`.

### 3. `mail_reports.last_accessed_at`
- **Model:** In `MailReport::$fillable` and `$casts`.
- **Usage:** Never read or written in controllers, services, or email flow.
- **Recommendation:** Safe to consider dropping after removing from model.

### 4. `workflow_stages.width`
- **Usage:** `workflow_stages.w_id` is used throughout (ApplicationsController, WorkflowController, etc.). `width` has no references.
- **Recommendation:** Safe to consider dropping.

### 5–8. `contacts.subject`, `contacts.message`, `contacts.image`, `contacts.ip_address`
- **Model:** Not in `Contact::$fillable`. Partner controller selects `name`, `contact_email`, `contact_phone`, `department`, `branch`, `fax`, `position`, `primary_contact`, `countrycode` only.
- **Usage:** No code reads or writes these four columns. (`subject`/`message`/`ip_address` are used on other tables, e.g. `mail_reports`, `user_logs`.)
- **Note:** Contact model/forms use `first_name`, `last_name`, `company_name`. If your `contacts` table differs from the schema analyzed (e.g. different columns), verify before dropping.
- **Recommendation:** Only drop after confirming `contacts` schema and that no external form/API uses these fields.

### 9. `countries.status`
- **Usage:** No references. `Country` model uses `sortname`, `name`, `phonecode` only.
- **Recommendation:** Safe to consider dropping.

### 10. `categories.status`
- **Usage:** Not in `Category::$fillable`. `MasterCategoryController` uses `category_name` only. No status filtering found.
- **Note:** `document_categories.status` IS used and should NOT be dropped (different table).
- **Recommendation:** Safe to consider dropping from `categories` table.

### 11. `application_document_lists.allow_client`
- **Usage:** `make_mandatory` and `typename` are used in ApplicationsController. `allow_client` is never referenced.
- **Recommendation:** Safe to consider dropping.

### 12–13. `agents.password`, `agents.remember_token`
- **Model:** Not in `Agent::$fillable`.
- **Usage:** No code references these columns. Agent login/auth flow was not found.
- **Recommendation:** **Verify** whether agents use Laravel auth (or any login) before dropping. If they do, keep these columns.

---

## Columns Explicitly Checked and **Used** (Do Not Drop)

- `documents`: `checklist`, `checklist_verified_by`, `checklist_verified_at`, `mail_type`, etc. (`office_id` on **admins** and **branches** is used; `documents.office_id` is not.)
- `mail_reports`: `message_id`, `thread_id`, `received_date`, `client_matter_id`, `conversion_type`, `fetch_mail_sent_time`, `reciept_id`, `python_analysis`, `category`, `priority`, `sentiment`, etc.
- `checkin_logs`: `sesion_start`, `sesion_end`, `wait_time`, `attend_time`, `wait_type`, `office`, **`is_archived`** (used in OfficeVisitController::archived() and multiple views).
- `admins`: `office_id`, `is_archived`, `archived_on`, `archived_by`, `att_email`, `att_phone`, `show_dashboard_per`, `lead_id`, `is_deleted`, plus client/lead fields.
- `workflow_stages`: `w_id`.
- `application_document_lists`: `make_mandatory`, `typename`; `application_documents`: `typename`.
- `document_checklists`: `status`; `document_categories`: `status` (different from `categories.status`).
- `signers`: `reminder_count`.
- `branches`: `mobile`, `choose_admin`.

---

## Suggested Next Steps (When You Decide to Apply)

1. **Back up the database** before any schema change.
2. **Re-run usage checks** (e.g. grep / semantic search) on the columns you plan to drop, in case of recent code changes.
3. **Remove references** from models (e.g. `fillable`, `casts`) for those columns.
4. **Create migrations** that `dropColumn` only for columns you have confirmed as unused.
5. **Test** locally and in staging after migrations.
6. **Deploy** migrations only when satisfied.

---

## Scope of Analysis

- **Schema source:** PostgreSQL `information_schema.columns` (all tables in `public` except `migrations`).
- **Codebase:** `app/`, `resources/`, `routes/`, `config/`, `database/migrations/`, `public/js` (excluding minified libraries).
- **Checks:** Model fillable/casts, controllers, services, views, JS, migrations, and grep for `table.column`, `->column`, `['column']`, etc.

---

*Generated from schema + codebase analysis. Last verification pass: 2026-01-25. Do not apply drops without backup and testing.*
