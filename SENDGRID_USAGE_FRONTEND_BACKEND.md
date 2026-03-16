# SendGrid Usage (Frontend + Backend)

This document explains how SendGrid is used across the CRM — configuration, frontend dropdown behavior, every backend send path, S3 archival, and troubleshooting commands.

> **Related docs:** `SENDGRID_EMAIL_MIGRATION.md` · `SENDGRID_VERIFICATION_REPORT.md`

---

## 1) High-Level Architecture

The system uses **SendGrid SMTP** via two named Laravel mailers:

| Mailer | Purpose |
|--------|---------|
| `sendgrid` | All CRM emails — compose, templates, invoices, reminders, signatures, scheduled jobs |
| `sendgrid_outlook` | Admin Outlook module sends only |

Main flow:

1. Frontend calls `/admin/outlook/senders` (AJAX) to fetch SendGrid verified senders
2. User picks a From address in a compose UI
3. Backend calls `Mail::mailer('sendgrid')` or `Mail::mailer('sendgrid_outlook')`
4. Sent metadata is stored in the `emails` table
5. For CRM compose, the full HTML + attachments are also archived to AWS S3

---

## 2) Configuration

### 2.1 Mailer definitions — `config/mail.php`

```php
'sendgrid' => [
    'transport'  => 'smtp',
    'host'       => env('MAIL_HOST', 'smtp.sendgrid.net'),
    'port'       => env('MAIL_PORT', 587),
    'username'   => env('MAIL_USERNAME', 'apikey'),
    'password'   => env('MAIL_PASSWORD') ?: env('SENDGRID_API_KEY'),
    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
],

'sendgrid_outlook' => [
    'transport'  => 'smtp',
    'host'       => env('MAIL_HOST2', 'smtp.sendgrid.net'),
    'port'       => env('MAIL_PORT2', 587),
    'username'   => env('MAIL_USERNAME2', 'apikey'),
    'password'   => env('MAIL_PASSWORD2') ?: env('SENDGRID_API_KEY'),
    'encryption' => env('MAIL_ENCRYPTION2', 'tls'),
],
```

Both mailers fall back to the same `SENDGRID_API_KEY` when their own `MAIL_PASSWORD*` env var is not set.

### 2.2 SendGrid service config — `config/services.php`

```php
'sendgrid' => [
    'api_key'    => env('SENDGRID_API_KEY'),
    'base_url'   => env('SENDGRID_BASE_URL', 'https://api.sendgrid.com'),
    'from_email' => env('SENDGRID_FROM_EMAIL', ''),
    'senders'    => env('SENDGRID_SENDERS', ''),  // comma-separated fallback list
],
```

### 2.3 Required `.env` values

```env
# Primary mailer
MAIL_MAILER=sendgrid
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.your_key_here
# OR (MAIL_PASSWORD can be left empty if this is set):
SENDGRID_API_KEY=SG.your_key_here

# Default From address
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your Company Name"

# Outlook module (can share same key, but separate SMTP credentials if needed)
MAIL_HOST2=smtp.sendgrid.net
MAIL_PORT2=587
MAIL_USERNAME2=apikey
MAIL_PASSWORD2=SG.your_key_here
```

### 2.4 Optional `.env` values

```env
SENDGRID_BASE_URL=https://api.eu.sendgrid.com   # EU accounts only
SENDGRID_SENDERS=a@domain.com,b@domain.com      # Fallback if API returns empty
SENDGRID_FROM_EMAIL=default@domain.com          # Default From for Outlook module
```

### 2.5 Sender verification requirement

**All From addresses must be verified in SendGrid** (Settings → Sender Authentication → Single Senders or Domain Authentication). If an address is used but not verified, SendGrid will reject the send with a 403 error.

---

## 3) Two Sender-Related Database Tables

These two tables are distinct — do not confuse them:

| Table | Model | Purpose |
|-------|-------|---------|
| `from_emails` | `App\Models\FromEmail` | Stores configured sender addresses with display names. Used by `EmailService` to resolve the From address at send time. |
| `emails` | `App\Models\Email` | Stores every sent/received email record. Used by client Email tab and Outlook Sent folder. |

The `from_emails` table **password column is no longer used** for authentication — SendGrid uses only the API key from `.env`.

---

## 4) Frontend Integration

### 4.1 Shared "From" dropdown component

Two partials work together to populate every compose form across the CRM:

**`resources/views/partials/email-from-sendgrid.blade.php`**

Renders a bare select element with the class `email-from-sendgrid`:

```html
<select class="form-control email-from-sendgrid" name="email_from" data-valid="required">
    <option value="">Select From</option>
</select>
```

**`resources/views/partials/email-from-sendgrid-script.blade.php`**

