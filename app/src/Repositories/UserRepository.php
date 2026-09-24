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
              AND deleted_at IS NULL
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

    public function findByRememberToken(string $tokenHash): array|false
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE remember_token = :token
              AND deleted_at IS NULL
              AND activated = 1
            LIMIT 1
        ");

        $stmt->execute(['token' => $tokenHash]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateRememberToken(int $userId, ?string $tokenHash): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET remember_token = :token
            WHERE id = :id
              AND deleted_at IS NULL
        ");

        return $stmt->execute([
            'token' => $tokenHash,
            'id' => $userId,
        ]);
    }

    public function create(array $data): bool
    {
        return $this->model->insertOne($data);
    }

    public function updateProfile(int $userId, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET
                first_name = :first_name,
                last_name = :last_name,
                user_image = :user_image
            WHERE id = :id
              AND deleted_at IS NULL
        ");

        return $stmt->execute([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'user_image' => $data['user_image'],
            'id' => $userId,
        ]);
    }

    public function updatePassword(int $userId, string $passwordHash): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET password = :password
            WHERE id = :id
              AND deleted_at IS NULL
        ");

        return $stmt->execute([
            'password' => $passwordHash,
            'id' => $userId,
        ]);
    }

    public function softDelete(int $userId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET deleted_at = CURRENT_TIMESTAMP
            WHERE id = :id
              AND deleted_at IS NULL
        ");

        return $stmt->execute(['id' => $userId]);
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
