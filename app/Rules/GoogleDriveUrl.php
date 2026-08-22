<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class GoogleDriveUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $parts = parse_url((string) $value);
        $scheme = $parts['scheme'] ?? null;
        $host = $parts['host'] ?? null;

        if ($scheme !== 'https') {
            $fail('URL harus menggunakan HTTPS.');
        }

        if (! in_array($host, ['drive.google.com', 'docs.google.com'], true)) {
            $fail('URL harus berasal dari Google Drive atau Google Docs.');
        }
    }
}
