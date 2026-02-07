# SMS System Migration Plan: migrationmanager2 → bansalcrm2

**Goal:** Replace bansalcrm2’s current SMS implementation with the full SMS system from migrationmanager2 (backend, not-picked-call, client-detail SMS button, templates, dashboard, history, webhooks). Keep new/changed files separate where possible.

**Source:** `C:\xampp\htdocs\migrationmanager2`  
**Target:** `C:\xampp\htdocs\bansalcrm2`

### Decisions (user choices)

- **VerifiedNumber:** Option A – leave table in place; do not use for new client-edit verification. New flow uses ClientPhone.is_verified only.
- **Which phones get Verify button:** All ClientPhone types (Personal, Work, Mobile, etc.), not only Personal.
- **Primary phone on client (Admin):** The client record (Admin) has its own `country_code` and `phone`. The plan does **not** add a separate "Verify primary phone" for that record. The Verify button appears only on **ClientPhone** rows. If the primary phone is also stored as a ClientPhone, it can be verified there; if it exists only on Admin and not as a ClientPhone, we do not add a separate verification target for it.
- **Send Message (note-only):** Remove entirely; only "Send SMS" remains on client detail.
- **SMS Management menu:** Visible to **all admins** (not only super admin).
- **Lead verification:** Yes – add phone verification for leads in this migration (see Phase 9.8).
- **Cellcast API:** Use **v3** (base URL `https://cellcast.com.au/api/v3`, success check `meta.code === 200`).

---

## Phase 1: Remove existing SMS in bansalcrm2

### 1.1 Files to delete

| File | Reason |
|------|--------|
| `app/Services/SmsService.php` | Replaced by migrationmanager2 SMS services |
| `app/Http/Controllers/Admin/SmsController.php` | Replaced by AdminConsole SMS controllers |
| `resources/views/sms/form.blade.php` | If exists; replaced by new SMS UI |

### 1.2 Code to remove or replace (do not delete whole files)

- **`app/Helpers/Helper.php`**  
  - Remove method `sendSms()` only (Twilio helper; unused and replaced by new system).

- **`routes/web.php`**  
  - Remove SMS-related routes (lines ~684–699):  
    - `sms.form`, `sms.send`, `sms.status`, `sms.responses`  
    - `verify.is-phone-verify-or-not`, `verify.send-code`, `verify.check-code`  
  - Keep or re-home phone verification later if needed (migrationmanager2 has `PhoneVerificationService`; can add in a follow-up).

- **`app/Http/Controllers/Admin/Client/ClientActivityController.php`**  
  - Replace `SmsService` with `UnifiedSmsManager` and update `notpickedcall()` to use it (see Phase 3).

- **`app/Http/Controllers/Admin/Client/ClientMessagingController.php`**  
  - **Remove the note-only Send Message flow entirely (user agreed).**
  - Delete or strip `sendmsg()` method and the sendmsg route; client detail will only have the new Send SMS button and modal.
- **Client detail view**  
  - Replace the “Send Message” (comment) button with “Send SMS” button and add the Send SMS modal + JS from migrationmanager2 (see Phase 6).

---

## Phase 2: Database and config

### 2.1 New migrations (create in bansalcrm2, copy/adapt from migrationmanager2)

Create under `database/migrations/` with new timestamps so they run after existing ones:

1. **`YYYY_MM_DD_HHMMSS_create_sms_logs_table.php`**  
   - Copy from migrationmanager2 `2025_10_14_201641_create_sms_logs_table.php`.  
   - No changes needed; `client_id` = `admins.id` (client).

2. **`YYYY_MM_DD_HHMMSS_create_sms_templates_table.php`**  
   - Copy from migrationmanager2 `2025_10_14_201706_create_sms_templates_table.php` (including seed defaults).  
   - No changes needed.

3. **`YYYY_MM_DD_HHMMSS_add_sms_fields_to_activities_logs_table.php`**  
   - Copy from migrationmanager2 `2025_10_14_201735_add_sms_fields_to_activities_logs_table.php`.  
   - Adds `sms_log_id` (nullable) and `activity_type` (default `'note'`).  
   - If `activity_type` or `sms_log_id` already exist in bansalcrm2, create a migration that only adds the missing column(s).

4. **`YYYY_MM_DD_HHMMSS_add_description_to_sms_templates_table.php`**  
   - Copy from migrationmanager2 `2025_10_15_183908_add_description_to_sms_templates_table.php`.

Run: `php artisan migrate`.

### 2.2 Config

- **`config/services.php`**  
  - **cellcast:** add `base_url`, `sender_id`, `timeout`. **Use Cellcast v3 API** (user confirmed): default `base_url` = `https://cellcast.com.au/api/v3`; success check is `meta.code === 200` (see Phase 3 CellcastProvider). **NOTE:** migrationmanager2 uses v1 API (`https://api.cellcast.com.au/v1`) with `meta.status === 'SUCCESS'` check - you MUST adapt this.  
  - **twilio:** align with migrationmanager2 keys: `account_sid`, `auth_token`, `from`, `timeout` (bansalcrm2 currently has `sid`, `token`, `phone`; keep env names if already in use, map to these keys).

