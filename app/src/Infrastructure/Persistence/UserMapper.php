<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Shared\PersonName;
use App\Domain\User\Email;
use App\Domain\User\HashedPassword;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\Username;
use DateTimeImmutable;

final class UserMapper
{
    public static function fromRow(object $row): User
    {
        $expiresAt = $row->activation_expires_at ?? null;

        return new User(
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

    private static function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
