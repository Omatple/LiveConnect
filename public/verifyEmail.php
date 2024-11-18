<?php

use MyApp\db\User;
use MyApp\utils\EmailConfirmationManager;
use MyApp\utils\UserSession;

require __DIR__ . "/../vendor/autoload.php";

session_start();

$email = filter_input(INPUT_GET, "email");
$hash = filter_input(INPUT_GET, "hash");
if ($email && $hash && EmailConfirmationManager::getStoredHashForEmail($email) === $hash) {
    $user = User::findUserByUsername(EmailConfirmationManager::getStoredUsernameForEmail($email));
    $user->setEmail($email)->updateUser(targetUsername: $user->getUsername(), email: true);
    $_SESSION["user"] = $user;
    $_SESSION["success_message"] = "Your email address has been successfully updated!";
}
UserSession::redirectTo("home.php");
