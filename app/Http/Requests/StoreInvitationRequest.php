<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvitationRequest extends FormRequest
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
            'candidate_name' => ['required', 'string', 'max:150'],
            'candidate_email' => ['required', 'string', 'email', 'max:254'],
            'candidate_phone' => ['required', 'string', 'max:30'],
            'candidate_position' => ['required', 'string', 'max:150'],
            'position_external_id' => ['nullable', 'string', 'max:100'],
            'access_starts_at' => ['required', 'date'],
            'access_ends_at' => ['required', 'date', 'after:access_starts_at'],
        ];
    }
}
