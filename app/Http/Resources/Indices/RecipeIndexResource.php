<?php

namespace App\Http\Resources\Indices;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        /* @var \App\Models\Recipe $this */
        /* @var \Illuminate\Database\Eloquent\Relations\BelongsToMany|\App\Models\Tag $tags */
        $tags = $this->tags();

        return [
            'id' => $this->getKey(),
            'name' => $this->name,
            'slug' => strSlug($this->name),
            'pdf' => $this->card_link,
            'headline' => $this->headline,
            'image' => $this->asset()->preview(),
            'label' => $this->label?->active() ? new LabelIndexResource($this->label) : null,
            'tags' => TagIndexResource::collection($tags->active()->get()),
        ];
    }
}
