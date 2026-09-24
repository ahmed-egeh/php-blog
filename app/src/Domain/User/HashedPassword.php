<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Shared\InvalidValue;

final readonly class HashedPassword
{
    public string $value;

    private function __construct(string $value)
    {
        if ($value === '') {
            throw new InvalidValue('Password hash is missing.');
        }

        $this->value = $value;
    }

    public static function fromHash(string $hash): self
    {
        return new self($hash);
    }

    public static function fromPlain(Password $password): self
    {
        $hash = password_hash($password->value, PASSWORD_DEFAULT);
        if ($hash === false) {
            throw new InvalidValue('Could not hash the password.');
        }

        return new self($hash);
    }

    public function matches(Password $password): bool
    {
        return password_verify($password->value, $this->value);
    }
}
