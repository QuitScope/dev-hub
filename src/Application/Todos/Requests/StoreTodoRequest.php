<?php

declare(strict_types=1);

namespace Application\Todos\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'in:Private,Work,Learning'],
            'priority' => ['required', 'in:Low,Medium,High'],
            'status' => ['required', 'in:Todo,In Progress,Done'],
            'jira_issue' => ['nullable', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
        ];
    }
}
