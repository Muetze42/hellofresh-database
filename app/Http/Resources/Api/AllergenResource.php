<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\Api\Concerns\HasTranslationFallbackTrait;
use App\Models\Allergen;
use App\Support\Api\ContentLocale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

/**
 * @mixin Allergen
 */
class AllergenResource extends JsonResource
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
        return [
            'id' => $this->id,
            'name' => $this->getTranslationWithAnyFallback('name', ContentLocale::get()),
            'icon_path' => $this->icon_path,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
