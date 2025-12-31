<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\Api\Concerns\HasTranslationFallbackTrait;
use App\Models\Label;
use App\Support\Api\ContentLocale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

/**
 * @mixin Label
 */
class LabelResource extends JsonResource
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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
