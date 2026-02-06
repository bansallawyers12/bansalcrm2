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

];
