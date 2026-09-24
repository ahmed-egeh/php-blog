<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\User as UserModel;
use App\Persistence\UserInsert;
use App\ValueObjects\Email;
use App\ValueObjects\HashedPassword;
use App\ValueObjects\PersonName;
use App\ValueObjects\User;
use App\ValueObjects\UserId;
use App\ValueObjects\Username;
use PDO;

class UserRepository
{
    private PDO $db;
    private UserModel $model;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->model = new UserModel();
    }

    public function find(UserId $id): ?User
    {
        return $this->hydrate($this->model->find($id->value));
    }

    public function findByEmail(Email $email): ?User
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE email = :email
              AND deleted_at IS NULL
            LIMIT 1
        ");

        $stmt->bindValue('email', $email->value);
        $stmt->execute();

        return $this->hydrate($stmt->fetch());
    }

    public function findByActivationToken(string $tokenHash): ?User
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE activation_token = :token
            AND activated = 0
            LIMIT 1
        ");

        $stmt->bindValue('token', $tokenHash);
        $stmt->execute();

        return $this->hydrate($stmt->fetch());
    }

    public function findByRememberToken(string $tokenHash): ?User
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE remember_token = :token
              AND deleted_at IS NULL
              AND activated = 1
            LIMIT 1
        ");

        $stmt->bindValue('token', $tokenHash);
        $stmt->execute();

        return $this->hydrate($stmt->fetch());
    }

    public function updateRememberToken(UserId $userId, ?string $tokenHash): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET remember_token = :token
            WHERE id = :id
              AND deleted_at IS NULL
        ");

        $stmt->bindValue('token', $tokenHash);
        $stmt->bindValue('id', $userId->value, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function create(
        Username $username,
        Email $email,
        HashedPassword $password,
        PersonName $firstName,
        PersonName $lastName,
        string $activationTokenHash,
        string $activationExpiresAt,
    ): bool {
        return $this->model->insertOne(new UserInsert(
            username: $username->value,
            email: $email->value,
            password: $password->value,
            first_name: $firstName->value,
            last_name: $lastName->value,
            activated: 0,
            user_image: null,
            activation_token: $activationTokenHash,
            activation_expires_at: $activationExpiresAt,
        ));
    }

    public function updateProfile(UserId $userId, PersonName $firstName, PersonName $lastName, ?string $image): bool
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

        $stmt->bindValue('first_name', $firstName->value);
        $stmt->bindValue('last_name', $lastName->value);
        $stmt->bindValue('user_image', $image);
        $stmt->bindValue('id', $userId->value, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function updatePassword(UserId $userId, HashedPassword $password): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET password = :password
            WHERE id = :id
              AND deleted_at IS NULL
        ");

        $stmt->bindValue('password', $password->value);
        $stmt->bindValue('id', $userId->value, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function softDelete(UserId $userId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET deleted_at = CURRENT_TIMESTAMP
            WHERE id = :id
              AND deleted_at IS NULL
        ");

        $stmt->bindValue('id', $userId->value, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function activate(UserId $userId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET
                activated = 1,
                activation_token = NULL,
                activation_expires_at = NULL
            WHERE id = :id
        ");

        $stmt->bindValue('id', $userId->value, PDO::PARAM_INT);

        return $stmt->execute();
    }

    private function hydrate(object|false $row): ?User
    {
        return $row === false ? null : User::fromRow($row);
    }
}
