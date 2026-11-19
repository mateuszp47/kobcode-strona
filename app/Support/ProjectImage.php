<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

final class ProjectImage
{
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
            if ($size) $sources[] = Storage::disk('public')->url($variant).' '.$size['width'].'w';
        }
        if (! $sources) return null;
        $sources[] = Storage::disk('public')->url($path).' '.$original['width'].'w';
        return implode(', ', $sources);
    }

}
