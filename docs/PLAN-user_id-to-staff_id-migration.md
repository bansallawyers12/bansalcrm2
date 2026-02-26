# Plan: user_id → staff_id Column Rename Migration

**Risk Level:** High  
**Scope:** Database migrations, Models, Controllers, Views  
**Estimated Effort:** 3–5 days (with testing)

---

## 1. Overview

Rename all `user_id` columns that reference Staff to `staff_id` across the codebase for consistent terminology (User → Staff). This improves clarity and aligns with the existing Staff/Client/Lead terminology.

---

## 2. Exclusions (Do NOT Rename)

| Location | Reason |
|----------|--------|
| `sessions.user_id` | Laravel standard – references generic user for session |
| `emails.user_id` | JSON-encoded array of recipient IDs – different semantics |
| `config/constants.php` `reception_user_id` | Config key name, not DB column |
| Form fields like `name="user_id"` in staff view, `change_password.blade.php` | Request param; consider `staff_id` for clarity in same PR |
| `followups.user_id` | **Dropped** in migration 2026_02_21_150000 – column no longer exists |
| `contacts.user_id` | Table **dropped** in migration 2026_02_21_200000 |
| `test_scores.user_id` | Table **dropped** in migration 2026_02_22_120000 |
| `tasks` / `task_*` tables | **Dropped** in migration 2025_12_28_000001 |

---

## 3. Tables to Migrate (user_id → staff_id)

Based on codebase analysis. **Verify each table exists** before migration.

### 3.1 Core Tables (High Traffic)

| Table | Column | References | Notes |
|-------|--------|-------------|-------|
| `applications` | `user_id` | staff.id (assignee) | `application_assignee` relationship |
| `notes` | `user_id` | staff.id (creator) | Actions/assignments |
| `checkin_logs` | `user_id` | staff.id | Office check-in |
| `staff_login_logs` | `user_id` | staff.id | Audit logs |
| `application_activities_logs` | `user_id` | staff.id | Activity log |
| `documents` | `user_id` | staff.id | Document uploader |
| `account_client_receipts` | `user_id` | staff.id | Receipt creator |

### 3.2 Supporting Tables

| Table | Column | References | Notes |
|-------|--------|-------------|-------|
| `document_categories` | `user_id` | staff.id | NULL = system, ID = staff-created |
| `email_labels` | `user_id` | staff.id | NULL = system, ID = staff-specific |
| `client_phones` | `user_id` | staff.id | Staff who added/verified |
| `client_emails` | `user_id` | staff.id | Staff who added |
| `application_reminders` | `user_id` | staff.id | Staff who created reminder |
| `mail_reports` | `user_id` | staff.id | Report owner |

### 3.3 Clients Table (admins = clients)

| Table | Column | References | Notes |
|-------|--------|-------------|-------|
| `admins` | `user_id` | staff.id | Client assignee/creator – used in staff/view, clients/detail |

**Note:** `admins` table holds clients/leads. `user_id` = staff who added/assigned the client. Add to fillable if missing.

### 3.4 Legacy Tables (user_id → admins.id – verify before rename)

| Table | Column | References | Notes |
|-------|--------|-------------|-------|
| `invoices` | `user_id` | admins.id | Assignee – Admin model; may need Staff mapping |
| `invoice_followups` | `user_id` | admins.id | Same as above |
| `share_invoices` | `user_id` | admins.id | Same as above |
| `leads` | `user_id` | admins.id | Assigned staff; admins being phased out |

**Decision:** If these reference staff (stored in admins legacy), rename to `staff_id` and update relationships to Staff. Verify data mapping first.

---

## 4. Migration Strategy

### Phase A: Preparation (Before Any Migrations)

1. **Create full inventory**
   - Run `\DB::select("SELECT table_name, column_name FROM information_schema.columns WHERE column_name = 'user_id'")` on production/staging to get exact list.
   - Compare with this plan and adjust.

2. **Foreign key audit**
   - List all FKs on `user_id` columns.
   - Ensure rename migration handles FKs (drop before rename, recreate after).

3. **Backup**
   - Full DB backup before first migration.
   - Consider blue/green deployment if possible.

### Phase B: Migration Execution Order

**Rule:** Rename in dependency order. Child tables first if they reference parent `user_id`; otherwise, lowest-traffic tables first.

**Suggested order (one migration per table or logical group):**

