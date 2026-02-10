# Review: Completed Column Removal – Errors and Gaps

## Critical errors (will cause SQL/runtime failure after migration)

### 1. **LeadController** still writes dropped columns
- **Line 318:** `$obj->profile_img = @$profile_img;` → column dropped.
- **Line 319:** `$obj->preferredIntake = @$requestData['preferredIntake'];` → column dropped.
- **Line 418:** `$obj->preferredIntake = @$enqdata->preferredIntake;` (lead conversion).
- **Line 441:** `$obj->profile_img = @$enqdata->profile_img;` (lead conversion).

**Effect:** Creating/updating a lead or converting lead to client will trigger SQL error (unknown column).

---

### 2. **StaffController** still requires and saves `profile_img`
- **Line 84:** Validation `'profile_img' => 'required'`.
- **Lines 102–111, 166–182:** Upload logic and `$obj->profile_img = ...`.

**Effect:** Creating/editing staff will fail (validation and then SQL error).

---

### 3. **AgentController** still saves `profile_img`
- **Lines 117–126, 206–222:** Upload and `$obj->profile_img = ...`.

**Effect:** Creating/editing agents will trigger SQL error.

---

### 4. **PartnersController** still saves `profile_img`
- **Lines 234–240, 416–431:** Upload and `$obj->profile_img = ...`.
- **Line 2207:** `DB::table('admins')->select('profile_img')->where('id',1)->first();` → column no longer exists.

**Effect:** Partner create/edit and the code at 2207 will trigger SQL error.

---

### 5. **ClientReceiptController** still selects `profile_img`
- **Line 472:** `$admin = DB::table('admins')->select(..., 'profile_img', ...)->...`

**Effect:** Receipt generation will trigger SQL error.

---

## Medium issues (no crash, but wrong or broken UI)

### 6. **Views still display dropped columns**
- **preferredIntake:** `clients/detail.blade.php`, `clients/edit.blade.php`, `clients/create.blade.php`, `leads/create.blade.php`, `clients/createbkk.blade.php`, `products/detail.blade.php` – form fields and display. After migration value is null; form still submits and LeadController tries to write → see #1.
- **profile_img:** `my_profile.blade.php`, `clients/edit.blade.php`, `clients/create.blade.php`, `leads/create.blade.php`, `layouts/adminconsole.blade.php`, `Elements/Admin/header.blade.php`, `partners/edit.blade.php`, `agents/edit.blade.php`, `staff/edit.blade.php`, `staff/create.blade.php` – image display and file inputs. After migration: null/empty image, upload does nothing (or triggers error if controller still saves).

### 7. **studentinvoice.blade.php**
- **Line 32:** `<img ... src=".../{{$admin->profile_img}}" />` – after migration `profile_img` is missing; can cause broken image or notice if strict.

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

**Recommendation:** Fix all critical errors before running `php artisan migrate`, then adjust views and student invoice as needed.
