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
    | The Elite inbox shows SendGrid Inbound Parse rows (elite_emails). Optionally
    | also CRM inbound email (emails.mail_type = 0) for the same domain.
    | SendGrid’s REST API does not list received mail; inbound is webhook-only.
    |
    | education_elite_sender_domain — only this domain may appear in From
    | (e.g. educationelite.com.au → accepts *@educationelite.com.au).
    |
    | education_elite_inbox_merge_crm — when true, merge CRM inbound (mail_type 0
    | where from or to is @sender_domain) with elite_emails in /elite/emails.
    |
    | education_elite_inbound_secret — if non-empty, POST /elite/emails must
    | include the same value as query ?secret=... or header X-Elite-Webhook-Secret.
    | Use this in your SendGrid Inbound Parse URL. Rotate if leaked.
    |
    | education_elite_inbound_parse_host — optional display hint only (e.g. parse.educationelite.com.au).
    | Set this to the hostname SendGrid Inbound Parse uses (MX → SendGrid). Mailboxes like apply@apex
    | can stay on Microsoft: add a forward/rule to forward@parse.apex so the CRM webhook still receives
    | a copy (see Elite inbox sidebar “Mailbox + Inbound Parse” steps).
    |
    | education_elite_inbound_reply_to — optional. When set (valid email on your Inbound Parse host),
    | Elite “New Message” sends (_elite_compose) add Reply-To to this address so recipient replies
    | go to SendGrid Inbound Parse (e.g. inbound@parse.educationelite.com.au). Leave empty to keep
    | default reply behaviour (reply to From).
    |
    */
    'education_elite_sender_domain' => env('EDUCATION_ELITE_SENDER_DOMAIN', 'educationelite.com.au'),

    'education_elite_inbox_merge_crm' => filter_var(
        env('EDUCATION_ELITE_INBOX_MERGE_CRM', true),
        FILTER_VALIDATE_BOOL
    ),

    'education_elite_inbound_secret' => env('EDUCATION_ELITE_INBOUND_SECRET', ''),

    'education_elite_inbound_parse_host' => env('EDUCATION_ELITE_INBOUND_PARSE_HOST', ''),

    'education_elite_inbound_reply_to' => env('EDUCATION_ELITE_INBOUND_REPLY_TO', ''),
];
