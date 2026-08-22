<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AmiResponseStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'sekolah';
    }

    public function rules(): array
    {
        return [
            'self_score' => ['nullable', 'integer', Rule::in([1, 2, 3, 4])],
            'answer' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'submitted'])],
        ];
    }
}
