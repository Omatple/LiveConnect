<?php

namespace MyApp\utils;

class UserSession
{
    public static function redirectTo(string $url, ?string $getName = null, ?string $getValue = null): void
    {
        header(sprintf("Location: %s", ($getName !== null && $getValue !== null) ? "$url?$getName=$getValue" : $url));
        exit();
    }

    public static function refreshPage(?string $getName = null, ?string $getValue = null): void
    {
        header(sprintf("Location: %s", ($getName !== null && $getValue !== null) ? $_SERVER['PHP_SELF'] . "?$getName=$getValue" : $_SERVER['PHP_SELF']));
        exit();
    }

    public static function requireLogin(?string $redirectUrl = null): bool
    {
        $isLoggedIn = isset($_SESSION["user"]);
        if (!$isLoggedIn && $redirectUrl !== null) {
            self::redirectTo($redirectUrl);
        }
        return $isLoggedIn;
    }
}