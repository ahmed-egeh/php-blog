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
                self::$connection = new PDO($dsn, $username, $password);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                self::$connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            } catch (PDOException $e) {
                throw new PDOException(
                    'Database connection failed: ' . $e->getMessage()
                );
            }
        }

        return self::$connection;
    }
}
