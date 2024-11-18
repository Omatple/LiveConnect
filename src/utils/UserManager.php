<?php

namespace MyApp\utils;

use DateInterval;
use \DateTime;
use MyApp\db\EmailConfirmation;
use MyApp\db\User;

require __DIR__ . "/../../vendor/autoload.php";
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

    public static function canChangeUsername(User $user): bool
    {
        return UserValidator::isUsernameChangeAllowed(DateTime::createFromFormat("Y-m-d H:i:s", $user->getUsername_last_changed()));
    }

    public static function updateUsername(User $user, string|false $username, string $redirectUrl): bool
    {
        if (!$username) return UserManager::canChangeUsername($user);
        $sanitizeUsername = UserValidator::sanitizeInput($username);
        if ($user->getUsername() === $sanitizeUsername) UserSession::redirectTo($redirectUrl);
        if (UserValidator::validateUsernameLength($sanitizeUsername) && !UserValidator::isUsernameInUse($sanitizeUsername)) {
            (new User)
                ->setUsername($username)
                ->setUsername_last_changed((new DateTime())->format("Y-m-d H:i:s"))
                ->updateUser(targetUsername: $user->getUsername(), username: true, username_last_changed: true);
            $user->setUsername($username);
            UserSession::redirectTo($redirectUrl);
        }
        return true;
    }

    public static function updateEmail(User $user, string|false $email, string $redirectUrl): bool
    {
        if ($email) {
            $sanitizeEmail = UserValidator::sanitizeInput($email);
            if ($user->getEmail() === $sanitizeEmail) UserSession::redirectTo($redirectUrl);
            if (UserValidator::validateEmailFormat($email) && !UserValidator::isEmailInUse($sanitizeEmail)) {
                $hash = bin2hex(random_bytes(32));
                if (EmailConfirmationManager::getStoredHashForEmail($email)) {
                    EmailConfirmation::deleteEmailConfirmation($email);
                }
                (new EmailConfirmation)
                    ->setEmail($sanitizeEmail)
                    ->setUsername($user->getUsername())
                    ->setHash($hash)
                    ->setExpires_at((new DateTime())->add(new DateInterval("PT10M"))->format("Y-m-d H:i:s"))
                    ->createEmailConfirmation();
                Mailer::sendConfirmationEmail($sanitizeEmail, $hash, "userProfile.php");
            }
        }
        return true;
    }

    public static function updatePassword(User $user, string $password, string $passwordConfirm, string $redirectUrl): bool
    {
        if ($password) {
            $sanitizePassword = UserValidator::sanitizeInput($password);
            $sanitizePasswordConfirm = UserValidator::sanitizeInput($passwordConfirm);
            if (UserValidator::isPasswordConfirmed($sanitizePassword, $sanitizePasswordConfirm)) {
                if (password_verify($sanitizePassword, $user->getPassword())) UserSession::redirectTo($redirectUrl);
                if (UserValidator::validatePasswordLength($sanitizePassword)) {
                    (new User)
                        ->setPassword($sanitizePassword)
                        ->updateUser(targetUsername: $user->getUsername(), password: true);
                    $_SESSION["user"] = User::findUserByUsername($user->getUsername());
                    $_SESSION["success_message"] = "Your password has been successfully updated.";
                    UserSession::redirectTo($redirectUrl);
                }
            }
        }
        return true;
    }
}