1. `email_labels` (low traffic)
2. `document_categories` (low traffic)
3. `client_phones`
4. `client_emails`
5. `application_reminders`
6. `staff_login_logs`
7. `application_activities_logs`
8. `mail_reports`
9. `documents`
10. `checkin_logs`
11. `notes`
12. `applications`
13. `account_client_receipts`
14. `admins` (clients table – user_id = assignee)
15. `invoices` (if migrating)
16. `invoice_followups` (if migrating)
17. `share_invoices` (if migrating)
18. `leads` (if migrating)

**Alternative:** Single migration that renames all in one transaction (safer for consistency, larger rollout).

### Phase C: Single Migration Template

```php
// database/migrations/YYYY_MM_DD_HHMMSS_rename_user_id_to_staff_id.php
Schema::table('email_labels', function (Blueprint $table) {
    $table->renameColumn('user_id', 'staff_id');
});
Schema::table('document_categories', function (Blueprint $table) {
    $table->renameColumn('user_id', 'staff_id');
});
// ... repeat for each table
// Handle indexes: drop index on user_id, create on staff_id if needed
// Handle FKs: drop FK, rename, recreate FK to staff.id
```

**Requirement:** `doctrine/dbal` package for `renameColumn()`. Run `composer require doctrine/dbal` if missing.

**PostgreSQL note:** For PostgreSQL with FKs:

```php
$table->dropForeign(['user_id']);
$table->renameColumn('user_id', 'staff_id');
$table->foreign('staff_id')->references('id')->on('staff')->onDelete('set null');
```

---

## 5. Model Updates (By File)

| Model | Changes |
|-------|---------|
| `Application` | `user_id` → `staff_id` in fillable, `application_assignee()` FK |
| `Note` | `user_id` → `staff_id` in fillable, creator/noteUser relationship |
| `CheckinLog` | `user_id` → `staff_id` in fillable, `assignee()` relationship (uses `user_id`) |
| `StaffLoginLog` | `user_id` → `staff_id` in fillable, `user()`/`staff()` relationship |
| `ApplicationActivitiesLog` | `user_id` → `staff_id` in fillable |
| `Document` | `user_id` → `staff_id` in fillable, relationships |
| `DocumentCategory` | `user_id` → `staff_id` in fillable, relationships, `scopeForUser` → `scopeForStaff` |
| `EmailLabel` | `user_id` → `staff_id` in fillable, relationships, scope queries |
| `ClientPhone` | `user_id` → `staff_id` in fillable |
| `ClientEmail` | `user_id` → `staff_id` (model in app/Models if exists) |
| `ApplicationReminder` | `user_id` → `staff_id` in fillable, relationships |
| `MailReport` | `user_id` → `staff_id` in fillable, relationships |
| `Admin` (clients) | Add `staff_id` to fillable; `user_id` → `staff_id` after migration |
| `Invoice` | `user_id` → `staff_id` (update Admin → Staff if applicable) |
| `InvoiceFollowup` | `user_id` → `staff_id` |
| `ShareInvoice` | `user_id` → `staff_id` |
| `Lead` | `user_id` → `staff_id` |

**Relationship renames:** Consider `user()` → `staff()` or `creator()` for clarity.

**Note:** `activities_logs` uses `created_by`, not `user_id` – exclude from this migration.

---

## 6. Controller Updates (By File)

| Controller | Changes |
|------------|---------|
| `ActionController` | `user_id` → `staff_id` in Note/ApplicationActivitiesLog assignments, queries |
| `ClientActionController` | `action->user_id`, `obj1->user_id` → `staff_id` |
| `PartnersController` | All `$obj->user_id` → `$obj->staff_id` (30+ occurrences) |
| `ClientNoteController` | `user_id` → `staff_id` in select, Staff::find |
| `ClientDocumentController` | `user_id` → `staff_id` |
| `ClientDocumentCategoryController` | `'user_id' => $userId` in create payload |
| `ClientReceiptController` | `user_id` → `staff_id` |
| `ClientMessagingController` | `user_id` → `staff_id` |
| `AccountController` | `user_id` → `staff_id` |
| `PromotionController` | `user_id` → `staff_id` |
| `OfficeVisitController` | `user_id` → `staff_id`; keep `reception_user_id` config (excluded) |
| `AdminController` | CheckinLog, Invoice, Note `user_id` → `staff_id` |
| `OngoingSheetController` | `applications.user_id` → `applications.staff_id`, CheckinLog |
| `AuditLogController` | `user_id` → `staff_id` in StaffLoginLog queries |
| `AdminConsole/EmailLabelController` | `user_id` → `staff_id` |
| `AdminConsole/EmailController` | **Exclude** – stores JSON array in `user_id` |
| `AdminConsole/RecentlyModifiedClientsController` | `user_id` → `staff_id` |
| `LeadController` | `'user_id' => Auth::user()->id` → `staff_id` |
| `PublicDocumentController` | `document->user_id` → `document->staff_id` |
| `Controller` (base) | `getRelatedSlugs()` – generic `where('user_id')` → `where('staff_id')` or pass column param |

