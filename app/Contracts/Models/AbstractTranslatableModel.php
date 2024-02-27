<?php

namespace App\Contracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Spatie\Translatable\HasTranslations;

abstract class AbstractTranslatableModel extends Model
{
    use HasTranslations;

    /**
     * Convert the model instance to an array.
     */
    public function toArray(): array
    {
        return Arr::mapWithKeys(
            parent::toArray(),
            fn (mixed $item, string $key) => [
                $key => !in_array($key, $this->translatable) ? $item : $this->getCurrentTranslation($key),
            ]
        );
    }

    protected function getCurrentTranslation(string $key): mixed
    {
        return $this->getTranslation($key, app()->getLocale());
    }
}
