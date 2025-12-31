<?php

namespace App\Livewire\Portal\Docs;

class MenusShowDoc extends AbstractEndpointDoc
{
    protected function title(): string
    {
        return 'Get Menu';
    }

    protected function description(): string
    {
        return 'Retrieve a single menu by year and week with full details.';
    }

    protected function endpoints(): array
    {
        return [
            [
                'method' => 'GET',
                'path' => '/{locale}-{country}/menus/{year_week}',
                'description' => 'Year and week format: YYYYWW (e.g., 202501 for week 1 of 2025)',
            ],
        ];
    }

    protected function responseFields(): array
    {
        return [
            ['name' => 'id', 'type' => 'integer', 'description' => 'Menu ID'],
            ['name' => 'year_week', 'type' => 'integer', 'description' => 'Year and week number (YYYYWW format)'],
            ['name' => 'start', 'type' => 'date', 'description' => 'Menu start date'],
            ['name' => 'recipes', 'type' => 'array', 'description' => 'Array of recipe objects'],
            ['name' => 'created_at', 'type' => 'datetime', 'description' => 'Creation timestamp'],
            ['name' => 'updated_at', 'type' => 'datetime', 'description' => 'Last update timestamp'],
        ];
    }

    protected function exampleRequest(): string
    {
        return 'curl -X GET "https://' . config('api.domain_name') . '/de-DE/menus/202501" \\
  -H "Authorization: Bearer YOUR_API_TOKEN" \\
  -H "Accept: application/json"';
    }
}
