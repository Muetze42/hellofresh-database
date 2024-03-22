<?php

namespace App\Models;

use App\Casts\LowerArrayCast;
use App\Casts\UpperStringCast;
use App\Contracts\Models\CanActivateTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Country extends Model
{
    use HasFactory;
    use CanActivateTrait;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'code',
        'locales',
        'domain',
        'data',
        'take',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'take',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'code' => UpperStringCast::class,
            'locales' => LowerArrayCast::class,
            'data' => 'array',
            'take' => 'int',
        ];
    }

    /**
     * Switch to this country.
     */
    public function switch(?string $locale = null): void
    {
        if (!$locale) {
            $locale = $this->locales[0];
        }

        App::setCountry($this);
        App::setLocale($locale);
    }

    /**
     * Get public route path for country.
     */
    protected function route(): Attribute
    {
        return new Attribute(
            get: fn () => '/' . Str::lower($this->code) . '-' . app()->getLocale(),
        );
    }
}
