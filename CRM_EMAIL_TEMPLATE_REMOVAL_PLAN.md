# CRM Email Template – Removal Plan

**Status:** Plan only – **do not apply yet**  
**Purpose:** Remove the CRM Email Template feature from bansalcrm2. Compose-email flows will no longer have a “Templates” dropdown; users will type subject and message manually.

---

## 1. Scope of removal

| Component | Action |
|-----------|--------|
| **Model** | Delete `App\Models\CrmEmailTemplate` |
| **Table** | Drop `crm_email_templates` (optional migration) |
| **Controller** | Delete `App\Http\Controllers\AdminConsole\CrmEmailTemplateController` |
| **Routes** | Remove CRM Email Template routes from `routes/adminconsole.php` |
| **Views** | Delete `resources/views/AdminConsole/crmemailtemplate/` (index, create, edit) |
| **Sidebar** | Remove “Crm Email Template” link from `resources/views/Elements/Admin/setting.blade.php` |
| **get-templates** | Remove route and `AdminController::gettemplates()` (or return empty JSON) |
| **Compose-email UI** | Remove Templates dropdown and related JS in all affected views |
| **sendmail** | Stop setting `template_id` on MailReport (or always set null) |
| **VisaExpireReminderEmail** | Stop using CrmEmailTemplate; use config or hardcoded subject/body |
| **deleteAction** | No change (generic delete by table name; table will be dropped) |

---

## 2. File-by-file plan

### 2.1 Routes

**File:** `routes/adminconsole.php`

- Remove: `use App\Http\Controllers\AdminConsole\CrmEmailTemplateController;`
- Remove block (lines ~128–134):
  - `Route::get('/crm_email_template', ...)->name('adminconsole.crmemailtemplate.index');`
  - `Route::get('/crm_email_template/create', ...)->name('adminconsole.crmemailtemplate.create');`
  - `Route::post('/crm_email_template/store', ...)->name('adminconsole.crmemailtemplate.store');`
  - `Route::get('/crm_email_template/edit/{id}', ...)->name('adminconsole.crmemailtemplate.edit');`
  - `Route::post('/crm_email_template/edit', ...)->name('adminconsole.crmemailtemplate.update');`

**File:** `routes/web.php`

- Remove (or comment):  
  `Route::get('/get-templates', [AdminController::class, 'gettemplates'])->name('clients.gettemplates');`  
  And the comment on the line above that references get-templates.

---

### 2.2 Admin Console sidebar

**File:** `resources/views/Elements/Admin/setting.blade.php`

- Remove the line that renders the “Crm Email Template” link (around line 28):  
  `<li class="..."><a class="nav-link" href="{{route('adminconsole.crmemailtemplate.index')}}">Crm Email Template</a></li>`
- Remove `adminconsole.crmemailtemplate.index`, `adminconsole.crmemailtemplate.create`, and `adminconsole.crmemailtemplate.edit` from any `Route::currentRouteName()` checks in the same file that include them.

---

### 2.3 Controller and model

**File:** `app/Http/Controllers/AdminConsole/CrmEmailTemplateController.php`  
- **Action:** Delete the entire file.

**File:** `app/Models/CrmEmailTemplate.php`  
- **Action:** Delete the entire file.

---

### 2.4 AdminController (get-templates and sendmail)

**File:** `app/Http/Controllers/Admin/AdminController.php`

1. **Remove or stub `gettemplates()` (around lines 1234–1254)**  
   - Option A: Delete the method and remove the route (see 2.1).  
   - Option B: Keep the route but have the method return empty JSON, e.g.  
     `return response()->json(['subject' => '', 'description' => '']);`  
     and remove any `use` or logic that references `CrmEmailTemplate`.

2. **sendmail() – template_id (around lines 1377–1379)**  
   - Set `template_id` to `null` always, e.g.  
     `$obj->template_id = null;`  
   - Remove the conditional that sets `template_id` from `$requestData['template']`.

---

### 2.5 VisaExpireReminderEmail command

**File:** `app/Console/Commands/VisaExpireReminderEmail.php`

