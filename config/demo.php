<?php

return [
    'enabled' => env('DEMO_APPS_ENABLED', in_array(env('APP_ENV', 'production'), ['local', 'testing'], true)),
    'allow_public' => env('DEMO_APPS_ALLOW_PUBLIC', false),
    'index_presentations' => env('DEMO_PAGES_INDEXABLE', false),
    'storage_path' => storage_path('app/private/demo/installflow'),
    'lifetime_minutes' => 120,
    'version' => 1,
];
