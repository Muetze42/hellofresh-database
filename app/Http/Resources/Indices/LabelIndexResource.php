<?php

namespace App\Http\Resources\Indices;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabelIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /* @var \App\Models\Label $this */
        return [
            'text' => $this->text,
            'color' => $this->foreground_color,
            'bg' => $this->background_color,
        ];
    }
}
