<?php
declare(strict_types=1);

namespace App\Domain\Post;

use App\Domain\Category\CategoryId;
use App\Domain\Category\CategoryName;
use App\Domain\Shared\PersonName;
use App\Domain\User\UserId;
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

    public function authorName(): string
    {
        return $this->authorFirstName->value . ' ' . $this->authorLastName->value;
    }

    public function isOwnedBy(UserId $userId): bool
    {
        return $this->userId->equals($userId);
    }

    public function isPublic(): bool
    {
        return $this->active;
    }

    public function excerpt(int $length = 180): string
    {
        return $this->content->excerpt($length);
    }
}