On `DOMContentLoaded` it:

1. Finds all `.email-from-sendgrid` selects on the page
2. Fetches `GET /admin/outlook/senders` (JSON)
3. Populates each select with the returned sender list
4. Auto-selects `data.default_from` if it matches a sender
5. Falls back to a single `default_from` option if the list is empty
6. Shows `"SendGrid unavailable – check SENDGRID_API_KEY"` on fetch error

This script is **globally included** in both main layouts:

- `resources/views/layouts/admin.blade.php` (line 566)
- `resources/views/layouts/adminconsole.blade.php` (line 322)

So every admin page automatically gets live sender options without additional setup.

### 4.2 Pages where the compose partial is used

The `@include('partials.email-from-sendgrid')` is included in:

| Page / Modal | View file |
|---|---|
| Client detail | `Admin/clients/detail.blade.php` |
| Client index | `Admin/clients/index.blade.php` |
| Add client modal | `Admin/clients/addclientmodal.blade.php` |
| Partner detail | `Admin/partners/detail.blade.php` |
| Partner index | `Admin/partners/index.blade.php` |
| Add partner modal | `Admin/partners/addpartnermodal.blade.php` |
| Product detail | `Admin/products/detail.blade.php` |
| Product index | `Admin/products/index.blade.php` |
| Add product modal | `Admin/products/addproductmodal.blade.php` |
| Invoice paid/unpaid/show | `Admin/invoice/paid.blade.php`, `unpaid.blade.php`, `show.blade.php` |
| Agent detail/active/inactive | `Admin/agents/detail.blade.php`, `active.blade.php`, `inactive.blade.php` |
| Staff view | `Admin/staff/view.blade.php` |
| Office visits | `Admin/officevisits/index.blade.php` |
| Account payment | `Admin/account/payment.blade.php` |

All of these forms POST to `POST /sendmail` → `AdminController@sendmail`.

### 4.3 Outlook frontend page

**`resources/views/Admin/outlook/index.blade.php`**

- On load: calls `GET /admin/outlook/senders` (same endpoint as the global script above) to refresh its own `#from_email` select
- New message: compose form submits to `POST /admin/outlook/send`
- Save draft: AJAX `POST /admin/outlook/draft`
- Get emails: AJAX `GET /admin/outlook/inbox?folder=sent|inbox|drafts|trash`

The Outlook page has a rich-text editor (`contenteditable` div), formatting ribbon (bold/italic/font/size), attachment support, and a Sent view grouped by sender address.

---

## 5) Backend Integration

### 5.1 Core CRM compose send — `POST /sendmail`

**Route:** `routes/web.php` → `AdminController@sendmail`  
**Controller:** `app/Http/Controllers/Admin/AdminController.php`

Actual execution order:

1. Validate `email_from`, `email_to`, `subject`, `message` — return JSON error if missing
2. Generate PDF attachment if `receipt` (payment receipt) or `invreceipt` (invoice) is in the request, upload to S3, keep temp path for attachment
3. Save `Email` record to `emails` table (`mail_type = 1`, `client_id`, `email_category`, attachments JSON)
4. Attach system `Sent` label + any user-selected labels via `email_labels` pivot
5. Write activity log entry based on `send_context` field:
   - `checklist` → "Checklist Email sent / resent", updates `applications.checklist_sent_at`
   - `email_reminder` → "Email reminder sent", creates `ApplicationReminder` record
   - (default) → "Sent email" with subject/recipient summary
6. **Loop over each recipient** — resolve ID to email address, replace template placeholders (`{Client First Name}`, `{DOB}`, `{Company Name}`)
7. Build final attachment array (checklist files, client documents, uploaded files)
8. Call `EmailService::sendEmail()` → `Mail::mailer('sendgrid')->send()`
9. After first successful send, call `CrmSentEmailS3Service::storeToS3()` to archive HTML + attachments to S3
10. Clean up temp files; return JSON `{status: true}` or redirect

### 5.2 EmailService — `app/Services/EmailService.php`

Central service used by `AdminController` and anywhere a single From address needs to be resolved.

```
DEFAULT_MAILER = 'sendgrid'
```

**`configureMailerForEmail(?string $emailAddress)`**

- If `$emailAddress` provided → look up in `from_emails` table (case-insensitive)
- If not found / null → fall back to `MAIL_FROM_ADDRESS` env, then first active `from_emails` row
- Returns an object with `email` and `display_name`
- Does **not** change any config — just resolves the From address

**`sendEmail($view, $data, $to, $subject, $fromEmailAddress, $attachments, $cc)`**

