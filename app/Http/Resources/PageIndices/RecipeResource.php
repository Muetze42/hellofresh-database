<?php

namespace App\Http\Resources\PageIndices;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
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
            'pdf' => $this->card_link,
            'headline' => $this->headline,
            'image' => $this->asset()->preview(),
            'label' => $this->label?->active() ? new LabelResource($this->label) : null,
            'tags' => TagResource::collection($tags->active()->get()),
        ];
    }
}
