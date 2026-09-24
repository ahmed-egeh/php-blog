<?php
declare(strict_types=1);

namespace App\Domain\Category;

use App\Domain\Shared\InvalidValue;

final readonly class CategoryName
{
    public string $value;

    public function __construct(string $value)
    {
        $normalized = trim($value);

        if ($normalized === '') {
            throw new InvalidValue('Category name is required.');
        }

        if (strlen($normalized) > 80) {
            throw new InvalidValue('Category name is too long.');
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
