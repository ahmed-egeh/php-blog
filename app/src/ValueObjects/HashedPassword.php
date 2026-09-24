<?php
declare(strict_types=1);

namespace App\ValueObjects;

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
        return new self(password_hash($password->value, PASSWORD_DEFAULT));
    }

    public function matches(Password $password): bool
    {
        return password_verify($password->value, $this->value);
    }
}