Example cellcast block (v3):

```php
'cellcast' => [
    'api_key' => env('CELLCAST_API_KEY'),
    'base_url' => env('CELLCAST_BASE_URL', 'https://cellcast.com.au/api/v3'),
    'sender_id' => env('CELLCAST_SENDER_ID', 'BANSALIMMI'),
    'timeout' => env('CELLCAST_TIMEOUT', 30),
],
'twilio' => [
    'account_sid' => env('TWILIO_SID'),
    'auth_token' => env('TWILIO_AUTH_TOKEN'),
    'from' => env('TWILIO_PHONE_NUMBER'),
    'timeout' => env('TWILIO_TIMEOUT', 30),
],
```

- **`.env`**  
  - Add if missing: `CELLCAST_BASE_URL`, `CELLCAST_SENDER_ID`, `CELLCAST_TIMEOUT`, and Twilio vars used by config.

---

## Phase 3: Backend – services and models (separate files)

### 3.1 New directories and files

Create and keep under `app/`:

**Contracts**

- `app/Services/Sms/Contracts/SmsProviderInterface.php`  
  - Copy from migrationmanager2 as-is.

**Providers**

- `app/Services/Sms/CellcastProvider.php`  
  - Copy from migrationmanager2 and **adapt for Cellcast v3 API** (user confirmed v3).  
  - Use `config('services.cellcast.base_url')` default **`https://cellcast.com.au/api/v3`** (not v1).  
  - **Success check:** use `meta.code === 200` (v3 response), not `meta.status === 'SUCCESS'` (v1).  
  - Keep `sender_id`, `timeout`, retry logic, and config keys matching `config/services.php`.

- `app/Services/Sms/TwilioProvider.php`  
  - Copy from migrationmanager2.  
  - Uses `config('services.twilio.account_sid')`, `auth_token`, `from`.  
  - Map config keys to bansalcrm2’s env names (e.g. `TWILIO_SID` → `account_sid`) in `config/services.php`.

**Manager**

- `app/Services/Sms/UnifiedSmsManager.php`  
  - Copy from migrationmanager2.  
  - **IMPORTANT:** Guard the `ClientContact::find()` block with `if (class_exists(\App\Models\ClientContact::class) && !empty($context['contact_id']))` to prevent errors since bansalcrm2 doesn't have `ClientContact` model.  
  - Depends on `PhoneValidationHelper`, `SmsLog`, `ActivitiesLog`.  
  - Ensure `ActivitiesLog` has `sms_log_id` and `activity_type` in `$fillable` after migration.

**Helper**

- `app/Helpers/PhoneValidationHelper.php`  
  - Copy from migrationmanager2 as-is (validation, formatForSMS, getProviderForNumber, etc.).

**Models**

- `app/Models/SmsLog.php`  
  - Copy from migrationmanager2.  
  - `client()` → `Admin::class` (bansalcrm2 client = Admin).  
  - `contact()` → Remove this relationship or guard it with `if (class_exists(ClientContact::class))` since bansalcrm2 doesn't have `ClientContact` model. Keep `client_contact_id` column nullable and unused.

- `app/Models/SmsTemplate.php`  
  - Copy from migrationmanager2 as-is.

**ActivitiesLog**

- `app/Models/ActivitiesLog.php`  
  - Add to `$fillable`: `sms_log_id`, `activity_type`.  
  - Add relation: `smsLog()` → `belongsTo(SmsLog::class, 'sms_log_id')`.  
  - Optionally add scope `scopeSms()` and any helper used by activity feed (see migrationmanager2).

---

## Phase 4: Backend – controllers and routes

### 4.1 AdminConsole SMS controllers (new, separate namespace)

Create under `app/Http/Controllers/AdminConsole/Sms/`:

- `SmsController.php` – dashboard, history, show, statistics, checkStatus.
- `SmsSendController.php` – create (form), send (POST), sendFromTemplate, sendBulk (stub).
- `SmsTemplateController.php` – index, create, store, edit, update, destroy, show, active.
- `SmsWebhookController.php` – twilioStatus, twilioIncoming, cellcastStatus, cellcastIncoming (copy from migrationmanager2).

All use `UnifiedSmsManager` where needed; keep middleware `auth:admin`.

### 4.2 Routes

**AdminConsole (features.sms.*)**

- In `routes/adminconsole.php`:  
  - Add a **features** group so route names match migrationmanager2:  
    - `Route::prefix('features')->name('features.')->group(...)`  
  - Inside it, add the SMS group (same as migrationmanager2):
    - Dashboard: `adminconsole.features.sms.dashboard`
    - History: `adminconsole.features.sms.history`, `adminconsole.features.sms.history.show`
    - Statistics: `adminconsole.features.sms.statistics`
    - Status: `adminconsole.features.sms.status.check`
    - Send: `adminconsole.features.sms.send.create`, `adminconsole.features.sms.send`, `adminconsole.features.sms.send.template`, `adminconsole.features.sms.send.bulk`
    - Templates: resource `adminconsole.features.sms.templates` + `adminconsole.features.sms.templates.active`

