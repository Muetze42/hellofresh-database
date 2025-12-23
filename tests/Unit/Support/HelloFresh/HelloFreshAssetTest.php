<?php

declare(strict_types=1);

namespace Tests\Unit\Support\HelloFresh;

use App\Support\HelloFresh\HelloFreshAsset;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class HelloFreshAssetTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        // Set up config for testing
        config([
            'hellofresh.cdn.base_url' => 'https://cdn.hellofresh.com',
            'hellofresh.cdn.bucket' => 'bucket',
            'hellofresh.assets.recipe.card' => 'card-transform',
            'hellofresh.assets.recipe.header' => 'header-transform',
            'hellofresh.assets.ingredient.thumbnail' => 'ingredient-transform',
            'hellofresh.assets.step.image' => 'step-transform',
        ]);
    }

    #[Test]
    public function url_returns_full_url_with_image_path(): void
    {
        $result = HelloFreshAsset::url('/images/recipe.jpg', 'my-transform');

        $this->assertSame('https://cdn.hellofresh.com/my-transform/bucket/images/recipe.jpg', $result);
    }

    #[Test]
    public function url_returns_null_when_image_path_is_null(): void
    {
        $result = HelloFreshAsset::url(null, 'my-transform');

        $this->assertNull($result);
    }

    #[Test]
    public function url_returns_null_when_image_path_is_empty_string(): void
    {
        $result = HelloFreshAsset::url('', 'my-transform');

        $this->assertNull($result);
    }

    #[Test]
    public function recipe_card_generates_correct_url(): void
    {
        $result = HelloFreshAsset::recipeCard('/images/card.jpg');

        $this->assertSame('https://cdn.hellofresh.com/card-transform/bucket/images/card.jpg', $result);
    }

    #[Test]
    public function recipe_card_returns_null_for_null_path(): void
    {
        $result = HelloFreshAsset::recipeCard(null);

        $this->assertNull($result);
    }

    #[Test]
    public function recipe_header_generates_correct_url(): void
    {
        $result = HelloFreshAsset::recipeHeader('/images/header.jpg');

        $this->assertSame('https://cdn.hellofresh.com/header-transform/bucket/images/header.jpg', $result);
    }

    #[Test]
    public function recipe_header_returns_null_for_null_path(): void
    {
        $result = HelloFreshAsset::recipeHeader(null);

        $this->assertNull($result);
    }

    #[Test]
    public function ingredient_thumbnail_generates_correct_url(): void
    {
        $result = HelloFreshAsset::ingredientThumbnail('/images/ingredient.jpg');

        $this->assertSame('https://cdn.hellofresh.com/ingredient-transform/bucket/images/ingredient.jpg', $result);
    }

    #[Test]
    public function ingredient_thumbnail_returns_null_for_null_path(): void
    {
        $result = HelloFreshAsset::ingredientThumbnail(null);

        $this->assertNull($result);
    }

    #[Test]
    public function step_image_generates_correct_url(): void
    {
        $result = HelloFreshAsset::stepImage('/images/step.jpg');

        $this->assertSame('https://cdn.hellofresh.com/step-transform/bucket/images/step.jpg', $result);
    }

    #[Test]
    public function step_image_returns_null_for_null_path(): void
    {
        $result = HelloFreshAsset::stepImage(null);

        $this->assertNull($result);
    }

    #[Test]
    public function step_image_returns_null_for_empty_path(): void
    {
        $result = HelloFreshAsset::stepImage('');

        $this->assertNull($result);
    }
}
