<?php

namespace App\ValueObjects;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Support\Arr;

class RecipeYieldsObject extends AbstractObject
{
    use HasAttributes;

    public array $data;

    protected function castData(): array
    {
        return Arr::map($this->data, function ($item) {
            $item['ingredients'] = $this->castIngredients($item['ingredients']);

            return $item;
        });
    }

    /**
     * @param array{id: string, amount: bool, unit: string}  $ingredients
     */
    protected function castIngredients(array $ingredients): array
    {
        return Arr::map($ingredients, function ($item) {
            $ingredient = Ingredient::find($item['id']);

            return [
                'amount' => $item['amount'],
                'unit' => $item['unit'],
                'name' => $ingredient?->name,
            ];
        });
    }
}
