<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'statement' => ['required', 'string', 'max:2000'],
            'language' => ['nullable', 'string', 'max:10'],
        ];
    }
}
