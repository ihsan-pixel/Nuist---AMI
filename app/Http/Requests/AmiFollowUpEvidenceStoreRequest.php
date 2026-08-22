<?php

namespace App\Http\Requests;

use App\Rules\GoogleDriveUrl;
use Illuminate\Foundation\Http\FormRequest;

class AmiFollowUpEvidenceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'sekolah';
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'url' => ['required', 'url', new GoogleDriveUrl()],
            'note' => ['nullable', 'string'],
        ];
    }
}
