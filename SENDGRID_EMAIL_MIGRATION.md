# SendGrid Email Migration (Zoho Replaced)

All CRM emails now send through **SendGrid** instead of Zoho SMTP. The `emails` table is still used for **From address** options (Admin Console → Emails), but authentication uses a single SendGrid API key.

---

## .env Configuration

Add or update these variables:

```env
# SendGrid (required for all CRM emails)
MAIL_MAILER=sendgrid
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key_here
# Or use SENDGRID_API_KEY (MAIL_PASSWORD can be left empty)
SENDGRID_API_KEY=SG.xxxxxxxxxxxxxxxxxxxxxxxx

# Default From (used when no sender is selected)
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your Company Name"

# Optional: EU region
# SENDGRID_BASE_URL=https://api.eu.sendgrid.com
```

---

## Sender Verification

**All From addresses must be verified in SendGrid.** Add your sender emails in:

1. **SendGrid Dashboard** → Settings → Sender Authentication
2. Add Single Senders or verify your domain
3. Add the same addresses to **Admin Console → Emails** for the From dropdown

If an address is not verified, SendGrid may reject the message.

---

## What Changed

| Area | Before | After |
|------|--------|-------|
| Client/Partner compose | Zoho SMTP (`emails` table password) | SendGrid |
| Email templates | Zoho SMTP | SendGrid |
| Invoices | Zoho SMTP | SendGrid |
| Document signatures | Zoho SMTP | SendGrid |
| Login alerts | Zoho SMTP | SendGrid |
| Outlook (Admin) | SendGrid | SendGrid (unchanged) |

---

## Password Column in `emails` Table

The `password` column in the `emails` table is **no longer used** for sending. SendGrid uses the API key in `.env`. The table is still used for:

- From address options (dropdown)
- Display name per sender

---

## Test Commands

```bash
# Test SendGrid API and list verified senders
php artisan sendgrid:test

# Debug email configuration (checks emails table)
php artisan email:debug your@email.com
```

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| 403 Forbidden | Check API key permissions in SendGrid |
| From address rejected | Verify sender in SendGrid Sender Authentication |
| 535 Auth failed | Ensure `MAIL_USERNAME=apikey` and `MAIL_PASSWORD` or `SENDGRID_API_KEY` is set |
| EU region | Set `SENDGRID_BASE_URL=https://api.eu.sendgrid.com` if using EU account |
