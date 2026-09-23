<?php

namespace Application\Todos\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'include' => ['nullable', 'array'],
            'include.*' => ['string', 'in:user,comments'],
        ];
    }
}
