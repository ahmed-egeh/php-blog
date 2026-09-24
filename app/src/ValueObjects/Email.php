<?php
declare(strict_types=1);

namespace App\ValueObjects;

final readonly class Email
{
    public string $value;

    public function __construct(string $value)
    {
        $normalized = trim($value);

        if ($normalized === '' || filter_var($normalized, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidValue('Invalid email address.');
        }

        if (strlen($normalized) > 200) {
            throw new InvalidValue('Email is too long.');
        }

        $this->value = $normalized;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
