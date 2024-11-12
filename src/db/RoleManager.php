<?php

namespace MyApp\db;

use Exception;
use MyApp\Connection;
use PDO;
use PDOException;

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
            throw new Exception("Error while searching for Role ID: {$e->getMessage()}", (int)$e->getCode());
        } finally {
            parent::closeConnection();
        }
    }

    public static function createRole(Role ...$role)
    {
        $query = "insert into roles (name) values (:n)";
        $stmt = parent::getConnection()->prepare($query);
        try {
            foreach ($role as $element) {
                $stmt->execute([":n" => $element->toString()]);
            }
        } catch (PDOException $e) {
            throw new Exception("Error on Insert Role: {$e->getMessage()}", (int)$e->getCode());
        } finally {
            parent::closeConnection();
        }
    }

    public static function deleteRole(Role $role)
    {
        $query = "delete from roles where id=:i";
        $stmt = parent::getConnection()->prepare($query);
        try {
            $stmt->execute([":i" => self::getIdByRole($role)]);
        } catch (PDOException $e) {
            throw new Exception("Error on Delete Role: {$e->getMessage()}", (int)$e->getCode());
        } finally {
            parent::closeConnection();
        }
    }

    public static function updateRole(string $oldRole, Role $newRole)
    {
        $query = "update roles set name=:n where id=:i";
        $stmt = parent::getConnection()->prepare($query);
        try {
            $stmt->execute(["n:" => $newRole->toString(), ":i" => self::getIdByRole($oldRole)]);
        } catch (PDOException $e) {
            throw new Exception("Error on Update Role: {$e->getMessage()}", (int)$e->getCode());
        } finally {
            parent::closeConnection();
        }
    }
}
