<?php

namespace App\Http\Requests;

use App\Enums\AmiFollowUpStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AmiFollowUpStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'sekolah';
    }

    public function rules(): array
    {
        return [
            'action_plan' => ['required', 'string'],
            'status' => ['required', Rule::in([AmiFollowUpStatus::DRAFT->value, AmiFollowUpStatus::SUBMITTED->value])],
        ];
    }
}
