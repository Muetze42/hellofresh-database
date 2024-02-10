<?php

namespace App\Models;

use App\Contracts\Models\CountryTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Menu extends Model
{
    use HasFactory;
    use CountryTrait;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'year_week',
        'start',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'start' => 'date',
    ];

    /**
     * The recipes that belong to the menu.
     */
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class);
    }
}
