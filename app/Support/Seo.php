<?php
namespace App\Support;

final class Seo
{
    public static function url(string $path = ''): string
    {
        return rtrim(config('app.url'), '/').($path === '' || $path === '/' ? '/' : '/'.ltrim($path, '/'));
    }
}
