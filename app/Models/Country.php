<?php

namespace App\Models;

use App\Casts\LowerStringCast;
use App\Casts\UpperStringCast;
use App\Models\Traits\CanActivateTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Country extends Model
{
    //use \Illuminate\Database\Eloquent\Factories\HasFactory; // Todo
    use CanActivateTrait;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'country',
        'locale',
        'domain',
        'data',
        'take',
        'recipes',
        'ingredients',
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
        'locale' => LowerStringCast::class,
        'data' => 'array',
        'take' => 'int',
        'recipes' => 'int',
        'ingredients' => 'int',
    ];

    /**
     * Switch to this country.
     */
    public function switch(): void
    {
        App::setLocale($this->locale);
        App::setCountry($this->country);
    }
}
