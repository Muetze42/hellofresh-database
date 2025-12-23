<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Model
 */
trait HasHelloFreshIdsTrait
{
    /**
     * Initialize the trait.
     */
    protected function initializeHasHelloFreshIdsTrait(): void
    {
        $this->mergeFillable(['hellofresh_ids']);
        $this->mergeCasts(['hellofresh_ids' => 'array']);
    }

    /**
     * Update or create a model by HelloFresh ID, managing the hellofresh_ids array.
     *
     * Searches by hellofresh_id first, then by name (using JSON path for locale),
     * and creates if not found.
     *
     * @template TParent of Model
     *
     * @param  HasMany<static, TParent>  $relation
     * @param  array<string, mixed>  $attributes
     */
    public static function updateOrCreateByHelloFreshId(
        HasMany $relation,
        string $hellofreshId,
        string $locale,
        array $attributes,
    ): static {
        // 1. Search by hellofresh_id in the array
        /** @var static|null $model */
        $model = (clone $relation)
            ->whereJsonContains('hellofresh_ids', $hellofreshId)
            ->first();

        if ($model !== null) {
            $model->update($attributes);

            return $model->addHelloFreshId($hellofreshId);
        }

        // 2. Search by name using JSON path for locale
        /** @var static|null $model */
        $model = isset($attributes['name'])
            ? (clone $relation)->where('name->' . $locale, $attributes['name'])->first()
            : null;

        if ($model !== null) {
            $model->update($attributes);

            return $model->addHelloFreshId($hellofreshId);
        }

        // 3. Not found, create new model
        /** @var static $model */
        $model = $relation->create($attributes);

        return $model->addHelloFreshId($hellofreshId);
    }

    /**
     * Add a HelloFresh ID to the model's hellofresh_ids array if not already present.
     */
    public function addHelloFreshId(string $hellofreshId): static
    {
        /** @var list<string> $hellofreshIds */
        $hellofreshIds = $this->hellofresh_ids ?? [];

        if (! in_array($hellofreshId, $hellofreshIds, true)) {
            /** @noinspection LaravelEloquentGuardedAttributeAssignmentInspection */
            $this->update(['hellofresh_ids' => [...$hellofreshIds, $hellofreshId]]);
        }

        return $this;
    }
}
