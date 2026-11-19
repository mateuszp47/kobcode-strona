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
}
