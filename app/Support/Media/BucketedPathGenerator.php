<?php

namespace App\Support\Media;

use Illuminate\Support\Str;
use Override;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class BucketedPathGenerator extends DefaultPathGenerator
{
    /**
     * Get the path for responsive images of the given media, relative to the root storage path.
     */
    #[Override]
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media) . '/responsive-images/';
    }

    /**
     * Get a unique base path for the given media.
     */
    #[Override]
    protected function getBasePath(Media $media): string
    {
        return implode('/', [
            '',
            Str::kebab(Str::plural(class_basename($media->model_type))),
            $this->getBucketNumber($media->getKey()),
            $media->getKey(),
        ]);
    }

    /**
     * Get index number for a unique base path for the given media.
     */
    protected function getBucketNumber(int $key): int
    {
        return (int) (floor($key / 100)) * 100;
    }
}
