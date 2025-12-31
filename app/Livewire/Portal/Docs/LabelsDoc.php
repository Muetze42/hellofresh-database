<?php

namespace App\Livewire\Portal\Docs;

use Override;

class LabelsDoc extends AbstractEndpointDoc
{
    protected function title(): string
    {
        return 'Labels API';
    }

    protected function description(): string
    {
        return 'Retrieve available recipe labels (e.g., "Family Friendly", "Quick & Easy").';
    }

    protected function endpoints(): array
    {
        return [
            [
                'method' => 'GET',
                'path' => '/{locale}-{country}/labels',
                'description' => 'List all available labels.',
            ],
        ];
    }

    #[Override]
    protected function queryParams(): array
    {
        return [
            ['name' => 'sort', 'type' => 'string', 'description' => 'Sort by: created_at (default) or updated_at, always descending'],
        ];
    }

    protected function responseFields(): array
    {
        return [
            ['name' => 'id', 'type' => 'integer', 'description' => 'Label ID'],
            ['name' => 'name', 'type' => 'string', 'description' => 'Label name (localized)'],
            ['name' => 'foreground_color', 'type' => 'string', 'description' => 'Text color (hex format)'],
            ['name' => 'background_color', 'type' => 'string', 'description' => 'Background color (hex format)'],
            ['name' => 'created_at', 'type' => 'datetime', 'description' => 'Creation timestamp'],
            ['name' => 'updated_at', 'type' => 'datetime', 'description' => 'Last update timestamp'],
        ];
    }

    protected function exampleRequest(): string
    {
        return 'curl -X GET "https://' . config('api.domain_name') . '/de-DE/labels" \\
  -H "Authorization: Bearer YOUR_API_TOKEN" \\
  -H "Accept: application/json"';
    }
}
