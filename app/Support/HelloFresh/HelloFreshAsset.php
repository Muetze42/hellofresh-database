<?php

namespace App\Support\HelloFresh;

class HelloFreshAsset
{
    protected ?string $image;

    public function __construct(?string $image)
    {
        $this->image = $image;
    }

    /**
     * Generate an asset url for a HelloFresh cloud media.
     */
    protected function asset(string $path): ?string
    {
        if (!$this->image) {
            return null;
        }

        return 'https://img.hellofresh.com/' . $path . '/hellofresh_s3' . $this->image;
    }

    public static function get(string $path, string $image): ?string
    {
        return (new static($image))->asset($path);
    }
}
