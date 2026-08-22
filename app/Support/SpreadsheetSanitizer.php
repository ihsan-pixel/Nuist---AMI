<?php

namespace App\Support;

class SpreadsheetSanitizer
{
    public static function text(mixed $value): string
    {
        $text = (string) $value;

        return preg_match('/^[=+\-@]/', $text) ? "'".$text : $text;
    }
}
