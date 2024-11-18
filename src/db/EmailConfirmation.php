<?php

namespace MyApp\db;

use \Exception;
use \PDOException;

class EmailConfirmation extends Connection
{
    private string $email;
    private string $username;
    private string $hash;
    private string $expires_at;

    public function createEmailConfirmation()
    {
        $query = "insert into emailsConfirmations (email, username, hash, expires_at) values (:e, :u, :h, :ex)";
        $stmt = Connection::getConnection()->prepare($query);
        try {
            $stmt->execute([
                ":e" => $this->email,
                ":u" => $this->username,
                ":h" => $this->hash,
                ":ex" => $this->expires_at,
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error creating EmailConfirmation '{$this->email}':{$e->getMessage()}", (int)$e->getCode());
        } finally {
            Connection::closeConnection();
        }
    }

    public static function getAttributeByEmail(string $email, string $attribute): string|false
    {
        $query = "select $attribute from emailsConfirmations where email=:e";
        $stmt = Connection::getConnection()->prepare($query);
        try {
            $stmt->execute([
                ":e" => $email,
            ]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Error fetching $attribute for the provided email '$email': {$e->getMessage()}", (int)$e->getCode());
        } finally {
            Connection::closeConnection();
        }
    }

    public static function deleteEmailConfirmation(string $email): void
    {
        $query = "delete from emailsConfirmations where email=:e";
        $stmt = Connection::getConnection()->prepare($query);
        try {
            $stmt->execute([
                ":e" => $email,
            ]);
        } catch (PDOException $e) {
            throw new Exception("Failed to delete EmailConfirmation: {$e->getMessage()}", (int)$e->getCode());
        } finally {
            Connection::closeConnection();
        }
    }

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of hash
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set the value of hash
     *
     * @return  self
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get the value of expires_at
     */
    public function getExpires_at()
    {
        return $this->expires_at;
    }

    /**
     * Set the value of expires_at
     *
     * @return  self
     */
    public function setExpires_at($expires_at)
    {
        $this->expires_at = $expires_at;

        return $this;
    }

    /**
     * Get the value of username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of username
     *
     * @return  self
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }
}
