<?php

namespace App\Models;

use App\Casts\LowerArrayCast;
use App\Casts\UpperStringCast;
use App\Models\Traits\CanActivateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\App;

class Country extends Model
{
    use HasFactory;
    use CanActivateTrait;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'country',
        'locale',
        'domain',
        'take',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'take',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'country' => UpperStringCast::class,
        'locales' => LowerArrayCast::class,
        'take' => 'int',
    ];

    /**
     * Switch to this country.
     */
    public function switch(string $locale): void
    {
        App::setCountry($this);
        App::setLocale($locale);
    }

    /**
     * Get the recipes for the country.
     */
    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    /**
     * Get the ingredients for the country.
     */
    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }

    /**
     * Get the menus for the country.
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }
}
