<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Post;
use PDO;

class PostRepository
{
    private PDO $db;
    private Post $model;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->model = new Post();
    }

    public function allPublished(): array
    {
        $stmt = $this->db->query("
            SELECT
                posts.*,
                users.username,
                users.first_name,
                users.last_name,
                categories.name AS category_name
            FROM posts
            INNER JOIN users ON users.id = posts.user_id
            INNER JOIN categories ON categories.id = posts.category_id
            WHERE posts.deleted_at IS NULL
              AND posts.active = 1
            ORDER BY posts.created_at DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                posts.*,
                users.username,
                users.first_name,
                users.last_name,
                categories.name AS category_name
            FROM posts
            INNER JOIN users ON users.id = posts.user_id
            INNER JOIN categories ON categories.id = posts.category_id
            WHERE posts.deleted_at IS NULL
              AND posts.user_id = :user_id
            ORDER BY posts.created_at DESC
        ");

        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT
                posts.*,
                users.username,
                users.first_name,
                users.last_name,
                categories.name AS category_name
            FROM posts
            INNER JOIN users ON users.id = posts.user_id
            INNER JOIN categories ON categories.id = posts.category_id
            WHERE posts.id = :id
              AND posts.deleted_at IS NULL
            LIMIT 1
        ");

        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data): int
    {
        $this->model->insertOne($data);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
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

        return $stmt->execute([
            'title' => $data['title'],
            'content' => $data['content'],
            'category_id' => $data['category_id'],
            'id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE posts
            SET deleted_at = CURRENT_TIMESTAMP
            WHERE id = :id
              AND deleted_at IS NULL
        ");

        return $stmt->execute(['id' => $id]);
    }
}
