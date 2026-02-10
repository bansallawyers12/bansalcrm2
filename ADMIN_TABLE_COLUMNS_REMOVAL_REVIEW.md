# Admin Table Columns Removal – Usage Review

**Purpose:** Review each column you plan to remove from the `admins` table. No changes have been applied; this is for your approval before any migration.

**Context:** The `admins` table is used for both **staff/users** (role ≠ 7) and **clients** (role = 7). Many columns are used only for one of these.

---

## Do not remove (critical / actively used)

### 1. **password**
- **Usage:** Laravel auth; `AdminLoginController` validates and checks password on login; `AdminController::change_password`, `UserController`, `StaffController` set hashed password; `ClientController` and `ClientImportService` set placeholder/temp passwords for clients and leads.
- **Recommendation:** **Do not remove.** Required for `Authenticatable` and login. Removing would break all admin and client logins.

### 2. **remember_token**
- **Usage:** In `Admin` model `$hidden`; Laravel’s “Remember me” uses it.
- **Recommendation:** **Do not remove.** Part of `Authenticatable`; removing can break remember-me and session handling.

### 3. **office_id**
- **Usage:** `Branch` model: `staff()`, `clients()`, `activeStaff()`; `OngoingSheetController` (filter/join by branch); client create/edit (assign office); `ClientController`, `ClientImportService`, `ClientExportService`; staff user view and client detail (office display/filter).
- **Recommendation:** **Do not remove.** Core for branch/office assignment for both staff and clients.

### 4. **att_email**
- **Usage:** Client create/edit forms and detail; `ClientController`, `ClientImportService`, `ClientExportService`; `SearchService` (client search and lead matching); Lead conversion copies to client; `ClientReceiptController`; multiple blade views.
- **Recommendation:** **Do not remove.** Standard “additional/attendant email” for clients and lead matching.

### 5. **att_phone**
- **Usage:** Same pattern as `att_email`: client CRUD, export/import, search, lead conversion, verification, views. `Admin` model has `getFormattedAttPhoneAttribute` using `att_phone`.
- **Recommendation:** **Do not remove.** Standard “additional/attendant phone” for clients.

### 6. **lead_id**
- **Usage:** `SearchService`: filters clients with `whereNull(admins.lead_id)` or `lead_id` in (0, ''); `ClientController` and `LeadController`: `Admin::where('lead_id', $id)` when converting lead to client and setting `$obj->lead_id = $lead->id`.
- **Recommendation:** **Do not remove.** Links client record to originating lead; used in search and lead conversion.

### 7. **default_email_id**
- **Usage:** `Admin::defaultEmail()` relationship; `UserController` (create/update staff) and views `users/edit.blade.php`, `users/create.blade.php` for “Default email (from Admin Console > Email)”.
- **Recommendation:** **Marked for deletion.** Remove after dropping the dropdown from user create/edit, removing from `UserController` and `Admin::$fillable`, and dropping the `defaultEmail()` relationship and migration/column.

### 8. **company_name**
- **Usage:** `AdminController` (profile), `AdminConsole\ProfileController`, `my_profile.blade.php`; invoice views and `InvoiceController`; `ApplicationController` and partner detail (e.g. `{Company Name}` in messages); `ClientReceiptController` (DB select); `emails/invoice.blade.php`.
- **Recommendation:** **Review later.** Consider moving to `companies` or `profiles` table; update all references before removal.

### 9. **company_website**
- **Usage:** `AdminController` (profile), `my_profile.blade.php`.
- **Recommendation:** **Review later.** Consider moving company profile elsewhere; update references before removal..

### 10. **primary_email**
- **Usage:** Form in `my_profile.blade.php`; read in `emails/invoice.blade.php`, `emails/reciept.blade.php`, `emails/application.blade.php`. Never saved (AdminController::myProfile does not write it).
- **Recommendation:** **Marked for deletion.** No data; remove form field, update email templates to use alternative (e.g. `email` or company email source), drop column.

### 11. **gst_no**, **gstin**, **gst_date**, **is_business_gst**
- **Usage:** `AdminController::returnsetting` and `returnsetting.blade.php` (is_business_gst, gstin, gst_date). `gst_no` field is commented out in `my_profile.blade.php` and never saved. GST Settings page has no nav link (Tax routes removed from admin console).
- **Recommendation:** **Marked for deletion.** Columns are unreachable in UI and never populated. Remove/update returnsetting form and controller usage, then drop columns.

### 12. **staff_id**
- **Usage:** `StaffController`: required, unique validation and assign on create/update; `staff/edit.blade.php`, `staff/create.blade.php` (“Staff Code”).
- **Recommendation:** **Do not remove.** Used as staff code/identifier for non-client admins.

### 13. **time_zone**
- **Usage:** `UserController` (timezone on update); `users/view.blade.php` (dropdown for staff timezone).
- **Recommendation:** **Review later.** Data: only 3 rows filled (~0%). Keep if per-user timezone is needed; consider removal if not used.