- Validates `$fromEmailAddress` is non-empty and a valid email
- Looks up in `from_emails` table (allows any valid email if not in DB — for SendGrid verified senders)
- Calls `Mail::mailer('sendgrid')->send($view, $data, function($message) {...})`
- Supports file attachments (array of local paths) and CC array
- Logs success/failure; throws `\Exception` on failure

Email view used: `resources/views/emails/template.blade.php`

### 5.3 Base controller helper methods — `app/Http/Controllers/Controller.php`

These protected methods are inherited by most admin controllers and used for template-driven sends:

| Method | Send mechanism | Mailable used |
|--------|---------------|---------------|
| `send_email_template()` | `Mail::mailer('sendgrid')->to()->send()` | `CommonMail` |
| `send_compose_template()` | `Mail::mailer('sendgrid')->to()->send()` with optional CC | `CommonMail` |
| `send_attachment_email_template()` | `Mail::mailer('sendgrid')->to()->queue()` | `InvoiceEmailManager` |
| `send_multipleattachment_email_template()` | `Mail::mailer('sendgrid')->to()->queue()` | `MultipleattachmentEmailManager` |
| `send_multiple_attach_compose()` | `Mail::mailer('sendgrid')->to()->queue()` | `MultipleattachmentEmailManager` |

All methods call `EmailService::configureMailerForEmail()` first to resolve the From address.

### 5.4 Mailables

| Mailable | File | Mailer source | Notes |
|----------|------|--------------|-------|
| `CommonMail` | `app/Mail/CommonMail.php` | Set at call site via `Mail::mailer('sendgrid')` | General-purpose HTML email; supports single PDF, multiple file, and uploaded file attachments |
| `InvoiceEmailManager` | `app/Mail/InvoiceEmailManager.php` | `->mailer('sendgrid')` in `build()` | Queued; attaches single PDF invoice |
| `MultipleattachmentEmailManager` | `app/Mail/MultipleattachmentEmailManager.php` | `->mailer('sendgrid')` in `build()` | Queued; attaches optional PDF + multiple files |
| `ClientVerifyMail` | `app/Mail/ClientVerifyMail.php` | Set at call site via `Mail::mailer('sendgrid')` | Email verification link; view: `emails.clientverifymail` |
| `GoogleReviewMail` | `app/Mail/GoogleReviewMail.php` | Set at call site via `Mail::mailer('sendgrid')` | Google review invitation |

`InvoiceEmailManager` and `MultipleattachmentEmailManager` also call `EmailService::configureMailerForEmail()` inside `build()` to re-resolve the From address when the queued job actually runs.

### 5.5 Outlook backend — `app/Http/Controllers/Admin/OutlookController.php`

**Routes** (all under `auth:admin` middleware, prefix `/admin/`):

| Method | Route | Named route | Description |
|--------|-------|------------|-------------|
| GET | `/admin/outlook` | `admin.outlook.index` | Render Outlook page |
| GET | `/admin/outlook/senders` | `admin.outlook.senders` | Return verified sender list as JSON |
| GET | `/admin/outlook/debug` | `admin.outlook.debug` | Raw API debug output (dev use) |
| GET | `/admin/outlook/inbox` | `admin.outlook.inbox` | Fetch folder (inbox/sent/drafts/trash) as JSON |
| POST | `/admin/outlook/send` | `admin.outlook.send` | Send email via `sendgrid_outlook` |
| POST | `/admin/outlook/draft` | `admin.outlook.saveDraft` | Save draft to `outlook_draft_emails` table |

**`getVerifiedSenders()` lookup chain:**

1. `GET {base_url}/v3/verified_senders` (primary endpoint)
2. `GET {base_url}/v3/senders` (fallback endpoint)
3. `GET https://api.eu.sendgrid.com/v3/verified_senders` (EU region fallback)
4. `SENDGRID_SENDERS` env var (comma-separated, last resort)

**`send()` method:** uses `Mail::mailer('sendgrid_outlook')->html(...)`, then persists a record to `emails` table (`mail_type = 1`, `type = 'outlook'`) so the Sent tab shows all Outlook sends alongside CRM emails.

---

## 6) Other SendGrid Send Paths

### 6.1 Document Signatures

Three components handle signature-related emails:

**`app/Services/SignatureService.php`**

- `send()` — creates `Signer` records, sends initial signing email to each signer via `Mail::mailer('sendgrid')->send('emails.signature-request', ...)`
- `remind()` — sends a reminder to a signer; enforces max 3 reminders (`reminder_count >= 3` check); uses same `emails.signature-request` template

**`app/Http/Controllers/Admin/Client/DocumentSignatureController.php`**

- `send()` — iterates pending signers, sends signing email directly via `Mail::mailer('sendgrid')` (does not go through `SignatureService::send()`)
- `reminder()` — delegates to `SignatureService::remind()`

**`app/Http/Controllers/CRM/SignatureDashboardController.php`**

