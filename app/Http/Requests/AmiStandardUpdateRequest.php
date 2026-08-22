<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AmiStandardUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'super_admin';
    }

    public function rules(): array
    {
        return [
            'ami_period_id' => ['required', Rule::exists('ami_periods', 'id')],
            'code' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'weight' => ['nullable', 'numeric'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