**StaffController** `$requestData['user_id']`: Form field – rename to `staff_id` in view + controller.

**AdminLoginController** `$obj->user_id` for StaffLoginLog – update to `staff_id`.

---

## 7. View Updates (By File)

| View | Changes |
|------|---------|
| `Admin/action/assign_to_me.blade.php` | `$list->user_id`, `$listC->user_id` → `staff_id` |
| `Admin/action/completed.blade.php` | `$list->user_id` → `$list->staff_id` |
| `Admin/auditlogs/index.blade.php` | `$list->user_id` in `$durationKey` → `staff_id` |
| `Admin/staff/view.blade.php` | `Admin::where('user_id',...)`, `$alist->user_id`, hidden `name="user_id"` → `staff_id` |
| `Admin/officevisits/index.blade.php` | `$list->user_id` → `$list->staff_id` |
| `Admin/clients/detail.blade.php` | `$fetchedData->user_id`, `$fetch->user_id`, `$list->user_id`, `$mailreport->user_id` |
| `Admin/clients/applicationdetail.blade.php` | `sheetCommentLog->user_id`, `applicationlist->user_id`, `doclist->user_id`, `fetchData->user_id`, `data-assignee-id` |
| `Admin/clients/clientreceiptlist.blade.php` | `$list->user_id` → `$list->staff_id` |
| `Admin/invoice/paid.blade.php` | `$invoicelist->user_id`, `$applicationdata->user_id` |
| `Admin/invoice/unpaid.blade.php` | `$invoicelist->user_id` |
| `Admin/account/payablepaid.blade.php` | `$list->user_id` |
| `Admin/partners/detail.blade.php` | `$list->user_id`, `$fetch->user_id`, `$mailreport->user_id` |
| `Admin/products/detail.blade.php` | `$alist->user_id` |
| `Admin/agents/detail.blade.php` | `$list->user_id` |
| `emails/application.blade.php` | `$applications->user_id`, `$applicationlist->user_id` (multiple) |
| `Elements/Admin/left-side-bar.blade.php` | `CheckinLog::where('user_id', ...)` → `staff_id` |
| `AdminConsole/emails/index.blade.php` | **Exclude** – `user_id` stores JSON; leave as-is |
| `AdminConsole/emails/edit.blade.php` | **Exclude** – same JSON semantics |
| `Admin/invoice/create.blade.php` | `Item::where('user_id',...)` – **verify Item model exists** |
| `change_password.blade.php` | Form hidden `user_id` – optional rename to `staff_id` |

---

## 8. Migration Data Scripts / Commands / Services

### Console Commands (update inserts/selects)

| Command | Changes |
|---------|---------|
| `MigratePendingLeadPhonesCommand` | `'user_id' => …` → `'staff_id' => …` |
| `MigratePendingLeadEmailsCommand` | `'user_id' => …` → `'staff_id' => …` |
| `MigrateApplicationDocumentsToDocumentsTable` | `'user_id' => $appDoc->user_id` → `staff_id` |
| `CheckinLogColumnStats` | Raw SQL `count(user_id)` → `count(staff_id)` |
| `CronJob` | `$objf->user_id = $invoice->user_id` → `staff_id` |

### Data Migrations (existing – for reference only)

Past migrations that **insert** `user_id` into client_emails/client_phones – no code change needed if tables are already migrated. New migrations must use `staff_id`.

### Services

| Service | Changes |
|---------|---------|
| `ClientImportService` | `'user_id' => Auth::id()` in client_phones insert → `'staff_id'` |
| `DashboardService` | `StaffLoginLog::where('user_id', …)` → `where('staff_id', …)` |

---

## 9. Execution Checklist

### Pre-Migration

- [ ] Run DB column inventory query (Section 4 Phase A)
- [ ] **Verify `admins` table has `user_id`** – used in staff/view, clients/detail
- [ ] **Verify `Item` model/table** – invoice/create references `Item::where('user_id', ...)`; model not in app/Models – may be deprecated
- [ ] Backup database
- [ ] Create feature branch
- [ ] Run full test suite baseline
- [ ] Document current `user_id` FKs and indexes

### Migration

