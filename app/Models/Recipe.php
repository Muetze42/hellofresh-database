<?php

namespace App\Models;

use App\Models\Concerns\ActivatableTrait;
use App\Observers\RecipeObserver;
use App\Support\HelloFresh\HelloFreshAsset;
use Database\Factories\RecipeFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin Builder<Recipe>
 */
#[ObservedBy([RecipeObserver::class])]
class Recipe extends Model
{
    use ActivatableTrait;

    /** @use HasFactory<RecipeFactory> */
    use HasFactory;

    use HasTranslations;
    use SoftDeletes;

    /**
     * The attributes that are translatable.
     *
     * @var list<string>
     */
    public array $translatable = [
        'name',
        'headline',
        'description',
        'card_link',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'hellofresh_id',
        'name',
        'headline',
        'description',
        'difficulty',
        'prep_time',
        'total_time',
        'image_path',
        'card_link',
        'steps_primary',
        'steps_secondary',
        'nutrition_primary',
        'nutrition_secondary',
        'yields_primary',
        'yields_secondary',
        'variant',
        'hellofresh_created_at',
        'hellofresh_updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'hellofresh_id',
        'headline',
        'description',
        'image_path',
        'card_link',
        'steps_primary',
        'steps_secondary',
        'nutrition_primary',
        'nutrition_secondary',
        'yields_primary',
        'yields_secondary',
        'hellofresh_created_at',
        'hellofresh_updated_at',
        'steps',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'difficulty' => 'int',
            'prep_time' => 'int',
            'total_time' => 'int',
            'steps_primary' => 'array',
            'steps_secondary' => 'array',
            'nutrition_primary' => 'array',
            'nutrition_secondary' => 'array',
            'yields_primary' => 'array',
            'yields_secondary' => 'array',
            'has_pdf' => 'bool',
            'variant' => 'bool',
            'hellofresh_created_at' => 'datetime',
            'hellofresh_updated_at' => 'datetime',
        ];
    }

    /**
     * Get the country that owns the recipe.
     *
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the canonical recipe (parent variant).
     *
     * @return BelongsTo<Recipe, $this>
     */
    public function canonical(): BelongsTo
    {
        return $this->belongsTo(self::class, 'canonical_id');
    }

    /**
     * Get the recipe variants.
     *
     * @return HasMany<Recipe, $this>
     */
    public function variants(): HasMany
    {
        return $this->hasMany(self::class, 'canonical_id');
    }

    /**
     * Get the ingredients for the recipe.
     *
     * @return BelongsToMany<Ingredient, $this>
     */
    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class);
    }

    /**
     * Get the allergens for the recipe.
     *
     * @return BelongsToMany<Allergen, $this>
     */
    public function allergens(): BelongsToMany
    {
        return $this->belongsToMany(Allergen::class);
    }

    /**
     * Get the tags for the recipe.
     *
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get the label for the recipe.
     *
     * @return BelongsTo<Label, $this>
     */
    public function label(): BelongsTo
    {
        return $this->belongsTo(Label::class);
    }

    /**
     * Get the cuisines for the recipe.
     *
     * @return BelongsToMany<Cuisine, $this>
     */
    public function cuisines(): BelongsToMany
    {
        return $this->belongsToMany(Cuisine::class);
    }

    /**
     * Get the utensils for the recipe.
     *
     * @return BelongsToMany<Utensil, $this>
     */
    public function utensils(): BelongsToMany
    {
        return $this->belongsToMany(Utensil::class);
    }

    /**
     * Get the menus that include this recipe.
     *
     * @return BelongsToMany<Menu, $this>
     */
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class);
    }

    /**
     * Get the recipe card image URL.
     *
     * @return Attribute<string|null, never>
     */
    protected function cardImageUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => HelloFreshAsset::recipeCard($this->image_path));
    }

    /**
     * Get the recipe header image URL.
     *
     * @return Attribute<string|null, never>
     */
    protected function headerImageUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => HelloFreshAsset::recipeHeader($this->image_path));
    }

    /**
     * Get the HelloFresh recipe URL.
     *
     * @return Attribute<string|null, never>
     */
    protected function hellofreshUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->buildHellofreshUrl());
    }

    /**
     * Build the HelloFresh URL.
     */
    protected function buildHellofreshUrl(): ?string
    {
        // Variant recipes often lead to 404 pages on HelloFresh
        if ($this->variant) {
            return null;
        }

        /** @var string|null $hellofreshId */
        $hellofreshId = $this->hellofresh_id;

        if ($hellofreshId === null || $hellofreshId === '') {
            return null;
        }

        if (! $this->relationLoaded('country') || $this->country?->domain === null) {
            return null;
        }

        $name = $this->name ?: $this->getFirstTranslation('name');
        $slug = $name !== null ? Str::slug($name) : '';

        return sprintf('%s/recipes/%s-%s', $this->country->domain, $slug, $hellofreshId);
    }

    /**
     * Get the PDF URL for the recipe card.
     *
     * @return Attribute<string|null, never>
     */
    protected function pdfUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->buildPdfUrl());
    }

    /**
     * Build the PDF URL.
     */
    protected function buildPdfUrl(): ?string
    {
        $url = $this->card_link;

        if ($url === null || $url === '') {
            $url = $this->getFirstTranslation('card_link');
        }

        return $url !== '' ? $url : null;
    }

    /**
     * Get the first available translation for an attribute.
     */
    public function getFirstTranslation(string $attribute): ?string
    {
        $translations = $this->getTranslations($attribute);

        return $translations === [] ? null : array_values($translations)[0];
    }
}
