<?php

namespace App\ValueObjects;

use App\Support\HelloFresh\HelloFreshAsset;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Support\Arr;

class RecipeStepsObject
{
    use HasAttributes;

    public array $data;

    public function __construct(?string $value)
    {
        $this->data = $this->fromJson($value);

        if (!$this->data) {
            return;
        }

        $this->data = Arr::mapWithKeys($this->data, function (array $item) {
            return [$item['index'] => [
                'instructions' => data_get($item, 'instructions'),
                //'ingredients' => data_get($item, 'ingredients'),
                //'timers' => data_get($item, 'timers'),
                'images' => $this->castImages(data_get($item, 'images', [])),
                //'videos' => data_get($item, 'videos'),
                //'utensils' => data_get($item, 'utensils'),
            ]];
        });
    }

    /**
     * @param array{array-key, array{link: string, path: string, caption: string}}  $images
     */
    protected function castImages(array $images): array
    {
        return Arr::mapWithKeys(
            $images,
            fn(array $image) => [
                HelloFreshAsset::get(config('hellofresh.assets.steps.image'), $image['path']) => $image['caption']
            ]
        );
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
