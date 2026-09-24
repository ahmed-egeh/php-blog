<?php
declare(strict_types=1);

namespace App\Application\Post;

use App\Application\Result;
use App\Domain\Category\CategoryId;
use App\Domain\Category\CategoryCollection;
use App\Domain\Category\CategoryRepository;
use App\Domain\Post\Post;
use App\Domain\Post\PostCollection;
use App\Domain\Post\PostContent;
use App\Domain\Post\PostId;
use App\Domain\Post\PostRepository;
use App\Domain\Post\PostTitle;
use App\Domain\User\UserId;
use Exception;

final class PostService
{
    public function __construct(
        private PostRepository $posts,
        private CategoryRepository $categories,
    ) {}

    public function listPublished(): PostCollection
    {
        return $this->posts->allPublished();
    }

    public function listHome(int $limit = 10): PostCollection
    {
        return $this->posts->featuredForHome($limit);
    }

    public function listMine(UserId $userId): PostCollection
    {
        return $this->posts->allByUser($userId);
    }

    public function categories(): CategoryCollection
    {
        return $this->categories->all();
    }

    public function find(PostId $id): ?Post
    {
        return $this->posts->find($id);
    }

    public function publish(UserId $userId, PostTitle $title, PostContent $content, CategoryId $categoryId): Result
    {
        if (!$this->categories->find($categoryId)) {
            return Result::fail('Please choose a valid category.', 422);
        }

        try {
            $id = $this->posts->create($userId, $title, $content, $categoryId);
        } catch (Exception) {
            return Result::fail('Could not create the post. Title may already be used.', 500);
        }

        return Result::ok('Post published.', 200, $id);
    }

    public function update(
        PostId $id,
        UserId $userId,
        PostTitle $title,
        PostContent $content,
        CategoryId $categoryId,
    ): Result {
        $post = $this->posts->find($id);

        if (!$post) {
            return Result::fail('Post not found.', 404);
        }

        if (!$post->isOwnedBy($userId)) {
            return Result::fail('You can only edit your own posts.', 403);
        }

        if (!$this->categories->find($categoryId)) {
            return Result::fail('Please choose a valid category.', 422);
        }

        try {
            $this->posts->update($id, $title, $content, $categoryId);
        } catch (Exception) {
            return Result::fail('Could not update the post. Title may already be used.', 500);
        }

        return Result::ok('Post updated.', 200, $id);
    }

    public function delete(PostId $id, UserId $userId): Result
    {
        $post = $this->posts->find($id);

        if (!$post) {
            return Result::fail('Post not found.', 404);
        }

        if (!$post->isOwnedBy($userId)) {
            return Result::fail('You can only delete your own posts.', 403);
        }

        $this->posts->delete($id);

        return Result::ok('Post deleted.');
    }
}
