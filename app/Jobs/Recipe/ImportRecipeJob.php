<?php

/** @noinspection DuplicatedCode */

namespace App\Jobs\Recipe;

use App\Enums\QueueEnum;
use App\Http\Clients\HelloFresh\Responses\RecipesResponse;
use App\Models\Allergen;
use App\Models\Country;
use App\Models\Cuisine;
use App\Models\Ingredient;
use App\Models\Label;
use App\Models\Recipe;
use App\Models\Tag;
use App\Models\Utensil;
use DateInterval;
use DateMalformedIntervalStringException;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;

/**
 * @phpstan-import-type Recipe from RecipesResponse as RecipeData
 * @phpstan-import-type RecipeStep from RecipesResponse
 * @phpstan-import-type RecipeIngredient from RecipesResponse
 * @phpstan-import-type RecipeAllergen from RecipesResponse
 * @phpstan-import-type RecipeTag from RecipesResponse
 * @phpstan-import-type RecipeCuisine from RecipesResponse
 * @phpstan-import-type RecipeUtensil from RecipesResponse
 */
class ImportRecipeJob implements ShouldBeUnique, ShouldQueue
{
    use Batchable;
    use Queueable;

    protected Recipe $recipeModel;

    /**
     * Create a new job instance.
     *
     * @param  RecipeData  $recipe
     */
    public function __construct(
        public Country $country,
        public string $locale,
        public array $recipe,
        public bool $ignoreActive = false,
    ) {
        $this->onQueue(QueueEnum::Import->value);
    }

    /**
     * The unique ID of the job.
     *
     * @noinspection PhpUnused
     */
    public function uniqueId(): string
    {
        $ignoreActive = $this->ignoreActive ? '1' : '0';

        return $this->country->id . '-' . $this->locale . '-' . $this->recipe['id'] . '-' . $ignoreActive;
    }

    /**
     * The number of seconds after which the job's unique lock will be released.
     */
    public int $uniqueFor = 120;

    /**
     * Execute the job.
     *
     * @throws DateMalformedIntervalStringException
     */
    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        Context::add([
            'country' => $this->country,
            'locale' => $this->locale,
            'recipe' => data_get($this->recipe, 'id'),
        ]);

        if (! $this->shouldImport()) {
            return;
        }

        App::setLocale($this->getLanguage());

        $this->recipeModel = $this->importRecipe();

