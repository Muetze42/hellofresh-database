<?php

namespace App\Models;

use App\Models\Concerns\ActivatableTrait;
use App\Models\Concerns\HasHelloFreshIdsTrait;
use Database\Factories\CuisineFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin Builder<Cuisine>
 */
class Cuisine extends Model
{
    use ActivatableTrait;

    /** @use HasFactory<CuisineFactory> */
    use HasFactory;

    use HasHelloFreshIdsTrait;
    use HasTranslations;

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
        'icon_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'hellofresh_ids',
        'icon_path',
    ];

    /**
     * Get the country that owns the cuisine.
     *
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the recipes that have this cuisine.
     *
     * @return BelongsToMany<Recipe, $this>
     */
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class);
    }
}