- [ ] Create migration(s) with `renameColumn`
- [ ] Handle FKs (drop → rename → recreate)
- [ ] Handle indexes
- [ ] Run `php artisan migrate` on dev
- [ ] Verify schema with `\Schema::getColumnListing()`

### Code Updates

- [ ] Update all models (fillable, relationships)
- [ ] Update all controllers
- [ ] Update all views
- [ ] Update config if any (e.g. `reception_user_id` → `reception_staff_id` – optional)
- [ ] Update Console commands and one-off migration scripts

### Post-Migration

- [ ] Run full test suite
- [ ] Manual smoke test: login, create note, create document, check-in, view audit logs
- [ ] Search codebase for remaining `user_id` (exclude exclusions)
- [ ] Deploy to staging
- [ ] Run staging tests
- [ ] Deploy to production with rollback plan

---

## 10. Rollback Plan

1. **Code rollback:** Revert all model/controller/view changes.
2. **DB rollback:** New migration `staff_id` → `user_id` on all tables (reverse of Phase C).
3. Ensure migrations are reversible (`down()` methods).

---

## 11. Risk Mitigation

| Risk | Mitigation |
|------|------------|
| Long-running migration locks tables | Rehearse on copy of prod data; consider splitting into smaller migrations |
| Missed references | Grep for `user_id` after changes; use IDE "Find in Path" |
| FK constraint errors | Drop FKs before rename; recreate in same migration |
| Different semantics (JSON vs single ID) | Clearly mark exclusions; double-check `emails` table |
| Admin vs Staff ID mismatch | Verify admin→staff mapping before touching invoices/leads |

---

## 12. File Count Summary

- **Migrations:** 1–18 (single or per-table, includes `admins`)
- **Models:** ~17 (includes Admin)
- **Controllers:** ~20
- **Views:** ~18
- **Commands:** ~5
- **Services:** 2

**Total:** ~65+ files to touch.

---

## 13. Implementation Order (Recommended)

**Option A – Big bang (single deploy):**  
All migrations + all code changes in one PR. Fast but higher risk.

**Option B – Phased (recommended):**

1. **Phase 1 – Low-risk tables:** `email_labels`, `document_categories`, `client_phones`, `client_emails`
   - Migrate, update models/controllers/views, deploy, verify.

2. **Phase 2 – Audit/activity tables:** `staff_login_logs`, `application_activities_logs`, `application_reminders`, `mail_reports`
   - Same pattern.

3. **Phase 3 – Core business:** `documents`, `checkin_logs`, `notes`, `applications`, `account_client_receipts`
   - Highest impact; do during low-traffic window.

4. **Phase 4 – Clients & legacy:** `admins`, then `invoices`, `invoice_followups`, `share_invoices`, `leads`
   - `admins` (clients) must be migrated with code that uses `Admin::where('user_id', ...)`.
   - Legacy invoice/lead tables only if Admin→Staff ID mapping is verified.

---

## 14. Grep Commands for Verification

```bash
# After implementation – should only match exclusions
rg "user_id" app/ --type php
rg "user_id" resources/views/
rg "user_id" database/migrations/

# Target matches (replace these)
rg "->user_id|'user_id'|\"user_id\"|user_id\s*=" app/
rg "\$[a-z_]+->user_id" resources/views/
```

---

## 15. Deep Review Corrections (2026-02-23)

- **Added** `admins` table – clients use `user_id` for assignee (staff/view, clients/detail).
- **Excluded** tables/columns already dropped: `followups.user_id`, `contacts`, `test_scores`, `tasks`.
- **Excluded** `AdminConsole/emails` views – `user_id` stores JSON, not single staff ID.
- **Excluded** `change_password.blade.php` form field (optional rename).
- **Added** controllers: `ClientActionController`, `ClientDocumentCategoryController`, `LeadController`, `PublicDocumentController`, `AdminLoginController`; base `Controller::getRelatedSlugs()`.
- **Added** views: `clients/detail`, `invoice/paid`, `invoice/unpaid`, `account/payablepaid`, `partners/detail`, `products/detail`, `agents/detail`, `emails/application`, `Elements/Admin/left-side-bar`.
- **Added** services: `DashboardService`, `ClientImportService`.
- **Added** commands: `MigrateApplicationDocumentsToDocumentsTable`, `CheckinLogColumnStats`, `CronJob`, `MigratePendingLeadEmailsCommand`.
- **Clarified** `activities_logs` uses `created_by`, not `user_id` – no change.
- **Noted** `Item` model (invoice/create) – verify existence; may be deprecated.

---

*Document created: 2026-02-23. Deep review update: 2026-02-23.*
