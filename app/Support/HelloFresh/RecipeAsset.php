<?php

namespace App\Support\HelloFresh;

class RecipeAsset extends HelloFreshAsset
{
    protected ?string $pdfUrl;

    public function __construct(?string $image, ?string $cardLink)
    {
        $this->pdfUrl = $cardLink;
        parent::__construct($image);
    }

    public function pdf(): ?string
    {
        return $this->pdfUrl;
    }

    /**
     * Get the header image for this recipe.
     */
    public function header(): ?string
    {
        return $this->asset(
            config('hellofresh.assets.recipes.header')
        );
    }

    /**
     * Get the preview image for this recipe.
     */
    public function preview(): ?string
    {
        return $this->asset(
            config('hellofresh.assets.recipes.preview')
        );
    }
}
