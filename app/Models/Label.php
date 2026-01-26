<?php

namespace App\Models;

use App\Models\Concerns\ActivatableTrait;
use App\Models\Concerns\HasHandlesTrait;
use App\Models\Concerns\LogsModificationsTrait;
use Database\Factories\LabelFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin Builder<Label>
 */
class Label extends Model
{
    use ActivatableTrait;

    /** @use HasFactory<LabelFactory> */
    use HasFactory;

    use HasHandlesTrait;
    use HasTranslations;
    use LogsModificationsTrait;

    /**
     * The attributes that are translatable.
     *
     * @var list<string>
     */
    public array $translatable = [
        'name',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'foreground_color',
        'background_color',
        'display_label',
        'cached_recipes_count',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'handles',
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
     * Get the country that owns the label.
     *
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the recipes that have this label.
     *
     * @return HasMany<Recipe, $this>
     */
    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }
}
