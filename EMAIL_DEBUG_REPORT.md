# Email Debug Report (AWS SES)

## Overview

All CRM outbound email uses **AWS SES** via the `ses` and `ses_elite` mailers. The `from_emails` table supplies From address display names; authentication uses IAM credentials in `.env` — not per-mailbox passwords.

> **See also:** `SES_EMAIL_MIGRATION.md` · `SES_USAGE_FRONTEND_BACKEND.md`

---

## Quick Diagnostics

```bash
php artisan config:clear
php artisan ses:test
php artisan email:debug
php artisan email:debug info@bansaleducation.com.au
```

| Command | What it checks |
|---------|----------------|
| `ses:test` | AWS credentials, SES region, verified identities, CRM sender list |
| `email:debug` | `from_emails` table entries and default sender resolution |

---

## Common Errors

### Message rejected / MessageRejected

| Cause | Solution |
|-------|----------|
| From address not verified in SES | Verify domain or email in AWS Console → SES → Verified identities |
| Recipient not verified (sandbox) | Verify recipient or request SES production access |
| Wrong AWS region | Set `SES_REGION` / `AWS_DEFAULT_REGION` to match verified identities |

### From dropdown empty / "SES unavailable"

| Cause | Solution |
|-------|----------|
| Missing AWS credentials | Set `AWS_ACCESS_KEY_ID` and `AWS_SECRET_ACCESS_KEY` in `.env` |
| No verified identities returned | Add addresses to `SES_SENDERS` env fallback |
| IAM permissions | Grant `ses:SendEmail`, `ses:SendRawEmail`, `ses:ListEmailIdentities` |

### AccessDenied on send

IAM user or role lacks SES send permissions. Add `ses:SendEmail` and `ses:SendRawEmail` for the verified identity ARNs.

### Queued emails not sending

Run `php artisan queue:work`. Invoice and multi-attachment templates use `Mail::mailer('ses')->queue()`.

### Elite inbox empty

1. Confirm SES receipt rule writes to S3 (`SES_INBOUND_BUCKET`, `SES_INBOUND_PREFIX`)
2. Run `php artisan ses:sync-inbound`
3. Check `storage/logs/laravel.log` for `ses.inbound.sync` entries

---

## Sender Resolution Flow

1. User selects From in compose UI (`.email-from-ses` dropdown)
2. Dropdown populated from `GET /admin/outlook/senders` → `SesSenderService`
3. On send, `EmailService::configureMailerForEmail()` resolves display name from `from_emails`
4. `Mail::mailer('ses')->send()` delivers via AWS SES API

If the chosen address is not in `from_emails`, send still works when the address is SES-verified — display name falls back to the email address itself.

---

## info@bansaleducation.com.au

Used by login alert emails and available in compose when configured.

| Check | Action |
|-------|--------|
| Not in `from_emails` | Add in Admin Console → Emails (display name only; password unused) |
| Not in SES | Verify in AWS SES console or add to `SES_SENDERS` |
| Send fails | Run `php artisan ses:test` and confirm identity appears |

---

## Code Paths

| Location | Mailer |
|----------|--------|
| `EmailService::sendEmail()` | `ses` |
| `OutlookController::send()` | `ses` / `ses_elite` |
| `Controller` template methods | `ses` |
| `ClientMessagingController` | `ses` |
| `SignatureService` | `ses` |
| `AdminLoginController` (login alerts) | `ses` |
