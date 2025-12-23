<?php

namespace App\Models;

use Database\Factories\CountryFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property list<string> $locales
 *
 * @mixin Builder<Country>
 */
class Country extends Model
{
    /** @use HasFactory<CountryFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'locales',
        'domain',
        'prep_min',
        'prep_max',
        'total_min',
        'total_max',
        'recipes_count',
        'ingredients_count',
        'take',
        'active',
        'has_allergens',
        'has_cuisines',
        'has_labels',
        'has_tags',
        'has_utensil',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'locales',
        'domain',
        'prep_min',
        'prep_max',
        'total_min',
        'total_max',
        'take',
        'has_allergens',
        'has_cuisines',
        'has_labels',
        'has_tags',
        'has_utensil',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'locales' => 'array',
            'prep_min' => 'int',
            'prep_max' => 'int',
            'total_min' => 'int',
            'total_max' => 'int',
            'recipes_count' => 'int',
            'ingredients_count' => 'int',
            'take' => 'int',
            'active' => 'bool',
            'has_allergens' => 'bool',
            'has_cuisines' => 'bool',
            'has_labels' => 'bool',
            'has_tags' => 'bool',
            'has_utensil' => 'bool',
        ];
    }

    /**
     * Get the recipes for the country.
     *
     * @return HasMany<Recipe, $this>
     */
    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    /**
     * Get the allergens for the country.
     *
     * @return HasMany<Allergen, $this>
     */
    public function allergens(): HasMany
    {
        return $this->hasMany(Allergen::class);
    }

    /**
     * Get the cuisines for the country.
     *
     * @return HasMany<Cuisine, $this>
     */
    public function cuisines(): HasMany
    {
        return $this->hasMany(Cuisine::class);
    }

    /**
     * Get the ingredients for the country.
     *
     * @return HasMany<Ingredient, $this>
     */
    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }

    /**
     * Get the labels for the country.
     *
     * @return HasMany<Label, $this>
     */
    public function labels(): HasMany
    {
        return $this->hasMany(Label::class);
    }

    /**
     * Get the tags for the country.
     *
     * @return HasMany<Tag, $this>
     */
    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    /**
     * Get the utensils for the country.
     *
     * @return HasMany<Utensil, $this>
     */
    public function utensils(): HasMany
    {
        return $this->hasMany(Utensil::class);
    }

    /**
     * Get the menus for the country.
     *
     * @return HasMany<Menu, $this>
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    /**
     * Get the favorites for the country.
     *
     * @return HasMany<Favorite, $this>
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get the recipe lists for the country.
     *
     * @return HasMany<RecipeList, $this>
     */
    public function recipeLists(): HasMany
    {
        return $this->hasMany(RecipeList::class);
    }

    /**
     * Get the shopping lists for the country.
     *
     * @return HasMany<ShoppingList, $this>
     */
    public function shoppingLists(): HasMany
    {
        return $this->hasMany(ShoppingList::class);
    }

    /**
     * Scope a query to only include active countries.
     *
     * @param  Builder<Country>  $query
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where(static function (Builder $query): void {
            $query->where('active', true)
                ->whereNotNull('prep_min')
                ->whereNotNull('prep_min')
                ->whereNotNull('recipes_count')
                ->whereNotNull('ingredients_count');
        });
    }
}