**Webhooks (no auth)**

- Create `routes/sms.php` (or add to `web.php` in a dedicated group):  
  - `POST webhooks/sms/twilio/status`, `webhooks/sms/twilio/incoming`, `webhooks/sms/cellcast/status`, `webhooks/sms/cellcast/incoming`.  
- Register in `bootstrap/app.php`: load `routes/sms.php` with `web` middleware (same as migrationmanager2’s RouteServiceProvider mapSmsRoutes).

**Remove from web.php**

- All old SMS and verify routes (Phase 1.2).

---

## Phase 5: Frontend – AdminConsole SMS views (separate views)

### 5.1 Layout and include path

- migrationmanager2 uses `layouts.crm_client_detail` and `@include('../Elements/CRM/setting')`.  
- bansalcrm2 AdminConsole uses `layouts.adminconsole` and `Elements/Admin/setting`.  
- So every copied view must be changed to:  
  - `@extends('layouts.adminconsole')`  
  - Sidebar: use `@include('../Elements/Admin/setting')` (or the path used by other AdminConsole pages in bansalcrm2).

### 5.2 New view files

Create under `resources/views/AdminConsole/features/sms/` (new directory):

- `dashboard.blade.php` – from migrationmanager2; fix layout and include; route names are already `adminconsole.features.sms.*`.
- `history/index.blade.php` – idem.
- `history/show.blade.php` – idem.
- `send/create.blade.php` – idem.
- `templates/index.blade.php` – idem.
- `templates/create.blade.php` – idem.
- `templates/edit.blade.php` – idem.

All route names in these views should stay `adminconsole.features.sms.*`.

---

## Phase 6: Frontend – Client detail SMS button and modal

### 6.1 Client detail blade

- In `resources/views/Admin/clients/detail.blade.php`:
  - Replace the “Send Message” (comment) icon/link with the “Send SMS” button:  
    - e.g. `<a href="javascript:;" class="send-sms-btn" data-client-id="..." data-client-name="..." title="Send SMS"><i class="fas fa-sms"></i></a>` in the same place as the current message icon (e.g. in `author-mail_sms` div).
  - Add the full **Send SMS modal** (form with phone dropdown, template dropdown, message textarea, character/parts count, submit button) from migrationmanager2’s client detail.  
  - Ensure the form posts via AJAX to `route('adminconsole.features.sms.send')` (POST) with `client_id`, `phone`, `message`.

### 6.2 Fetch phone numbers

- migrationmanager2 uses `fetchClientContactNo` returning `clientContacts` with `country_code`, `phone`, `contact_type`.  
- bansalcrm2 already has `ClientMessagingController::fetchClientContactNo` returning `clientContacts` with `client_country_code`, `client_phone`, `contact_type`.  
- In the **SMS modal JS**, when building the phone dropdown, use the bansalcrm2 response shape:  
  - `contact.client_country_code`, `contact.client_phone`, `contact.contact_type`;  
  - build `fullPhone = (contact.client_country_code || '') + (contact.client_phone || '')` and use that as option value; label can be `contact_type + ': ' + fullPhone`.

### 6.3 SMS modal JavaScript

- Copy the SMS block from migrationmanager2 client detail (open modal, load phones, load templates, template change handler, character counter, form submit).  
- Fix:
  - Phone fetch URL: keep existing bansalcrm2 endpoint (e.g. `url("/clients/fetchClientContactNo")` or same as current client detail).
  - Response parsing: use `client_country_code` and `client_phone` as above.
  - Template and send URLs: `route('adminconsole.features.sms.templates.active')` and `route('adminconsole.features.sms.send')`.  
- Optional: add `ClientDetailConfig` (or equivalent) for `staffName`, `matterNumber`, `officePhone`, `officeCountryCode` if template variables are used; if not present, leave placeholders or omit.

### 6.4 “Send Message” / sendmsg (note-only)

- **Option A:** Remove the old “Send Message” modal and `sendmsg` route entirely; only “Send SMS” remains.  
- **Option B:** Keep the current “Send Message” as a separate “Save note only” action (no SMS), and add “Send SMS” as a second button/modal.  
- Plan recommends **Option A** (single “Send SMS” that sends SMS and logs via UnifiedSmsManager) for parity with migrationmanager2.

---

## Phase 7: Not-picked-call (NP) integration

