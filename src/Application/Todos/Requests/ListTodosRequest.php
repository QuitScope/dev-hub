<?php

namespace Application\Todos\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListTodosRequest extends FormRequest
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
            'filter.status' => ['nullable', 'string', 'in:pending,completed'],
            'filter.priority' => ['nullable', 'string', 'in:low,medium,high'],

            // Spatie Query Builder sort parameter
            'sort' => ['nullable', 'string'],

            // Pagination
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
