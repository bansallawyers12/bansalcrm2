# AWS SES Email Migration (SendGrid Replaced)

All CRM outbound emails now send through **AWS SES** instead of SendGrid SMTP. The `from_emails` table is still used for **From address** options (Admin Console → Emails), but authentication uses AWS IAM credentials.

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
```

---

## Sender Verification

**All From addresses must be verified in AWS SES.** Verify domains or individual emails in:

1. **AWS Console** → Amazon SES → Verified identities
2. Add the same addresses to **Admin Console → Emails** for display names (optional)
3. Set `SES_SENDERS` for domain-verified addresses that do not appear as EMAIL_ADDRESS identities

If an address is not verified, SES will reject the send.

---

## What Changed

| Area | Before | After |
|------|--------|-------|
| Client/Partner compose | SendGrid SMTP | AWS SES (`ses` mailer) |
| Email templates | SendGrid | AWS SES |
| Invoices | SendGrid | AWS SES |
| Document signatures | SendGrid | AWS SES |
| Login alerts | SendGrid | AWS SES |
| Outlook (Admin) | SendGrid | AWS SES |
| Education Elite compose | SendGrid / SES | AWS SES (`ses_elite`) |
| Elite inbound | SendGrid Inbound Parse | AWS SES → S3 + `ses:sync-inbound` |

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