- `send()` — sends via `Mail::mailer('sendgrid')` using `emails.signature-request` template (CRM-side signature flow)

**`app/Http/Controllers/PublicDocumentController.php`**

- Sends a plain-text reminder link (no HTML template) via `Mail::mailer('sendgrid')->raw(...)` when a signed document reminder is triggered from the public-facing document page

### 6.2 Client verification and Google review

**`app/Http/Controllers/Admin/Client/ClientMessagingController.php`**

- `emailVerify()` — sends email verification link via `Mail::mailer('sendgrid')->to()->send(new ClientVerifyMail(...))`
- `isgreviewmailsent()` — sends Google review invitation via `Mail::mailer('sendgrid')->to()->send(new GoogleReviewMail(...))`

Both check `EmailService::configureMailerForEmail(null)` before sending (uses env/first active email as From).

### 6.3 Scheduled commands

**`app/Console/Commands/VisaExpireReminderEmail.php`**

- Signature: `php artisan VisaExpireReminderEmail:daily`
- Queries clients whose visa expires in exactly 15 days, sends reminder email
- Has its own `send_compose_template()` method identical to the base controller version
- Uses `Mail::mailer('sendgrid')->to()->send(new CommonMail(...))`
- From address: resolved from first active `from_emails` row via `EmailService::getDefaultEmail()`

**`app/Console/Commands/CronJob.php`**

- Contains `send_attachment_email_template()` that calls `Mail::mailer('sendgrid')->to()->queue(new InvoiceEmailManager(...))`
- Used for scheduled invoice email delivery

---

## 7) S3 Archival After Send

**`app/Services/CrmSentEmailS3Service.php`**

Called by `AdminController::sendmail()` after a successful send (only for the main CRM compose flow, not Outlook or signature sends).

What it does:

1. Builds a full HTML snapshot of the email (From, To, Date, Subject, body)
2. Uploads the HTML to S3 at path `{client_unique_id}/crm_sent/sent/{timestamp}-email.html`
3. Creates a `documents` table record (`doc_type = 'crm_sent'`) linking to the S3 file
4. Sets `emails.uploaded_doc_id` to the new Document ID
5. For each attachment, uploads to S3 at `{client_unique_id}/attachments/...` and creates a `mail_report_attachments` record

Only runs when S3 is configured (checks `filesystems.disks.s3.key` and `bucket`). Skipped silently if S3 is not set up.

---

## 8) Email Template Views

| View | Used by | Description |
|------|---------|-------------|
| `emails/template.blade.php` | `EmailService::sendEmail()` | Main CRM compose HTML wrapper |
| `emails/common.blade.php` | `CommonMail` | General-purpose HTML email |
| `emails/signature-request.blade.php` | `SignatureService`, `DocumentSignatureController`, `SignatureDashboardController` | Document signing request/reminder |
| `emails/invoice.blade.php` | `InvoiceEmailManager` | Invoice PDF email body |
| `emails/clientverifymail.blade.php` | `ClientVerifyMail` | Email verification link |
| `emails/reciept.blade.php` | `AdminController::sendmail` (receipt attachment) | Payment receipt email |
| `emails/studentinvoice.blade.php` | Partner invoices | Student invoice email |

---

## 9) Commands for Verification and Debugging

```bash
# Test SendGrid API connectivity and list all discovered verified senders
php artisan sendgrid:test

# List all active From addresses in the from_emails DB table
php artisan email:debug

# Check a specific From address in the DB
php artisan email:debug your@email.com

# Run visa expiry reminder manually
php artisan VisaExpireReminderEmail:daily
```

For live API debugging (shows raw SendGrid API response):

```
GET /admin/outlook/debug     (must be logged in as admin)
```

---

## 10) Troubleshooting

| Symptom | Likely cause | Fix |
|---------|-------------|-----|
| 403 Forbidden | API key lacks permissions | Check key scope in SendGrid dashboard |
| From address rejected | Sender not verified | Add to SendGrid → Settings → Sender Authentication |
| 535 Auth failed | Wrong username/password in config | Ensure `MAIL_USERNAME=apikey` and `MAIL_PASSWORD` or `SENDGRID_API_KEY` is set |
| From dropdown shows "No SendGrid senders found" | API key not set or wrong region | Set `SENDGRID_API_KEY`; try `SENDGRID_BASE_URL=https://api.eu.sendgrid.com` for EU accounts |
| From dropdown shows "SendGrid unavailable" | `/admin/outlook/senders` request failed | Check API key, run `php artisan sendgrid:test` |
| Queued emails not sending | Queue worker not running | Run `php artisan queue:work` |
| S3 archival silently skipped | S3 not configured | Set `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET` in `.env` |
