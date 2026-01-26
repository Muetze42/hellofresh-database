<?php

namespace App\Models;

use App\Models\Concerns\ActivatableTrait;
use App\Models\Concerns\HasHelloFreshIdsTrait;
use App\Models\Concerns\LogsModificationsTrait;
use Database\Factories\IngredientFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin Builder<Ingredient>
 */
class Ingredient extends Model
{
    use ActivatableTrait;

    /** @use HasFactory<IngredientFactory> */
    use HasFactory;

    use HasHelloFreshIdsTrait;
    use HasTranslations;
    use LogsModificationsTrait;

    /**
     * The attributes that are translatable.
     *
     * @var list<string>
     */
    public array $translatable = [
        'name',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'name_slug',
        'image_path',
        'cached_recipes_count',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'hellofresh_ids',
        'name_slug',
        'image_path',
    ];

    /**
     * Get the country that owns the ingredient.
     *
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the recipes that use this ingredient.
     *
     * @return BelongsToMany<Recipe, $this>
     */
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class);
    }

    /**
     * Get the allergens for the ingredient.
     *
     * @return BelongsToMany<Allergen, $this>
     */
    public function allergens(): BelongsToMany
    {
        return $this->belongsToMany(Allergen::class);
    }

    /**
     * Normalize a name to a match slug for duplicate detection.
     *
     * Uses Laravel's Str::slug with German locale for proper umlaut conversion.
     */
    public static function normalizeToSlug(string $name): string
    {
        return Str::slug($name, '', 'de');
    }

    /**
     * Update or create an ingredient by HelloFresh ID with slug-based matching.
     *
     * For primary locale: also matches by normalized slug to detect spelling variants.
     * For secondary locale: only matches by hellofresh_id.
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
        bool $isPrimaryLocale = true,
    ): static {
        // 1. Search by hellofresh_id in the array (most common case, no slug needed)
        /** @var static|null $model */
        $model = (clone $relation)
            ->whereJsonContains('hellofresh_ids', $hellofreshId)
            ->first();

        if ($model !== null) {
            // Update slug on primary locale if not set yet
            if ($isPrimaryLocale && $model->name_slug === null && isset($attributes['name'])) {
                $attributes['name_slug'] = self::normalizeToSlug($attributes['name']);
            }

            $model->update($attributes);

            // No need to addHelloFreshId - we found it by that ID, so it's already there
            return $model;
        }

        // Calculate slug only when needed (not found by hellofresh_id)
        $searchSlug = isset($attributes['name'])
            ? self::normalizeToSlug($attributes['name'])
            : null;

        // 2. For primary locale: search by normalized slug (spelling variants)
        /** @var static|null $model */
        $model = $isPrimaryLocale && $searchSlug !== null
            ? (clone $relation)->where('name_slug', $searchSlug)->first()
            : null;

        if ($model !== null) {
            // Found by slug - add hellofresh_id but DON'T update name or slug
            return $model->addHelloFreshId($hellofreshId);
        }

        // 3. Search by exact name using JSON path for locale
        /** @var static|null $model */
        $model = isset($attributes['name'])
            ? (clone $relation)->where('name->' . $locale, $attributes['name'])->first()
            : null;

        if ($model !== null) {
            // Set slug on primary locale if not set yet
            if ($isPrimaryLocale && $model->name_slug === null && $searchSlug !== null) {
                $attributes['name_slug'] = $searchSlug;
            }

            $model->update($attributes);

            return $model->addHelloFreshId($hellofreshId);
        }

        // 4. Not found, create new model with slug
        if ($isPrimaryLocale && $searchSlug !== null) {
            $attributes['name_slug'] = $searchSlug;
        }

        /** @var static $model */
        $model = $relation->create($attributes);

        return $model->addHelloFreshId($hellofreshId);
    }
}