### 14. **position**
- **Usage:** In `Admin` `$fillable`; `users/index.blade.php`, `users/view.blade.php` (display staff position).
- **Recommendation:** **Review later.** Data: 261 rows filled (0.5%). Keep if displaying staff position; consider removal if rarely used.

### 15. **telephone**
- **Usage:** Was used for normalized phone country code; duplicate of `country_code`. Code now uses `country_code` only (UserController, users/edit, ClientExportService, ClientImportService). Removed from Admin `$fillable`.
- **Recommendation:** **Do not remove** if you still use “telephone” for clients (or staff). Confirm whether this is redundant with `phone`/`att_phone`; if redundant, you could deprecate after migrating data and references.

### 16. **user_id** (on admins)
- **Usage:** `users/view.blade.php`: “clients under this staff” with `where('user_id', $fetchedData->id)` and display; `clients/detail.blade.php`: “Added by” via `$fetchedData->user_id` (lookup admin). So this is “assigned staff/agent” for clients (role = 7).
- **Recommendation:** **Do not remove** if you still assign clients to a specific staff member; removing would break “assigned agent” and “added by” behaviour.

### 17. **profile_img**
- **Usage:** Client create/update and lead conversion in `ClientController`; `ClientExportService`, `ClientImportService`; `ClientReceiptController`; agents, partners, profile, header, invoices.
- **Recommendation:** **Marked for deletion.** Migrate profile images to another store (e.g. media table) and update all references before dropping column.

### 18. **comments_note**
- **Usage:** `ClientController` (save on create/update); `ClientExportService`, `ClientImportService`; `clients/edit.blade.php`; `LeadController` (copy from lead to client).
- **Recommendation:** **Do not remove.** Used as client comments/notes.

### 19. **rating**
- **Usage:** `ClientController` (save rating, activity log); `users/view.blade.php` (display client rating in list).
- **Recommendation:** **Marked for deletion.** Not using client rating; remove from ClientController, users/view, drop column.

### 20. **applications** (column)
- **Usage:** `ClientController`: `$obj->applications = @$requestData['applications']`; `ClientExportService`, `ClientImportService`. Column is redundant with `applications()` relationship (hasMany to applications table).
- **Recommendation:** **Marked for deletion.** Replace reads with `$client->applications()->count()`; remove writes from ClientController; update Export/Import; drop column..

### 21. **followers**
- **Usage:** `ClientController` (saving from request); `ClientExportService`, `ClientImportService`; `Admin::setFollowersAttribute` mutator.
- **Recommendation:** **Do not remove** unless you drop this feature and update/remove all references.

### 22. **preferredintake**
- **Usage:** `Admin` model has `getPreferredIntakeAttribute` / `setPreferredIntakeAttribute`; `ClientExportService`, `ClientImportService`.
- **Recommendation:** **Marked for deletion.** Remove from Admin model (accessor/mutator), ClientExportService, ClientImportService, drop column.

### 23. **is_greview_mail_sent**
- **Usage:** On admins (client record). Used in `ClientMessagingController::isgreviewmailsent()` (read/update), `clients/detail.blade.php` (Google Review button and `data-is_greview_mail_sent`), and `communications.js`.
- **Recommendation:** **Do not remove** if you still use the “Google Review” mail-sent tracking for clients.

---

## Safe to remove (no or negligible code usage)

### 24. **decrypt_password**
- **Usage:** In `Admin` `$fillable`; `ClientImportService` sets `$client->decrypt_password = null`; `StaffController` has commented `//$objAdmin->decrypt_password = ...`. No login or critical path uses it.
- **Recommendation:** **Can remove** after confirming no external/legacy system reads it. Remove from `$fillable` and any remaining references.

### 25. **latitude**, **longitude**
- **Usage:** No references found in codebase.
- **Recommendation:** **Safe to remove** (and drop from `$fillable` if present).

### 26. **lead_status**
- **Usage:** No references found in codebase.
- **Recommendation:** **Safe to remove.**

### 27. **followup_date** (on admins table)
- **Usage:** All `followup_date` usages found are on **actions**, **followups**, or **leads** tables, not on `admins`. No code reads or writes `admins.followup_date`.
- **Recommendation:** **Safe to remove** from admins if the column exists there (likely legacy).

### 28. **wp_customer_id**
- **Usage:** No references found in codebase.
- **Recommendation:** **Safe to remove** (e.g. WordPress integration remnant).

### 29. **is_greview_post**
- **Usage:** No references found in codebase.
- **Recommendation:** **Safe to remove** (likely legacy/unused).

### 30. **prev_visa**
- **Usage:** No references found in codebase.
- **Recommendation:** **Safe to remove** (likely legacy; visa data may now be in `visa_type_id` / related tables).

