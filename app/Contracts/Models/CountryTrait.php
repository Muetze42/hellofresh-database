<?php

namespace App\Contracts\Models;

use App\Models\Country;
use App\Models\Recipe;
use Exception;
use Illuminate\Support\Carbon;
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
        $casts = (new static())->getCasts();
        $data = $hfModel->data();

        foreach ($columns as $key => $column) {
            $value = data_get($data, Str::camel(str_replace(array_keys($replace), array_values($replace), $column)));

            if (data_get($casts, $column) == 'datetime') {
                try {
                    $value = Carbon::parse($value);
                    $supported = Carbon::parse('1970-01-01 00:00:01');

                    if ($value < $supported) {
                        $value = $supported;
                    }
                } catch (Exception) {
                    // silent
                    $value = null;
                }
            }
            unset($columns[$key]);
            $columns[$column] = $value;
        }

        return $columns;
        //return Arr::mapWithKeys(
        //    $columns,
        //    fn (string $column) => [$column => data_get(
        //        $data,
        //        Str::camel(str_replace(array_keys($replace), array_values($replace), $column))
        //    )]
        //);
    }

    protected function freshKey(string $column): string
    {
        return str_replace(
            ['external_created_at', 'external_updated_at'],
            ['created_at', 'updated_at'],
            $column
        );
    }

    /**
     * Get the table associated with the model.
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function getTable(): string
    {
        $this->table = $this->getCountryPrefix() . Str::snake(Str::pluralStudly(class_basename($this)));

        return $this->table;
    }

    /**
     * @throws \Exception
     */
    protected function getCountryPrefix(): string
    {
        $country = country();

        /* Fallback for IDE Helper */
        if (!$country) {
            if (!app()->runningConsoleCommand('helper')) {
                throw new Exception('No Country activated');
            }

            /* @var \App\Models\Country $country */
            $country = Country::inRandomOrder()->first();

            return Str::lower($country->code) . '__';
        }


        return Str::lower(country()->code) . '__';
    }

    /**
     * Get the joining table name for a many-to-many relation.
     *
     * @noinspection PhpMultipleClassDeclarationsInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function joiningTable($related, $instance = null): string
    {
        return $this->getCountryPrefix() . parent::joiningTable($related, $instance);
    }
}
