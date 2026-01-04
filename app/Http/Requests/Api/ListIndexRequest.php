<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Base request for API list endpoints with common pagination and sorting validation.
 */
class ListIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'perPage' => ['sometimes', 'int', 'min:' . config('api.pagination.per_page_min'), 'max:' . config('api.pagination.per_page_max')],
            'sort' => ['sometimes', 'string', 'in:created_at,updated_at'],
            'search' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
