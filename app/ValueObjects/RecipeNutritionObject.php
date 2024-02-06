<?php

namespace App\ValueObjects;

use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Support\Arr;
use Illuminate\Support\Number;

class RecipeNutritionObject extends AbstractObject
{
    use HasAttributes;

    public array $data;

    protected function castData(): array
    {
        return Arr::map($this->data, fn (array $item) => [
            'name' => $item['name'],
            'amount' => Number::format($item['amount']),
            'unit' => $item['unit'],
        ]);
    }
}
