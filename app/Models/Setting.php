<?php

namespace App\Models;

class Setting
{
    /**
     * The data array.
     */
    public array $data;

    protected function __construct()
    {
        $this->data = json_decode(file_get_contents(base_path('settings.json')), true);
    }

    /**
     * Get the collection of items as a plain array.
     *
     * @return array{
     *      filter: array{
     *          max_filterable_items: int,
     *      },
     *      pagination: array{
     *          per_page: int,
     *      },
     *      shopping_list: array{
     *          max_items: int,
     *      },
     *      users: array{
     *          name: array{
     *              max_length: int,
     *          }
     *      },
     *     flash: array{
     *         duration: int,
     *     }
     *  }
     */
    public static function toArray(): array
    {
        return (new static())->data;
    }

    /**
     * Get the specified setting value.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return data_get(self::toArray(), $key, $default);
    }
}
