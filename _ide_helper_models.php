<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property array $name
 * @property string $type
 * @property string|null $icon_path
 * @property array|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ingredient> $ingredients
 * @property-read int|null $ingredients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen query()
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereIconPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AbstractTranslatableModel whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder|AbstractTranslatableModel whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereUpdatedAt($value)
 */
	class Allergen extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property array $name
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AbstractTranslatableModel whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder|AbstractTranslatableModel whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $code
 * @property mixed $locales
 * @property string $domain
 * @property array|null $data
 * @property int $take
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $route
 * @method static \Illuminate\Database\Eloquent\Builder|Country active()
 * @method static \Illuminate\Database\Eloquent\Builder|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Country query()
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereLocales($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereTake($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereUpdatedAt($value)
 */
	class Country extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $type
 * @property array $name
 * @property string|null $icon_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder|Cuisine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cuisine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cuisine query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cuisine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cuisine whereIconPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cuisine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AbstractTranslatableModel whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder|AbstractTranslatableModel whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder|Cuisine whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cuisine whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cuisine whereUpdatedAt($value)
 */
	class Cuisine extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $uuid
 * @property array $name
 * @property string $type
 * @property string|null $icon_path
 * @property array|null $description
 * @property int $priority
 * @property \Illuminate\Support\Carbon|null $external_created_at
 * @property \Illuminate\Support\Carbon|null $external_updated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ingredient> $ingredients
 * @property-read int|null $ingredients_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder|Family newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Family newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Family query()
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereExternalCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereExternalUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereIconPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AbstractTranslatableModel whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder|AbstractTranslatableModel whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Family wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereUuid($value)
 */
	class Family extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $data
 * @method static \Illuminate\Database\Eloquent\Builder|Filter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Filter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Filter query()
 * @method static \Illuminate\Database\Eloquent\Builder|Filter whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Filter whereId($value)
 */
	class Filter extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string|null $family_id
 * @property string $uuid
 * @property array $name
 * @property string $type
 * @property string|null $image_path
 * @property array|null $description
 * @property bool $shipped
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Allergen> $allergens
 * @property-read int|null $allergens_count
 * @property-read \App\Models\Family|null $family
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient query()
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereFamilyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AbstractTranslatableModel whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder|AbstractTranslatableModel whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereShipped($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereUuid($value)
 */
	class Ingredient extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $handle
 * @property array $text
 * @property string|null $foreground_color
 * @property string|null $background_color
 * @property bool $display_label
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder|Label active()
 * @method static \Illuminate\Database\Eloquent\Builder|Label newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Label newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Label query()
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereDisplayLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereForegroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereHandle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AbstractTranslatableModel whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder|AbstractTranslatableModel whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereUpdatedAt($value)
 */
	class Label extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $year_week
 * @property \Illuminate\Support\Carbon $start
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @method static \Illuminate\Database\Eloquent\Builder|Menu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Menu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Menu query()
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereYearWeek($value)
 */
	class Menu extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property int|null $label_id
 * @property string|null $category_id
 * @property string|null $uuid
 * @property array|null $name
 * @property string|null $card_link
 * @property string|null $cloned_from
 * @property array $headline
 * @property string|null $image_path
 * @property string|null $total_time
 * @property string|null $prep_time
 * @property int|null $minutes
 * @property array|null $description
 * @property int $average_rating
 * @property int $favorites_count
 * @property int $ratings_count
 * @property int $serving_size
 * @property int $difficulty
 * @property bool $active
 * @property bool $is_addon
 * @property array|null $nutrition
 * @property array|null $steps
 * @property array|null $yields
 * @property \Illuminate\Support\Carbon|null $external_created_at
 * @property \Illuminate\Support\Carbon|null $external_updated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Allergen> $allergens
 * @property-read int|null $allergens_count
 * @property-read \App\Models\Category|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cuisine> $cuisines
 * @property-read int|null $cuisines_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ingredient> $ingredients
 * @property-read int|null $ingredients_count
 * @property-read \App\Models\Label|null $label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Menu> $menus
 * @property-read int|null $menus_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read mixed $translations
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Utensil> $utensils
 * @property-read int|null $utensils_count
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe active()
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe query()
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereAverageRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereCardLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereClonedFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereDifficulty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereExternalCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereExternalUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereFavoritesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereHeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereIsAddon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereLabelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AbstractTranslatableModel whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder|AbstractTranslatableModel whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereNutrition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe wherePrepTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereRatingsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereServingSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereSteps($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereTotalTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereYields($value)
 */
	class Recipe extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $type
 * @property array $name
 * @property string|null $color_handle
 * @property array|null $preferences
 * @property bool $display_label
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder|Tag active()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereColorHandle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereDisplayLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AbstractTranslatableModel whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder|AbstractTranslatableModel whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag wherePreferences($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereUpdatedAt($value)
 */
	class Tag extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $active_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereActiveAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
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

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string|null $type
 * @property array $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder|Utensil newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Utensil newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Utensil query()
 * @method static \Illuminate\Database\Eloquent\Builder|Utensil whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Utensil whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AbstractTranslatableModel whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder|AbstractTranslatableModel whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder|Utensil whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Utensil whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Utensil whereUpdatedAt($value)
 */
	class Utensil extends \Eloquent {}
}