- In `app/Http/Controllers/Admin/Client/ClientActivityController.php`:
  - Inject `UnifiedSmsManager` instead of `SmsService`.
  - In `notpickedcall()`:
    - Build `$userPhone` from `$userInfo->country_code` and `$userInfo->phone` (same as now).
    - Call `$this->smsManager->sendSms($userPhone, $message, 'notification', ['client_id' => $data['id']])`.
    - Use the returned `success` to set the response message:  
      - If `$result['success']`: “Call not picked. SMS sent successfully!”  
      - Else: “Call not picked. SMS failed to send.”  
    - Do not create a separate ActivitiesLog for “call not picked” when SMS is sent; UnifiedSmsManager already creates the activity log (and SmsLog). So remove the manual `ActivitiesLog::create` that was used for “Call not picked. SMS sent successfully!” when `not_picked_call == 1`.

---

## Phase 8: Menu and navigation

- In `resources/views/Elements/Admin/setting.blade.php`:  
  - Add an “SMS Management” link (same as migrationmanager2):  
    - Route: `adminconsole.features.sms.dashboard`.  
    - **Visible to all admins** (user choice). Set active when `Route::currentRouteName()` starts with `adminconsole.features.sms.`.

---

## Phase 9: Phone verification (same as migrationmanager2, adapted for ClientPhone)

Make phone verification work like migrationmanager2: OTP sent via UnifiedSmsManager, rate limiting, expiry, and verified state stored per phone record. migrationmanager2 uses **ClientContact**; bansalcrm2 uses **ClientPhone** and has no ClientContact. The flow is adapted to use **client_phone_id** and to update **ClientPhone** when verified.

### 9.1 Database

- **Add columns to `client_phones`:**  
  - `is_verified` (boolean, default false)  
  - `verified_at` (timestamp, nullable)  
  - `verified_by` (unsignedBigInteger, nullable)  
  - Migration: `add_verification_fields_to_client_phones_table.php`

- **Create `phone_verifications` table** (same structure as migrationmanager2 but keyed by ClientPhone):  
  - Copy migrationmanager2’s `2025_10_04_192020_create_phone_verifications_table.php`.  
  - Replace `client_contact_id` with `client_phone_id` (unsignedBigInteger, nullable, references client_phones.id).  
  - Add `lead_id` (unsignedBigInteger, nullable, references leads.id) for lead verification (Phase 9.8).  
  - Keep: `client_id` (nullable for lead rows), `phone`, `country_code`, `otp_code`, `otp_sent_at`, `otp_expires_at`, `is_verified`, `verified_at`, `verified_by`, `attempts`, `max_attempts`.  
  - Indexes: `client_phone_id`, `lead_id`, `otp_code`, `['phone','country_code']`, `otp_expires_at`.

### 9.2 Models

- **`app/Models/PhoneVerification.php`**  
  - Copy from migrationmanager2 and adapt:  
  - Use `client_phone_id` instead of `client_contact_id`.  
  - Relation `clientPhone()` → `belongsTo(ClientPhone::class)`.  
  - Keep `client()`, `verifier()`, scopes (`active`, `expired`, `forPhone`), and helpers (`isExpired`, `canAttempt`, `incrementAttempts`, `generateOTP`).  
  - `scopeForPhone` stays the same (phone + country_code).

- **`app/Models/ClientPhone.php`**  
  - Add to `$fillable`: `is_verified`, `verified_at`, `verified_by`.  
  - Add `$casts`: `is_verified` => boolean, `verified_at` => datetime.  
  - Add relation: `verifications()` → `hasMany(PhoneVerification::class)`.  
  - Add helpers (like migrationmanager2’s ClientContact): `isAustralianNumber()` (use `client_country_code === '+61'` or normalized), `needsVerification()`, `isPlaceholderNumber()` (use PhoneValidationHelper or same logic), `canVerify()`.

### 9.3 Service

- **`app/Services/Sms/PhoneVerificationService.php`**  
  - Copy from migrationmanager2 and adapt for ClientPhone (no ClientContact):  
  - **sendOTP($clientPhoneId):** Load `ClientPhone::findOrFail($clientPhoneId)`. Validate placeholder using `PhoneValidationHelper::isPlaceholderNumber()`. Validate Australian using `$clientPhone->isAustralianNumber()` (checks `client_country_code === '+61'` or normalized). Rate limiting: use `PhoneVerification::forPhone($clientPhone->client_phone, $clientPhone->client_country_code)` for last hour count. Generate OTP, invalidate previous unverified OTPs for this `client_phone_id`, create `PhoneVerification` with `client_phone_id`, `client_id`, `phone` = `client_phone`, `country_code` = `client_country_code`, **OTP expires in 5 minutes** (same as migrationmanager2). Send SMS via `$this->smsManager->sendSms($fullNumber, $message, 'verification', ['client_id' => $clientPhone->client_id])` (no contact_id in bansalcrm2). On SMS failure delete the new verification and return error.  
  - **verifyOTP($clientPhoneId, $otpCode):** Find latest unverified `PhoneVerification` for `client_phone_id`. Check expired, check attempts, compare OTP; on success update verification and **update ClientPhone** `is_verified` = true, `verified_at` = now(), `verified_by` = Auth::id().  
  - **canResendOTP($clientPhoneId):** Same logic as migrationmanager2, keyed by `client_phone_id`. Check last sent time and enforce 30-second cooldown.  
  - **canSendOTP($phone, $countryCode):** Rate limit by phone + country_code (same as migrationmanager2, e.g., max 3 OTP attempts per hour).  
  - Remove any `ClientContact::find` usage; use ClientPhone only.

