<?php

namespace Application\Todos\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'category' => ['sometimes', 'in:Private,Work,Learning'],
            'priority' => ['sometimes', 'in:Low,Medium,High'],
            'status' => ['sometimes', 'in:Todo,In Progress,Done'],
            'jira_issue' => ['sometimes', 'string', 'max:255'],
            'due_date' => ['sometimes', 'date'],
        ];
    }
}
