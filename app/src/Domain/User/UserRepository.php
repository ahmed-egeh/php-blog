<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Shared\PersonName;

interface UserRepository
{
    public function find(UserId $id): ?User;

    public function findByEmail(Email $email): ?User;

    public function findByActivationToken(string $tokenHash): ?User;

    public function findByRememberToken(string $tokenHash): ?User;

    public function updateRememberToken(UserId $userId, ?string $tokenHash): bool;

    public function create(
        Username $username,
        Email $email,
        HashedPassword $password,
        PersonName $firstName,
        PersonName $lastName,
        string $activationTokenHash,
        string $activationExpiresAt,
    ): bool;

    public function updateProfile(UserId $userId, PersonName $firstName, PersonName $lastName, ?string $image): bool;

    public function updatePassword(UserId $userId, HashedPassword $password): bool;

    public function softDelete(UserId $userId): bool;

    public function activate(UserId $userId): bool;
}