### 9.4 Controller and routes

- **`app/Http/Controllers/Admin/Client/PhoneVerificationController.php`** (or under `Admin` if you prefer; migrationmanager2 uses CRM namespace).  
  - Copy from migrationmanager2’s `PhoneVerificationController`.  
  - **sendOTP:** Validate `client_phone_id` and `exists:client_phones,id`; call `$this->verificationService->sendOTP($request->client_phone_id)`.  
  - **verifyOTP:** Validate `client_phone_id`, `otp_code` (size 6); call `verifyOTP($request->client_phone_id, $request->otp_code)`.  
  - **resendOTP:** Validate `client_phone_id`; check `canResendOTP`; call `sendOTP`.  
  - **getStatus($clientPhoneId):** Load `ClientPhone::find($clientPhoneId)`; return JSON `is_verified`, `verified_at`, `needs_verification` (e.g. Australian and not verified).  
  - Middleware: `auth:admin`.

- **Routes** (e.g. in `routes/clients.php` or `web.php` under client group):  
  - `POST .../phone-verification/send-otp` → sendOTP (body: `client_phone_id`)  
  - `POST .../phone-verification/verify-otp` → verifyOTP (body: `client_phone_id`, `otp_code`)  
  - `POST .../phone-verification/resend-otp` → resendOTP (body: `client_phone_id`)  
  - `GET .../phone-verification/status/{clientPhoneId}` → getStatus  
  - Name them e.g. `clients.phone.sendOTP`, `clients.phone.verifyOTP`, `clients.phone.resendOTP`, `clients.phone.status`.

### 9.5 Client edit UI

- **`resources/views/Admin/clients/edit.blade.php`**  
  - Replace the current “Verify” flow (btn-verify phone_verified, verifyphonemodal, verify.send-code / verify.check-code) with the migrationmanager2-style flow:  
  - Each ClientPhone row (all contact types): show “Verify” button that passes **client_phone_id** (e.g. `data-client-phone-id="{{ $clientphone->id }}"`).  
  - Modal: “Send code” calls send-otp with `client_phone_id`; show “Enter OTP” and “Verify” that call verify-otp; optional “Resend” with 30s cooldown (resend-otp).  
  - On success: close modal, refresh or update the row to show verified badge (read from `ClientPhone.is_verified` or status endpoint).  
  - Use the same UX as migrationmanager2 (send OTP → enter code → verify; resend cooldown; expiry message).  
  - Verified state: show green check next to the phone when `ClientPhone.is_verified` is true (or from getStatus).  
  - Remove or stop using: `verify.send-code`, `verify.check-code`, `VerifiedNumber` for this flow.  
  - Ensure `verify_phone_number` / `verification_code` and any old config (verifySendCode, verifyCheckCode) are replaced by the new endpoints and `client_phone_id`.

### 9.6 Old verify routes and VerifiedNumber

- **Remove from web.php:**  
  - `verify.send-code`, `verify.check-code`, `verify.is-phone-verify-or-not` (and any other old verify routes that were in Phase 1.2).  
- **SmsController (Admin):**  
  - When deleting/replacing the old SMS controller (Phase 1), remove `sendVerificationCode`, `verifyCode`, `isPhoneVerifyOrNot`. These are replaced by PhoneVerificationController and ClientPhone.is_verified.  
- **VerifiedNumber:**  
  - Option A: Leave table and model in place but no longer use for client-edit verification; any “verified numbers” list (e.g. old SMS form) can be removed or repurposed.  
  - Option B: Migrate existing VerifiedNumber rows into ClientPhone (set `is_verified` where phone matches a ClientPhone) and then stop using VerifiedNumber.  
  - **User chose Option A;** use ClientPhone.is_verified everywhere in the new flow.

### 9.7 Summary for Phase 9 (clients)

- Add verification fields to `client_phones`; create `phone_verifications` with `client_phone_id`.  
- Add `PhoneVerification` model and update `ClientPhone` (fillable, relations, helpers).  
- Add `PhoneVerificationService` (sendOTP/verifyOTP/canResend/canSend by client_phone_id) and `PhoneVerificationController` (sendOTP, verifyOTP, resendOTP, getStatus).  
- Add routes for send-otp, verify-otp, resend-otp, status.  
- Update client edit: Verify button per ClientPhone (all contact types), modal with send OTP → enter code → verify, verified badge from ClientPhone.is_verified.  
- Remove old verify routes and old SmsController verify methods; stop using VerifiedNumber for this flow.

### 9.8 Lead phone verification (user said yes)

