<?php

namespace MyApp\db;

use \Exception;
use MyApp\db\Connection;
use MyApp\utils\Role;
use \PDO;
use \PDOException;

require __DIR__ . "/../../vendor/autoload.php";

class RoleManager extends Connection
{
    public static function getIdByRole(Role|string $role): int
    {
        $query = "select id from roles where name=:n";
        $stmt = parent::getConnection()->prepare($query);
        try {
            $stringRole = ($role instanceof Role) ? $role->toString() : $role;
            $stmt->execute([":n" => $stringRole]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) throw new Exception("Role '$stringRole' not found in the database.");
            return (int)$result["id"];
        } catch (PDOException $e) {
            throw new Exception("Error occurred while retrieving the ID for role '$stringRole': {$e->getMessage()}", (int)$e->getCode());
        } finally {
            parent::closeConnection();
        }
    }

    public static function getRoleNameById(int $role_id): string
    {
        $query = "select name from roles where id=:i";
        $stmt = parent::getConnection()->prepare($query);
        try {
            $stmt->execute([":i" => $role_id]);
            $roleName = $stmt->fetchColumn();
            if (!$roleName) throw new Exception("Role with ID '$role_id' not found.");
            return $roleName;
        } catch (PDOException $e) {
            throw new Exception("Error occurred while retrieving the name for role '$role_id': {$e->getMessage()}", (int)$e->getCode());
        } finally {
            parent::closeConnection();
        }
    }

    public static function createRole(Role ...$role): void
    {
        $query = "insert into roles (name) values (:n)";
        $stmt = parent::getConnection()->prepare($query);
        try {
            foreach ($role as $element) {
                $stmt->execute([":n" => $element->toString()]);
            }
        } catch (PDOException $e) {
            throw new Exception("Error inserting role '{$element->toString()}': {$e->getMessage()}", (int)$e->getCode());
        } finally {
            parent::closeConnection();
        }
    }

    public static function createRolesEnum(): void
    {
        foreach (Role::cases() as $role) {
            self::createRole($role);
        }
    }

    public static function deleteRole(Role $role): void
    {
        $query = "delete from roles where id=:i";
        $stmt = parent::getConnection()->prepare($query);
        try {
            $stmt->execute([":i" => self::getIdByRole($role)]);
        } catch (PDOException $e) {
            throw new Exception("Error deleting role '{$role->toString()}': {$e->getMessage()}", (int)$e->getCode());
        } finally {
            parent::closeConnection();
        }
    }

    public static function updateRole(string $oldRole, Role $newRole): void
    {
        $query = "update roles set name=:n where id=:i";
        $stmt = parent::getConnection()->prepare($query);
        try {
            $stmt->execute(["n:" => $newRole->toString(), ":i" => self::getIdByRole($oldRole)]);
        } catch (PDOException $e) {
            throw new Exception("Error updating role from '{$oldRole}' to '{$newRole->toString()}': {$e->getMessage()}", (int)$e->getCode());
        } finally {
            parent::closeConnection();
        }
    }
}
