<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function connect(): PDO
    {
        if (self::$connection === null) {
            $host = $_ENV['DB_HOST'];
            $port = $_ENV['DB_PORT'] ?? 3306;
            $database = $_ENV['MARIADB_DATABASE'];
            $username = $_ENV['MARIADB_USER'];
            $password = $_ENV['MARIADB_PASSWORD'];

            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

            try {
                self::$connection = new PDO(
                    $dsn,
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                throw new PDOException(
                    'Database connection failed: ' . $e->getMessage()
                );
            }
        }

        return self::$connection;
    }
}