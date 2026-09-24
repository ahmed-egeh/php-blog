<?php

namespace App\Core;

use Generator;
use PDO;

abstract class Model
{
    protected PDO $db;

    protected string $table;

    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function all(): Generator
    {
        $statement = $this->db->query(
            "SELECT * FROM {$this->table}"
        );

        while ($row = $statement->fetch()) {
            yield $row;
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

        return $statement->fetch();
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
            array_map(fn($column) => ':' . $column, $columns)
        );

        $sql = "
            INSERT INTO {$this->table} ({$columnNames})
            VALUES ({$placeholders})
        ";

        $statement = $this->db->prepare($sql);

        return $statement->execute($fields);
    }
}
