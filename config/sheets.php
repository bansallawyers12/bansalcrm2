<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Checklist sheet (Awaiting document = Checklist only)
    |--------------------------------------------------------------------------
    |
    | Checklist sheet shows only applications in "Awaiting document" stage.
    | Ongoing sheet excludes this stage so those applications appear only
    | on Checklist. When user sets "Convert to client", stage is set to
    | checklist_convert_to_client_stage so the row moves to Ongoing.
    |
    */
    'checklist_early_stages' => [
        'Awaiting document',
    ],

    /*
    | Stage to set when user selects "Convert to client" on Checklist sheet.
    | Application moves to Ongoing (no longer in Awaiting document).
    */
    'checklist_convert_to_client_stage' => 'Document received',

    /*
    |--------------------------------------------------------------------------
    | Stage filter options per sheet (Option 1: config-driven)
    |--------------------------------------------------------------------------
    |
    | These lists define which stages appear in the "Current Stage" filter
    | dropdown on each sheet. They always show regardless of whether
    | applications exist. Add or reorder stages to match your workflow.
    |
    */
    'ongoing_stages' => [
        'Document received',
        'Visa applied',
        'Visa received',
        'Enrollment',
        // Add other ongoing stages as needed (exclude COE issued, Enrolled, COE cancelled, Awaiting document).
    ],

    'coe_enrolled_stages' => [
        'Coe issued',
        'Enrolled',
    ],

    'discontinue_stages' => [
        'Coe cancelled',
        'Refund',
        // Add other stages that appear on discontinued applications.
    ],

    /*
    | Checklist sheet uses checklist_early_stages for its stage filter options.
    */
];
