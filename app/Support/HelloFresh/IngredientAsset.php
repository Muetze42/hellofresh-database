<?php

namespace App\Support\HelloFresh;

class IngredientAsset extends HelloFreshAsset
{
    /**
     * Get the image for this ingredient.
     */
    public function image(): ?string
    {
        return $this->asset(
            config('hellofresh.assets.ingredient.image')
        );
    }
}
