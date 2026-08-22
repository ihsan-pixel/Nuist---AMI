<?php

namespace App\Http\Requests;

use App\Enums\AuditorFindingType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AmiFindingStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'auditor';
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(array_column(AuditorFindingType::cases(), 'value'))],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'recommendation' => ['required', 'string'],
        ];
    }
}
