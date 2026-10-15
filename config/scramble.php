<?php

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

return [
    'api_path' => 'api',

    'api_domain' => null,

    'export_path' => 'api.json',

    'cache' => [
        'key' => 'scramble.openapi',
        'store' => 'file',
    ],

    'info' => [
        /*
         * API version.
         */
        'version' => env('API_VERSION', '1.0.0'),

        /*
         * Description rendered on the home page of the API documentation (`/docs/api`).
         */
        'description' => 'Official API documentation for Crop Pay digital services.',
    ],

    'ui' => [
        'title' => 'Crop Pay API Documentation',
    ],

    'dev_tools' => [
        'enabled' => env('SCRAMBLE_DEV_TOOLS', env('APP_DEBUG', true)),
    ],

    /*
     * Switched to 'scalar' because Stoplight Elements does not support OpenAPI 3.1.0
     * and causes a blank screen.
     */
    'renderer' => 'scalar',

    'renderers' => [
        'elements' => [
            'view' => 'scramble::docs',
            'theme' => 'light',
            'hideTryIt' => false,
            'hideSchemas' => false,
            'logo' => '',
            'tryItCredentialsPolicy' => 'include',
            'layout' => 'responsive',
            'router' => 'hash',
        ],
        'scalar' => [
            'view' => 'scramble::scalar',
            'cdn' => 'https://cdn.jsdelivr.net/npm/@scalar/api-reference',
            'theme' => 'laravel',
            /*
             * Set proxyUrl to empty string.
             * Leaving proxyUrl set to proxy.scalar.com routes local browser requests 
             * to external servers, causing "Network Error" on local endpoints.
             */
            'proxyUrl' => '',
            'darkMode' => false,
            'showDeveloperTools' => 'never',
            'agent' => ['disabled' => true],
            'credentials' => 'include',
        ],
    ],

    /*
     * Explicit API servers list.
     * Uses 127.0.0.1 for local testing to ensure consistent browser origin matching.
     */
    'servers' => [
        'Local (Development)' => 'http://127.0.0.1:8000/api',
        'Render (Production)' => 'https://crop-pay.onrender.com/api',
    ],

    'enum_cases_description_strategy' => 'description',

    'enum_cases_names_strategy' => false,

    'flatten_deep_query_parameters' => true,

    'middleware' => [
        'web',
        // RestrictedDocsAccess::class,
    ],

    'extensions' => [],

    'security_strategy' => \Dedoc\Scramble\SecurityDocumentation\MiddlewareAuthSecurityStrategy::class,
];