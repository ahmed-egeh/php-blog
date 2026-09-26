<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Shared\PersonName;
use DateTimeImmutable;

final readonly class User
{
    public function __construct(
        public UserId $id,
        public Username $username,
        public Email $email,
        public HashedPassword $password,
        public PersonName $firstName,
        public PersonName $lastName,
        public bool $activated,
        public ?string $image,
        public ?string $activationTokenHash,
        public ?DateTimeImmutable $activationExpiresAt,
        public ?string $rememberTokenHash,
        public ?string $passwordResetTokenHash,
        public ?DateTimeImmutable $passwordResetExpiresAt,
    ) {}

    public function fullName(): string
    {
        return $this->firstName->value . ' ' . $this->lastName->value;
    }

    public function canLogin(): bool
    {
        return $this->activated;
    }

    public function passwordMatches(Password $password): bool
    {
        return $password->verify($this->password);
    }

    public function activationExpired(): bool
    {
        return $this->activationExpiresAt !== null
            && $this->activationExpiresAt->getTimestamp() < time();
    }

    public function passwordResetExpired(): bool
    {
        return $this->passwordResetExpiresAt === null
            || $this->passwordResetExpiresAt->getTimestamp() < time();
    }
}
