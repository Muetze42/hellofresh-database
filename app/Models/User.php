<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin Builder<User>
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
            'active_at' => 'datetime',
            'admin' => 'bool',
        ];
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification(mixed $token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the favorites for the user.
     *
     * @return HasMany<Favorite, $this>
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get the recipe lists for the user.
     *
     * @return HasMany<RecipeList, $this>
     */
    public function recipeLists(): HasMany
    {
        return $this->hasMany(RecipeList::class);
    }

    /**
     * Get the shopping lists for the user.
     *
     * @return HasMany<ShoppingList, $this>
     */
    public function shoppingLists(): HasMany
    {
        return $this->hasMany(ShoppingList::class);
    }

    /**
     * Get the email verifications of the user.
     *
     * @return HasMany<EmailVerification, $this>
     */
    public function emailVerifications(): HasMany
    {
        return $this->hasMany(EmailVerification::class);
    }

    /**
     * Mark the given user's email as verified.
     */
    public function markEmailAsVerified(): bool
    {
        $this->emailVerifications()->updateOrCreate(
            ['email' => $this->email],
            ['verified_at' => now()]
        );

        return parent::markEmailAsVerified();
    }

    /**
     * Get the recipe lists shared with this user.
     *
     * @return BelongsToMany<RecipeList, $this>
     */
    public function sharedRecipeLists(): BelongsToMany
    {
        return $this->belongsToMany(RecipeList::class, 'recipe_list_shares')
            ->withPivot('created_at');
    }
}
