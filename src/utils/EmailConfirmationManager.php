<?php

namespace MyApp\utils;

use MyApp\db\EmailConfirmation;

require __DIR__ . "/../../vendor/autoload.php";

class EmailConfirmationManager
{
    public static function getStoredHashForEmail(string $email): string|false
    {
        return EmailConfirmation::getAttributeByEmail($email, "hash");
    }

    public static function getStoredUsernameForEmail(string $email): string|false
    {
        return EmailConfirmation::getAttributeByEmail($email, "username");
    }
}
