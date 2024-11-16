<?php

use MyApp\db\User;
use MyApp\utils\ImageUploader;
use MyApp\utils\Mailer;
use MyApp\utils\Role;
use MyApp\utils\SessionErrorDisplay;
use MyApp\utils\SessionSuccessDisplay;
use MyApp\utils\UserManager;
use MyApp\utils\UserSession;
use MyApp\utils\UserValidator;

require __DIR__ . "/../vendor/autoload.php";

session_start();

UserSession::requireLogin("home.php");
$user = $_SESSION["user"];
if (isset($_POST["deleteImage"])) (new ImageUploader())->resetToDefaultImage($user);
if ($imageData = $_FILES["image"] ?? false) $newImage = ((new ImageUploader())->upload($user, $imageData));
$updateUsername = (isset($_POST["editUsername"]) && UserManager::updateUsername($user, $_POST["username"] ?? false, $_SERVER['PHP_SELF']));
$updateEmail = (isset($_POST["editEmail"]) && UserManager::updateEmail($user, $_POST["email"] ?? false, $_SERVER['PHP_SELF']));
$updatePassword = (isset($_POST["editPassword"]) && UserManager::updatePassword($user, $_POST["password"] ?? false, $_POST["passwordConfirm"] ?? false, $_SERVER['PHP_SELF']));
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
    <title>User Profile | File-To</title>
    <!-- CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- CDN FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- CDN SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center min-h-screen">
        <div class="container">
            <div class="bg-white rounded-lg shadow-lg p-5 md:p-10 w-3/5 m-auto">
                <div class="flex justify-<?= (UserSession::hasRole(Role::Admin, Role::Root)) ? "between" : "end"; ?> mb-5">
                    <?php
                    if (UserSession::hasRole(Role::Admin, Role::Root)) {
                        echo <<< TXT
                        <div class="m-2">
                            <a href="admin.php" title="Admin View"
                                class="md:w-42 bg-white tracking-wide text-gray-800 font-bold rounded border-2 border-red-600 hover:border-red-600 hover:bg-red-600 hover:text-white shadow-md py-2 px-6 inline-flex items-center">
                                <span class="mx-auto"><i class="fa-solid fa-user-gear mr-2"></i>Admin View</span>
                            </a>
                        </div>
                        TXT;
                    }
                    ?>
                    <div class="m-2">
                        <a href="logout.php" title="Logout"
                            class="md:w-42 bg-white tracking-wide text-gray-800 font-bold rounded border-2 border-orange-500 hover:border-orange-500 hover:bg-orange-500 hover:text-white shadow-md py-2 px-6 inline-flex items-center">
                            <span class="mx-auto">Logout<i class="fa-solid fa-right-from-bracket ml-2"></i></span>
                        </a>
                    </div>
                </div>
                <div class="text-center">
                    <h2
                        class="text-4xl tracking-tight leading-10 font-extrabold text-gray-900 sm:text-5xl sm:leading-none md:text-6xl">
                        Your profile</span>
                    </h2>
                </div>
                <div class="container mt-10">
                    <div class="max-w-sm mx-auto bg-white dark:bg-gray-900 rounded-lg overflow-hidden shadow-lg">
                        <div class="border-b px-4 py-6">
                            <div class="text-center mt-4">
                                <img class="h-32 w-32 rounded-full border-4 border-white dark:border-slate-800 mx-auto mt-4"
                                    src="<?= $user->getImage(); ?>" alt="<?= $user->getUsername(); ?> photo">
                                <form action="<?= $_SERVER["PHP_SELF"]; ?>" method="POST">
                                    <div class="mx-auto flex w-32 justify-end">
                                        <button type="submit" name="deleteImage" class="mb-2 md:w-42 tracking-wide text-red-700 font-bold hover:border-orange-500 hover:text-red-500 shadow-md inline-flex items-center"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </form>
                                <?=
                                SessionSuccessDisplay::displaySessionSuccess("image");
                                SessionErrorDisplay::displaySessionError("upload");
                                ?>
                                <div class="py-2 mt-2">
                                    <?php
                                    if (!$updateUsername) {
                                        echo <<< TXT
                                            <div class="flex justify-center">
                                                <h3 class="ml-7 pr-3 font-bold text-2xl text-gray-800 dark:text-white mb-1">{$user->getUsername()}</h3>
                                                <form action='{$_SERVER["PHP_SELF"]}' method="POST">
                                                    <button type="submit" name="editUsername" class="tracking-wide text-gray-700 font-bold hover:border-orange-500 hover:text-gray-300 inline-flex items-center"><i class="fa-solid fa-pen"></i></button>
                                                </form>
                                            </div>                                        
                                        TXT;
                                    } else {
                                        echo <<< TXT
                                            <div class="flex items-center justify-center mt-1">
                                                <form action='{$_SERVER["PHP_SELF"]}' method="POST">
                                                    <input type="text" name="username" class="w-40 h-8 px-3 text-sm bg-gray-900 text-white border-2 border-r-0 rounded-r-none border-blue-500 focus:outline-none rounded shadow-sm" value="{$user->getUsername()}" />
                                                    <button name="editUsername" class="h-8 px-4 text-sm bg-blue-500 border-2 border-l-0 border-blue-500 rounded-r shadow-sm text-blue-50 hover:text-white hover:bg-blue-400 hover:border-blue-400 focus:outline-none"><i class="fa-solid fa-check"></i></button>
                                                </form>
                                            </div>                                        
                                        TXT;
                                    }
                                    SessionErrorDisplay::displaySessionError("editUsername");
                                    SessionErrorDisplay::displaySessionError("username");
                                    SessionErrorDisplay::displaySessionError("usernameInUse");
                                    if (!$updateEmail) {
                                        echo <<< TXT
                                            <div class="flex justify-center">
                                                <div class="ml-4 pr-2 inline-flex text-gray-700 dark:text-gray-300 items-center">{$user->getEmail()}</div>
                                                <form action="{$_SERVER["PHP_SELF"]}" method="POST" class="mb-2">
                                                    <button type="submit" name="editEmail" class="tracking-wide text-gray-700 font-bold hover:border-orange-500 hover:text-gray-300 inline-flex items-center"><i class="fa-solid fa-pen fa-2xs"></i></button>
                                                </form>
                                            </div>                                        
                                        TXT;
                                    } else {
                                        echo <<< TXT
                                            <div class="flex items-center justify-center mt-1">
                                                <form action='{$_SERVER["PHP_SELF"]}' method="POST">
                                                    <input type="text" name="email" class="w-60 h-8 px-3 text-sm bg-gray-900 text-white border-2 border-r-0 rounded-r-none border-blue-500 focus:outline-none rounded shadow-sm" value="{$user->getEmail()}" />
                                                    <button name="editEmail" class="h-8 px-4 text-sm bg-blue-500 border-2 border-l-0 border-blue-500 rounded-r shadow-sm text-blue-50 hover:text-white hover:bg-blue-400 hover:border-blue-400 focus:outline-none"><i class="fa-solid fa-check"></i></button>
                                                </form>
                                            </div>                                        
                                        TXT;
                                    }
                                    SessionErrorDisplay::displaySessionError("email");
                                    SessionErrorDisplay::displaySessionError("emailInUse");
                                    if (!$updatePassword) {
                                        echo <<< TXT
                                            <form action="{$_SERVER["PHP_SELF"]}" method="POST">
                                                <button type="submit" name="editPassword" class="text-blue-500 hover:underline hover:text-blue-600 p-0 border-0 bg-transparent text-sm mt-6">
                                                    Reset your password
                                                </button>
                                            </form>                                        
                                        TXT;
                                        SessionSuccessDisplay::displaySessionSuccess("password");
                                    } else {
                                        echo <<< TXT
                                        <form action="{$_SERVER["PHP_SELF"]}" method="POST">
                                            <div class="flex flex-col items-center justify-center mt-4 mb-7">
                                                <div class="mb-1">
                                                    <label for="password" class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">
                                                        New Password
                                                    </label>
                                                    <input type="password" name="password" id="password" placeholder="••••••••" 
                                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-md focus:ring-primary-600 focus:border-primary-600 block w-48 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                        required="">
                                                </div>
                                        TXT;
                                        SessionErrorDisplay::displaySessionError("password");
                                        echo <<< TXT
                                                <div class="mt-4">
                                                    <label for="passwordConfirm" class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">
                                                        Confirm Password
                                                    </label>
                                                    <input type="password" name="passwordConfirm" id="passwordConfirm" placeholder="••••••••"
                                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-md focus:ring-primary-600 focus:border-primary-600 block w-48 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                        required="">
                                                </div>
                                                <div class="mt-4">
                                                    <button type="submit" name="editPassword"
                                                        class="mt-2 flex items-center justify-center bg-blue-700 text-white text-xs font-medium rounded-md w-48 p-2 hover:bg-blue-600 focus:ring-2 focus:ring-blue-300">
                                                        <i class="fa-solid fa-key w-4 mr-2"></i>
                                                        Reset Password
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                        TXT;
                                    }
                                    ?>
                                </div>
                            </div>
                            <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                                <div class="flex justify-center">
                                    <div class="overflow-hidden relative mx-2">
                                        <input type="file" name="image" accept="image/*" oninput="window.document.getElementById('upPhoto').click();"
                                            class="cursor-pointer absolute block py-2 px-4 w-full opacity-0 pin-r pin-t">
                                        <button type="submit" id="upPhoto"
                                            class="md:w-42 bg-blue-700 tracking-wide text-white font-bold rounded border-2 border-blue-600 hover:border-blue-500 hover:bg-blue-500 hover:text-white shadow-md py-2 px-6 inline-flex items-center">
                                            <span class="mx-auto"><i class="fa-solid fa-camera mr-2"></i>Update Photo</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <div class="flex justify-center mx-3 mb-2">
                                <?= SessionErrorDisplay::displaySessionError("image"); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center">
                    <div class="m-2 mt-8">
                        <a href="home.php" title="Home"
                            class="md:w-42 bg-white tracking-wide text-gray-800 font-bold rounded border-2 border-indigo-500 hover:border-indigo-500 hover:bg-indigo-500 hover:text-white shadow-md py-2 px-6 inline-flex items-center">
                            <span class="mx-auto"><i class="fa-solid fa-house mr-2"></i>Home</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php
$message = $_SESSION["success_message"] ?? false;
if ($message) {
    echo <<< TXT
    <script>
        Swal.fire({
          position: "center",
          icon: "success",
          title: "$message",
          showConfirmButton: false,
          timer: 1500
        }); 
    </script>   
    TXT;
    unset($_SESSION["success_message"]);
}
?>

</html>