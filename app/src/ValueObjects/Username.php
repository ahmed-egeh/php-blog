<?php
declare(strict_types=1);

namespace App\ValueObjects;

final readonly class Username
{
    public string $value;

    public function __construct(string $value)
    {
        $normalized = trim($value);

        if ($normalized === '') {
            throw new InvalidValue('Username is required.');
        }

        if (strlen($normalized) > 200) {
            throw new InvalidValue('Username is too long.');
        }

        $this->value = $normalized;
    }

    public static function fromNames(PersonName $firstName, PersonName $lastName): self
    {
        return new self($firstName->value . '_' . $lastName->value . '_' . random_int(1, 1000));
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
