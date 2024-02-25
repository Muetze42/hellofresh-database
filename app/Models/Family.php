<?php

namespace App\Models;

use App\Contracts\Models\AbstractTranslatableModel;
use App\Contracts\Models\CountryTrait;
use App\Contracts\Models\UseHelloFreshIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Family extends AbstractTranslatableModel
{
    use HasFactory;
    use CountryTrait;
    use UseHelloFreshIdTrait;

    /**
     * The attributes that are translatable.
     */
    public array $translatable = ['name', 'description'];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'name',
        'type',
        'description',
        'priority',
        'icon_path',
        'external_created_at',
        'external_updated_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'priority' => 'int',
        'external_created_at' => 'datetime',
        'external_updated_at' => 'datetime',
    ];

    /**
     * Get the ingredients for the family.
     */
    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }
}
