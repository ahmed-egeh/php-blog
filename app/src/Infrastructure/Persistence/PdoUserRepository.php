<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Shared\PersonName;
use App\Domain\User\Email;
use App\Domain\User\HashedPassword;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\Username;
use App\Domain\User\UserRepository;
use PDO;
use PDOStatement;

final class PdoUserRepository implements UserRepository
{
    public function __construct(private PDO $pdo) {}

    public function find(UserId $id): ?User
    {
        $statement = $this->pdo->prepare("
            SELECT *
            FROM users
            WHERE id = :id
              AND deleted_at IS NULL
            LIMIT 1
        ");
        $statement->bindValue('id', $id->value, PDO::PARAM_INT);
        $statement->execute();

        return $this->hydrate($this->fetchRow($statement));
    }

    public function findByEmail(Email $email): ?User
    {
        $statement = $this->pdo->prepare("
            SELECT *
            FROM users
            WHERE email = :email
              AND deleted_at IS NULL
            LIMIT 1
        ");
        $statement->bindValue('email', $email->value);
        $statement->execute();

        return $this->hydrate($this->fetchRow($statement));
    }

    public function findByActivationToken(string $tokenHash): ?User
    {
        $statement = $this->pdo->prepare("
            SELECT *
            FROM users
            WHERE activation_token = :token
              AND activated = 0
            LIMIT 1
        ");
        $statement->bindValue('token', $tokenHash);
        $statement->execute();

        return $this->hydrate($this->fetchRow($statement));
    }

    public function findByRememberToken(string $tokenHash): ?User
    {
        $statement = $this->pdo->prepare("
            SELECT *
            FROM users
            WHERE remember_token = :token
              AND deleted_at IS NULL
              AND activated = 1
            LIMIT 1
        ");
        $statement->bindValue('token', $tokenHash);
        $statement->execute();

        return $this->hydrate($this->fetchRow($statement));
    }

    public function updateRememberToken(UserId $userId, ?string $tokenHash): bool
    {
        $statement = $this->pdo->prepare("
            UPDATE users
            SET remember_token = :token
            WHERE id = :id
              AND deleted_at IS NULL
        ");
        $statement->bindValue('token', $tokenHash);
        $statement->bindValue('id', $userId->value, PDO::PARAM_INT);

        return $statement->execute();
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
        $statement = $this->pdo->prepare("
            INSERT INTO users (
                username,
                email,
                password,
                first_name,
                last_name,
                activated,
                user_image,
                activation_token,
                activation_expires_at
            ) VALUES (
                :username,
                :email,
                :password,
                :first_name,
                :last_name,
                0,
                NULL,
                :activation_token,
                :activation_expires_at
            )
        ");

        return $statement->execute([
            'username' => $username->value,
            'email' => $email->value,
            'password' => $password->value,
            'first_name' => $firstName->value,
            'last_name' => $lastName->value,
            'activation_token' => $activationTokenHash,
            'activation_expires_at' => $activationExpiresAt,
        ]);
    }

    public function updateProfile(UserId $userId, PersonName $firstName, PersonName $lastName, ?string $image): bool
    {
        $statement = $this->pdo->prepare("
            UPDATE users
            SET
                first_name = :first_name,
                last_name = :last_name,
                user_image = :user_image
            WHERE id = :id
              AND deleted_at IS NULL
        ");
        $statement->bindValue('first_name', $firstName->value);
        $statement->bindValue('last_name', $lastName->value);
        $statement->bindValue('user_image', $image);
        $statement->bindValue('id', $userId->value, PDO::PARAM_INT);

        return $statement->execute();
    }

    public function updatePassword(UserId $userId, HashedPassword $password): bool
    {
        $statement = $this->pdo->prepare("
            UPDATE users
            SET password = :password
            WHERE id = :id
              AND deleted_at IS NULL
        ");
        $statement->bindValue('password', $password->value);
        $statement->bindValue('id', $userId->value, PDO::PARAM_INT);

        return $statement->execute();
    }

    public function softDelete(UserId $userId): bool
    {
        $statement = $this->pdo->prepare("
            UPDATE users
            SET deleted_at = CURRENT_TIMESTAMP
            WHERE id = :id
              AND deleted_at IS NULL
        ");
        $statement->bindValue('id', $userId->value, PDO::PARAM_INT);

        return $statement->execute();
    }

    public function activate(UserId $userId): bool
    {
        $statement = $this->pdo->prepare("
            UPDATE users
            SET
                activated = 1,
                activation_token = NULL,
                activation_expires_at = NULL
            WHERE id = :id
        ");
        $statement->bindValue('id', $userId->value, PDO::PARAM_INT);

        return $statement->execute();
    }

    private function hydrate(object|false $row): ?User
    {
        return $row === false ? null : UserMapper::fromRow($row);
    }

    private function fetchRow(PDOStatement $statement): object|false
    {
        $row = $statement->fetch();

        return is_object($row) ? $row : false;
    }
}
