<?php

namespace MyApp\db;

use Exception;
use MyApp\Connection;
use PDO;
use PDOException;

require __DIR__ . "/../../vendor/autoload.php";

class User extends Connection
{
    private string $username;
    private string $email;
    private string $password;
    private string $image;
    private Role $role;

    public function createUser(): void
    {
        $query = "insert into users values (:u, :e, :p, :i, :r);";
        $stmt = parent::getConnection()->prepare($query);
        try {
            $stmt->execute([
                ":u" => $this->username,
                ":e" => $this->email,
                ":p" => password_hash($this->password, PASSWORD_BCRYPT),
                ":i" => $this->image,
                ":r" => RoleManager::getIdByRole($this->role),
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error creating user '{$this->username}': {$e->getMessage()}", (int)$e->getCode());
        } finally {
            parent::closeConnection();
        }
    }

    public function readUsers(): array
    {
        $query = "select * from users";
        $stmt = parent::getConnection()->prepare($query);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error reading users: {$e->getMessage()}", (int)$e->getCode());
        } finally {
            parent::closeConnection();
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS, User::class);
    }

    public function deleteUser(): void
    {
        $query = "delete from users where username=:u";
        $stmt = parent::getConnection()->prepare($query);
        try {
            $stmt->execute([":u" => $this->username]);
            $imagePath = __DIR__ . "/../../public/" . basename($this->image);
            if (basename($this->image) !== "default.png" && file_exists($imagePath)) {
                unlink($imagePath);
            }
        } catch (PDOException $e) {
            throw new Exception("Error deleting user '{$this->username}': {$e->getMessage()}", (int)$e->getCode());
        } finally {
            parent::closeConnection();
        }
    }

    public function updateUser(string $username): void
    {
        $query = "update users set username=:u, email=:e, password=:p, image=:i, role=:r where username=:us";
        $stmt = parent::getConnection()->prepare($query);
        try {
            $stmt->execute([
                ":u" => $this->username,
                ":e" => $this->email,
                ":p" => password_hash($this->password, PASSWORD_BCRYPT),
                ":i" => $this->image,
                ":r" => RoleManager::getIdByRole($this->role),
                ":us" => $username,
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error updating user '{$this->username}': {$e->getMessage()}", (int)$e->getCode());
        } finally {
            parent::closeConnection();
        }
    }

    public function existsAttribute(string $field, string $value, ?string $excludeUsername = null): bool
    {
        if (!in_array($field, ['username', 'email'])) throw new Exception("Invalid field name: $field");
        $query = ($excludeUsername === null) ? "select count(*) as total from users where $field=:v"
            : "select count(*) as total from users where $field=:v and username<>:eu";
        $stmt = parent::getConnection()->prepare($query);
        try {
            ($excludeUsername !== null) ? $stmt->execute([":v" => $value]) : $stmt->execute([":v" => $value, ":eu" => $excludeUsername]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] != 0;
        } catch (PDOException $e) {
            throw new Exception("Error while checking if '$value' exists for '$field': {$e->getMessage()}", (int)$e->getCode());
        } finally {
            parent::closeConnection();
        }
    }
}
