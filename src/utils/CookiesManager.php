<?php

namespace MyApp\utils;

class CookiesManager
{
    public static function set(string $name, string $value, int $expiryDays = 365, string $path = "/"): bool
    {
        $expires = time() + $expiryDays * 24 * 60 * 60;
        return setcookie($name, $value, $expires, $path);
    }

    public static function get(string $name): string|false
    {
        return $_COOKIE[$name] ?? false;
    }

    public static function delete(string $name, string $path = "/"): bool
    {
        return setcookie($name, "", time() - 3600, $path);
    }

    public static function getCookieValueWithPrefix(string $cookieName, string $prefix = ""): string
    {
        $cookieValue = self::get($cookieName);
        return $cookieValue ? $prefix . $cookieValue : '';
    }
}
