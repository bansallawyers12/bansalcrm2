# AWS SES Email Configuration

All CRM outbound emails send through **AWS SES**. The `from_emails` table is used for **From address** options (Admin Console → Emails), but authentication uses AWS IAM credentials — not per-mailbox passwords.

> **See also:** `SES_USAGE_FRONTEND_BACKEND.md` · `SES_VERIFICATION_REPORT.md`

---

## .env Configuration

Add or update these variables:

```env
# AWS SES (required for all CRM emails)
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=ap-southeast-2
SES_REGION=ap-southeast-2

# Default From (used when no sender is selected)
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your Company Name"

# Fallback From list for compose dropdown (comma-separated, used when SES API list is empty)
SES_SENDERS=admin@bansaleducation.com.au,admission@bansalimmigration.com.au
SES_FROM_EMAIL=admin@bansaleducation.com.au

# Education Elite outbound (same AWS credentials)
SES_ELITE_FROM_EMAIL=info@educationelite.com.au
SES_ELITE_SENDERS=info@educationelite.com.au
EDUCATION_ELITE_MAILER=ses_elite

# Elite inbound (SES receipt rule → S3)
SES_INBOUND_BUCKET=bansalcrm
SES_INBOUND_PREFIX=emails/incoming/
```

---

## Sender Verification

**All From addresses must be verified in AWS SES.** Verify domains or individual emails in:

1. **AWS Console** → Amazon SES → Verified identities
2. Add the same addresses to **Admin Console → Emails** for display names (optional)
3. Set `SES_SENDERS` for domain-verified addresses that do not appear as EMAIL_ADDRESS identities

If an address is not verified, SES will reject the send.

---

## Email Areas Using AWS SES

| Area | Mailer |
|------|--------|
| Client/Partner compose | `ses` |
| Email templates | `ses` |
| Invoices | `ses` |
| Document signatures | `ses` |
| Login alerts | `ses` |
| Outlook (Admin) | `ses` |
| Education Elite compose | `ses_elite` |
| Elite inbound | SES → S3 + `ses:sync-inbound` |

---

## Password Column in `from_emails` Table

The `password` column is **no longer used** for sending. AWS SES uses IAM credentials in `.env`. The table is still used for:

- From address options (dropdown display names)
- Resolving display name at send time

---

## Test Commands

```bash
# Test AWS SES credentials and list verified identities / CRM senders
php artisan ses:test

# Debug email configuration (checks from_emails table)
php artisan email:debug your@email.com

# Import Elite inbound mail from S3
php artisan ses:sync-inbound
```

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Message rejected | Verify sender/domain in AWS SES console |
| From dropdown empty | Set `SES_SENDERS` or verify addresses in SES; run `php artisan ses:test` |
| AccessDenied on send | IAM user needs `ses:SendEmail` and `ses:SendRawEmail` |
| Sandbox mode | Request production access in SES or verify recipient addresses |
| Queued emails not sending | Run `php artisan queue:work` |
| Elite inbox empty | Check `SES_INBOUND_BUCKET` / `SES_INBOUND_PREFIX`; run `php artisan ses:sync-inbound` |

---

## Legacy Inbound Webhook

Legacy inbound webhook `POST /emails/elite` remains for older integrations. Primary Elite inbound is AWS SES receipt rules → S3 → `ses:sync-inbound`.
