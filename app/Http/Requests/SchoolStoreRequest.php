<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'super_admin';
    }

    public function rules(): array
    {
        return [
            'scod' => ['nullable', 'string', 'max:100', Rule::unique('schools', 'scod')],
            'name' => ['required', 'string', 'max:255'],
            'education_level' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:150'],
            'address' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}