- Remove: `use App\Models\CrmEmailTemplate;`
- Replace the block that uses `CrmEmailTemplate::where('id', 35)` (around lines 88–112) with one of:
  - **Option A:** Hardcode subject and body (with placeholders like `{Client First Name}`, `{Visa Valid Upto}`, `{Company Name}`) in the command or in `config/` (e.g. `config/visa_reminder.php`).
  - **Option B:** Read from a different source (e.g. `email_templates` table or config key) if you have another template system.
- Keep the rest of the flow (replace placeholders, call `send_compose_template`, update `is_visa_expire_mail_sent`) unchanged.

---

### 2.6 Compose-email views (remove Templates dropdown and getTemplates URL)

For each file below:

1. Remove the **Templates** dropdown: the `<select name="template">` (or equivalent) and its `<option>` list built from `CrmEmailTemplate::all()` or `CrmEmailTemplate::orderBy('id','desc')->get()`.
2. Remove any **JavaScript** that:
   - Calls `/get-templates` or `getTemplates` URL to load subject/description when a template is selected.
   - Populates subject and message fields from the template response.
3. Remove any **config/global JS** that passes `getTemplates` URL into the page (e.g. `getTemplates: '{{ url("/get-templates") }}'` or similar).

**Blade files to update:**

| # | File | What to remove / change |
|---|------|--------------------------|
| 1 | `resources/views/Admin/clients/detail.blade.php` | Templates dropdown (around 2148–2153), `getTemplates` in JS config (around 2908). |
| 2 | `resources/views/Admin/clients/index.blade.php` | Templates dropdown (around 402–406), `url: '.../get-templates'` (around 742). |
| 3 | `resources/views/Admin/clients/addclientmodal.blade.php` | Templates dropdown (around 942–946). |
| 4 | `resources/views/Admin/partners/detail.blade.php` | Templates dropdown (around 2606–2610), `getTemplates` (around 3113). |
| 5 | `resources/views/Admin/partners/index.blade.php` | Templates dropdown (around 311–315), `url` (around 519). |
| 6 | `resources/views/Admin/partners/inactive.blade.php` | Templates dropdown (around 309–313), `url` (around 517). |
| 7 | `resources/views/Admin/partners/addpartnermodal.blade.php` | Templates dropdown (around 1086–1090). |
| 8 | `resources/views/Admin/agents/detail.blade.php` | Templates dropdown (around 454–458), `url` (around 856, 874). |
| 9 | `resources/views/Admin/agents/active.blade.php` | Templates dropdown (around 180–184), `url` (around 400). |
| 10 | `resources/views/Admin/users/view.blade.php` | Templates dropdown (around 452–456), `url` (around 991, 1009). |
| 11 | `resources/views/Admin/products/detail.blade.php` | Templates dropdown (around 330–334), `url` (around 715, 733). |
| 12 | `resources/views/Admin/products/index.blade.php` | `url` (around 362). |
| 13 | `resources/views/Admin/products/addproductmodal.blade.php` | Templates dropdown (around 819–823). |
| 14 | `resources/views/Admin/invoice/show.blade.php` | Templates dropdown (around 660–664), `url` (around 931). |
| 15 | `resources/views/Admin/invoice/unpaid.blade.php` | Templates dropdown (around 323–327), `url` (around 485). |
| 16 | `resources/views/Admin/invoice/paid.blade.php` | Templates dropdown (around 238–242). |
| 17 | `resources/views/Admin/account/payment.blade.php` | Templates dropdown (around 142–146). |

**JS / built assets:**

| # | File | What to remove / change |
|---|------|--------------------------|
| 18 | `public/js/pages/admin/client-detail/email-handlers.js` | Remove or stub logic that uses `getTemplates` URL (around 133, 166). |
| 19 | `public/js/pages/admin/partner-detail/notes-contact-handlers.js` | Remove or stub logic that uses `getTemplates` (around 529, 546). |
| 20 | `resources/js/pages/admin/account.js` | Remove or stub `templatesUrl` / `getTemplates` (around 34). |
| 21 | `public/build/assets/account-CccHk4Jb.js` | Rebuild from source after changing `resources/js/pages/admin/account.js` (do not edit this file by hand). |

