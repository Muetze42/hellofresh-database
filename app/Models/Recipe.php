<?php

namespace App\Models;

use App\Casts\HelloFreshTimeCast;
use App\Models\Traits\CanActivateTrait;
use App\Models\Traits\CountryTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Recipe extends Model
{
    //use \Illuminate\Database\Eloquent\Factories\HasFactory; // Todo
    use CountryTrait;
    use CanActivateTrait;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'external_id',
        'average_rating',
        'canonical',
        'canonical_link',
        'card_link',
        'cloned_from',
        'comment',
        'country',
        'external_created_at',
        'description',
        'description_markdown',
        'difficulty',
        'favorites_count',
        'headline',
        'image_link',
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
        'external_created_at' => 'timestamp',
        'external_updated_at' => 'timestamp',
        'nutrition' => 'array',
        'steps' => 'array',
        'yields' => 'array',
        'total_time' => HelloFreshTimeCast::class,
        'prep_time' => HelloFreshTimeCast::class,
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
        return $this->belongsToMany(Cuisine::class);
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
}
