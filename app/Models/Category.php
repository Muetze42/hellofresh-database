<?php

namespace App\Models;

use App\Contracts\Models\CountryTrait;
use App\Contracts\Models\HasTranslationsTrait;
use App\Contracts\Models\UseHelloFreshIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    use CountryTrait;
    use HasTranslationsTrait;
    use UseHelloFreshIdTrait;

    /**
     * The attributes that are translatable.
     */
    public array $translatable = ['name'];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'type',
    ];

    /**
     * Get the recipes for the category.
     */
    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }
}
