<?php

namespace App\Models;

use App\Contracts\Models\AbstractTranslatableModel;
use App\Contracts\Models\CountryTrait;
use App\Contracts\Models\UseHelloFreshIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cuisine extends AbstractTranslatableModel
{
    use HasFactory;
    use CountryTrait;
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
        'icon_path',
    ];

    /**
     * The recipes that belong to the cuisine.
     */
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class);
    }
}
