<?php

namespace App\Models\Traits;

use App\Models\Recipe;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait CountryTrait
{
    /**
     *  Get the first record matching the attributes. If the record is not found, create it.
     */
    public static function freshAttributes(mixed $hfModel): array
    {
        $replace = [
            'external_created_at' => 'created_at',
            'external_updated_at' => 'updated_at',
        ];

        if (static::class == Recipe::class) {
            $replace['description'] = 'description_markdown';
        }

        /* @var \NormanHuth\HellofreshScraper\Models\AbstractModel $hfModel */
        $columns = (new static())->getFillable();
        $data = $hfModel->data();

        return Arr::mapWithKeys(
            $columns,
            fn (string $column) => [$column => data_get(
                $data,
                Str::camel(str_replace(array_keys($replace), array_values($replace), $column))
            )]
        );
    }

    protected function freshKey(string $column): string
    {
        return str_replace(
            ['external_created_at', 'external_updated_at'],
            ['created_at', 'updated_at'],
            $column
        );
    }
}
