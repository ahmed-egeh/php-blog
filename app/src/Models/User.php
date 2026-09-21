<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model {
    protected string $table = 'users';

    public function findByActivationToken(string $token): array|false
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE activation_token = :token
            AND activated = 0
            LIMIT 1
        ");

        $stmt->execute([
            'token' => $token,
        ]);

        return $stmt->fetch();
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

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE email = :email
            LIMIT 1
        ");

        $stmt->execute([
            'email' => $email
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
