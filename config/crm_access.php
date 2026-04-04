<?php

$intList = static function (string $envKey, string $envDefault, array $hardDefault): array {
    $raw = env($envKey, $envDefault);
    $filtered = array_values(array_filter(
        array_map('intval', explode(',', (string) $raw)),
        static fn (int $v) => $v > 0
    ));

    return $filtered !== [] ? $filtered : $hardDefault;
};

return [
    /*
     | Global search (SearchService) cache keys include the staff id when strict_allocation is on,
     | so results are not shared across users via Redis/file cache.
     */

    // Roles that bypass allocation (see all clients/leads) and can approve supervisor requests.
    'exempt_role_ids' => $intList('CRM_ACCESS_EXEMPT_ROLE_IDS', '1,12', [1, 12]),

    // Specific staff.id values that bypass allocation (optional). Also set per-user in Staff → CRM access → Full access (crm_full_access).

    'exempt_staff_ids' => $intList('CRM_ACCESS_EXEMPT_STAFF_IDS', '', []),

    'quick_reason_options' => [
        'calling' => 'Calling / Reception',
        'cover' => 'Covering Absent Colleague',
        'urgent' => 'Urgent Client Follow-up',
        'admin_task' => 'Administrative Task',
    ],

    // Staff Calling (role 9): 15-minute quick grant; may also request supervisor-approved longer access.
    'quick_access_only_role_ids' => $intList('CRM_ACCESS_QUICK_ONLY_ROLE_IDS', '9', [9]),

    'quick_grant_minutes' => max(1, (int) env('CRM_ACCESS_QUICK_GRANT_MINUTES', 15)),

    'supervisor_grant_hours' => max(1, (int) env('CRM_ACCESS_SUPERVISOR_GRANT_HOURS', 24)),

    'strict_allocation' => filter_var(env('CRM_ACCESS_STRICT_ALLOCATION', true), FILTER_VALIDATE_BOOLEAN),

    'max_pending_supervisor_requests' => max(1, (int) env('CRM_ACCESS_MAX_PENDING_SUPERVISOR_REQUESTS', 5)),

    'pending_ttl_days' => max(1, (int) env('CRM_ACCESS_PENDING_TTL_DAYS', 14)),
];
