<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Category\CategoryId;
use App\Domain\Category\CategoryName;
use App\Domain\Post\Post;
use App\Domain\Post\PostCollection;
use App\Domain\Post\PostContent;
use App\Domain\Post\PostId;
use App\Domain\Post\PostTitle;
use App\Domain\Shared\PersonName;
use App\Domain\User\UserId;
use DateTimeImmutable;
use PDOStatement;

final class PostMapper
{
    public static function fromRow(object $row): Post
    {
        return new Post(
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

    public static function collection(PDOStatement $statement): PostCollection
    {
        $posts = [];

        while ($row = $statement->fetch()) {
            if (is_object($row)) {
                $posts[] = self::fromRow($row);
            }
        }

        return new PostCollection(...$posts);
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
