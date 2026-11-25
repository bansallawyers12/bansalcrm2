<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Waiting / Attending "Set Assignee" allowlist
    |--------------------------------------------------------------------------
    |
    | Matched against staff first_name / email local-part (see aliases).
    | Only used for public /office-check-in visits — not staff Create In Person.
    |
    */

    'assignee_staff_keys' => [
        'arun',
        'ajay',
        'pevy',
        'ankit',
        'sam',
        'rakshita',
        'sayed',
        'ajneet',
        'vijay',
        'jsapreet',
    ],

    'assignee_staff_aliases' => [
        'sayed' => ['sayed', 'syed'],
        'jsapreet' => ['jsapreet', 'jaspreet'],
    ],

    'history_subject' => 'Check-in created via public office check-in page',
];
