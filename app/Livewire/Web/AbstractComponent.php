<?php

namespace App\Livewire\Web;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Component;

abstract class AbstractComponent extends Component
{
    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    protected function prepareForValidation($attributes): array
    {
        return $this->transformAttributesForValidation(parent::prepareForValidation($attributes));
    }

    /**
     * The attributes that shouldn't trim.
     *
     * @return string[]
     */
    protected function exceptFromTrimStrings(): array
    {
        return [
            'current_password',
            'password',
            'password_confirmation',
        ];
    }

    /**
     * The attributes that shouldn't transform to null if empty string.
     *
     * @return string[]
     */
    protected function exceptFromConvertEmptyStringsToNull(): array
    {
        return [];
    }

    /**
     * Get the properties that should be excluded from realtime mapping.
     *
     * @return string[]
     */
    protected function exceptFromRealtimeMappingProperties(): array
    {
        return $this->exceptFromTrimStrings();
    }

    /**
     * Map a given property and its value in real-time, applying necessary transformations.
     */
    protected function realtimeMapping(string $property, mixed $value): void
    {
        if (! is_string($value)) {
            return;
        }

        $parts = explode('.', $property, 2);
        $key = $parts[0];

        if (in_array($key, $this->exceptFromRealtimeMappingProperties(), true)) {
            return;
        }

        if (! property_exists($this, $key)) {
            return;
        }

        $value = Str::trim($value);

        if (in_array($value, ['', '__rm__', 'null'])) {
            $value = null;
        }

        if (is_array($this->{$key}) && isset($parts[1])) {
            data_set($this->{$key}, $parts[1], $value);

            $keyParts = explode('.', strrev($parts[1]), 2);

            if (isset($keyParts[1])) {
                $property = strrev($keyParts[1]);

                data_set($this->{$key}, $property, array_filter(data_get($this->{$key}, $property)));

                return;
            }

            return;
        }

        $this->{$property} = is_string($value) ? Str::trim($value) : null;
    }

    /**
     * Transform the given value.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    protected function transformAttributesForValidation(array $attributes): array
    {
        return $this->convertEmptyStringsToNullTransform(
            $this->trimStringsTransform($attributes)
        );
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    protected function trimStringsTransform(array $attributes): array
    {
        return Arr::map($attributes, fn (mixed $value, string $key): mixed => $this->trimString($key, $value));
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    protected function convertEmptyStringsToNullTransform(array $attributes): array
    {
        return Arr::map($attributes, fn (mixed $value, string $key): mixed => $this->convertEmptyStringToNull($key, $value));
    }

    /**
     * Transform the given value.
     */
    protected function trimString(string $key, mixed $value): mixed
    {
        if (is_array($value) && ! in_array($key, $this->exceptFromTrimStrings(), true)) {
            return Arr::map($value, fn (mixed $item): mixed => $this->trimString($key, $item));
        }

        if ($this->shouldSkipTrimString($key, $value)) {
            return $value;
        }

        return Str::trim($value);
    }

    /**
     * Transform the given value.
     */
    protected function convertEmptyStringToNull(string $key, mixed $value): mixed
    {
        if (is_array($value) && ! in_array($key, $this->exceptFromConvertEmptyStringsToNull(), true)) {
            return Arr::map($value, fn (mixed $item): mixed => $this->convertEmptyStringToNull($key, $item));
        }

        if ($this->shouldSkipConvertEmptyStringToNull($key, $value)) {
            return $value;
        }

        return $value === '' ? null : $value;
    }

    /**
     * Determine if the given key should skip.
     */
    protected function shouldSkipTrimString(string $key, mixed $value): bool
    {
        return ! is_string($value) || in_array($key, $this->exceptFromTrimStrings(), true);
    }

    /**
     * Determine if the given key should skip.
     */
    protected function shouldSkipConvertEmptyStringToNull(string $key, mixed $value): bool
    {
        return ! is_string($value) || in_array($key, $this->exceptFromConvertEmptyStringsToNull(), true);
    }
}
