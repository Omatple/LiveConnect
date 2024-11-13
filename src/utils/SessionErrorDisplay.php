<?php

namespace MyApp\utils;

class SessionErrorDisplay
{
    public static function displaySessionError(string $sessionError): void
    {
        if (isset($_SESSION["error_$sessionError"])) {
            echo "<p class='text-red-500 text-xs italic'>{$_SESSION["error_$sessionError"]}</p>";
            unset($_SESSION["error_$sessionError"]);
        }
    }
}
