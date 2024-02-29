<?php

namespace App\Services;

class Asset
{
    public static function socialPreview(): string
    {
        $path = 'assets/social-preview.png';

        return asset($path . '?v=' . md5_file(public_path($path)));
    }
}
