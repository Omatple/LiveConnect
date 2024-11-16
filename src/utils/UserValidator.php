<?php

namespace MyApp\utils;

use \DateTime;
use MyApp\db\User;

require __DIR__ . "/../../vendor/autoload.php";

class UserValidator
{
    public static function sanitizeInput(string $input): string
    {
        return htmlspecialchars(trim($input));
    }

    public static function validateUsernameLength(string $username): bool
    {
        $min = 3;
        $max = 16;
        if (!ValidationUtils::isWithinLength($username, $min, $max)) {
            $_SESSION["error_username"] = "Username must be between $min and $max characters.";
            return false;
        }
        return true;
    }

    public static function validateEmailFormat(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION["error_email"] = "Please provide a valid email address.";
            return false;
        }
        return true;
    }

    public static function validatePasswordLength(string $password): bool
    {
        $min = 8;
        $max = 30;
        if (!ValidationUtils::isWithinLength($password, $min, $max)) {
            $_SESSION["error_password"] = "Password must be between $min and $max characters.";
            return false;
        }
        return true;
    }

    public static function isPasswordConfirmed(string $originalPassword, string $confirmationPassword): bool
    {
        if ($originalPassword !== $confirmationPassword) {
            $_SESSION["error_password"] = "Password confirmation does not match the password.";
            return false;
        }
        return true;
    }

    public static function isAccountInfoInUse(string $username, string $email): bool
    {
        if (self::isUsernameInUse($username) || self::isEmailInUse($email)) {
            $_SESSION["error_register"] = "Username or email is already in use.";
            return true;
        }
        return false;
    }

    public static function isUsernameInUse(string $username): bool
    {
        if (!User::isAttributeTaken("username", $username)) {
            $_SESSION["error_usernameInUse"] = "Username is already in use.";
            return true;
        }
        return false;
    }

    public static function isEmailInUse(string $email): bool
    {
        if (!User::isAttributeTaken("email", $email)) {
            $_SESSION["error_emailInUse"] = "Email is already in use.";
            return true;
        }
        return false;
    }

    public static function getUserIfValidCredentials(string $username, string $password): User|false
    {
        $user = User::findUserByUsername($username);
        if (!$user || !password_verify($password, $user->getPassword())) {
            $_SESSION["error_login"] = "Invalid username or password.";
            return false;
        }
        return $user;
    }

    public static function isUsernameChangeAllowed(DateTime $lastUsernameChangeDate, int $requiredDaysForChange = 30): bool
    {
        $daysSinceLastChange = $lastUsernameChangeDate->diff(new DateTime())->days;
        if ($daysSinceLastChange < $requiredDaysForChange) {
            $daysLeft = $requiredDaysForChange - $daysSinceLastChange;
            $_SESSION["error_editUsername"] = "Username change is not allowed yet. Please wait $daysLeft more days until the required number of days has passed.";
            return false;
        }
        return true;
    }
}
