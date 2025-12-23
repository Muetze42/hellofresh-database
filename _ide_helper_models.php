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
 * @mixin Builder<Allergen>
 * @property int $id
 * @property int $country_id
 * @property array<array-key, mixed> $name
 * @property string|null $icon_path
 * @property array<array-key, mixed>|null $hellofresh_ids
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Country $country
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ingredient> $ingredients
 * @property-read int|null $ingredients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergen active()
 * @method static \Database\Factories\AllergenFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergen newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergen newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergen query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergen whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergen whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergen whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergen whereHellofreshIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergen whereIconPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergen whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergen whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergen whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergen whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergen whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergen whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Allergen whereUpdatedAt($value)
 */
	class Allergen extends \Eloquent {}
}

namespace App\Models{
/**
 * @property list<string> $locales
 * @mixin Builder<Country>
 * @property int $id
 * @property string $code
 * @property string $domain
 * @property int|null $prep_min
 * @property int|null $prep_max
 * @property int|null $total_min
 * @property int|null $total_max
 * @property-read int|null $recipes_count
 * @property-read int|null $ingredients_count
 * @property int $take
 * @property bool $active
 * @property bool $has_allergens
 * @property bool $has_cuisines
 * @property bool $has_labels
 * @property bool $has_tags
 * @property bool $has_utensil
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Allergen> $allergens
 * @property-read int|null $allergens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cuisine> $cuisines
 * @property-read int|null $cuisines_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Favorite> $favorites
 * @property-read int|null $favorites_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ingredient> $ingredients
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Label> $labels
 * @property-read int|null $labels_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Menu> $menus
 * @property-read int|null $menus_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RecipeList> $recipeLists
 * @property-read int|null $recipe_lists_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShoppingList> $shoppingLists
 * @property-read int|null $shopping_lists_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Utensil> $utensils
 * @property-read int|null $utensils_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country active()
 * @method static \Database\Factories\CountryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereHasAllergens($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereHasCuisines($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereHasLabels($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereHasTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereHasUtensil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereIngredientsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereLocales($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country wherePrepMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country wherePrepMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereRecipesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereTake($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereTotalMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereTotalMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereUpdatedAt($value)
 */
	class Country extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin Builder<Cuisine>
 * @property int $id
 * @property int $country_id
 * @property array<array-key, mixed> $name
 * @property string|null $icon_path
 * @property array<array-key, mixed>|null $hellofresh_ids
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Country $country
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine active()
 * @method static \Database\Factories\CuisineFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine whereHellofreshIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine whereIconPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cuisine whereUpdatedAt($value)
 */
	class Cuisine extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin Builder<Favorite>
 * @property int $id
 * @property int $user_id
 * @property int $country_id
 * @property int $recipe_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Country $country
 * @property-read \App\Models\Recipe $recipe
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\FavoriteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite whereRecipeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite whereUserId($value)
 */
	class Favorite extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin Builder<Ingredient>
 * @property int $id
 * @property int $country_id
 * @property array<array-key, mixed> $name
 * @property string|null $name_slug
 * @property array<array-key, mixed>|null $hellofresh_ids
 * @property string|null $image_path
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Allergen> $allergens
 * @property-read int|null $allergens_count
 * @property-read \App\Models\Country $country
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingredient active()
 * @method static \Database\Factories\IngredientFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingredient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingredient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingredient query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingredient whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingredient whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingredient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingredient whereHellofreshIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingredient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingredient whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingredient whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingredient whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingredient whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingredient whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingredient whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingredient whereNameSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingredient whereUpdatedAt($value)
 */
	class Ingredient extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin Builder<Label>
 * @property int $id
 * @property array<array-key, mixed> $handles
 * @property int $country_id
 * @property array<array-key, mixed> $name
 * @property string|null $foreground_color
 * @property string|null $background_color
 * @property bool $display_label
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Country $country
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Label active()
 * @method static \Database\Factories\LabelFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Label newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Label newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Label query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Label whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Label whereBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Label whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Label whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Label whereDisplayLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Label whereForegroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Label whereHandles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Label whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Label whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Label whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Label whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Label whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Label whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Label whereUpdatedAt($value)
 */
	class Label extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $year_week
 * @property Carbon $start
 * @mixin Builder<Menu>
 * @property int $id
 * @property int $country_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Country $country
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu active()
 * @method static \Database\Factories\MenuFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereYearWeek($value)
 */
	class Menu extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin Builder<Recipe>
 * @property int $id
 * @property string $hellofresh_id
 * @property int $country_id
 * @property int|null $label_id
 * @property int|null $canonical_id
 * @property array<array-key, mixed> $name
 * @property array<array-key, mixed>|null $headline
 * @property array<array-key, mixed>|null $description
 * @property int|null $difficulty
 * @property int|null $prep_time
 * @property int|null $total_time
 * @property string|null $image_path
 * @property array<array-key, mixed>|null $card_link
 * @property array<array-key, mixed>|null $steps_primary
 * @property array<array-key, mixed>|null $steps_secondary
 * @property array<array-key, mixed>|null $nutrition_primary
 * @property array<array-key, mixed>|null $nutrition_secondary
 * @property array<array-key, mixed>|null $yields_primary
 * @property array<array-key, mixed>|null $yields_secondary
 * @property bool $has_pdf
 * @property \Illuminate\Support\Carbon|null $hellofresh_created_at
 * @property \Illuminate\Support\Carbon|null $hellofresh_updated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Allergen> $allergens
 * @property-read int|null $allergens_count
 * @property-read Recipe|null $canonical
 * @property-read string|null $card_image_url
 * @property-read \App\Models\Country $country
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cuisine> $cuisines
 * @property-read int|null $cuisines_count
 * @property-read string|null $header_image_url
 * @property-read string|null $hellofresh_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ingredient> $ingredients
 * @property-read int|null $ingredients_count
 * @property-read \App\Models\Label|null $label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Menu> $menus
 * @property-read int|null $menus_count
 * @property-read string|null $pdf_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read mixed $translations
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Utensil> $utensils
 * @property-read int|null $utensils_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Recipe> $variants
 * @property-read int|null $variants_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe active()
 * @method static \Database\Factories\RecipeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereCanonicalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereCardLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereDifficulty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereHasPdf($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereHeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereHellofreshCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereHellofreshId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereHellofreshUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereLabelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereNutritionPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereNutritionSecondary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe wherePrepTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereStepsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereStepsSecondary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereTotalTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereYieldsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe whereYieldsSecondary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipe withoutTrashed()
 */
	class Recipe extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin Builder<RecipeList>
 * @property int $id
 * @property int $user_id
 * @property int $country_id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Country $country
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\RecipeListFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecipeList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecipeList newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecipeList query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecipeList whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecipeList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecipeList whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecipeList whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecipeList whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecipeList whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecipeList whereUserId($value)
 */
	class RecipeList extends \Eloquent {}
}

namespace App\Models{
/**
 * @property array<int, array{recipe_id: int, servings: int}> $items
 * @mixin Builder<ShoppingList>
 * @property int $id
 * @property int $user_id
 * @property int $country_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Country $country
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\ShoppingListFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShoppingList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShoppingList newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShoppingList query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShoppingList whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShoppingList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShoppingList whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShoppingList whereItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShoppingList whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShoppingList whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShoppingList whereUserId($value)
 */
	class ShoppingList extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin Builder<Tag>
 * @property int $id
 * @property int $country_id
 * @property array<array-key, mixed> $name
 * @property array<array-key, mixed>|null $hellofresh_ids
 * @property bool $active
 * @property bool $display_label
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Country $country
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag active()
 * @method static \Database\Factories\TagFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereDisplayLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereHellofreshIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereUpdatedAt($value)
 */
	class Tag extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin Builder<User>
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property bool $admin
 * @property \Illuminate\Support\Carbon|null $active_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Favorite> $favorites
 * @property-read int|null $favorites_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RecipeList> $recipeLists
 * @property-read int|null $recipe_lists_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShoppingList> $shoppingLists
 * @property-read int|null $shopping_lists_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereActiveAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin Builder<Utensil>
 * @property int $id
 * @property int $country_id
 * @property array<array-key, mixed> $name
 * @property string|null $type
 * @property array<array-key, mixed>|null $hellofresh_ids
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Country $country
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipe> $recipes
 * @property-read int|null $recipes_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utensil active()
 * @method static \Database\Factories\UtensilFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utensil newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utensil newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utensil query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utensil whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utensil whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utensil whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utensil whereHellofreshIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utensil whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utensil whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utensil whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utensil whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utensil whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utensil whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utensil whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utensil whereUpdatedAt($value)
 */
	class Utensil extends \Eloquent {}
}

