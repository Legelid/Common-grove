<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Admin notification email
    |--------------------------------------------------------------------------
    | All admin alert emails (reports, problem reports, etc.) go here.
    | Set ADMIN_SUPPORT_EMAIL in .env — leave blank to disable email alerts.
    */
    'support_email' => env('ADMIN_SUPPORT_EMAIL'),
];
