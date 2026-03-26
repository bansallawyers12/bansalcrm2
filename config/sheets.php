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
    | Stage filter options per sheet (config + database)
    |--------------------------------------------------------------------------
    |
    | These lists are shown first in the "Current Stage" dropdown (in this order).
    | Distinct stages from applications on that sheet are merged in after them,
    | so stages not listed here (e.g. new workflow names) still appear.
    |
    */
    /*
     | Leave empty to let the dropdown be driven entirely by distinct stages in the DB.
     | Add stage names here only to pin them at the top of the dropdown in a fixed order.
     | Stages present in the DB but absent from this list are always appended automatically.
     | Stages listed here but absent from the DB appear in the dropdown but match no rows.
     */
    'ongoing_stages' => [
        // Pinned preferred order — DB stages not listed here are appended automatically.
        'Offer letter processing',
        'Offer letter sent',
        'Coe processing',
        'Payment Received from Client',
        'Payment verified By',
    ],

    'coe_enrolled_stages' => [
        'Coe issued',
        'Enrolled',
    ],

    'discontinue_stages' => [
        'Coe cancelled',
        // Add other stages that appear on discontinued applications (excludes Refund – those appear on Refund sheet).
    ],

    'refund_stages' => [
        'Coe cancelled',
        'Refund',
        // Add other stages that appear on refunded applications.
    ],

    /*
    | Checklist sheet uses checklist_early_stages for its stage filter options.
    */
];