        $this->syncIngredients();
        $this->syncAllergens();
        $this->syncTags();
        $this->syncLabel();
        $this->syncCuisines();
        $this->syncUtensils();
    }

    /**
     * Import the recipe and return the model.
     *
     * @throws DateMalformedIntervalStringException
     */
    protected function importRecipe(): Recipe
    {
        $suffix = $this->isPrimaryLocale() ? 'primary' : 'secondary';

        /** @var Recipe $recipe */
        $recipe = $this->country->recipes()->updateOrCreate(
            ['hellofresh_id' => $this->recipe['id']],
            [
                'name' => $this->recipe['name'],
                'headline' => $this->recipe['headline'],
                'description' => $this->recipe['descriptionMarkdown'],
                'card_link' => $this->recipe['cardLink'],
                'difficulty' => $this->recipe['difficulty'],
                'prep_time' => $this->parseIsoDuration($this->recipe['prepTime']),
                'total_time' => $this->parseIsoDuration($this->recipe['totalTime']),
                'image_path' => $this->recipe['imagePath'],
                'steps_' . $suffix => $this->transformSteps($this->recipe['steps']),
                'nutrition_' . $suffix => $this->recipe['nutrition'],
                'yields_' . $suffix => $this->recipe['yields'],
                'hellofresh_created_at' => $this->recipe['createdAt'],
                'hellofresh_updated_at' => $this->recipe['updatedAt'],
            ],
        );

        return $recipe;
    }

    /**
     * Check if the current locale is the primary locale for the country.
     */
    protected function isPrimaryLocale(): bool
    {
        return ($this->country->locales[0] ?? null) === $this->getLanguage();
    }

    /**
     * Sync ingredients to the recipe.
     */
    protected function syncIngredients(): void
    {
        $ingredientIds = [];

        /** @var RecipeIngredient $ingredientData */
        foreach ($this->recipe['ingredients'] as $ingredientData) {
            if (empty($ingredientData['name'])) {
                continue;
            }

            $ingredient = $this->findOrCreateIngredient($ingredientData);

            $ingredientIds[] = $ingredient->getKey();
        }

        if (! $this->isPrimaryLocale()) {
            return;
        }

        $this->recipeModel->ingredients()->sync(array_unique($ingredientIds));
    }

    /**
     * Find or create an ingredient by its family type.
     *
     * @param  RecipeIngredient  $ingredientData
     */
    protected function findOrCreateIngredient(array $ingredientData): Ingredient
    {
        return Ingredient::updateOrCreateByHelloFreshId(
            relation: $this->country->ingredients(),
            hellofreshId: $ingredientData['id'],
            locale: $this->getLanguage(),
            attributes: array_filter([
                'name' => Str::normalizeNameStrict($ingredientData['name']),
                'image_path' => $ingredientData['imagePath'],
            ]),
            isPrimaryLocale: $this->isPrimaryLocale(),
        );
    }

    /**
     * Sync allergens to the recipe.
     */
    protected function syncAllergens(): void
    {
        $allergenIds = [];

        /** @var RecipeAllergen $allergenData */
        foreach ($this->recipe['allergens'] as $allergenData) {
            $allergen = Allergen::updateOrCreateByHelloFreshId(
                relation: $this->country->allergens(),
                hellofreshId: $allergenData['id'],
                locale: $this->getLanguage(),
                attributes: array_filter([
                    'name' => Str::normalizeName($allergenData['name']),
                    'icon_path' => $allergenData['iconPath'],
                ]),
            );

            $allergenIds[] = $allergen->getKey();
        }

        if (! $this->isPrimaryLocale()) {
            return;
        }

        $this->recipeModel->allergens()->sync(array_unique($allergenIds));
    }

    /**
     * Sync tags to the recipe.
     */
    protected function syncTags(): void
    {
        $tagIds = [];

        /** @var RecipeTag $tagData */
        foreach ($this->recipe['tags'] as $tagData) {
            $tag = Tag::updateOrCreateByHelloFreshId(
                relation: $this->country->tags(),
                hellofreshId: $tagData['id'],
                locale: $this->getLanguage(),
                attributes: [
                    'name' => Str::normalizeName($tagData['name']),
                    'display_label' => $tagData['displayLabel'],
                ],
            );

            $tagIds[] = $tag->getKey();
        }

        if (! $this->isPrimaryLocale()) {
            return;
        }

        $this->recipeModel->tags()->sync(array_unique($tagIds));
    }

    /**
     * Sync the label to the recipe.
     */
    protected function syncLabel(): void
    {
        $labelData = $this->recipe['label'];

        if ($labelData === null) {
            $this->recipeModel->label()->dissociate()->save();

            return;
        }

        $handle = $this->normalizeLabelHandle($labelData['handle']);

        if ($handle === null) {
            $this->recipeModel->label()->dissociate()->save();

            return;
        }

        $label = Label::updateOrCreateByHandle(
            relation: $this->country->labels(),
            handle: $handle,
            locale: $this->getLanguage(),
            attributes: [
                'name' => Str::normalizeName($labelData['text']),
                'foreground_color' => $labelData['foregroundColor'],
                'background_color' => $labelData['backgroundColor'],
                'display_label' => $labelData['displayLabel'],
            ],
        );

        if (! $this->isPrimaryLocale()) {
            return;
        }

        $this->recipeModel->label()->associate($label)->save();
    }

    /**
     * Normalize the label handle by removing country suffixes and filtering unwanted labels.
     *
     * Returns null if the label should be ignored (contains -discount or -sale).
     */
    protected function normalizeLabelHandle(string $handle): ?string
    {
        // Ignore discount and sale labels
        if (Str::contains($handle, ['-discount', '-sale'])) {
            return null;
        }

        // Remove trailing 2-letter country codes (e.g., -de, -at, -ch)
        return preg_replace('/-[a-z]{2}$/', '', $handle);
    }

    /**
     * Sync cuisines to the recipe.
     */
    protected function syncCuisines(): void
    {
        $cuisineIds = [];

        /** @var RecipeCuisine $cuisineData */
        foreach ($this->recipe['cuisines'] as $cuisineData) {
            if (empty($cuisineData['iconLink'] ?? null)) {
                continue;
            }

            $cuisine = Cuisine::updateOrCreateByHelloFreshId(
                relation: $this->country->cuisines(),
                hellofreshId: $cuisineData['id'],
                locale: $this->getLanguage(),
                attributes: [
                    'name' => Str::normalizeName($cuisineData['name']),
                    'icon_path' => $cuisineData['iconLink'],
                ],
            );

            $cuisineIds[] = $cuisine->getKey();
        }

        if (! $this->isPrimaryLocale()) {
            return;
        }

        $this->recipeModel->cuisines()->sync(array_unique($cuisineIds));
    }

    /**
     * Sync utensils to the recipe.
     */
    protected function syncUtensils(): void
    {
        $utensilIds = [];

        /** @var RecipeUtensil $utensilData */
        foreach ($this->recipe['utensils'] as $utensilData) {
            $utensil = Utensil::updateOrCreateByHelloFreshId(
                relation: $this->country->utensils(),
                hellofreshId: $utensilData['id'],
                locale: $this->getLanguage(),
                attributes: [
                    'name' => Str::normalizeName($utensilData['name']),
                    'type' => $utensilData['type'],
                ],
            );

            $utensilIds[] = $utensil->getKey();
        }

        if (! $this->isPrimaryLocale()) {
            return;
        }

        $this->recipeModel->utensils()->sync(array_unique($utensilIds));
    }

    /**
     * Determine if the recipe should be imported.
     */
    protected function shouldImport(): bool
    {
        if (! $this->ignoreActive && ! $this->recipe['active']) {
            return false;
        }

        if (! in_array((int) $this->recipe['difficulty'], [1, 2, 3])) {
            return false;
        }

        if (! $this->recipe['isPublished']) {
            return false;
        }

        if (empty($this->recipe['imagePath'])) {
            return false;
        }

        if ($this->recipe['isAddon']) {
            return false;
        }

        if ($this->recipe['steps'] === []) {
            return false;
        }

        if (count($this->recipe['ingredients']) < 4) {
            return false;
        }

        if (trim($this->recipe['name']) === '') {
            return false;
        }

        // Skip recipes without proper serving sizes (at least 2 portions)
        if (count($this->recipe['yields']) < 2) {
            return false;
        }

        // Skip recipes where both prep and total time are zero
        return ! ($this->isZeroDuration($this->recipe['prepTime']) && $this->isZeroDuration($this->recipe['totalTime']));
    }

    /**
     * Check if a duration string represents zero time.
     */
    protected function isZeroDuration(string $duration): bool
    {
        return in_array($duration, ['', 'PT', 'PT0S'], true);
    }

    /**
     * Parse an ISO 8601 duration string to minutes.
     *
     * @throws DateMalformedIntervalStringException
     */
    protected function parseIsoDuration(string $duration): int
    {
        if ($duration === '' || $duration === 'PT') {
            return 0;
        }

        $interval = new DateInterval($duration);

        return ($interval->h * 60) + $interval->i;
    }

    /**
     * Transform recipe steps to a storable format.
     *
     * @param  list<RecipeStep>  $steps
     * @return list<array{index: int, instructions: string, images: list<array{path: string, caption: string}>}>
     */
    protected function transformSteps(array $steps): array
    {
        return array_map(static fn (array $step): array => [
            'index' => $step['index'],
            'instructions' => $step['instructionsMarkdown'],
            'images' => array_map(static fn (array $image): array => [
                'path' => $image['path'],
                'caption' => $image['caption'],
            ], $step['images']),
        ], $steps);
    }

    /**
     * Get the language code from the locale.
     */
    protected function getLanguage(): string
    {
        return Str::lower($this->locale);
    }
}
