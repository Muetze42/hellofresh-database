<?php

namespace App\Http\Resources;

use App\Contracts\Http\Resources\HasIndexCollectionTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    use HasIndexCollectionTrait;

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $array = $this->toIndexArray($request);

        if (static::$isIndex) {
            return $array;
        }

        return array_merge($array, [
            '', // Todo
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
            'tags' => $tags->active()->get()->pluck('name')->toArray(),
        ];
    }
}
