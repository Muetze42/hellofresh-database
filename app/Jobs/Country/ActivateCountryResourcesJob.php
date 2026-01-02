<?php

namespace App\Jobs\Country;

use App\Enums\QueueEnum;
use App\Models\Allergen;
use App\Models\Country;
use App\Models\Cuisine;
use App\Models\Ingredient;
use App\Models\Label;
use App\Models\Tag;
use App\Models\Utensil;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Updates the active status for activatable models based on recipe count.
 */
class ActivateCountryResourcesJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Country $country,
    ) {
        $this->onQueue(QueueEnum::Long->value);
    }

    public function handle(): void
    {
        $countryId = $this->country->getKey();

        // Update allergens - use withCount to get recipe counts efficiently
        Allergen::where('country_id', $countryId)
            ->withCount(['recipes' => fn (Builder $query) => $query->whereNull('recipes.deleted_at')])
            ->get()
            ->each(fn (Allergen $allergen) => $allergen->updateQuietly(['active' => $allergen->recipes_count > 3]));

        // Update cuisines
        Cuisine::where('country_id', $countryId)
            ->withCount(['recipes' => fn (Builder $query) => $query->whereNull('recipes.deleted_at')])
            ->get()
            ->each(fn (Cuisine $cuisine) => $cuisine->updateQuietly(['active' => $cuisine->recipes_count > 3]));

        // Update tags
        Tag::where('country_id', $countryId)
            ->withCount(['recipes' => fn (Builder $query) => $query->whereNull('recipes.deleted_at')])
            ->get()
            ->each(fn (Tag $tag) => $tag->updateQuietly(['active' => $tag->recipes_count > 3]));

        // Update utensils
        Utensil::where('country_id', $countryId)
            ->withCount(['recipes' => fn (Builder $query) => $query->whereNull('recipes.deleted_at')])
            ->get()
            ->each(fn (Utensil $utensil) => $utensil->updateQuietly(['active' => $utensil->recipes_count > 3]));

        // Update labels
        Label::where('country_id', $countryId)
            ->withCount(['recipes' => fn (Builder $query) => $query->whereNull('recipes.deleted_at')])
            ->get()
            ->each(fn (Label $label) => $label->updateQuietly(['active' => $label->recipes_count > 3]));

        // Update ingredients
        Ingredient::where('country_id', $countryId)
            ->withCount(['recipes' => fn (Builder $query) => $query->whereNull('recipes.deleted_at')])
            ->get()
            ->each(fn (Ingredient $ingredient) => $ingredient->updateQuietly(['active' => $ingredient->recipes_count > 0]));
    }
}
