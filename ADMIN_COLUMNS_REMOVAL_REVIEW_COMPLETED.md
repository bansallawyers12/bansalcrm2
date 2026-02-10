# Review: Completed Column Removal – Errors and Gaps

## Critical errors (will cause SQL/runtime failure after migration) — **FIXED**

### 1. **LeadController** ~~still writes dropped columns~~ **FIXED**
- Removed: `$obj->profile_img`, `$obj->preferredIntake` (create/update and lead conversion). Upload block removed; assignments replaced with comments.

---

### 2. **StaffController** ~~still requires and saves `profile_img`~~ **FIXED**
- Removed: `profile_img` validation rule, upload logic, and `$obj->profile_img` in store and update.

---

### 3. **AgentController** ~~still saves `profile_img`~~ **FIXED**
- Removed: Upload blocks and `$obj->profile_img` in store and update.

---

### 4. **PartnersController** ~~still saves `profile_img`~~ **FIXED**
- Removed: Upload and `$obj->profile_img` in create/update. Replaced `select('profile_img')` with full admin row; pass `$logo = ''` to `studentinvoice` view.

---

### 5. **ClientReceiptController** ~~still selects `profile_img`~~ **FIXED**
- Removed: `profile_img` from `admins` select in receipt generation.

---

## Medium issues (no crash, but wrong or broken UI)

### 6. **Views still display dropped columns**
- **preferredIntake:** `clients/detail.blade.php`, `clients/edit.blade.php`, `clients/create.blade.php`, `leads/create.blade.php`, `clients/createbkk.blade.php`, `products/detail.blade.php` – form fields and display. After migration value is null; form still submits and LeadController tries to write → see #1.
- **profile_img:** `my_profile.blade.php`, `clients/edit.blade.php`, `clients/create.blade.php`, `leads/create.blade.php`, `layouts/adminconsole.blade.php`, `Elements/Admin/header.blade.php`, `partners/edit.blade.php`, `agents/edit.blade.php`, `staff/edit.blade.php`, `staff/create.blade.php` – image display and file inputs. After migration: null/empty image, upload does nothing (or triggers error if controller still saves).

### 7. **studentinvoice.blade.php** — **FIXED**
- Logo now uses `$logo` (passed from controller as `''`). Image only shown when `@if(!empty($logo))`.

---

## Low / optional

### 8. **ClientHelpers::processFollowers()**
- Method still exists; not called from ClientController (we removed the call). Only used in unit test. No runtime error; can be removed or left as dead code.

### 9. **Return setting (GST) form**
- Form still posts `is_business_gst`, `gstin`, `gst_date`; controller no longer saves them. No error, just no-op.

---

## Migration file

- **Migration:** Correct: drops FK for `default_email_id`, then drops only existing columns. No issues found.

---

## Summary

| Severity | Count | Action |
|----------|--------|--------|
| Critical | 5 (LeadController, StaffController, AgentController, PartnersController, ClientReceiptController) | Remove all writes/selects of dropped columns before or right after running migration. |
| Medium   | Views + studentinvoice | Update views to stop displaying/using preferredIntake and profile_img; fix student invoice logo. |
| Low      | processFollowers, GST form | Optional cleanup. |

**Recommendation:** ~~Fix all critical errors before running `php artisan migrate`~~ **Critical fixes applied.** You can run `php artisan migrate`. Optionally adjust remaining views (preferredIntake/profile_img form fields and display) for consistency.
