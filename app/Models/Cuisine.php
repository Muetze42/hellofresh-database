<?php

namespace App\Models;

use App\Models\Traits\CountryTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Cuisine extends Model
{
    use HasFactory;
    use CountryTrait;
    use HasTranslations;

    /**
     * The attributes that are translatable.
     */
    public array $translatable = ['name'];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'external_id',
        'type',
        'name',
        'icon_link',
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