- **Database:** Add to `leads` table: `is_verified` (boolean, default false), `verified_at` (timestamp, nullable), `verified_by` (unsignedBigInteger, nullable). Migration: `add_verification_fields_to_leads_table.php`.  
- **phone_verifications table:** Already includes nullable `lead_id` (unsignedBigInteger, references leads.id). So each row is either for a ClientPhone (`client_phone_id` set) or for a Lead (`lead_id` set). This is included in the create_phone_verifications migration (Phase 9.1).  
- **PhoneVerification model:** Add `lead_id` to fillable; add relation `lead()` → `belongsTo(Lead::class)`.  
- **Lead model:** Add `is_verified`, `verified_at`, `verified_by` to fillable and casts (`is_verified` => boolean, `verified_at` => datetime); add helper methods similar to ClientPhone: `isAustralianNumber()` (checks `country_code === '+61'` or normalized), `needsVerification()` (Australian and not verified and not placeholder), `isPlaceholderNumber()` (use PhoneValidationHelper).  
- **PhoneVerificationService:** Add `sendOTPForLead($leadId)` and `verifyOTPForLead($leadId, $otpCode)`. Load Lead by id; use `$lead->country_code` and `$lead->phone` for building the full number and sending OTP; create PhoneVerification with `lead_id`, `client_phone_id` = null, `client_id` = null; on success update `Lead::is_verified`, `verified_at`, `verified_by`. Rate limiting and expiry logic same as client (max 3 per hour, 30-second resend cooldown, **5-minute OTP expiry** - same as migrationmanager2). **Note:** If you want to support `att_phone` / `att_country_code` (second number), add a second Verify button or a separate flow; this plan assumes only primary phone is verifiable.  
- **PhoneVerificationController** (or a dedicated Lead controller like `app/Http/Controllers/Admin/Lead/LeadPhoneVerificationController.php`): Add endpoints that accept `lead_id`: e.g. `POST .../phone-verification/lead/send-otp`, `POST .../phone-verification/lead/verify-otp`, `POST .../phone-verification/lead/resend-otp`, `GET .../phone-verification/lead/status/{leadId}`. Validate `lead_id` exists in leads table. Middleware: `auth:admin`.  
- **Lead edit view:** Add "Verify" button for the lead’s primary phone (and optionally for att_phone if desired). Reuse same OTP modal pattern: send OTP → enter code → verify; show verified badge when `Lead.is_verified` is true.  
- **Routes:** Register lead verification routes in `routes/web.php` or a leads route file under the same auth group as client verification. Name them e.g. `leads.phone.sendOTP`, `leads.phone.verifyOTP`, `leads.phone.resendOTP`, `leads.phone.status`.

---

## Phase 10: Cleanup and checks

- Remove `App\Services\SmsService` from anywhere it’s referenced (only `ClientActivityController` and old routes after this plan).  
- Remove `App\Http\Controllers\Admin\SmsController` and any references (including verify methods; verification is now in PhoneVerificationController).  
- Ensure no remaining references to `sms.form`, `sms.send` (old), or old verify routes (`verify.send-code`, `verify.check-code`, etc.).  
- Search for `SmsService` and replace with `UnifiedSmsManager` only where we intend to send SMS; verification uses `UnifiedSmsManager` via `PhoneVerificationService`.  
- Run migrations; confirm all tables are created without errors.
- **Test SMS sending:** Send a test SMS from client detail, from NP (not-picked-call), and from AdminConsole SMS send/template; check SmsLog and ActivitiesLog are populated correctly.  
- **Test phone verification on client edit:** Send OTP to a ClientPhone, verify OTP, check resend cooldown works; confirm ClientPhone shows verified badge when `is_verified` = true.
- **Test phone verification on lead edit:** Send OTP to a Lead phone, verify OTP; confirm Lead shows verified badge when `is_verified` = true.
- **Check webhooks:** Verify webhook routes are accessible (without auth) and correctly update SMS status in database.
- **Check templates:** Ensure templates can be created/edited in AdminConsole and appear in the "Send SMS" modal dropdowns.

---

## File checklist (summary)

**Delete:**  
- `app/Services/SmsService.php`  
- `app/Http/Controllers/Admin/SmsController.php`  
- `resources/views/sms/form.blade.php` (if present)

