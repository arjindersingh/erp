<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Public Tenant Resolution
    |--------------------------------------------------------------------------
    |
    | Public pages are resolved from the request host before authentication.
    | This lets the public portal, login screens, and open access pages render
    | in the correct tenant context.
    |
    */
    'public_resolution' => [
        'enabled' => env('TENANCY_PUBLIC_RESOLUTION_ENABLED', true),

        /*
         * When true, localhost and loopback hosts will not require a tenant.
         * This keeps local development and health checks convenient.
         */
        'allow_central_domains' => env('TENANCY_ALLOW_CENTRAL_DOMAINS', true),

        'central_domains' => array_filter(array_map(
            'trim',
            explode(',', env('TENANCY_CENTRAL_DOMAINS', 'localhost,127.0.0.1,::1'))
        )),

        /*
         * Supported mode: domain.
         * Subdomain/path modes can be added later without changing callers.
         */
        'strategy' => env('TENANCY_PUBLIC_RESOLUTION_STRATEGY', 'domain'),

        /*
         * In public mode, unknown domains are rejected by default to avoid
         * accidentally rendering one tenant's data for another host.
         */
        'fail_on_unknown_domain' => env('TENANCY_FAIL_ON_UNKNOWN_DOMAIN', true),
    ],
];
