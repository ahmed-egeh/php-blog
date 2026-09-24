<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Collections\PostCollection;
use App\Core\Database;
use App\Models\Post as PostModel;
use App\Persistence\PostInsert;
use App\ValueObjects\CategoryId;
use App\ValueObjects\Post;
use App\ValueObjects\PostContent;
use App\ValueObjects\PostId;
use App\ValueObjects\PostTitle;
use App\ValueObjects\UserId;
use PDO;

class PostRepository
{
    private PDO $db;
    private PostModel $model;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->model = new PostModel();
    }

    public function allPublished(): PostCollection
    {
        $stmt = $this->db->query("
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
            WHERE posts.deleted_at IS NULL
              AND posts.active = 1
            ORDER BY posts.created_at DESC
        ");

        return PostCollection::fromRows($stmt);
    }

    public function featuredForHome(int $limit = 10): PostCollection
    {
        $stmt = $this->db->prepare("
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
            WHERE posts.deleted_at IS NULL
              AND posts.active = 1
            ORDER BY posts.created_at DESC
            LIMIT :limit
        ");

        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return PostCollection::fromRows($stmt);
    }

    public function allByUser(UserId $userId): PostCollection
    {
        $stmt = $this->db->prepare("
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
            WHERE posts.deleted_at IS NULL
              AND posts.user_id = :user_id
            ORDER BY posts.created_at DESC
        ");

        $stmt->bindValue('user_id', $userId->value, PDO::PARAM_INT);
        $stmt->execute();

        return PostCollection::fromRows($stmt);
    }

    public function find(PostId $id): ?Post
    {
        $stmt = $this->db->prepare("
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
            WHERE posts.id = :id
              AND posts.deleted_at IS NULL
            LIMIT 1
        ");

        $stmt->bindValue('id', $id->value, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();

        return is_object($row) ? Post::fromRow($row) : null;
    }

    public function create(
        UserId $userId,
        PostTitle $title,
        PostContent $content,
        CategoryId $categoryId,
    ): PostId {
        $this->model->insertOne(new PostInsert(
            user_id: $userId->value,
            title: $title->value,
            content: $content->value,
            active: 1,
            image: null,
            category_id: $categoryId->value,
        ));

        return new PostId((int) $this->db->lastInsertId());
    }

    public function update(PostId $id, PostTitle $title, PostContent $content, CategoryId $categoryId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE posts
            SET
                title = :title,
                content = :content,
                category_id = :category_id
            WHERE id = :id
              AND deleted_at IS NULL
        ");

        $stmt->bindValue('title', $title->value);
        $stmt->bindValue('content', $content->value);
        $stmt->bindValue('category_id', $categoryId->value, PDO::PARAM_INT);
        $stmt->bindValue('id', $id->value, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function delete(PostId $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE posts
            SET deleted_at = CURRENT_TIMESTAMP
            WHERE id = :id
              AND deleted_at IS NULL
        ");

        $stmt->bindValue('id', $id->value, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
