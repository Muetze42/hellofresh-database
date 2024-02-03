<?php

namespace App\Models;

use App\Models\Traits\CountryTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Family extends Model
{
    use HasFactory;
    use CountryTrait;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'external_id',
        'uuid',
        'name',
        'type',
        'description',
        'priority',
        'icon_link',
        'icon_path',
        'usage_by_country',
        'external_created_at',
        'external_updated_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'priority' => 'int',
        'usage_by_country' => 'array',
        'external_created_at' => 'datetime',
        'external_updated_at' => 'datetime',
    ];

    /**
     * Get the recipes for the family.
     */
    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }
}
