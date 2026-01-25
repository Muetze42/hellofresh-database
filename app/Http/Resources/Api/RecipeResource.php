<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\Api\Concerns\HasTranslationFallbackTrait;
use App\Models\Recipe;
use App\Models\RecipeList;
use App\Models\User;
use App\Support\Api\ContentLocale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

/**
 * @mixin Recipe
 */
class RecipeResource extends JsonResource
{
    use HasTranslationFallbackTrait;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(Request $request): array
    {
        $locale = ContentLocale::get();
        $isSecondaryLocale = $this->isSecondaryLocale($locale);
        $user = auth()->user();

        return [
            'id' => $this->id,
            'canonical_id' => $this->canonical_id,
            'variant' => $this->variant,
            'url' => config('app.url') . '/' . $locale . '-' . resolve('current.country')->code .
                '/recipes/' . slugify($this->getTranslationWithAnyFallback('name', $locale)) . '-' . $this->id,
            'name' => $this->getTranslationWithAnyFallback('name', $locale),
            'headline' => $this->getTranslationWithAnyFallback('headline', $locale),
            'description' => $this->getTranslationWithAnyFallback('description', $locale),
            'difficulty' => $this->difficulty,
            'prep_time' => $this->prep_time,
            'total_time' => $this->total_time,
            'pdf_url' => $this->pdf_url,
            'has_pdf' => $this->has_pdf,
            'nutrition' => $isSecondaryLocale ? $this->nutrition_secondary : $this->nutrition_primary,
            'label' => new LabelResource($this->whenLoaded('label')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'allergens' => AllergenResource::collection($this->whenLoaded('allergens')),
            'ingredients' => IngredientResource::collection($this->whenLoaded('ingredients')),
            'cuisines' => CuisineResource::collection($this->whenLoaded('cuisines')),
            'utensils' => UtensilResource::collection($this->whenLoaded('utensils')),
            'saved_in_lists' => $this->when($user instanceof User, function () use ($user): array {
                assert($user instanceof User);

                return $this->getSavedInLists($user);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Check if the given locale is the secondary locale for this recipe's country.
     */
    protected function isSecondaryLocale(string $locale): bool
    {
        $locales = $this->country->locales ?? [];

        return isset($locales[1]) && $locales[1] === $locale;
    }

    /**
     * Get the lists this recipe is saved in for the given user.
     *
     * @return array<int, array{id: int, name: string}>
     */
    protected function getSavedInLists(User $user): array
    {
        return $user->recipeLists()
            ->whereHas('recipes', fn (Builder $query) => $query->where('recipes.id', $this->id))
            ->get(['id', 'name'])
            ->map(fn (RecipeList $list): array => [
                'id' => $list->id,
                'name' => $list->name,
            ])
            ->all();
    }
}
