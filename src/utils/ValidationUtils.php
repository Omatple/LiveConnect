<?php

namespace MyApp\utils;

class ValidationUtils
{
    public static function isWithinLength(string $input, int $minChars, int $maxChars): bool
    {
        return strlen($input) >= $minChars && strlen($input) <= $maxChars;
    }
}
