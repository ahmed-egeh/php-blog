<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Http\Request;
use App\Services\AuthService;
use App\Services\PostService;
use App\ValueObjects\CategoryId;
use App\ValueObjects\InvalidValue;
use App\ValueObjects\PostContent;
use App\ValueObjects\PostId;
use App\ValueObjects\PostTitle;
use App\ViewModels\PostFormPage;
use App\ViewModels\PostShowPage;
use App\ViewModels\PostsIndexPage;

class PostsController extends Controller
{
    public function __construct(
        private PostService $posts = new PostService(),
    ) {}

    public function index(): void
    {
        $this->view('posts/index', new PostsIndexPage(
            title: 'Posts | Space Blog',
            heading: 'Posts',
            posts: $this->posts->listPublished(),
        ));
    }

    public function mine(): void
    {
        $this->view('posts/index', new PostsIndexPage(
            title: 'My posts | Space Blog',
            heading: 'My posts',
            posts: $this->posts->listMine(AuthService::requireUserId()),
        ));
    }

    public function create(): void
    {
        $this->view('posts/form', new PostFormPage(
            title: 'New post | Space Blog',
            heading: 'Write a post',
            action: '/posts',
            post: null,
            categories: $this->posts->categories(),
        ));
    }

    public function store(): void
    {
        try {
            $title = new PostTitle(Request::string('title'));
            $content = new PostContent(Request::string('content'));
            $categoryId = new CategoryId(Request::int('category_id'));
        } catch (InvalidValue $e) {
            $this->flash($e->getMessage());
            $this->redirect('/posts/create');
        }

        $result = $this->posts->create(
            AuthService::requireUserId(),
            $title,
            $content,
            $categoryId
        );

        $this->flash($result->message);

        if (!$result->success) {
            $this->redirect('/posts/create');
        }

        $this->redirect('/posts/' . $result->id);
    }

    public function show(string $id): void
    {
        try {
            $postId = new PostId((int) $id);
        } catch (InvalidValue) {
            http_response_code(404);
            echo 'Post not found.';
            return;
        }

        $post = $this->posts->find($postId);

        if (!$post) {
            http_response_code(404);
            echo 'Post not found.';
            return;
        }

        $userId = AuthService::loggedInUserId();
        $isOwner = $userId !== null && $post->isOwnedBy($userId);

        if (!$post->active && !$isOwner) {
            http_response_code(404);
            echo 'Post not found.';
            return;
        }

        $this->view('posts/show', new PostShowPage(
            title: $post->title->value . ' | Space Blog',
            post: $post,
            isOwner: $isOwner,
        ));
    }

    public function edit(string $id): void
    {
        try {
            $postId = new PostId((int) $id);
        } catch (InvalidValue) {
            $this->flash('You can only edit your own posts.');
            $this->redirect('/posts');
        }

        $post = $this->posts->find($postId);
        $userId = AuthService::loggedInUserId();

        if (!$post || $userId === null || !$post->isOwnedBy($userId)) {
            $this->flash('You can only edit your own posts.');
            $this->redirect('/posts');
        }

        $this->view('posts/form', new PostFormPage(
            title: 'Edit post | Space Blog',
            heading: 'Edit post',
            action: '/posts/' . $post->id->value,
            post: $post,
            categories: $this->posts->categories(),
        ));
    }

    public function update(string $id): void
    {
        try {
            $postId = new PostId((int) $id);
            $title = new PostTitle(Request::string('title'));
            $content = new PostContent(Request::string('content'));
            $categoryId = new CategoryId(Request::int('category_id'));
        } catch (InvalidValue $e) {
            $this->flash($e->getMessage());
            $this->redirect('/posts/' . (int) $id . '/edit');
        }

        $result = $this->posts->update(
            $postId,
            AuthService::requireUserId(),
            $title,
            $content,
            $categoryId
        );

        $this->flash($result->message);

        if (!$result->success) {
            $this->redirect('/posts');
        }

        $this->redirect('/posts/' . $postId->value);
    }

    public function destroy(string $id): void
    {
        try {
            $postId = new PostId((int) $id);
        } catch (InvalidValue $e) {
            $this->flash($e->getMessage());
            $this->redirect('/posts');
        }

        $result = $this->posts->delete($postId, AuthService::requireUserId());
        $this->flash($result->message);
        $this->redirect('/posts');
    }
}
