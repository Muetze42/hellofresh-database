<?php

namespace App\Contracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\Translatable\Events\TranslationHasBeenSetEvent;
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

    /**
     * @throws \Spatie\Translatable\Exceptions\AttributeIsNotTranslatable
     */
    public function setTranslation(string $key, string $locale, $value): self
    {
        $this->guardAgainstNonTranslatableAttribute($key);

        $translations = $this->getTranslations($key);

        $oldValue = $translations[$locale] ?? '';

        if ($this->hasSetMutator($key)) {
            $method = 'set' . Str::studly($key) . 'Attribute';

            $this->{$method}($value, $locale);

            $value = $this->attributes[$key];
        } elseif ($this->hasAttributeSetMutator($key)) { // handle new attribute mutator
            $this->setAttributeMarkedMutatedAttributeValue($key, $value);

            $value = $this->attributes[$key];
        }

        $translations[$locale] = $value;

        //$this->attributes[$key] = $this->asJson($translations);
        $this->attributes[$key] = json_encode($translations, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        event(new TranslationHasBeenSetEvent($this, $key, $locale, $oldValue, $value));

        return $this;
    }
}
