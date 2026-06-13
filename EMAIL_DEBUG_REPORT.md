# Email Debug Report (AWS SES)

## Note

This report was written during the Zoho → SendGrid migration. Outbound email now uses **AWS SES**. See `SES_EMAIL_MIGRATION.md` and run `php artisan ses:test`.

## Error Summary
**Error:** Failed to authenticate on SMTP server with username `info@bansaleducation.com.au` (535 Authentication Failed)

## Root Cause Analysis

### 1. Database Check (Local Environment)
Running `php artisan email:debug` showed:
- **info@bansaleducation.com.au does NOT exist** in the `emails` table (local/your DB)
- All 15 active emails are `@bansalimmigration.com.au` only
- No `@bansaleducation.com.au` addresses are configured

### 2. When Does This Username Get Used?
The username `info@bansaleducation.com.au` is used when:
1. **AdminLoginController** – Hardcoded for login IP alert emails (to/from)
2. **User selects it from dropdown** – Only possible if it exists in `emails` table
3. **User types it** – On forms with text input (e.g. users/view, products) instead of select

### 3. Flow When Sender Is info@bansaleducation.com.au
- `configureMailerForEmail('info@bansaleducation.com.au')` is called
- **If record exists:** Uses its stored password → 535 = wrong password or Zoho rejection
- **If record does NOT exist:** Falls back to first active email (e.g. info@bansalimmigration.com.au) → auth would succeed, but FROM would show bansalimmigration

### 4. Why 535 Occurs (When Record Exists)
Zoho returns 535 when:
| Cause | Solution |
|-------|----------|
| Wrong password in DB | Re-enter correct password in Admin Console → Emails → Edit |
| 2FA enabled - need App Password | Generate App Password in Zoho, use that instead of account password |
| Domain not verified in Zoho | Verify bansaleducation.com.au in Zoho Mail admin |
| Account locked | Unlock in Zoho admin |
| Different Zoho org | info@ and noreply@ may be in separate Zoho accounts - each needs correct creds |

---

## Action Plan

### Step 1: Verify Record Exists
```bash
php artisan email:debug info@bansaleducation.com.au
```
- If "No record found" → **Add it** in Admin Console → Emails
- If found → Check password length (should be 8+ chars, 16 for Zoho App Passwords)

### Step 2: Add info@bansaleducation.com.au to emails Table
1. Go to **Admin Console → Emails → Add Email**
2. Email: `info@bansaleducation.com.au`
3. Display Name: Bansal Education (or similar)
4. Password: **Zoho mailbox password** OR **App Password** (if 2FA is on)
5. Save

### Step 3: Get Zoho App Password (If 2FA Enabled)
1. Log into Zoho Mail (mail.zoho.com)
2. Settings → Security → Application-Specific Passwords
3. Generate new App Password
4. Use that 16-char password in the CRM (not your normal login password)

### Step 4: Re-test
Send a test email from client compose using info@bansaleducation.com.au

---

## Code Paths That Use info@bansaleducation.com.au
- `AdminLoginController.php:120,168` – Login/failed-login IP alerts (commented in one place)
