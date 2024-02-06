<?php

namespace App\Casts\Traits;

use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Model;

trait AttributeAsJsonSetterTrait
{
    use HasAttributes;

    /**
     * Prepare the given value for storage.
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value) {
            return $this->castAttributeAsJson($key, $value);
        }

        return null;
    }
}
