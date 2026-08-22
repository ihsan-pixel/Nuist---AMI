<?php

namespace App\Http\Requests;

use App\Enums\AmiAssessmentRating;
use App\Enums\AuditorAssessmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AmiAssessmentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'auditor';
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_column(AuditorAssessmentStatus::cases(), 'value'))],
            'rating' => ['nullable', Rule::in(array_column(AmiAssessmentRating::cases(), 'value'))],
            'score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'auditor_note' => ['nullable', 'string'],
            'verification_methods' => ['nullable', 'array'],
            'verification_methods.*' => ['string'],
            'verification_note' => ['nullable', 'string'],
        ];
    }
}
