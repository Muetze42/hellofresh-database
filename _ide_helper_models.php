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
 * @property string $external_id
 * @property string $name
 * @property string $type
 * @property string $icon_path
 * @property string|null $description
 * @property bool $triggers_traces_of
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ingredient> $ingredients
 * @property-read int|null $ingredients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @method static \Database\Factories\AllergenFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen query()
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereIconPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereTriggersTracesOf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allergen whereUpdatedAt($value)
 */
	class Allergen extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Category
 *
 * @property int $id
 * @property string $external_id
 * @property string $name
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @method static \Database\Factories\CategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 */
	class Category extends \Eloquent {}
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
 * @method static \Database\Factories\CountryFactory factory($count = null, $state = [])
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
 * App\Models\Cuisine
 *
 * @property int $id
 * @property string $external_id
 * @property string $type
 * @property string $name
 * @property string $icon_link
 * @property string $icon_path
 * @property int $usage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @method static \Database\Factories\CuisineFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Cuisine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cuisine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cuisine query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cuisine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cuisine whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cuisine whereIconLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cuisine whereIconPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cuisine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cuisine whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cuisine whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cuisine whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cuisine whereUsage($value)
 */
	class Cuisine extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Family
 *
 * @property int $id
 * @property string $external_id
 * @property string $uuid
 * @property string $name
 * @property string $type
 * @property string $icon_link
 * @property string $icon_path
 * @property string|null $description
 * @property array|null $usage_by_country
 * @property int $priority
 * @property \Illuminate\Support\Carbon|null $external_created_at
 * @property \Illuminate\Support\Carbon|null $external_updated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @method static \Database\Factories\FamilyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Family newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Family newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Family query()
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereExternalCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereExternalUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereIconLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereIconPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Family wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereUsageByCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Family whereUuid($value)
 */
	class Family extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Ingredient
 *
 * @property int $id
 * @property string $external_id
 * @property string $uuid
 * @property string $slug
 * @property string $type
 * @property string $country
 * @property string $image_link
 * @property string $image_path
 * @property string $name
 * @property string $internal_name
 * @property string|null $description
 * @property string|null $has_duplicated_name
 * @property bool $shipped
 * @property int $usage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Allergen> $allergens
 * @property-read int|null $allergens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @method static \Database\Factories\IngredientFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient query()
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereHasDuplicatedName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereImageLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereInternalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereShipped($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereUsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ingredient whereUuid($value)
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @method static \Database\Factories\LabelFactory factory($count = null, $state = [])
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
 * App\Models\Recipe
 *
 * @property int $id
 * @property int|null $label_id
 * @property int|null $category_id
 * @property int|null $family_id
 * @property string $external_id
 * @property string $uuid
 * @property string|null $name
 * @property string|null $canonical
 * @property string|null $canonical_link
 * @property string|null $card_link
 * @property string|null $cloned_from
 * @property string $headline
 * @property string|null $image_link
 * @property string|null $image_path
 * @property mixed|null|null $total_time
 * @property mixed|null|null $prep_time
 * @property string|null $country
 * @property string|null $comment
 * @property string|null $description
 * @property string|null $description_markdown
 * @property int $average_rating
 * @property int $favorites_count
 * @property int $ratings_count
 * @property int $serving_size
 * @property int $difficulty
 * @property bool $active
 * @property bool $is_addon
 * @property array $nutrition
 * @property array $steps
 * @property array $yields
 * @property int|null $external_created_at
 * @property int|null $external_updated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Allergen> $allergens
 * @property-read int|null $allergens_count
 * @property-read \App\Models\Category|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cuisine> $cuisines
 * @property-read int|null $cuisines_count
 * @property-read \App\Models\Family|null $family
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cuisine> $ingredients
 * @property-read int|null $ingredients_count
 * @property-read \App\Models\Label|null $label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Utensil> $utensils
 * @property-read int|null $utensils_count
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe active()
 * @method static \Database\Factories\RecipeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe query()
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereAverageRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereCanonical($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereCanonicalLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereCardLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereClonedFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereDescriptionMarkdown($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereDifficulty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereExternalCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereExternalUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereFamilyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereFavoritesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereHeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereImageLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereIsAddon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipe whereLabelId($value)
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
 * App\Models\Tag
 *
 * @property int $id
 * @property string $external_id
 * @property string $type
 * @property string $name
 * @property string $color_handle
 * @property array $preferences
 * @property bool $display_label
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @method static \Database\Factories\TagFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereColorHandle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereDisplayLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag wherePreferences($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereUpdatedAt($value)
 */
	class Tag extends \Eloquent {}
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

namespace App\Models{
/**
 * App\Models\Utensil
 *
 * @property int $id
 * @property string $external_id
 * @property string|null $type
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @method static \Database\Factories\UtensilFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Utensil newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Utensil newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Utensil query()
 * @method static \Illuminate\Database\Eloquent\Builder|Utensil whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Utensil whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Utensil whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Utensil whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Utensil whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Utensil whereUpdatedAt($value)
 */
	class Utensil extends \Eloquent {}
}

