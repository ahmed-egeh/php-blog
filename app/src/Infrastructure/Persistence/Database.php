<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Infrastructure\Env;
use PDO;
use PDOException;

final class Database
{
    private static ?PDO $connection = null;

    public static function connect(): PDO
    {
        if (self::$connection === null) {
            $host = Env::string('DB_HOST');
            $port = Env::string('DB_PORT');
            $database = Env::string('MARIADB_DATABASE');
            $username = Env::string('MARIADB_USER');
            $password = Env::string('MARIADB_PASSWORD');

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
