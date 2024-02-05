<?php

namespace App\Models;

use App\Models\Traits\CountryTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    //use \Illuminate\Database\Eloquent\Factories\HasFactory; // Todo
    use CountryTrait;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'year_week',
        'start',
        'end',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'start' => 'date',
        'end' => 'date',
    ];

    /**
     * Get the country that owns the menu.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
