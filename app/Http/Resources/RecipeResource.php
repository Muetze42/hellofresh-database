<?php

namespace App\Http\Resources;

use App\Contracts\Http\Resources\AbstractResource;
use Illuminate\Http\Request;

class RecipeResource extends AbstractResource
{
    /**
     * Transform the resource into an array.
     */
    public function toShowArray(Request $request): array
    {
        return array_merge($this->toIndexArray($request), [
            '' // Todo
        ]);
    }

    /**
     * Transform the resource into an array for an index collection.
     */
    public function toIndexArray(Request $request): array
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
            'label' => $this->label?->active() ? new LabelResource($this->label) : null,
            'tags' => TagResource::collection($tags->active()->get()),
        ];
    }
}
