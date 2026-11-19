<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

final class ProjectImage
{
    public static function url(string $path): string
    {
        // Local images must follow the current host and port, not a stale APP_URL.
        // Remote disks retain their configured CDN / object-storage URLs.
        if (config('filesystems.disks.public.driver') === 'local') {
            return '/storage/'.implode('/', array_map('rawurlencode', explode('/', ltrim($path, '/'))));
        }

        return Storage::disk('public')->url($path);
    }

    public static function dimensions(?string $cover): ?array
    {
        if (! $cover || config('filesystems.disks.public.driver') !== 'local'
            || str_contains($cover, '..') || str_starts_with($cover, '/')) {
            return null;
        }

        $path = Storage::disk('public')->path($cover);
        $size = is_file($path) ? @getimagesize($path) : false;

        return $size ? ['width' => $size[0], 'height' => $size[1]] : null;
    }
    public static function srcset(?string $path): ?string
    {
        $original = self::dimensions($path);
        if (! $original || ! str_ends_with($path, '.webp')) return null;
        $sources = [];
        foreach ([800, 1600] as $width) {
            if ($width >= $original['width']) continue;
            $variant = substr($path, 0, -5).'-'.$width.'.webp';
            $size = self::dimensions($variant);
            if ($size) $sources[] = self::url($variant).' '.$size['width'].'w';
        }
        if (! $sources) return null;
        $sources[] = self::url($path).' '.$original['width'].'w';
        return implode(', ', $sources);
    }

}
