<?php
declare(strict_types=1);

namespace App\Core;

use Generator;
use PDO;
use PDOStatement;

abstract class Model
{
    protected PDO $db;

    protected string $table;

    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /** @return Generator<int, object> */
    public function all(): Generator
    {
        $statement = $this->db->query(
            "SELECT * FROM {$this->table}"
        );

        if (!$statement instanceof PDOStatement) {
            return;
        }

        while ($row = $statement->fetch()) {
            if (is_object($row)) {
                yield $row;
            }
        }
    }

    public function find(int $id): object|false
    {
        $statement = $this->db->prepare(
            "SELECT * FROM {$this->table}
             WHERE {$this->primaryKey} = :id
             LIMIT 1"
        );

        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch();

        return is_object($row) ? $row : false;
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare(
            "DELETE FROM {$this->table}
             WHERE {$this->primaryKey} = :id"
        );

        $statement->bindValue('id', $id, PDO::PARAM_INT);

        return $statement->execute();
    }

    public function insertOne(object $data): bool
    {
        $fields = get_object_vars($data);
        $columns = array_keys($fields);

        $columnNames = implode(', ', $columns);

        $placeholders = implode(
            ', ',
            array_map(
                static fn(string|int $column): string => ':' . $column,
                $columns
            )
        );

        $sql = "
            INSERT INTO {$this->table} ({$columnNames})
            VALUES ({$placeholders})
        ";

        $statement = $this->db->prepare($sql);

        return $statement->execute($fields);
    }
}
