<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\User;
use PDO;

class UserRepository
{
    private PDO $db;
    private User $model;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->model = new User();
    }

    public function find(int $id): array|false
    {
        return $this->model->find($id);
    }

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE email = :email
            LIMIT 1
        ");

        $stmt->execute([
            'email' => $email,
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByActivationToken(string $tokenHash): array|false
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE activation_token = :token
            AND activated = 0
            LIMIT 1
        ");

        $stmt->execute([
            'token' => $tokenHash,
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data): bool
    {
        return $this->model->insertOne($data);
    }

    public function activate(int $userId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET
                activated = 1,
                activation_token = NULL,
                activation_expires_at = NULL
            WHERE id = :id
        ");

        return $stmt->execute([
            'id' => $userId,
        ]);
    }
}
