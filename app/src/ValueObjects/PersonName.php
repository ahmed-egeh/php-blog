<?php
declare(strict_types=1);

namespace App\ValueObjects;

final readonly class PersonName
{
    public string $value;

    public function __construct(string $value)
    {
        $normalized = trim($value);

        if ($normalized === '') {
            throw new InvalidValue('Name is required.');
        }

        if (strlen($normalized) > 200) {
            throw new InvalidValue('Name is too long.');
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
