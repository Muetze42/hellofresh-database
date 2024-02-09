<?php

namespace App\Models;

use App\Casts\HelloFreshTimeCast;
use App\Casts\RecipeNutritionCast;
use App\Casts\RecipeStepsCast;
use App\Casts\RecipeYieldsCast;
use App\Contracts\Models\CanActivateTrait;
use App\Contracts\Models\CountrySluggableRouteTrait;
use App\Contracts\Models\CountryTrait;
use App\Contracts\Models\HasTranslationsTrait;
use App\Contracts\Models\UseHelloFreshIdTrait;
use App\Support\HelloFresh\RecipeAsset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Recipe extends Model
{
    use HasFactory;
    use CountryTrait;
    use CanActivateTrait;
    use HasTranslationsTrait;
    use UseHelloFreshIdTrait;
    use CountrySluggableRouteTrait;

    /**
     * The attributes that are translatable.
     */
    public array $translatable = [
        'name',
        'description',
        'headline',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'average_rating',
        'card_link',
        'cloned_from',
        'external_created_at',
        'description',
        'difficulty',
        'favorites_count',
        'headline',
        'image_path',
        'is_addon',
        'name',
        'nutrition',
        'ratings_count',
        'serving_size',
        'total_time',
        'prep_time',
        'uuid',
        'external_created_at',
        'external_updated_at',
        'steps',
        'yields',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'average_rating' => 'int',
        'difficulty' => 'int',
        'favorites_count' => 'int',
        'ratings_count' => 'int',
        'serving_size' => 'int',
        'is_addon' => 'bool',
        'external_created_at' => 'datetime',
        'external_updated_at' => 'datetime',
        //'nutrition' => RecipeNutritionCast::class,
        //'steps' => RecipeStepsCast::class,
        //'yields' => RecipeYieldsCast::class,
        //'total_time' => HelloFreshTimeCast::class,
        //'prep_time' => HelloFreshTimeCast::class,
        'nutrition' => 'array',
        'steps' => 'array',
        'yields' => 'array',
        'total_time' => 'array',
        'prep_time' => 'array',
    ];

    /**
     * The allergens that belong to the recipe.
     */
    public function allergens(): BelongsToMany
    {
        return $this->belongsToMany(Allergen::class);
    }

    /**
     * The cuisines that belong to the recipe.
     */
    public function cuisines(): BelongsToMany
    {
        return $this->belongsToMany(Cuisine::class);
    }

    /**
     * The ingredients that belong to the recipe.
     */
    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class);
    }

    /**
     * The tags that belong to the recipe.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * The utensils that belong to the recipe.
     */
    public function utensils(): BelongsToMany
    {
        return $this->belongsToMany(Utensil::class);
    }

    /**
     * Get the label that owns the recipe.
     */
    public function label(): BelongsTo
    {
        return $this->belongsTo(Label::class);
    }

    /**
     * Get the category that owns the recipe.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the country that owns the recipe.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * The menus that belong to the recipe.
     */
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class);
    }

    /**
     * Get a recipe asset.
     */
    public function asset(): RecipeAsset
    {
        return new RecipeAsset($this->image_path, $this->card_link);
    }
}
