<?php

namespace App\Http\Resources\Api;

use App\Models\Menu;
use App\Support\Api\ContentLocale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

/**
 * @mixin Menu
 */
class MenuResource extends JsonResource
{
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
            'url' => config('app.url') . '/' . $locale . '-' . resolve('current.country')->code . '/menus/' . $this->year_week,
            'year_week' => $this->year_week,
            'start' => $this->start->toDateString(),
            'recipes' => RecipeCollectionResource::collection($this->whenLoaded('recipes')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
