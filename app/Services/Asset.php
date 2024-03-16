<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;

class Asset
{
    public static function socialPreview(): string
    {
        $path = 'assets/social-preview.png';

        if (!file_exists(public_path($path))) {
            Artisan::call('app:assets:generate-social-preview');

            return asset($path);
        }

        return asset($path . '?v=' . md5_file(public_path($path)));
    }
}
