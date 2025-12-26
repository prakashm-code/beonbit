<?php

return [

    'paths' => [
        'api/*',
        'admin/api/*',
        'sanctum/csrf-cookie',
    ],

    'allowed_methods' => ['*'],

    // ✅ Exact domains
    'allowed_origins' => [
        'http://localhost:5173',
        'https://infinitewealth.uk',
        'https://admin.infinitewealth.uk',
        'https://app.infinitewealth.uk',
    ],

    // ✅ Wildcard subdomains go here
    'allowed_origins_patterns' => [
        '#^https://.*\.infinitewealth\.uk$#',
    ],

    'allowed_headers' => [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'Accept',
    ],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
