<?php

namespace App\Http\Resources\PageIndices;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /* @var \App\Models\Tag $this */
        return [
            'name' => $this->name,
            'color' => $this->color_handle,
        ];
    }
}
