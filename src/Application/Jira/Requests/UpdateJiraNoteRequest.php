<?php

namespace Application\Jira\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJiraNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'note' => ['required', 'string', 'max:10000'],
        ];
    }

    public function messages(): array
    {
        return [
            'note.required' => 'Die Notiz darf nicht leer sein.',
            'note.max' => 'Die Notiz darf maximal 10.000 Zeichen lang sein.',
        ];
    }
}
