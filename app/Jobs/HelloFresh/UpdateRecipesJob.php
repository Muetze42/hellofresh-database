<?php

namespace App\Jobs\HelloFresh;

use App\Contracts\Jobs\AbstractCountryUpdateJob;
use App\Models\Allergen;
use App\Models\Category;
use App\Models\Cuisine;
use App\Models\Ingredient;
use App\Models\Label;
use App\Models\Recipe;
use App\Models\Tag;
use App\Models\Utensil;
use Illuminate\Support\Str;

class UpdateRecipesJob extends AbstractCountryUpdateJob
{
    /**
     * Execute the job.
     *
     * @throws \NormanHuth\HellofreshScraper\Exceptions\HellofreshScraperException
     */
    public function handleCountry(): void
    {
        $response = $this->client->recipes($this->skip);
        foreach ($response->items() as $item) {
            if (!$item->active()) {
                continue;
            }

            $recipe = Recipe::updateOrCreate(
                ['id' => $item->getKey()],
                Recipe::freshAttributes($item)
            );

            $relations = [Label::class, Category::class];
            foreach ($relations as $relation) {
                /* @var \App\Models\Label|\App\Models\Category $relation */
                $method = Str::lower(class_basename($relation));
                if ($item->{$method}()) {
                    $key = $relation == Label::class ? 'handle' : 'id';
                    $label = $relation::updateOrCreate(
                        [$key => $item->{$method}()->getKey()],
                        $relation::freshAttributes($item->{$method}())
                    );
                    $recipe->{$method}()->associate($label)->save();
                }
            }

            $relations = [
                Allergen::class,
                Cuisine::class,
                Ingredient::class,
                Tag::class,
                Utensil::class,
            ];
            foreach ($relations as $relation) {
                /* @var \App\Models\Allergen|\App\Models\Cuisine|\App\Models\Ingredient $relation */
                $ids = [];
                $method = Str::lower(Str::plural(class_basename($relation)));
                foreach ($item->{$method}() as $child) {
                    $ids[] = $relation::updateOrCreate(
                        ['id' => $child->getKey()],
                        $relation::freshAttributes($child)
                    )->getKey();
                }
                $recipe->{$method}()->sync($ids);
            }
        }

        $this->afterCountryHandle($response);
    }
}
