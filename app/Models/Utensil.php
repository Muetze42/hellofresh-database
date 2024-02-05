<?php

namespace App\Models;

use App\Models\Traits\CountryTrait;
use App\Models\Traits\HasTranslationsTrait;
use App\Models\Traits\UseHelloFreshIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Utensil extends Model
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
        'type',
        'name',
    ];

    /**
     * The recipes that belong to the utensil.
     */
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class);
    }
}
