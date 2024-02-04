<?php

namespace App\Models;

use App\Models\Traits\CountryTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Ingredient extends Model
{
    use HasFactory;
    use CountryTrait;
    use HasTranslations;

    /**
     * The attributes that are translatable.
     */
    public array $translatable = ['name', 'description'];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'external_id',
        'uuid',
        'type',
        'image_link',
        'image_path',
        'name',
        'shipped',
        'description',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'shipped' => 'bool',
    ];

    /**
     * The recipes that belong to the ingredient.
     */
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class);
    }

    /**
     * The allergens that belong to ingredient.
     */
    public function allergens(): BelongsToMany
    {
        return $this->belongsToMany(Allergen::class);
    }

    /**
     * Get the family that owns the ingredient.
     */
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * Get the country that owns the recipe.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
