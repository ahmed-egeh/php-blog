<?php
declare(strict_types=1);

namespace App\ValueObjects;

use DateTimeImmutable;

final readonly class Post
{
    public function __construct(
        public PostId $id,
        public UserId $userId,
        public PostTitle $title,
        public PostContent $content,
        public bool $active,
        public ?string $image,
        public CategoryId $categoryId,
        public CategoryName $categoryName,
        public PersonName $authorFirstName,
        public PersonName $authorLastName,
        public DateTimeImmutable $createdAt,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id: new PostId((int) $row->id),
            userId: new UserId((int) $row->user_id),
            title: new PostTitle((string) $row->title),
            content: new PostContent((string) $row->content),
            active: (bool) (int) ($row->active ?? 0),
            image: self::nullableString($row->image ?? null),
            categoryId: new CategoryId((int) $row->category_id),
            categoryName: new CategoryName((string) $row->category_name),
            authorFirstName: new PersonName((string) $row->first_name),
            authorLastName: new PersonName((string) $row->last_name),
            createdAt: new DateTimeImmutable((string) ($row->created_at ?? 'now')),
        );
    }

    public function authorName(): string
    {
        return $this->authorFirstName->value . ' ' . $this->authorLastName->value;
    }

    public function isOwnedBy(UserId $userId): bool
    {
        return $this->userId->equals($userId);
    }

    public function excerpt(int $length = 180): string
    {
        return $this->content->excerpt($length);
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
