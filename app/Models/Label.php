<?php

namespace App\Models;

use App\Contracts\Models\AbstractTranslatableModel;
use App\Contracts\Models\CountryTrait;
use App\Contracts\Models\HasActiveDisplayTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Label extends AbstractTranslatableModel
{
    use HasFactory;
    use CountryTrait;
    use HasActiveDisplayTrait;

    /**
     * The attributes that are translatable.
     */
    public array $translatable = ['text'];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'text',
        'handle',
        'foreground_color',
        'background_color',
        'display_label',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'display_label' => 'bool',
        ];
    }

    /**
     * Get the recipes for the label.
     */
    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }
}
