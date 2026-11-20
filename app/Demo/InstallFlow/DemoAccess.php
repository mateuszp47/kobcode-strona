<?php

namespace App\Demo\InstallFlow;

final class DemoAccess
{
    public static function enabled(): bool
    {
        return config('demo.enabled') && (app()->environment(['local', 'testing']) || config('demo.allow_public'));
    }

    public static function indexable(): bool
    {
        return self::enabled() && config('site.indexable') && config('demo.index_presentations');
    }
}
