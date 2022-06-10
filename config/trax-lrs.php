<?php

return [
    'version' => '2.0.1',
    'lrs_sync' => [
        'dev' => env('LRS_SYNC_DEV', true),
        'prod' => env('LRS_SYNC_PROD', false),
        'dev_url' => env('LRS_SYNC_DEV_URL', 'https://devadmin.topgeometri.it'),
        'prod_url' => env('LRS_SYNC_PROD_URL', 'https://topgeometri.it'),
        'endpoint' => env('LRS_SYNC_ENDPOINT', '/api/courses/lrs-statements'),
    ]
];
