<?php

namespace MyApp\utils;

use MyApp\db\Role;
use MyApp\db\User;

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
        $isLoggedIn = isset($_SESSION["user"]) && !self::hasRole(Role::Guest);
        if (!$isLoggedIn && $redirectUrl !== null) {
            self::redirectTo($redirectUrl);
        }
        return $isLoggedIn;
    }

    public static function initializeGuestSession(): User
    {
        $guestUser = User::getGuestUser();
        if (!$guestUser) {
            User::createGuestUser();
            $guestUser = User::getGuestUser();
        }
        $_SESSION["user"] = $guestUser;
        return $guestUser;
    }

    public static function hasRole(Role ...$roles): bool
    {
        return isset($_SESSION["user"]) && in_array($_SESSION["user"]->getRole(), $roles, true);
    }
}
