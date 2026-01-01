<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\PersonalAccessToken as Model;

class PersonalAccessToken extends Model
{
    use SoftDeletes;

    /**
     * Get the usages for the token.
     *
     * @return HasMany<PersonalAccessTokenUsage, $this>
     */
    public function usages(): HasMany
    {
        return $this->hasMany(PersonalAccessTokenUsage::class, 'token_id');
    }
}
