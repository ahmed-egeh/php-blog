<?php
declare(strict_types=1);

namespace App\Domain\Post;

use App\Domain\Category\CategoryId;
use App\Domain\User\UserId;

interface PostRepository
{
    public function allPublished(): PostCollection;

    public function featuredForHome(int $limit = 10): PostCollection;

    public function allByUser(UserId $userId): PostCollection;

    public function find(PostId $id): ?Post;

    public function create(
        UserId $userId,
        PostTitle $title,
        PostContent $content,
        CategoryId $categoryId,
    ): PostId;

    public function update(PostId $id, PostTitle $title, PostContent $content, CategoryId $categoryId): bool;

    public function delete(PostId $id): bool;
}
