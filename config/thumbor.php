<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Thumbor Server URL
    |--------------------------------------------------------------------------
    |
    | The URL to your Thumbor server instance. If your server runs on a port
    | other than 80, be sure to include it in the URL.
    |
    | Example: 'https://thumbor.example.com' or 'http://localhost:8888'
    |
    */

    'server' => env('THUMBOR_SERVER', 'http://localhost:8888'),

    /*
    |--------------------------------------------------------------------------
    | Thumbor Secret Key
    |--------------------------------------------------------------------------
    |
    | The secret key used to sign Thumbor URLs. This should match the
    | SECURITY_KEY setting in your Thumbor server configuration.
    |
    | Set to null for unsafe URLs (not recommended for production).
    |
    */

    'key' => env('THUMBOR_KEY'),

];
