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
     * Retrieve the model for a bound value.
     */
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        return $this->where('year_week', $value)->firstOrFail();
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'year_week',
        'start',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start' => 'date',
        ];
    }

    /**
     * The recipes that belong to the menu.
     */
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class);
    }
}
