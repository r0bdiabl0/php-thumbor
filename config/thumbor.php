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

    /*
    |--------------------------------------------------------------------------
    | Default Output Format
    |--------------------------------------------------------------------------
    |
    | When set (e.g. 'webp'), this format is applied to every generated URL via
    | Thumbor's format() filter, UNLESS the URL already specifies a format
    | explicitly (via format()/webp()/avif() or addFilter('format', ...)).
    |
    | Pinning a single deterministic format is what makes Thumbor responses
    | cacheable on CDNs: content-negotiated responses carry a `Vary: Accept`
    | header that many CDNs (e.g. Cloudflare, CloudFront) refuse to cache.
    |
    | Leave null to preserve the default behaviour (no format pinning).
    |
    | Supported: 'webp', 'jpeg', 'png', 'gif', 'avif', 'heic'.
    |
    */

    'default_format' => env('THUMBOR_DEFAULT_FORMAT'),

];
