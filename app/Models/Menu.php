<?php

namespace App\Models;

use App\Models\Concerns\ActivatableTrait;
use App\Models\Concerns\LogsModificationsTrait;
use Carbon\Carbon;
use Database\Factories\MenuFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $year_week
 * @property Carbon $start
 *
 * @mixin Builder<Menu>
 */
class Menu extends Model
{
    use ActivatableTrait;

    /** @use HasFactory<MenuFactory> */
    use HasFactory;

    use LogsModificationsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'year_week',
        'start',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'year_week',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'year_week' => 'int',
            'start' => 'date',
        ];
    }

    /**
     * Get the country that owns the menu.
     *
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the recipes in this menu.
     *
     * @return BelongsToMany<Recipe, $this>
     */
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class);
    }

    /**
     * Scope a query to only selectable menus.
     *
     * @param  Builder<Menu>  $query
     */
    #[Scope]
    protected function selectable(Builder $query): void
    {
        $query->where('start', '>=', now()->subWeeks(2));
    }
}
