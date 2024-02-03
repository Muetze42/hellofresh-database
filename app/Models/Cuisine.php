<?php

namespace App\Models;

use App\Models\Traits\CountryTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cuisine extends Model
{
    use HasFactory;
    use CountryTrait;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'external_id',
        'type',
        'name',
        'icon_link',
        'icon_path',
        'usage',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'usage' => 'int',
    ];

    /**
     * The recipes that belong to the cuisine.
     */
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class);
    }
}
