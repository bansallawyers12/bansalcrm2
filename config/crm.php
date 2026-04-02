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
];
