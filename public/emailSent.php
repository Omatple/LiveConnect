<?php

use MyApp\utils\UserSession;

require __DIR__ . "/../vendor/autoload.php";

session_start();

if (!isset($_SESSION["emailSent"])) UserSession::redirectTo("home.php");
session_destroy();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Confirmation email sent page">
    <meta name="keywords" content="Email Confirmation, Tailwind CSS, Responsive Design">
    <meta name="author" content="Ángel Martínez Otero">
    <title>Email Sent | File-To</title>
    <!-- CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- CDN FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="bg-gray-100 dark:bg-gray-900 flex items-center justify-center min-h-screen transition duration-300">
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8 max-w-md w-full mx-4 text-center">
        <div class="text-primary dark:text-green-400">
            <i class="fa-solid fa-circle-check fa-3x"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-4">Email Sent!</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            Check your inbox to confirm your email address. You can now safely close this window.
        </p>
        <button onclick="window.close();"
            class="mt-6 w-full py-2 px-4 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-blue-600 dark:bg-green-700 dark:hover:bg-green-800 transition">
            Close Window
        </button>
        <footer class="mt-6 text-xs text-gray-500 dark:text-gray-400">
            &copy; 2024 File-To. All rights reserved.
        </footer>
    </div>
</body>

</html>