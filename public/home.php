<?php

use MyApp\utils\Role;
use MyApp\utils\UserSession;

require __DIR__ . "/../vendor/autoload.php";

session_start();

$user = $_SESSION["user"] ?? UserSession::initializeGuestSession();
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
    <title>Home | File-To</title>
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
            <div class="bg-white rounded-lg shadow-lg p-5 md:px-20 md:pb-20 md:pt-10 -mx-2">
                <div class="flex justify-end mb-5">
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
                        File-<span class="text-indigo-600">To</span>
                    </h2>
                    <h3 class='text-xl md:text-3xl mt-10'>Coming Soon</h3>
                    <p class="text-md md:text-xl mt-10">We’re working hard to bring you a new experience. Stay tuned and check back for updates!</p>
                </div>
                <div class="flex flex-wrap mt-10 justify-center">
                    <?php
                    if (UserSession::hasRole(Role::Admin, Role::Root)) {
                        echo <<< TXT
                        <div class="m-3">
                            <a href="admin.php" title="Admin View"
                                class="md:w-42 bg-white tracking-wide text-gray-800 font-bold rounded border-2 border-red-600 hover:border-red-600 hover:bg-red-600 hover:text-white shadow-md py-2 px-6 inline-flex items-center">
                                <span class="mx-auto"><i class="fa-solid fa-user-gear mr-2"></i>Admin View</span>
                            </a>
                        </div>
                        <div class="m-3">
                            <a href="userProfile.php" title="User View"
                                class="md:w-42 bg-white tracking-wide text-gray-800 font-bold rounded border-2 border-blue-600 hover:border-blue-600 hover:bg-blue-600 hover:text-white shadow-md py-2 px-6 inline-flex items-center">
                                <span class="mx-auto"><i class="fa-solid fa-user-large mr-2"></i>User View</span>
                            </a>
                        </div>
                        TXT;
                    }
                    ?>
                    <?php
                    if (UserSession::hasRole(Role::User)) {
                        echo <<< TXT
                        <div class="m-3">
                            <a href="userProfile.php" title="Your profile"
                                class="md:w-42 bg-white tracking-wide text-gray-800 font-bold rounded border-2 border-blue-500 hover:border-blue-500 hover:bg-blue-500 hover:text-white shadow-md py-2 px-6 inline-flex items-center">
                                <span class="mx-auto"><i class="fa-solid fa-id-badge mr-2"></i>Your profile</span>
                            </a>
                        </div>
                        TXT;
                    }
                    ?>
                    <?php
                    if (UserSession::hasRole(Role::Guest)) {
                        echo <<< TXT
                        <div class="m-3">
                            <a href="register.php" title="Create an Account"
                                class="md:w-42 bg-white tracking-wide text-gray-800 font-bold rounded border-2 border-green-500 hover:border-teal-500 hover:bg-green-500 hover:text-white shadow-md py-2 px-6 inline-flex items-center">
                                <span class="mx-auto"><i class="fa-solid fa-square-plus mr-2"></i>Create an Account</span>
                            </a>
                        </div>
                        TXT;
                    }
                    ?>
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
          timer: 2000
        }); 
    </script>   
    TXT;
    unset($_SESSION["success_message"]);
}
?>

</html>