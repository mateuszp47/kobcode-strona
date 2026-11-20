<?php
namespace App\Support;

final class Seo
{
    public static function url(string $path = ''): string
    {
        $path = trim($path, '/');

        return rtrim(config('app.url'), '/').($path === '' ? '/' : '/'.$path);
    }
}