**Create (separate):**  
- `app/Services/Sms/Contracts/SmsProviderInterface.php`  
- `app/Services/Sms/CellcastProvider.php`  
- `app/Services/Sms/TwilioProvider.php`  
- `app/Services/Sms/UnifiedSmsManager.php`  
- `app/Services/Sms/PhoneVerificationService.php`  
- `app/Helpers/PhoneValidationHelper.php`  
- `app/Models/SmsLog.php`  
- `app/Models/SmsTemplate.php`  
- `app/Models/PhoneVerification.php`  
- `app/Http/Controllers/AdminConsole/Sms/SmsController.php`  
- `app/Http/Controllers/AdminConsole/Sms/SmsSendController.php`  
- `app/Http/Controllers/AdminConsole/Sms/SmsTemplateController.php`  
- `app/Http/Controllers/AdminConsole/Sms/SmsWebhookController.php`  
- `app/Http/Controllers/Admin/Client/PhoneVerificationController.php`  
- 7 new migrations (sms_logs, sms_templates, activities_logs sms fields, sms_templates description, add_verification_to_client_phones, create_phone_verifications_table with optional lead_id, add_verification_fields_to_leads_table)  
- `resources/views/AdminConsole/features/sms/dashboard.blade.php`  
- `resources/views/AdminConsole/features/sms/history/index.blade.php`  
- `resources/views/AdminConsole/features/sms/history/show.blade.php`  
- `resources/views/AdminConsole/features/sms/send/create.blade.php`  
- `resources/views/AdminConsole/features/sms/templates/index.blade.php`  
- `resources/views/AdminConsole/features/sms/templates/create.blade.php`  
- `resources/views/AdminConsole/features/sms/templates/edit.blade.php`  
- `routes/sms.php` (if not inlined in web.php)

**Edit:**  
- `app/Helpers/Helper.php` (remove `sendSms`)  
- `config/services.php` (cellcast + twilio)  
- `routes/web.php` (remove old SMS/verify routes)  
- `routes/adminconsole.php` (add features.sms group)  
- `routes/clients.php` or `web.php` (add phone verification routes: send-otp, verify-otp, resend-otp, status)  
- `bootstrap/app.php` (load routes/sms.php if created)  
- `app/Models/ActivitiesLog.php` (fillable + smsLog relation)  
- `app/Models/ClientPhone.php` (fillable + casts + relations + isAustralianNumber, needsVerification, canVerify, isPlaceholderNumber)  
- `app/Models/Lead.php` (add is_verified, verified_at, verified_by to fillable/casts; add verification helpers)  
- `app/Http/Controllers/Admin/Client/ClientActivityController.php` (UnifiedSmsManager + notpickedcall)  
- `app/Http/Controllers/Admin/Client/ClientMessagingController.php` (remove or narrow sendmsg)  
- `resources/views/Admin/clients/detail.blade.php` (SMS button + modal + JS)  
- `resources/views/Admin/clients/edit.blade.php` (Verify button per ClientPhone, OTP modal using client_phone_id and new routes)  
- `resources/views/Admin/leads/edit.blade.php` (Verify button for lead primary phone, OTP modal using lead_id and lead verification routes)  
- `resources/views/Elements/Admin/setting.blade.php` (SMS Management link)

---

## Order of application

1. Phase 2 (migrations + config).  
2. Phase 3 (models + services + helper).  
3. Phase 4 (controllers + routes).  
4. Phase 5 (AdminConsole views).  
5. Phase 6 (client detail SMS button + modal + JS).  
6. Phase 7 (notpickedcall).  
7. Phase 8 (menu).  
8. Phase 1 (remove old SMS code and routes).  
9. Phase 9 (phone verification: migrations, service, controller, routes, client edit UI, lead verification and lead edit UI).  
10. Phase 10 (cleanup and test).

---

## Review: Will both SMS systems be similar after this?

**Short answer: Yes.** After the plan, core SMS and phone verification will both be similar to migrationmanager2. Verification is adapted for ClientPhone (bansalcrm2 has no ClientContact).

### What will match migrationmanager2

| Area | Parity |
|------|--------|
| **Sending from client detail** | Same: “Send SMS” button, modal with phone dropdown + templates, POST to unified manager, SmsLog + activity entry. |
| **Not-picked-call** | Same: UnifiedSmsManager, result-based message, activity created by manager (no duplicate log). |
| **Provider selection** | Same: AU (+61) → Cellcast, others → Twilio; PhoneValidationHelper. |
| **Templates** | Same: CRUD in AdminConsole, active list for dropdown, variables in message. |
| **Dashboard & history** | Same: stats, recent activity, history list, single SMS view. |
| **Webhooks** | Same: Twilio/Cellcast status and incoming endpoints. |
| **Config** | Same: cellcast base_url/sender_id/timeout, twilio account_sid/auth_token/from. |
| **Activity timeline** | Same: SMS entries with `activity_type = 'sms'`, `sms_log_id`, full message in description. |
| **Phone verification** | Same flow: send OTP via UnifiedSmsManager, verify OTP, rate limiting, expiry, resend cooldown; verified state on the phone record. Clients: **ClientPhone** (client_phone_id, is_verified on ClientPhone; all contact types). Leads: **Lead** (lead_id, is_verified on Lead; primary phone). See Decisions for primary-phone note. |

### Intended differences (plan choices)

1. **ClientContact vs ClientPhone**  
   - migrationmanager2: SmsLog has `client_contact_id` → `ClientContact`; UnifiedSmsManager uses it for country_code when `contact_id` is in context.  
   - bansalcrm2: has `ClientPhone`, no `ClientContact`.  
   - Plan: keep `client_contact_id` nullable, do not add ClientContact.  
   - **Result:** Same behaviour from a user perspective; we never pass `contact_id` from the client detail form, so country is derived from the phone string.  
   - **Implementation note:** When copying `UnifiedSmsManager`, guard the `ClientContact::find` block so it does not run in bansalcrm2 (e.g. `if (class_exists(\App\Models\ClientContact::class) && !empty($context['contact_id'])) { ... }`). Otherwise the missing model will cause an error.  
   - **Phone verification:** Same flow as migrationmanager2 but keyed by `client_phone_id` and updating `ClientPhone.is_verified` instead of ClientContact.

