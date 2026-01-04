<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validation for the Menu API index endpoint.
 */
class MenuIndexRequest extends ListIndexRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|string|list<ValidationRule|string>>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'include_recipes' => ['sometimes', 'boolean'],
            'from' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'to' => ['sometimes', 'date', 'date_format:Y-m-d', 'after_or_equal:from'],
        ]);
    }
}
