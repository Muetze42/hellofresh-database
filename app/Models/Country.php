<?php

namespace App\Models;

use App\Casts\LowerStringCast;
use App\Casts\UpperStringCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Country extends Model
{
    //use \Illuminate\Database\Eloquent\Factories\HasFactory; // Todo

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'country',
        'locale',
        'domain',
        'data',
        'take',
        'recipes',
        'ingredients',
        'active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'country' => UpperStringCast::class,
        'locale' => LowerStringCast::class,
        'data' => 'array',
        'take' => 'int',
        'recipes' => 'int',
        'ingredients' => 'int',
        'active' => 'bool',
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
