<?php

namespace App\Models;

use App\Models\Concerns\ActivatableTrait;
use App\Models\Concerns\HasHelloFreshIdsTrait;
use Database\Factories\UtensilFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin Builder<Utensil>
 */
class Utensil extends Model
{
    use ActivatableTrait;

    /** @use HasFactory<UtensilFactory> */
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
        'type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'hellofresh_ids',
        'type',
    ];

    /**
     * Get the country that owns the utensil.
     *
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the recipes that use this utensil.
     *
     * @return BelongsToMany<Recipe, $this>
     */
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class);
    }
}
