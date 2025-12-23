<?php

namespace App\Observers;

use App\Models\Recipe;

class RecipeObserver
{
    /**
     * Handle the Recipe "saving" event.
     */
    public function saving(Recipe $recipe): void
    {
        $recipe->has_pdf = $this->hasPdf($recipe);
    }

    protected function hasPdf(Recipe $recipe): bool
    {
        $cardLinks = $recipe->getTranslations('card_link');
        $cardLinks = array_filter($cardLinks);

        return $cardLinks !== [];
    }
}