3. **SmsLog `contact()` relation**  
   - Plan: in bansalcrm2, either remove the `contact()` relation from SmsLog or point it to a dummy/null so it doesn’t reference ClientContact.  
   - **Result:** Same data and behaviour; only the optional relation is adjusted.

### Optional parity not in the plan

4. **Send SMS on company/partner detail**  
   - migrationmanager2 has “Send SMS” on companies detail; **bansalcrm2 does not have company**, so this is **not** in scope. Plan only adds “Send SMS” on **clients** detail. No change needed.

5. **Layout / sidebar**  
   - migrationmanager2: CRM layout + `Elements/CRM/setting`.  
   - bansalcrm2: AdminConsole layout + `Elements/Admin/setting`.  
   - **Result:** Same features, different layout; no functional difference.

### Summary

- **Core SMS (send from client, NP, templates, dashboard, history, providers, webhooks):** Will be **similar** after the plan.  
- **Phone verification:** Will be **similar** after the plan (Phase 9), adapted for ClientPhone; same OTP flow, rate limits, and verified state per phone.  
- **Company/partner detail SMS:** Not applicable; bansalcrm2 has no company. SMS only on clients detail.

Adding the **UnifiedSmsManager ClientContact guard** and the **SmsLog contact()** adjustment to Phase 3 when you implement will prevent errors and keep behaviour aligned with the plan.

---

## Key Implementation Notes

### Critical Guards and Adaptations

1. **UnifiedSmsManager ClientContact Guard** (Phase 3)
   - When copying from migrationmanager2, wrap any `ClientContact::find()` calls in:
   ```php
   if (class_exists(\App\Models\ClientContact::class) && !empty($context['contact_id'])) {
       // ClientContact logic
   }
   ```
   - This prevents fatal errors since bansalcrm2 doesn't have ClientContact model.

2. **SmsLog Model contact() Relationship** (Phase 3)
   - Option A: Remove the `contact()` relationship entirely.
   - Option B: Guard it with `if (class_exists(ClientContact::class))`.
   - The `client_contact_id` column remains nullable and unused.

3. **Cellcast API Version** (Phase 3)
   - migrationmanager2 uses Cellcast API v1 with `meta.status === 'SUCCESS'` check.
   - bansalcrm2 uses Cellcast API v3 with `meta.code === 200` check.
   - When copying `CellcastProvider.php`, update:
     - Base URL to `https://cellcast.com.au/api/v3`
     - Success condition to `$meta['code'] === 200`

4. **Phone Verification Data Model** (Phase 9)
   - migrationmanager2: Uses `ClientContact` for both clients and leads.
   - bansalcrm2: Uses `ClientPhone` for clients (polymorphic: one client has many ClientPhone records) and `Lead` model directly for leads (monomorphic: phone is on leads table).
   - `phone_verifications` table is polymorphic: either `client_phone_id` OR `lead_id` is set, never both.

### Testing Checklist

Before considering the migration complete, verify:

- [ ] SMS sends successfully from client detail page
- [ ] SMS sends successfully from not-picked-call (NP) action
- [ ] SMS sends successfully from AdminConsole send form
- [ ] SMS templates can be created and edited
- [ ] Templates appear in "Send SMS" modal dropdown
- [ ] Australian numbers (+61) use Cellcast provider
- [ ] International numbers use Twilio provider
- [ ] SmsLog records are created for all sent messages
- [ ] ActivitiesLog entries are created with correct `activity_type` = 'sms'
- [ ] Phone verification works on client edit (all ClientPhone types)
- [ ] Phone verification works on lead edit
- [ ] OTP rate limiting prevents spam (max 3 per hour)
- [ ] OTP resend has 30-second cooldown
- [ ] OTP expires after 5 minutes (same as migrationmanager2)
- [ ] Verified badge shows when phone is verified
- [ ] Webhook routes are accessible without authentication
- [ ] SMS Management menu appears in admin sidebar
- [ ] Dashboard shows SMS statistics correctly

### Migration Order Priority

The phases should be executed in this strict order:
1. Phase 2 first (database + config) - foundation for everything
2. Phase 3 next (backend services/models) - core logic
3. Phase 4 (controllers + routes) - API layer
4. Phase 5-8 (frontend + UI) - user interface
5. Phase 1 (cleanup old code) - remove after new system works
6. Phase 9 (phone verification) - can be done incrementally
7. Phase 10 (final testing) - validation

**Do not delete old code (Phase 1) until the new system is fully tested and working.**

---

**End of plan. Apply after approval.**
