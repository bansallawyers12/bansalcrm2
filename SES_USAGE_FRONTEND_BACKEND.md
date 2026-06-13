# AWS SES Usage (Frontend + Backend)

This document explains how AWS SES is used across the CRM — configuration, frontend dropdown behavior, every backend send path, S3 archival, and troubleshooting commands.

> **Related docs:** `SES_EMAIL_MIGRATION.md` · `SES_VERIFICATION_REPORT.md`

---

## 1) High-Level Architecture

The system uses **AWS SES** via two named Laravel mailers:

| Mailer | Purpose |
|--------|---------|
| `ses` | All CRM emails — compose, templates, invoices, reminders, signatures, scheduled jobs, Outlook |
| `ses_elite` | Education Elite compose only |

Main flow:

1. Frontend calls `/admin/outlook/senders` (AJAX) to fetch SES verified senders
2. User picks a From address in a compose UI
3. Backend calls `Mail::mailer('ses')` or `Mail::mailer('ses_elite')`
4. Sent metadata is stored in the `emails` table
5. For CRM compose, the full HTML + attachments are also archived to AWS S3

---

## 2) Configuration

### 2.1 Mailer definitions — `config/mail.php`

```php
'default' => env('MAIL_MAILER', 'ses'),

'ses' => [
    'transport' => 'ses',
],

'ses_elite' => [
    'transport' => 'ses',
],
```

Both mailers use credentials from `config/services.php` → `services.ses`.

### 2.2 SES service config — `config/services.php`

```php
'ses' => [
    'key'        => env('SES_KEY', env('AWS_ACCESS_KEY_ID')),
    'secret'     => env('SES_SECRET', env('AWS_SECRET_ACCESS_KEY')),
    'region'     => env('SES_REGION', env('AWS_DEFAULT_REGION', 'ap-southeast-2')),
    'configuration_set' => env('SES_CONFIGURATION_SET', ''),
],

'ses_crm' => [
    'senders'    => env('SES_SENDERS', env('MAIL_FROM_ADDRESS', '')),
    'from_email' => env('SES_FROM_EMAIL', env('MAIL_FROM_ADDRESS', '')),
],

'ses_elite' => [
    'senders'    => env('SES_ELITE_SENDERS', env('SES_ELITE_FROM_EMAIL', 'info@educationelite.com.au')),
    'from_email' => env('SES_ELITE_FROM_EMAIL', 'info@educationelite.com.au'),
],
```

### 2.3 Required `.env` values

```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=ap-southeast-2
SES_REGION=ap-southeast-2

MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your Company Name"

SES_SENDERS=admin@bansaleducation.com.au,admission@bansalimmigration.com.au
SES_FROM_EMAIL=admin@bansaleducation.com.au
```

### 2.4 Optional `.env` values

```env
SES_ELITE_FROM_EMAIL=info@educationelite.com.au
SES_ELITE_SENDERS=info@educationelite.com.au
SES_INBOUND_BUCKET=bansalcrm
SES_INBOUND_PREFIX=emails/incoming/
```

### 2.5 Sender verification requirement

**All From addresses must be verified in AWS SES** (domain or email identity). Domain verification allows any `@yourdomain.com` address; individual emails must be verified as EMAIL_ADDRESS identities or listed in `SES_SENDERS`.

---

## 3) Two Sender-Related Database Tables

| Table | Model | Purpose |
|-------|-------|---------|
| `from_emails` | `App\Models\FromEmail` | Stores configured sender addresses with display names. Used by `EmailService` and `SesSenderService`. |
| `emails` | `App\Models\Email` | Stores every sent/received email record. Used by client Email tab and Outlook Sent folder. |

The `from_emails` **password column is not used** — AWS SES uses IAM credentials from `.env`.

---

## 4) Frontend Integration

### 4.1 Shared "From" dropdown component

**`resources/views/partials/email-from-ses.blade.php`**

