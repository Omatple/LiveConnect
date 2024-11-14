<?php

namespace MyApp\db;

use \Exception;
use \Faker\Factory;
use \Mmo\Faker\FakeimgProvider;
use MyApp\db\Connection;
use \PDO;
use \PDOException;

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
        $query = "insert into users (username, email, password, img, role_id) values (:u, :e, :p, :i, :r);";
        $stmt = parent::getConnection()->prepare($query);
        try {
            $stmt->execute([
                ":u" => $this->username,
                ":e" => $this->email,
                ":p" => $this->password,
                ":i" => $this->image,
                ":r" => RoleManager::getIdByRole($this->role),
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error creating user '{$this->username}': {$e->getMessage()}", (int)$e->getCode());
        } finally {
            parent::closeConnection();
        }
    }

    public static function createDefaultUser(string $username, string $email, string $password): void
    {
        (new self())
            ->setUsername($username)
            ->setEmail($email)
            ->setPassword($password)
            ->setImage()
            ->setRole(Role::User)
            ->createUser();
    }

    public static function createGuestUser(): void
    {
        (new self())
            ->setUsername("")
            ->setEmail("")
            ->setPassword("")
            ->setImage()
            ->setRole(Role::Guest)
            ->createUser();
    }

    public static function getGuestUser(): User|false
    {
        $role_id = RoleManager::getIdByRole(Role::Guest);
        $query = "select * from users where role_id=:r limit 1";
        $stmt = parent::getConnection()->prepare($query);
        try {
            $stmt->execute([
                ":r" => $role_id,
            ]);
            return $stmt->fetchObject(self::class);
        } catch (PDOException $e) {
            throw new Exception("Error retrieving guest user with role_id '{$role_id}': {$e->getMessage()}", (int)$e->getCode());
        } finally {
            parent::closeConnection();
        }
    }

    public static function createRandomUsers(int $amount): void
    {
        $faker = Factory::create('es_ES');
        $faker->addProvider(new FakeimgProvider($faker));
        for ($i = 0; $i < $amount; $i++) {
            $username = $faker->unique()->userName();
            (new self())
                ->setUsername($username)
                ->setEmail("{$username}@{$faker->freeEmailDomain()}")
                ->setPassword("admin")
                ->setRole($faker->randomElement(Role::cases()))
                ->setImage("img/" . $faker->fakeImg(dir: __DIR__ . "/../../public/img", width: 480, height: 480, fullPath: false, text: strtoupper(substr($username, 0, 2)), backgroundColor: [random_int(0, 255), random_int(0, 255), random_int(0, 255)]))
                ->createUser();
        }
    }

    public function readUsers(): array
    {
        $query = "select * from users";
        $stmt = parent::getConnection()->prepare($query);
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
        } catch (PDOException $e) {
            throw new Exception("Error reading users: {$e->getMessage()}", (int)$e->getCode());
        } finally {
            parent::closeConnection();
        }
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
        $query = "update users set username=:u, email=:e, password=:p, img=:i, role=:r where username=:us";
        $stmt = parent::getConnection()->prepare($query);
        try {
            $stmt->execute([
                ":u" => $this->username,
                ":e" => $this->email,
                ":p" => $this->password,
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

    public static function findUserByUsername(string $username): self|false
    {
        $query = "select * from users where username=:u";
        $stmt = parent::getConnection()->prepare($query);
        try {
            $stmt->execute([
                ":u" => $username,
            ]);
            return $stmt->fetchObject(self::class);
        } catch (PDOException $e) {
            throw new Exception("Error retrieving user '{$username}': {$e->getMessage()}", (int)$e->getCode());
        } finally {
            parent::closeConnection();
        }
    }

    public static function isAttributeTaken(string $field, string $value, ?string $excludeUsername = null): bool
    {
        if (!in_array($field, ['username', 'email'])) throw new Exception("Invalid field name: $field");
        $query = ($excludeUsername === null) ? "select count(*) as total from users where $field=:v"
            : "select count(*) as total from users where $field=:v and username<>:eu";
        $stmt = parent::getConnection()->prepare($query);
        try {
            ($excludeUsername === null) ? $stmt->execute([":v" => $value]) : $stmt->execute([":v" => $value, ":eu" => $excludeUsername]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] === 0;
        } catch (PDOException $e) {
            throw new Exception("Error while checking if '$value' exists for '$field': {$e->getMessage()}", (int)$e->getCode());
        } finally {
            parent::closeConnection();
        }
    }

    /**
     * Get the value of username
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Set the value of username
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set the value of password
     */
    public function setPassword(string $password): self
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);

        return $this;
    }

    /**
     * Get the value of image
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * Set the value of image
     */
    public function setImage(?string $image = null): self
    {
        $this->image = $image ?? 'img/default.png';

        return $this;
    }

    /**
     * Get the value of role
     */
    public function getRole(): Role
    {
        return $this->role;
    }

    /**
     * Set the value of role
     */
    public function setRole(?Role $role): self
    {
        $this->role = $role ?? Role::Guest;

        return $this;
    }
}
