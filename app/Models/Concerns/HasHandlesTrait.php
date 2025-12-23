<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Trait for models that can have multiple handles mapping to one entity.
 *
 * @mixin Model
 *
 * @property list<string>|null $handles
 */
trait HasHandlesTrait
{
    /**
     * Initialize the trait.
     */
    protected function initializeHasHandlesTrait(): void
    {
        $this->mergeFillable(['handles']);
        $this->mergeCasts(['handles' => 'array']);
    }

    /**
     * Update or create a model by handle, managing the handles array.
     *
     * Searches by handle first, then by name (using JSON path for locale),
     * and creates if not found.
     *
     * @template TParent of Model
     *
     * @param  HasMany<static, TParent>  $relation
     * @param  array<string, mixed>  $attributes
     */
    public static function updateOrCreateByHandle(
        HasMany $relation,
        string $handle,
        string $locale,
        array $attributes,
    ): static {
        // 1. Search by handle in the array
        /** @var static|null $model */
        $model = (clone $relation)
            ->whereJsonContains('handles', $handle)
            ->first();

        if ($model !== null) {
            $model->update($attributes);

            return $model->addHandle($handle);
        }

        // 2. Search by name using JSON path for locale
        /** @var static|null $model */
        $model = isset($attributes['name'])
            ? (clone $relation)->where('name->' . $locale, $attributes['name'])->first()
            : null;

        if ($model !== null) {
            $model->update($attributes);

            return $model->addHandle($handle);
        }

        // 3. Not found, create new model with initial handle
        /** @var static $model */
        $model = $relation->create([
            ...$attributes,
            'handles' => [$handle],
        ]);

        return $model;
    }

    /**
     * Add a handle to the model's handles array if not already present.
     */
    public function addHandle(string $handle): static
    {
        /** @var list<string> $handles */
        $handles = $this->handles ?? [];

        if (! in_array($handle, $handles, true)) {
            $this->update(['handles' => [...$handles, $handle]]);
        }

        return $this;
    }

    /**
     * Check if this model has a specific handle.
     */
    public function hasHandle(string $handle): bool
    {
        /** @var list<string> $handles */
        $handles = $this->handles ?? [];

        return in_array($handle, $handles, true);
    }
}
