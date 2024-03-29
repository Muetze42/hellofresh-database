<?php

namespace App\Models;

use App\Contracts\Models\AbstractTranslatableModel;
use App\Contracts\Models\CanActivateTrait;
use App\Contracts\Models\CountryTrait;
use App\Contracts\Models\UseHelloFreshIdTrait;
use App\Support\HelloFresh\RecipeAsset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Recipe extends AbstractTranslatableModel
{
    use HasFactory;
    use CountryTrait;
    use CanActivateTrait;
    use UseHelloFreshIdTrait;

    /**
     * Retrieve the model for a bound value.
     */
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        return $this->active()->where('id', explode('-', $value)[0])->firstOrFail();
    }

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
        'minutes',
        'uuid',
        'external_created_at',
        'external_updated_at',
        'steps',
        'yields',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'average_rating' => 'int',
            'difficulty' => 'int',
            'favorites_count' => 'int',
            'ratings_count' => 'int',
            'serving_size' => 'int',
            'is_addon' => 'bool',
            'external_created_at' => 'datetime',
            'external_updated_at' => 'datetime',
            'nutrition' => 'array',
            'steps' => 'array',
            'yields' => 'array',
            'minutes' => 'int',
        ];
    }

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

    /**
     * Perform any actions required after the model boots.
     */
    public static function booted(): void
    {
        static::saving(function (self $recipe) {
            $minutes = $recipe->prep_time ? iso8601ToMinutes($recipe->prep_time) : 0;
            $recipe->minutes = $minutes > 0 ? $minutes : null;
        });
    }
}
