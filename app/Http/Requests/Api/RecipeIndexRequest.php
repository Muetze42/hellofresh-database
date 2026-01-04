<?php

namespace App\Http\Requests\Api;

use App\Enums\DifficultyEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

/**
 * Validation for the Recipe API index endpoint.
 */
class RecipeIndexRequest extends ListIndexRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|string|list<ValidationRule|string>>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'tag' => ['sometimes', 'int', 'min:1'],
            'label' => ['sometimes', 'int', 'min:1'],
            'difficulty' => ['sometimes', 'int', Rule::enum(DifficultyEnum::class)],
            'has_pdf' => ['sometimes', 'boolean'],
        ]);
    }
}
