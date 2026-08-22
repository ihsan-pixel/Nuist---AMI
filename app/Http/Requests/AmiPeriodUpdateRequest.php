<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AmiPeriodUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'super_admin';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'submission_start_at' => ['nullable', 'date'],
            'submission_end_at' => ['nullable', 'date', 'after_or_equal:submission_start_at'],
            'review_start_at' => ['nullable', 'date'],
            'review_end_at' => ['nullable', 'date', 'after_or_equal:review_start_at'],
            'status' => ['required', Rule::in(['draft', 'upcoming', 'active', 'review', 'completed', 'archived'])],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
