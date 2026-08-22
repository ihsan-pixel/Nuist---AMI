<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AmiIndicatorStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'super_admin';
    }

    public function rules(): array
    {
        return [
            'ami_standard_id' => ['required', Rule::exists('ami_standards', 'id')],
            'code' => ['required', 'string', 'max:100'],
            'statement' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'guidance' => ['nullable', 'string'],
            'evidence_requirement' => ['nullable', 'string'],
            'weight' => ['nullable', 'numeric'],
            'max_score' => ['sometimes', 'integer', 'min:1', 'max:10'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_required' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
