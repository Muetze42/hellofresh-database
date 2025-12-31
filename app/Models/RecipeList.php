<?php

namespace App\Models;

use Database\Factories\RecipeListFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Builder<RecipeList>
 */
class RecipeList extends Model
{
    /** @use HasFactory<RecipeListFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the user that owns the recipe list.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the country for this recipe list.
     *
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the recipes in this list.
     *
     * @return BelongsToMany<Recipe, $this>
     */
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class)
            ->withPivot('added_at')
            ->orderByPivot('added_at', 'desc');
    }

    /**
     * Get the users this list is shared with.
     *
     * @return BelongsToMany<User, $this>
     */
    public function sharedWith(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'recipe_list_shares')
            ->withPivot('created_at');
    }

    /**
     * Get the activities for this list.
     *
     * @return HasMany<RecipeListActivity, $this>
     */
    public function activities(): HasMany
    {
        return $this->hasMany(RecipeListActivity::class)->latest();
    }

    /**
     * Check if a user can access this list.
     */
    public function isAccessibleBy(?User $user): bool
    {
        if (! $user instanceof User) {
            return false;
        }

        return $this->user_id === $user->id
            || $this->sharedWith()->where('users.id', $user->id)->exists();
    }

    /**
     * Check if a user is the owner of this list.
     */
    public function isOwnedBy(?User $user): bool
    {
        if (! $user instanceof User) {
            return false;
        }

        return $this->user_id === $user->id;
    }
}
