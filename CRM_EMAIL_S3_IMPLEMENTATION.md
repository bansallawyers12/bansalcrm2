# CRM Email S3 Storage Implementation

This document describes the implementation of S3 storage for CRM-sent emails (full HTML snapshot + attachments). It enables consistent archival with uploaded `.msg` emails and attachment download in the Email tab.

> See also: `c:\xampp\htdocs\migrationmanager2\CRM_EMAIL_S3_IMPLEMENTATION.md` for the full doc covering both migrationmanager2 and bansalcrm2.

---

## bansalcrm2 Implementation Summary

### Files Created/Modified

| File | Change |
|------|--------|
| `app/Services/CrmSentEmailS3Service.php` | **Created** – Service for `MailReport` / `MailReportAttachment` |
| `app/Http/Controllers/Admin/AdminController.php` | Injected service; set `client_id`, `client_matter_id` on MailReport; call `storeToS3()` after send |
| `app/Http/Controllers/CRM/EmailQueryV2Controller.php` | Updated `filterSentEmails` for S3 preview fallback |
| `resources/views/Admin/clients/detail.blade.php` | Added hidden `client_id`, `type`, `compose_client_matter_id` to sendmail form |
| `resources/views/Admin/partners/detail.blade.php` | Added hidden `client_id` to sendmail form |

### CrmSentEmailS3Service

- **Models:** `MailReport`, `MailReportAttachment`, `Document`, `Admin`, `Partner`
- **S3 path:** `{client_ref}/crm_sent/sent/{timestamp}-{uniqid}-email.html`
- **Attachment path:** `{client_ref}/attachments/{timestamp}_{uniqid}_{filename}`
- **`resolveClientUniqueId()`:** Handles `partner` type → Partner id; else Admin `client_id` or `'client_' . $entityId`
- **Document:** `doc_type = 'crm_sent'`, `myfile` = full S3 URL, `myfile_key` = filename. `client_matter_id` not set (not in Document fillable).

### AdminController sendmail

- Sets `obj->client_id` = `$requestData['client_id'] ?? $requestData['email_to'][0] ?? null`
- Sets `obj->client_matter_id` = `$requestData['compose_client_matter_id'] ?? null`
- After `sendEmail()` success: builds `attachmentTuples`, calls `storeToS3($obj, $subject, $message, $attachmentTuples)`
- S3 failure is caught/logged; send still succeeds

### Filter Logic (filterSentEmails)

- If `docInfo.myfile_key` exists → use `docInfo.myfile` (full S3 URL)
- Else → build URL with `docType` (incl. `crm_sent`) and `clientRef` fallback

### Form Hidden Inputs

- **Client detail:** `client_id`, `type`, `compose_client_matter_id`
- **Partner detail:** `client_id` (type already present)

### Attachment Download

- `MailReportAttachmentController` uses `s3_key` – no changes needed.

---

## Troubleshooting

| Issue | Check |
|-------|------|
| Sent emails not in Email tab | `client_id` set on MailReport; correct `type` (client/partner) |
| Preview URL 404/blank | Document has `myfile_key`, `myfile`; S3 config correct |
| Attachment download fails | `s3_key` set on `MailReportAttachment`; file exists in S3 |
| S3 upload fails silently | Logs; S3 key/bucket in config; service skips if not configured |
