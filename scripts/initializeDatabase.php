<?php

use MyApp\db\RoleManager;
use MyApp\db\User;

require __DIR__ . "/../vendor/autoload.php";

$insertRoles = readline("Do you want to insert the different 'roles' into the database? (Note: If they already exist, there might be conflicts.) (y/n): ");
if (strtolower($insertRoles) === 'y') {
    RoleManager::createRolesEnum();
    echo "Roles have been successfully created or already exist." . PHP_EOL;
}

$insertUsers = readline("Do you want to insert random test users into the database? (y/n): ");
if (strtolower($insertUsers) === 'y') {
    $amount = (int) readline("How many users would you like to insert? (2-20): ");
    while ($amount < 2 || $amount > 20) {
        echo "Error: Please enter a number between 2 and 20." . PHP_EOL;
        $amount = (int) readline("How many users would you like to insert? (2-20): ");
    }
    User::createRandomUsers($amount);
    echo "Success: $amount random users have been created." . PHP_EOL;
}