### 31. **smtp_host**, **smtp_port**, **smtp_enc**, **smtp_user**, **smtp_pas** (SMTP columns on admins)
- **Usage:** `Controller::send_email_template()` uses an object with these SMTP fields, but no caller passes an `Admin` instance as that sender; sending now uses `EmailService` and the `emails` table. So these on `admins` are likely legacy.
- **Recommendation:** **Safe to remove** from admins after confirming no other code or external process uses them.

---

## Partially used / needs decision

### 32. **manual_email_phone_verified**
- **Usage:** `ClientExportService` (is_verified from this flag); `ClientImportService` sets to 1 when importing verified; `clients/detail.blade.php` shows verification badge. One line in `ClientController` is commented: `//$obj->manual_email_phone_verified = ...`
- **Recommendation:** **Do not remove** if you still show “verified” for client email/phone. Only remove if you fully replace with another verification mechanism (e.g. `client_phones` / VerifiedNumber).

---

## Summary table columns: position, telephone, time_zone, user_id, profile_img

These are **columns on the `admins` table**, not separate tables. Usage and data notes:

- **position** — User Management (UserController, users create/edit/index/view). Often empty if not filled.
- **telephone** — Set from country_code; ClientExport/Import; may overlap with phone/att_phone. Can be sparse.
- **time_zone** — UserController::savezone; users/view dropdown. Often empty if never set.
- **user_id** — **Heavily used:** assigned staff, Added by, applications/leads assignee, Ongoing Sheet, documents. **Keep.**
- **profile_img** — **Marked for deletion.** Heavily used; migrate to media store before removal.

Run **`php check_admin_columns.php`** to see non-empty counts. Example run (53,320 admins): **position** 0.5%, **telephone** 0.5%, **time_zone** ~0%, **profile_img** ~0%, **user_id** (non-null > 0) ~0%. So most of these columns are **empty or rarely set** in current data; the code still uses them, so keep the columns unless you migrate usage elsewhere.

---

## Summary table

### Keeping (do not remove)

| Column | Notes |
|--------|-------|
| password | Auth required |
| remember_token | Auth required |
| staff_id | Staff code (Staff UI); empty in DB if staff not used |
| office_id | Branch assignment |
| position | **Review later** – staff position; 0.5% filled |
| telephone | Client/staff telephone (or migrate then remove) |
| time_zone | **Review later** – staff timezone; ~0% filled |
| user_id | Client's assigned staff |
| profile_img | **Marked for deletion** – migrate to media store first |
| company_name | **Review later** – company profile / invoices |
| company_website | **Review later** – company profile |
| primary_email | **Marked for deletion** – no data; never saved by profile controller |
| gst_no, gstin, gst_date, is_business_gst | GST/return settings |
| preferredintake | **Marked for deletion** – remove from Admin model, Export/Import services |
| applications (column) | **Marked for deletion** – redundant with applications() relationship |
| followers | Client followers |
| att_email, att_phone | Client/lead contact and search |
| lead_id | Lead conversion and search |
| comments_note | Client notes |
| rating | **Marked for deletion** – not using client rating |
| default_email_id | **Marked for deletion** – was staff default sending email |
| manual_email_phone_verified | Unless replaced by another verification |
| is_greview_mail_sent | Google Review mail-sent flag (clients) |

### Removing (safe to drop after confirmation)

| Column | Notes |
|--------|-------|
| default_email_id | Marked for deletion; drop after removing from UserController, user create/edit views, Admin model |
| primary_email | Marked for deletion; no data; remove from my_profile form, update invoice/receipt/application email templates |
| profile_img | Marked for deletion; migrate images to media store, update ClientController, Export/Import, receipts, agents, partners, profile, header, invoices |
| rating | Marked for deletion; remove from ClientController, users/view |
| preferredintake | Marked for deletion; remove from Admin model, ClientExportService, ClientImportService |
| applications (column) | Marked for deletion; replace with applications()->count(); remove from ClientController, Export/Import |
| lead_status | No code usage |
| followup_date (on admins) | Not used on admins |
| decrypt_password | No critical usage |
| latitude, longitude | No usage |
| wp_customer_id | No usage |
| is_greview_post | No code usage |
| prev_visa | No code usage |
| smtp_host, smtp_port, smtp_enc, smtp_username, smtp_password | Legacy on admins; sending uses emails table |

---

## Recommended next steps

1. **Do not drop** any column marked **Keep** until it is migrated or replaced elsewhere (e.g. company/GST moved to another table).
2. **Only drop** columns marked **Remove** after:
   - Confirming in the actual DB that the column exists on `admins`.
   - Running a migration that only drops those columns and updating `Admin::$fillable` and any accessors/mutators.
3. **applications** (column): Search for any raw SQL or reports using `admins.applications`; if none, you can plan to remove and use `applications()` relationship only.
4. **telephone**: If you decide it’s redundant with `phone`/`att_phone`, migrate data and references first, then remove in a separate step.

If you tell me which columns you want to remove in the first pass, I can draft the migration and model changes for that subset only.
