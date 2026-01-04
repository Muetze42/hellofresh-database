<?php

namespace App\Http\Requests\Api;

use Override;

/**
 * Validation for the Menu API index endpoint.
 */
class MenuIndexRequest extends ListIndexRequest
{
    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'include_recipes' => ['sometimes', 'boolean'],
            'from' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'to' => ['sometimes', 'date', 'date_format:Y-m-d', 'after_or_equal:from'],
        ]);
    }
}
