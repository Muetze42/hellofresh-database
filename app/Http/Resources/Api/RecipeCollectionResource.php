<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\Api\Concerns\HasTranslationFallbackTrait;
use App\Models\Recipe;
use App\Support\Api\ContentLocale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

/**
 * @mixin Recipe
 */
class RecipeCollectionResource extends JsonResource
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

        return [
            'id' => $this->id,
            'url' => config('app.url') . '/' . $locale . '-' . resolve('current.country')->code .
                '/recipes/' . slugify($this->getTranslationWithAnyFallback('name', $locale)) . '-' . $this->id,
            'name' => $this->getTranslationWithAnyFallback('name', $locale),
            'headline' => $this->getTranslationWithAnyFallback('headline', $locale),
            'difficulty' => $this->difficulty,
            'prep_time' => $this->prep_time,
            'total_time' => $this->total_time,
            'has_pdf' => $this->has_pdf,
            'label' => new LabelResource($this->whenLoaded('label')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
