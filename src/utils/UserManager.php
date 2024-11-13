<?php

namespace MyApp\utils;

use MyApp\db\User;

require __DIR__ . "/../../vendor/autoload.php";

session_start();
class UserManager
{
    public static function isUserOrEmailTaken(string $username, string $email): bool
    {
        if (self::isUsernameTaken($username) || self::isEmailTaken($email)) {
            $_SESSION["error_username"] = "Username or email already exits";
            return false;
        }
        return true;
    }
    public static function isUsernameTaken(string $username): bool
    {
        return User::isAttributeTaken("username", $username);
    }
    public static function isEmailTaken(string $email): bool
    {
        return User::isAttributeTaken("email", $email);
    }
}