If other Blade or JS files reference `CrmEmailTemplate`, `get-templates`, or `getTemplates`, add them to this list and apply the same pattern (remove dropdown + template-loading JS).

---

### 2.7 AdminConsole views (delete)

**Directory:** `resources/views/AdminConsole/crmemailtemplate/`

- Delete: `index.blade.php`
- Delete: `create.blade.php`
- Delete: `edit.blade.php`

---

### 2.8 Database

**Option A – Migration to drop table (recommended if you want a clean schema)**

- Create a new migration, e.g. `database/migrations/YYYY_MM_DD_HHMMSS_drop_crm_email_templates_table.php`.
- In `up()`: `Schema::dropIfExists('crm_email_templates');`
- In `down()`: recreate the table and columns to match the previous structure (or leave `down()` empty if you do not need rollback).

**Option B – Leave table in place**

- Do not create a migration; the table will remain but no code will use it. You can drop it later in a separate cleanup.

**MailReport.template_id**

- No migration required. Leave the column; `sendmail` will set it to `null`. Optionally add a comment in code that `template_id` is deprecated/unused.

---

### 2.9 Other references (no structural change)

- **`database/migrations/2025_12_28_091723_fix_all_primary_keys_and_sequences.php`**  
  References `crm_email_templates` in a list. Leave as-is (historical migration) or add a comment that the table has been dropped in a later migration.

- **`database/migrations/2025_12_29_080000_drop_empty_unused_tables.php`**  
  Comment mentions `crm_email_templates`. No change required unless you want to note that this table is now dropped elsewhere.

- **`app\Models\MailReport.php`**  
  Keeps `template_id` in `$fillable`. No change required.

---

## 3. Order of implementation (recommended)

1. **VisaExpireReminderEmail** – Replace CrmEmailTemplate usage with config/hardcoded content so cron keeps working.
2. **Compose-email views and JS** – Remove Templates dropdown and get-templates usage so no front end calls a removed API.
3. **Routes** – Remove `/get-templates` and Admin Console CRM Email Template routes.
4. **AdminController** – Remove or stub `gettemplates()`; set `template_id = null` in `sendmail()`.
5. **Sidebar** – Remove “Crm Email Template” link and related route checks in `setting.blade.php`.
6. **Controller and model** – Delete `CrmEmailTemplateController` and `CrmEmailTemplate` model.
7. **Views** – Delete `AdminConsole/crmemailtemplate/` (index, create, edit).
8. **Database** – Run migration to drop `crm_email_templates` (if using Option A).
9. **Build assets** – Run `npm run build` or `yarn build` (or equivalent) so `public/build/` reflects JS changes.
10. **Smoke test** – Compose email from client/partner/agent/product/invoice/account; run VisaExpireReminderEmail once; open Admin Console and confirm no Crm Email Template link and no JS errors.

---

## 4. Testing checklist (after applying)

- [ ] Admin Console: “Crm Email Template” no longer appears in sidebar; direct URL `/adminconsole/crm_email_template` returns 404 or redirect.
- [ ] Compose email (client detail, partners, agents, users, products, invoices, account): form loads without Templates dropdown; subject and message are empty; sending email works and does not call `/get-templates`.
- [ ] No JS errors in browser console on compose-email modals/pages.
- [ ] `php artisan visaexpire:reminder` (or equivalent) runs and sends visa reminder emails using the new subject/body source (config/hardcoded).
- [ ] MailReport rows created by sendmail have `template_id` = null.
- [ ] Optional: migration to drop `crm_email_templates` runs successfully; no code references `CrmEmailTemplate` or `crm_email_templates`.

---

## 5. Rollback (if needed)

- Restore deleted files from version control (CrmEmailTemplateController, CrmEmailTemplate model, AdminConsole/crmemailtemplate views).
- Restore routes and sidebar link.
- Restore get-templates route and `AdminController::gettemplates()`.
- Restore Templates dropdown and getTemplates JS in all compose-email views.
- Restore VisaExpireReminderEmail use of CrmEmailTemplate.
- If you dropped the table, restore it from a backup or re-run the original migration that creates `crm_email_templates`.

---

*Document generated as a removal plan only. Do not apply changes until approved.*
