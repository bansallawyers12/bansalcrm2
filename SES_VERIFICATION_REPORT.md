# AWS SES Integration – Verification Report

**Scope:** AWS SES for all CRM outbound emails and Elite inbound

> **Related docs:** `SES_EMAIL_MIGRATION.md` · `SES_USAGE_FRONTEND_BACKEND.md`

---

## 1. Email Sending Paths Verified

| Location | Method | Mailer Used | Status |
|----------|--------|-------------|--------|
| **AdminController::sendmail** | EmailService::sendEmail() | `ses` | ✅ |
| **Controller::send_email_template** | Mail::mailer('ses')->send(CommonMail) | `ses` | ✅ |
| **Controller::send_compose_template** | Mail::mailer('ses')->send(CommonMail) | `ses` | ✅ |
| **Controller::send_attachment_email_template** | Mail::mailer('ses')->queue(InvoiceEmailManager) | `ses` | ✅ |
| **Controller::send_multipleattachment_email_template** | Mail::mailer('ses')->queue(MultipleattachmentEmailManager) | `ses` | ✅ |
| **Controller::send_multiple_attach_compose** | Mail::mailer('ses')->queue(MultipleattachmentEmailManager) | `ses` | ✅ |
| **CronJob::send_attachment_email_template** | Mail::mailer('ses')->queue(InvoiceEmailManager) | `ses` | ✅ |
| **SignatureService** (2 places) | Mail::mailer('ses')->send() | `ses` | ✅ |
| **DocumentSignatureController** | Mail::mailer('ses')->send() | `ses` | ✅ |
| **SignatureDashboardController** | Mail::mailer('ses')->send() | `ses` | ✅ |
| **ClientMessagingController** (verify + Google review) | Mail::mailer('ses')->send() | `ses` | ✅ |
| **PublicDocumentController** | Mail::mailer('ses')->raw() | `ses` | ✅ |
| **VisaExpireReminderEmail** | Mail::mailer('ses')->send(CommonMail) | `ses` | ✅ |
| **OutlookController** | Mail::mailer('ses') / `ses_elite` | `ses` / `ses_elite` | ✅ |

---

## 2. Queued Mailables

| Mailable | mailer() in build() | Notes |
|----------|---------------------|-------|
| **InvoiceEmailManager** | `->mailer('ses')` | ✅ Uses SES when job runs |
| **MultipleattachmentEmailManager** | `->mailer('ses')` | ✅ Uses SES when job runs |

---

## 3. Configuration Summary

| Setting | Value |
|---------|-------|
| Default mailer | `MAIL_MAILER=ses` |
| Credentials | `AWS_ACCESS_KEY_ID` + `AWS_SECRET_ACCESS_KEY` |
| Sender list | `SesSenderService` + SES `listEmailIdentities` |
| CRM Outlook mailer | `ses` |
| Elite compose mailer | `ses_elite` |
| Elite inbound | S3 bucket + `ses:sync-inbound` |

---

## 4. Frontend

| Component | Path / class |
|-----------|--------------|
| From dropdown partial | `partials/email-from-ses.blade.php` |
| Sender fetch script | `partials/email-from-ses-script.blade.php` |
| CSS class | `email-from-ses` |
| Senders API | `GET /admin/outlook/senders` |

---

## 5. Commands

```bash
php artisan ses:test          # Verify SES credentials and list senders
php artisan ses:sync-inbound  # Import Elite inbound .eml from S3
php artisan email:debug       # Check from_emails table
```

---

## 6. Test Checklist

```bash
php artisan config:clear
php artisan ses:test
php artisan email:debug
# Send test email from client compose modal
# Send test from Outlook module
# Run php artisan ses:sync-inbound to verify Elite inbound
```
