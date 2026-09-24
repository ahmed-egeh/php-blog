<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;
use Exception;

class PostService
{
    public function __construct(
        private PostRepository $posts = new PostRepository(),
        private CategoryRepository $categories = new CategoryRepository(),
    ) {}

    public function listPublished(): array
    {
        return $this->posts->allPublished();
    }

    public function listHome(int $limit = 10): array
    {
        return $this->posts->featuredForHome($limit);
    }

    public function listMine(int $userId): array
    {
        return $this->posts->allByUser($userId);
    }

    public function categories(): array
    {
        return $this->categories->all();
    }

    public function find(int $id): array|false
    {
        return $this->posts->find($id);
    }

    public function findPublic(int $id): array|false
    {
        $post = $this->posts->find($id);

        if (!$post || (int) $post['active'] !== 1) {
            return false;
        }

        return $post;
    }

    public function create(int $userId, string $title, string $content, int $categoryId): array
    {
        if (!$this->categories->find($categoryId)) {
            return $this->result(false, 'Please choose a valid category.', 422);
        }

        try {
            $id = $this->posts->create([
                'user_id' => $userId,
                'title' => $title,
                'content' => $content,
                'active' => 1,
                'image' => null,
                'category_id' => $categoryId,
            ]);
        } catch (Exception) {
            return $this->result(false, 'Could not create the post. Title may already be used.', 500);
        }

        return $this->result(true, 'Post published.', 200, $id);
    }

    public function update(int $id, int $userId, string $title, string $content, int $categoryId): array
    {
        $post = $this->posts->find($id);

        if (!$post) {
            return $this->result(false, 'Post not found.', 404);
        }

        if ((int) $post['user_id'] !== $userId) {
            return $this->result(false, 'You can only edit your own posts.', 403);
        }

        if (!$this->categories->find($categoryId)) {
            return $this->result(false, 'Please choose a valid category.', 422);
        }

        try {
            $this->posts->update($id, [
                'title' => $title,
                'content' => $content,
                'category_id' => $categoryId,
            ]);
        } catch (Exception) {
            return $this->result(false, 'Could not update the post. Title may already be used.', 500);
        }

        return $this->result(true, 'Post updated.', 200, $id);
    }

    public function delete(int $id, int $userId): array
    {
        $post = $this->posts->find($id);

        if (!$post) {
            return $this->result(false, 'Post not found.', 404);
        }

        if ((int) $post['user_id'] !== $userId) {
            return $this->result(false, 'You can only delete your own posts.', 403);
        }

        $this->posts->delete($id);

        return $this->result(true, 'Post deleted.');
    }

    private function result(bool $success, string $message, int $status = 200, ?int $id = null): array
    {
        return [
            'success' => $success,
            'message' => $message,
            'status' => $status,
            'id' => $id,
        ];
    }
}
