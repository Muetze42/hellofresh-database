<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class LabelResource extends JsonResource
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
            'text' => Str::ucfirst($this->text),
            'color' => $this->foreground_color,
            'bg' => $this->background_color,
        ];
    }
}
