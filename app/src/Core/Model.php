<?php

namespace App\Core;

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

    public function all(): array
    {
        $statement = $this->db->query(
            "SELECT * FROM {$this->table}"
        );

        return $statement->fetchAll();
    }

    public function find(int $id): array|false
    {
        $statement = $this->db->prepare(
            "SELECT * FROM {$this->table}
             WHERE {$this->primaryKey} = :id
             LIMIT 1"
        );

        $statement->execute([
            'id' => $id,
        ]);

        return $statement->fetch();
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare(
            "DELETE FROM {$this->table}
             WHERE {$this->primaryKey} = :id"
        );

        return $statement->execute([
            'id' => $id,
        ]);
    }
}