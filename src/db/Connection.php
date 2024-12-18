<?php

namespace MyApp\db;

use \Dotenv\Dotenv;
use \Exception;
use \PDO;
use \PDOException;

require __DIR__ . "/../../vendor/autoload.php";

class Connection
{
    private static ?PDO $connection = null;

    protected static function getConnection(): PDO
    {
        return self::$connection ?? self::openConnection();
    }

    private static function openConnection(): PDO
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../../");
        $dotenv->safeLoad();
        foreach (["USER", "HOST", "PORT", "DBNAME", "PASSWORD"] as $key) {
            if (!isset($_ENV[$key])) throw new Exception("Error: Missing required environment variable '$key'");
        }
        $dsn = "mysql:dbname={$_ENV['DBNAME']};port={$_ENV['PORT']};host={$_ENV['HOST']};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => true,
        ];
        try {
            self::$connection = new PDO($dsn, $_ENV["USER"], $_ENV["PASSWORD"], $options);
        } catch (PDOException $e) {
            throw new Exception("Connection error: {$e->getMessage()}", (int)$e->getCode());
        }
        return self::$connection;
    }

    protected static function closeConnection(): void
    {
        self::$connection = null;
    }
}
