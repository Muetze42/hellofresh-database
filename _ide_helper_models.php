<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Allergen
 *
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string $type
 * @property string $slug
 * @property string $icon_path
 * @property string|null $description
 * @property bool $triggers_traces_of
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen query()
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereIconPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereTriggersTracesOf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereUuid($value)
 */
	class Allergen extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Country
 *
 * @property int $id
 * @property string $country
 * @property string $locale
 * @property string $domain
 * @property array|null $data
 * @property int $take
 * @property int|null $recipes
 * @property int|null $ingredients
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Country active()
 * @method static \Illuminate\Database\Eloquent\Builder|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Country query()
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereIngredients($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereRecipes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereTake($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereUpdatedAt($value)
 */
	class Country extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Ingredient
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient query()
 */
	class Ingredient extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Label
 *
 * @property int $id
 * @property string $text
 * @property string $handle
 * @property string $foreground_color
 * @property string $background_color
 * @property bool $display_label
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Label newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Label newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Label query()
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereDisplayLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereForegroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereHandle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereUpdatedAt($value)
 */
	class Label extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

