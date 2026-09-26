<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Category\CategoryId;
use App\Domain\Post\Post;
use App\Domain\Post\PostCollection;
use App\Domain\Post\PostContent;
use App\Domain\Post\PostId;
use App\Domain\Post\PostRepository;
use App\Domain\Post\PostTitle;
use App\Domain\User\UserId;
use PDO;
use PDOStatement;

final class PdoPostRepository implements PostRepository
{
    private const SELECT_WITH_RELATIONS = "
        SELECT
            posts.*,
            users.username,
            users.first_name,
            users.last_name,
            users.user_image AS author_image,
            categories.name AS category_name
        FROM posts
        INNER JOIN users ON users.id = posts.user_id
        INNER JOIN categories ON categories.id = posts.category_id
    ";

    public function __construct(private PDO $pdo) {}

    public function countPublished(): int
    {
        $statement = $this->pdo->query("
            SELECT COUNT(*)
            FROM posts
            WHERE deleted_at IS NULL
              AND active = 1
        ");

        if (!$statement instanceof PDOStatement) {
            return 0;
        }

        return (int) $statement->fetchColumn();
    }

    public function publishedSlice(int $offset, int $limit): PostCollection
    {
        $statement = $this->pdo->prepare(
            self::SELECT_WITH_RELATIONS . "
            WHERE posts.deleted_at IS NULL
              AND posts.active = 1
            ORDER BY posts.created_at DESC
            LIMIT :limit OFFSET :offset
        "
        );
        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return PostMapper::collection($statement);
    }

    public function featuredForHome(int $limit = 10): PostCollection
    {
        $statement = $this->pdo->prepare(
            self::SELECT_WITH_RELATIONS . "
            WHERE posts.deleted_at IS NULL
              AND posts.active = 1
            ORDER BY posts.created_at DESC
            LIMIT :limit
        "
        );
        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return PostMapper::collection($statement);
    }

    public function allByUser(UserId $userId): PostCollection
    {
        $statement = $this->pdo->prepare(
            self::SELECT_WITH_RELATIONS . "
            WHERE posts.deleted_at IS NULL
              AND posts.user_id = :user_id
            ORDER BY posts.created_at DESC
        "
        );
        $statement->bindValue('user_id', $userId->value, PDO::PARAM_INT);
        $statement->execute();

        return PostMapper::collection($statement);
    }

    public function find(PostId $id): ?Post
    {
        $statement = $this->pdo->prepare(
            self::SELECT_WITH_RELATIONS . "
            WHERE posts.id = :id
              AND posts.deleted_at IS NULL
            LIMIT 1
        "
        );
        $statement->bindValue('id', $id->value, PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch();

        return is_object($row) ? PostMapper::fromRow($row) : null;
    }

    public function create(
        UserId $userId,
        PostTitle $title,
        PostContent $content,
        CategoryId $categoryId,
    ): PostId {
        $statement = $this->pdo->prepare("
            INSERT INTO posts (user_id, title, content, active, image, category_id)
            VALUES (:user_id, :title, :content, 1, NULL, :category_id)
        ");
        $statement->execute([
            'user_id' => $userId->value,
            'title' => $title->value,
            'content' => $content->value,
            'category_id' => $categoryId->value,
        ]);

        return new PostId((int) $this->pdo->lastInsertId());
    }

    public function update(PostId $id, PostTitle $title, PostContent $content, CategoryId $categoryId): bool
    {
        $statement = $this->pdo->prepare("
            UPDATE posts
            SET
                title = :title,
                content = :content,
                category_id = :category_id
            WHERE id = :id
              AND deleted_at IS NULL
        ");
        $statement->bindValue('title', $title->value);
        $statement->bindValue('content', $content->value);
        $statement->bindValue('category_id', $categoryId->value, PDO::PARAM_INT);
        $statement->bindValue('id', $id->value, PDO::PARAM_INT);

        return $statement->execute();
    }

    public function delete(PostId $id): bool
    {
        $statement = $this->pdo->prepare("
            UPDATE posts
            SET deleted_at = CURRENT_TIMESTAMP
            WHERE id = :id
              AND deleted_at IS NULL
        ");
        $statement->bindValue('id', $id->value, PDO::PARAM_INT);

        return $statement->execute();
    }
}
