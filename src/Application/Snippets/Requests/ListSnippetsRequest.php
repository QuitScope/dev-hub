<?php

namespace Application\Snippets\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListSnippetsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Spatie Query Builder filter parameters
            'filter.search' => ['nullable', 'string', 'max:255'],
            'filter.language' => ['nullable', 'string', 'max:64'],
            'filter.tags' => ['nullable', 'array'],
            'filter.tags.*' => ['string'],

            // Spatie Query Builder sort parameter
            'sort' => ['nullable', 'string'],

            // Pagination
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
