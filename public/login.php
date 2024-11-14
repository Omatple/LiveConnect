<?php

use MyApp\db\Role;
use MyApp\utils\CookiesManager;
use MyApp\utils\SessionErrorDisplay;
use MyApp\utils\UserSession;
use MyApp\utils\UserValidator;

require __DIR__ . "/../vendor/autoload.php";

session_start();

if (UserSession::requireLogin()) UserSession::redirectTo("home.php");
if (UserSession::hasRole(Role::Guest)) unset($_SESSION["user"]);
if (isset($_POST["guestLogin"])) {
    UserSession::initializeGuestSession();
    UserSession::redirectTo("home.php");
}
if (isset($_POST["username"])) {
    $username = UserValidator::sanitizeInput($_POST["username"]);
    $password = UserValidator::sanitizeInput($_POST["password"]);
    $remember = CookiesManager::get("remember");
    $postRemember = isset($_POST["remember"]);
    if ($postRemember && !$remember) {
        CookiesManager::set("remember", "checked");
    } else if (!$postRemember && $remember) {
        CookiesManager::delete("remember");
        if (CookiesManager::get("username")) {
            CookiesManager::delete("username");
            CookiesManager::delete("password");
        }
    }
    $hasErrors = false;
    if (!UserValidator::validateUsernameLength($username)) $hasErrors  = true;
    if (!UserValidator::validatePasswordLength($password)) $hasErrors  = true;
    if (!$hasErrors && !($user = UserValidator::getUserIfValidCredentials($username, $password))) $hasErrors = true;
    if ($hasErrors) UserSession::refreshPage();
    $_SESSION["user"] = $user;
    if ($postRemember) {
        CookiesManager::set("username", $username);
        CookiesManager::set("password", $password);
    }
    UserSession::redirectTo("home.php");
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="Ángel Martínez Otero">
    <title>Login | File-To</title>
    <!-- CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- CDN FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Sign in to your Account</h2>
        <form action="<?= $_SERVER["PHP_SELF"]; ?>" method="POST" novalidate>
            <div class="mb-4">
                <label for="username" class="block text-gray-700 font-semibold mb-2">Username</label>
                <input type="text" id="username" name="username" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter your username" <?= CookiesManager::getCookieValueWithPrefix("username", "value="); ?> required>
                <?php SessionErrorDisplay::displaySessionError("username"); ?>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter your password" <?= CookiesManager::getCookieValueWithPrefix("password", "value="); ?> required>
                <?php SessionErrorDisplay::displaySessionError("password"); ?>
            </div>
            <div class="w-1/2 flex items-center mb-4">
                <input type="checkbox" name="remember" value="remember" class="mt-1 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 focus:outline-none" <?= CookiesManager::getCookieValueWithPrefix("remember"); ?>>
                <label for="remember" class="ml-2 text-gray-600">Remember me</label>
            </div>
            <button type="submit" class="mb-4 w-full bg-blue-500 text-white py-2 rounded-lg font-semibold hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">Sign In</button>
            <button type="submit" name="guestLogin" class="w-full bg-blue-500 text-white py-2 rounded-lg font-semibold hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">Enter as Guest</button>
        </form>
        <p class="text-center text-gray-600 mt-4">Don´t have an account? <a href="register.php" class="text-blue-500 font-semibold">Create an Account</a></p>
    </div>
</body>

</html>