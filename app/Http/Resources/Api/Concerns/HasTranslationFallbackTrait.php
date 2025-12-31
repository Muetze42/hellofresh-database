<?php

namespace App\Http\Resources\Api\Concerns;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * @property Model&HasTranslations $resource
 */
trait HasTranslationFallbackTrait
{
    /**
     * Get translation with fallback to any available locale.
     */
    protected function getTranslationWithAnyFallback(string $key, string $locale): string
    {
        $translation = $this->resource->getTranslation($key, $locale);

        if ($translation !== '') {
            return $translation;
        }

        /** @var array<string, string> $translations */
        $translations = $this->resource->getTranslations($key);
        $values = array_values($translations);

        return $values[0] ?? '';
    }
}
