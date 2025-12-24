<?php

namespace App\Models;

use App\Enums\RecipeListActionEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Builder<RecipeListActivity>
 */
class RecipeListActivity extends Model
{
    public const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'action',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'action' => RecipeListActionEnum::class,
        ];
    }

    /**
     * Get the recipe list this activity belongs to.
     *
     * @return BelongsTo<RecipeList, $this>
     */
    public function recipeList(): BelongsTo
    {
        return $this->belongsTo(RecipeList::class);
    }

    /**
     * Get the user who performed this action.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the recipe this activity is about.
     *
     * @return BelongsTo<Recipe, $this>
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
