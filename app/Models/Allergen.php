<?php

namespace App\Models;

use App\Models\Traits\CountryTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Allergen extends Model
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
        'name',
        'description',
        'icon_path',
    ];

    /**
     * The recipes that belong to the allergen.
     */
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class);
    }

    /**
     * The ingredients that belong to allergen.
     */
    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class);
    }
}
