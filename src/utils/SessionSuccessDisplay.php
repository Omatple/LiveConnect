<?php

namespace MyApp\utils;

class SessionSuccessDisplay
{
    public static function displaySessionSuccess(string $sessionSuccess): void
    {
        if (isset($_SESSION["success_$sessionSuccess"])) {
            echo "<p class='text-green-500 text-xs italic'>{$_SESSION["success_$sessionSuccess"]}</p>";
            unset($_SESSION["success_$sessionSuccess"]);
        }
    }
}
