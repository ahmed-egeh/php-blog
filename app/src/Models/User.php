<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Model;

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
}