```html
<select class="form-control email-from-ses" name="email_from" data-valid="required">
    <option value="">Select From</option>
</select>
```

**`resources/views/partials/email-from-ses-script.blade.php`**

On `DOMContentLoaded`:

1. Finds all `.email-from-ses` selects
2. Fetches `GET /admin/outlook/senders` (JSON)
3. Populates each select; auto-selects `default_from`
4. Shows `"SES unavailable – check AWS credentials"` on fetch error

Included globally in `layouts/admin.blade.php` and `layouts/adminconsole.blade.php`.

### 4.2 Compose partial usage

The `@include('partials.email-from-ses')` is included in client detail, partners, products, invoices, agents, staff, office visits, and payment views. All POST to `POST /sendmail` → `AdminController@sendmail`.

---

## 5) Backend Integration

### 5.1 Core CRM compose — `POST /sendmail`

Uses `EmailService::sendEmail()` → `Mail::mailer('ses')->send()`.

### 5.2 EmailService — `app/Services/EmailService.php`

```
DEFAULT_MAILER = 'ses'
```

### 5.3 Base controller helpers — `app/Http/Controllers/Controller.php`

All template/compose/invoice methods use `Mail::mailer('ses')`.

### 5.4 Mailables

| Mailable | Mailer |
|----------|--------|
| `InvoiceEmailManager` | `->mailer('ses')` in `build()` |
| `MultipleattachmentEmailManager` | `->mailer('ses')` in `build()` |
| `CommonMail`, `ClientVerifyMail`, `GoogleReviewMail` | `Mail::mailer('ses')` at call site |

### 5.5 Outlook backend — `app/Http/Controllers/Admin/OutlookController.php`

**`GET /admin/outlook/senders`** returns verified senders via `SesSenderService`:

1. AWS SES `listEmailIdentities` API (EMAIL_ADDRESS identities)
2. `from_emails` DB table (display names + extra addresses)
3. `SES_SENDERS` env fallback

CRM senders are filtered to `@bansaleducation.com.au` and `admission@bansalimmigration.com.au`.

**`POST /admin/outlook/send`** uses `Mail::mailer('ses')` (CRM) or `Mail::mailer('ses_elite')` (Elite compose).

---

## 6) Other SES Send Paths

- **SignatureService**, **DocumentSignatureController**, **SignatureDashboardController**, **PublicDocumentController**
- **ClientMessagingController** (verify + Google review)
- **VisaExpireReminderEmail**, **CronJob** (queued invoices)

All use `Mail::mailer('ses')`.

---

## 7) S3 Archival After Send

**`app/Services/CrmSentEmailS3Service.php`** — archives CRM compose sends to S3 after successful delivery.

---

## 8) Elite Inbound (SES → S3)

Inbound mail for Education Elite is imported from S3:

```bash
php artisan ses:sync-inbound   # scheduled every minute in app/Console/Kernel.php
```

Configure `SES_INBOUND_BUCKET` and `SES_INBOUND_PREFIX` in `.env`. Legacy webhook `POST /emails/elite` remains for older integrations.

---

## 9) Commands

```bash
php artisan ses:test
php artisan email:debug
php artisan email:debug your@email.com
php artisan ses:sync-inbound
php artisan VisaExpireReminderEmail:daily
```

---

## 10) Troubleshooting

| Symptom | Likely cause | Fix |
|---------|-------------|-----|
| From address rejected | Not verified in SES | Verify domain/email in AWS SES console |
| From dropdown empty | No identities / missing SES_SENDERS | Set `SES_SENDERS`; run `php artisan ses:test` |
| SES unavailable in UI | AWS credentials missing | Set `AWS_ACCESS_KEY_ID` and `AWS_SECRET_ACCESS_KEY` |
| Message not delivered (sandbox) | SES sandbox | Verify recipient or request production access |
| Queued emails not sending | Queue worker stopped | Run `php artisan queue:work` |
