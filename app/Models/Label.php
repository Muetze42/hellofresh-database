<?php

namespace App\Models;

use App\Contracts\Models\CountryTrait;
use App\Contracts\Models\HasTranslationsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Label extends Model
{
    use HasFactory;
    use CountryTrait;
    use HasTranslationsTrait;

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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'display_label' => 'bool',
    ];

    /**
     * Get the recipes for the label.
     */
    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }
}
