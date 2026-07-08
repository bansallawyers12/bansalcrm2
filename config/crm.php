<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google review reminder modal (client/lead detail)
    |--------------------------------------------------------------------------
    |
    | staff.role values (user_roles.id) that never see the reminder popup.
    | Default: 14 = Calling Team, 15 = Accountant. Override via
    | CRM_GOOGLE_REVIEW_REMINDER_EXCLUDE_ROLE_IDS e.g. "14,15,20".
    |
    | Delay before the modal opens (milliseconds). Default 60000 = 1 minute.
    | CRM_GOOGLE_REVIEW_REMINDER_DELAY_MS=0 opens immediately.
    | Capped at 30 minutes to avoid accidental huge values in .env.
    |
    */
    'google_review_reminder_exclude_role_ids' => array_values(array_filter(array_map(
        'intval',
        explode(',', (string) env('CRM_GOOGLE_REVIEW_REMINDER_EXCLUDE_ROLE_IDS', '14,15'))
    ), static fn (int $id) => $id > 0)),

    'google_review_reminder_modal_delay_ms' => min(
        30 * 60 * 1000,
        max(0, (int) env('CRM_GOOGLE_REVIEW_REMINDER_DELAY_MS', 60000))
    ),

    /*
    |--------------------------------------------------------------------------
    | Education Elite inbound email (/elite/emails)
    |--------------------------------------------------------------------------
    |
    | The Elite inbox shows AWS SES inbound rows (elite_emails), imported from S3
    | via `php artisan ses:sync-inbound` (scheduled every minute). Optionally also
    | CRM inbound email (emails.mail_type = 0) for the same domain.
    |
    | education_elite_sender_domain — only this domain may appear in From
    | (e.g. educationelite.com.au → accepts *@educationelite.com.au).
    |
    | education_elite_inbox_merge_crm — when true, merge CRM inbound (mail_type 0
    | where from or to is @sender_domain) with elite_emails in /elite/emails.
    |
    | education_elite_inbound_secret — if non-empty, legacy POST /emails/elite must
    | include query ?secret=... or header X-Elite-Webhook-Secret. Rotate if leaked.
    |
    | education_elite_inbound_parse_host — optional inbound hostname for Reply-To
    | (legacy). Primary inbound path is SES receipt rule → S3 → ses:sync-inbound.
    |
    | education_elite_inbound_reply_to — optional full address (e.g. inbound@parse.educationelite.com.au).
    | When set, Elite “New Message” uses this as Reply-To so customer replies hit Inbound Parse → CRM inbox.
    |
    | If empty but education_elite_inbound_parse_host is set, Reply-To defaults to
    | {education_elite_inbound_reply_local}@{parse_host} (default local part: inbound).
    |
    | education_elite_inbound_set_reply_to — set to false to disable Reply-To on Elite sends (replies only
    | to From; use M365 forwarding if you still need CRM copies).
    |
    | education_elite_inbound_webhook_log — when true, POST /elite/emails logs diagnostic lines
    | (elite.inbound, elite.inbound.parsed, elite.inbound.rejected, elite.inbound.stored, etc.) to the default log channel.
    |
    | education_elite_inbound_attachments_disk — filesystem disk name for inbound multipart attachments
    | (elite_email_attachments.storage_path is the object key). Default s3 uses AWS_* from .env.
    | Use local for legacy/on-prem only; serving tries this disk first, then local for old rows.
    |
    */
    'education_elite_sender_domain' => env('EDUCATION_ELITE_SENDER_DOMAIN', 'educationelite.com.au'),

    /*
    | Outbound mailer for Elite compose (_elite_compose). Default ses_elite (AWS SES).
    */
    'education_elite_mailer' => env('EDUCATION_ELITE_MAILER', 'ses_elite'),

    'education_elite_from_name' => env('EDUCATION_ELITE_FROM_NAME', env('MAIL_FROM_NAME', 'Education Elite')),

    'education_elite_inbox_merge_crm' => filter_var(
        env('EDUCATION_ELITE_INBOX_MERGE_CRM', true),
        FILTER_VALIDATE_BOOL
    ),

    'education_elite_inbound_secret' => env('EDUCATION_ELITE_INBOUND_SECRET', ''),

    'education_elite_inbound_parse_host' => env('EDUCATION_ELITE_INBOUND_PARSE_HOST', ''),

    'education_elite_inbound_reply_to' => env('EDUCATION_ELITE_INBOUND_REPLY_TO', ''),

    'education_elite_inbound_reply_local' => env('EDUCATION_ELITE_INBOUND_REPLY_LOCAL', 'inbound'),

    'education_elite_inbound_set_reply_to' => filter_var(
        env('EDUCATION_ELITE_INBOUND_SET_REPLY_TO', false),
        FILTER_VALIDATE_BOOL
    ),

    'education_elite_inbound_webhook_log' => filter_var(
        env('EDUCATION_ELITE_INBOUND_WEBHOOK_LOG', true),
        FILTER_VALIDATE_BOOL
    ),

    'education_elite_inbound_attachments_disk' => env('EDUCATION_ELITE_INBOUND_ATTACHMENTS_DISK', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Client / partner Emails tab (email-v2) — upload settings
    |--------------------------------------------------------------------------
    |
    | Phase 0 prep: defaults preserve current behaviour (.msg only).
    | Phase 1 will wire controllers/JS to these keys and add .eml via env.
    |
    | EMAIL_UPLOAD_ALLOWED_EXTENSIONS=msg,eml
    | EMAIL_UPLOAD_MAX_KB=30720
    |
    */
    'email_upload_allowed_extensions' => array_values(array_unique(array_filter(array_map(
        static function (string $ext): string {
            return strtolower(ltrim(trim($ext), '.'));
        },
        explode(',', (string) env('EMAIL_UPLOAD_ALLOWED_EXTENSIONS', 'msg,eml'))
    )))),

    'email_upload_max_kb' => max(1, (int) env('EMAIL_UPLOAD_MAX_KB', 30720)),

    /** Absolute path to BansalLaw_CRM for port reference / prep scripts only. */
    'bansallaw_crm_root' => env('BANSALLAW_CRM_ROOT', 'C:\\xampp\\htdocs\\BansalLaw_CRM'),
];
