<?php

use MyApp\utils\UserSession;

require __DIR__ . "/../vendor/autoload.php";

session_start();
session_destroy();

UserSession::redirectTo("login.php");
