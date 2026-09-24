<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Shared\InvalidValue;

final readonly class Password
{
    public string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromPlain(string $value): self
    {
        if ($value === '') {
            throw new InvalidValue('Password is required.');
        }

        return new self($value);
    }

    public static function fromNew(string $value): self
    {
        $password = self::fromPlain($value);

        if (strlen($value) < 8 || !preg_match('/\d/', $value) || !preg_match('/[a-zA-Z]/', $value)) {
            throw new InvalidValue('Password must be at least 8 characters with a letter and a number.');
        }

        return $password;
    }

    public function hash(): HashedPassword
    {
        return HashedPassword::fromPlain($this);
    }

    public function matches(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function verify(HashedPassword $hash): bool
    {
        return $hash->matches($this);
    }
}
