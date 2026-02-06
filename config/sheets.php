<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Checklist sheet (first-stage / follow-up sheet)
    |--------------------------------------------------------------------------
    |
    | Applications in these stages appear on the Checklist sheet when created,
    | with or without a follow-up. Add your workflow's "first" or early stage
    | names here (case-insensitive match). New applications get default
    | Status "Active" and show here until status is changed.
    |
    */
    'checklist_early_stages' => [
        'New',
        'Inquiry',
        'Application received',
    ],

];
