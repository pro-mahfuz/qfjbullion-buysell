<?php

return [

    'paths' => [
        'api/*',
        'rate',
        'sanctum/csrf-cookie',
    ],

    'allowed_methods' => [
        '*',
    ],

    'allowed_origins' => [
        'https://buysell.qfjbullion.com',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => [
        '*',
    ],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];