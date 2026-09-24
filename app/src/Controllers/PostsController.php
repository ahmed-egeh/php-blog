<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\PostService;

class PostsController extends Controller
{
    public function __construct(
        private PostService $posts = new PostService(),
    ) {}

    public function index(): void
    {
        $this->view('posts/index', [
            'title' => 'Posts | Space Blog',
            'heading' => 'Posts',
            'posts' => $this->posts->listPublished(),
            'mine' => false,
        ]);
    }

    public function mine(): void
    {
        $this->view('posts/index', [
            'title' => 'My posts | Space Blog',
            'heading' => 'My posts',
            'posts' => $this->posts->listMine((int) $_SESSION['user_id']),
            'mine' => true,
        ]);
    }

    public function create(): void
    {
        $this->view('posts/form', [
            'title' => 'New post | Space Blog',
            'heading' => 'Write a post',
            'action' => '/posts',
            'post' => [
                'title' => '',
                'content' => '',
                'category_id' => '',
            ],
            'categories' => $this->posts->categories(),
        ]);
    }

    public function store(): void
    {
        $title = trim((string) ($_POST['title'] ?? ''));
        $content = trim((string) ($_POST['content'] ?? ''));
        $categoryId = (int) ($_POST['category_id'] ?? 0);

        if ($title === '' || $content === '' || $categoryId < 1) {
            $this->flash('Title, content and category are required.');
            $this->redirect('/posts/create');
        }

        $result = $this->posts->create(
            (int) $_SESSION['user_id'],
            $title,
            $content,
            $categoryId
        );

        $this->flash($result['message']);

        if (!$result['success']) {
            $this->redirect('/posts/create');
        }

        $this->redirect('/posts/' . $result['id']);
    }

    public function show(string $id): void
    {
        $postId = (int) $id;
        $post = $this->posts->find($postId);

        if (!$post) {
            http_response_code(404);
            echo 'Post not found.';
            return;
        }

        $isOwner = isset($_SESSION['user_id']) && (int) $post['user_id'] === (int) $_SESSION['user_id'];

        if ((int) $post['active'] !== 1 && !$isOwner) {
            http_response_code(404);
            echo 'Post not found.';
            return;
        }

        $this->view('posts/show', [
            'title' => $post['title'] . ' | Space Blog',
            'post' => $post,
            'isOwner' => $isOwner,
        ]);
    }

    public function edit(string $id): void
    {
        $post = $this->posts->find((int) $id);

        if (!$post || (int) $post['user_id'] !== (int) $_SESSION['user_id']) {
            $this->flash('You can only edit your own posts.');
            $this->redirect('/posts');
        }

        $this->view('posts/form', [
            'title' => 'Edit post | Space Blog',
            'heading' => 'Edit post',
            'action' => '/posts/' . $post['id'],
            'post' => $post,
            'categories' => $this->posts->categories(),
        ]);
    }

    public function update(string $id): void
    {
        $postId = (int) $id;
        $title = trim((string) ($_POST['title'] ?? ''));
        $content = trim((string) ($_POST['content'] ?? ''));
        $categoryId = (int) ($_POST['category_id'] ?? 0);

        if ($title === '' || $content === '' || $categoryId < 1) {
            $this->flash('Title, content and category are required.');
            $this->redirect('/posts/' . $postId . '/edit');
        }

        $result = $this->posts->update(
            $postId,
            (int) $_SESSION['user_id'],
            $title,
            $content,
            $categoryId
        );

        $this->flash($result['message']);

        if (!$result['success']) {
            $this->redirect('/posts');
        }

        $this->redirect('/posts/' . $postId);
    }

    public function destroy(string $id): void
    {
        $result = $this->posts->delete((int) $id, (int) $_SESSION['user_id']);
        $this->flash($result['message']);
        $this->redirect('/posts');
    }
}
