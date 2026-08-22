<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AmiSchoolAssignmentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'super_admin';
    }

    public function rules(): array
    {
        return [
            'ami_period_id' => ['required', Rule::exists('ami_periods', 'id')],
            'school_ids' => ['required', 'array', 'min:1'],
            'school_ids.*' => ['integer', Rule::exists('schools', 'id')],
        ];
    }
}
