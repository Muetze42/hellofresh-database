<?php

namespace App\Models;

use App\Contracts\Models\AbstractTranslatableModel;
use App\Contracts\Models\CountryTrait;
use App\Contracts\Models\UseHelloFreshIdTrait;
use App\Support\HelloFresh\IngredientAsset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredient extends AbstractTranslatableModel
{
    use HasFactory;
    use CountryTrait;
    use UseHelloFreshIdTrait;

    /**
     * The attributes that are translatable.
     */
    public array $translatable = ['name', 'description'];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'type',
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
     * Get a ingredient asset.
     */
    public function asset(): IngredientAsset
    {
        return new IngredientAsset($this->image_path);
    }
}
