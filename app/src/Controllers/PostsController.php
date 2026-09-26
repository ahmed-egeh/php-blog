<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Application\Port\CurrentUser;
use App\Application\Post\PostService;
use App\Core\Controller;
use App\Domain\Category\CategoryId;
use App\Domain\Post\PostContent;
use App\Domain\Post\PostId;
use App\Domain\Post\PostTitle;
use App\Domain\Shared\InvalidValue;
use App\Http\Request;
use App\Http\Response;
use App\ViewModels\Pagination;
use App\ViewModels\PostFormPage;
use App\ViewModels\PostShowPage;
use App\ViewModels\PostsIndexPage;

class PostsController extends Controller
{
    public function __construct(
        private PostService $posts,
        CurrentUser $currentUser,
    ) {
        parent::__construct($currentUser);
    }

    public function index(): Response
    {
        $rawPage = Request::query('page');
        $pageNumber = ctype_digit($rawPage) ? (int) $rawPage : 1;

        $paged = $this->posts->listPublished($pageNumber);

        return $this->view('posts/index', new PostsIndexPage(
            title: 'Posts | Space Blog',
            posts: $paged->posts,
            pagination: new Pagination($paged->page, $paged->lastPage()),
        ));
    }

    public function mine(): Response
    {
        return $this->view('posts/index', new PostsIndexPage(
            title: 'My posts | Space Blog',
            posts: $this->posts->listMine($this->currentUser->requireId()),
        ));
    }

    public function create(): Response
    {
        return $this->view('posts/form', new PostFormPage(
            title: 'New post | Space Blog',
            heading: 'Write a post',
            action: '/posts',
            post: null,
            categories: $this->posts->categories(),
        ));
    }

    public function store(): Response
    {
        try {
            $title = new PostTitle(Request::string('title'));
            $content = new PostContent(Request::string('content'));
            $categoryId = new CategoryId(Request::int('category_id'));
        } catch (InvalidValue $e) {
            $this->flash($e->getMessage());
            return $this->redirect('/posts/create');
        }

        $result = $this->posts->publish(
            $this->currentUser->requireId(),
            $title,
            $content,
            $categoryId
        );

        $this->flash($result->message);

        if (!$result->success || $result->id === null) {
            return $this->redirect('/posts/create');
        }

        return $this->redirect('/posts/' . $result->id->value);
    }

    public function show(string $id): Response
    {
        try {
            $postId = new PostId((int) $id);
        } catch (InvalidValue) {
            return $this->text('Post not found.', 404);
        }

        $post = $this->posts->find($postId);

        if (!$post) {
            return $this->text('Post not found.', 404);
        }

        $userId = $this->currentUser->id();
        $isOwner = $userId !== null && $post->isOwnedBy($userId);

        if (!$post->isPublic() && !$isOwner) {
            return $this->text('Post not found.', 404);
        }

        return $this->view('posts/show', new PostShowPage(
            title: $post->title->value . ' | Space Blog',
            post: $post,
            isOwner: $isOwner,
        ));
    }

    public function edit(string $id): Response
    {
        try {
            $postId = new PostId((int) $id);
        } catch (InvalidValue) {
            $this->flash('You can only edit your own posts.');
            return $this->redirect('/posts');
        }

        $post = $this->posts->find($postId);
        $userId = $this->currentUser->id();

        if (!$post || $userId === null || !$post->isOwnedBy($userId)) {
            $this->flash('You can only edit your own posts.');
            return $this->redirect('/posts');
        }

        return $this->view('posts/form', new PostFormPage(
            title: 'Edit post | Space Blog',
            heading: 'Edit post',
            action: '/posts/' . $post->id->value,
            post: $post,
            categories: $this->posts->categories(),
        ));
    }

    public function update(string $id): Response
    {
        try {
            $postId = new PostId((int) $id);
            $title = new PostTitle(Request::string('title'));
            $content = new PostContent(Request::string('content'));
            $categoryId = new CategoryId(Request::int('category_id'));
        } catch (InvalidValue $e) {
            $this->flash($e->getMessage());
            return $this->redirect('/posts/' . (int) $id . '/edit');
        }

        $result = $this->posts->update(
            $postId,
            $this->currentUser->requireId(),
            $title,
            $content,
            $categoryId
        );

        $this->flash($result->message);

        if (!$result->success) {
            return $this->redirect('/posts');
        }

        return $this->redirect('/posts/' . $postId->value);
    }

    public function destroy(string $id): Response
    {
        try {
            $postId = new PostId((int) $id);
        } catch (InvalidValue $e) {
            $this->flash($e->getMessage());
            return $this->redirect('/posts');
        }

        $result = $this->posts->delete($postId, $this->currentUser->requireId());
        $this->flash($result->message);

        return $this->redirect('/posts');
    }
}
