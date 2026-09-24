<?php
declare(strict_types=1);

namespace App\ValueObjects;

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
    ) {}

    public static function fromRow(object $row): self
    {
        $expiresAt = $row->activation_expires_at ?? null;

        return new self(
            id: new UserId((int) $row->id),
            username: new Username((string) $row->username),
            email: new Email((string) $row->email),
            password: HashedPassword::fromHash((string) $row->password),
            firstName: new PersonName((string) $row->first_name),
            lastName: new PersonName((string) $row->last_name),
            activated: (bool) (int) ($row->activated ?? 0),
            image: self::nullableString($row->user_image ?? null),
            activationTokenHash: self::nullableString($row->activation_token ?? null),
            activationExpiresAt: $expiresAt ? new DateTimeImmutable((string) $expiresAt) : null,
            rememberTokenHash: self::nullableString($row->remember_token ?? null),
        );
    }

    public function fullName(): string
    {
        return $this->firstName->value . ' ' . $this->lastName->value;
    }

    public function activationExpired(): bool
    {
        return $this->activationExpiresAt !== null
            && $this->activationExpiresAt->getTimestamp() < time();
    }

    private static function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
