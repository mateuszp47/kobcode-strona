<?php
return [
    'contact_enabled' => env('SITE_CONTACT_ENABLED', false),
    'email' => env('SITE_CONTACT_EMAIL'),
    'phone' => env('SITE_CONTACT_PHONE') ?: '+48 882 137 202',
    'indexable' => env('SITE_INDEXABLE', false),
];
