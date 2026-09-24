<?php
declare(strict_types=1);

session_start();

require_once dirname(__DIR__) . '/autoload.php';

(new \App\Services\UserService())->resumeRememberedSession();


use App\Controllers\HomeController;
use App\Controllers\PlaygroundController;
use App\Controllers\PostsController;
use App\Controllers\ProfileController;
use App\Controllers\UsersController;
use App\Middleware\AuthMiddleware;
use App\Router;

$router = new Router();

$router->get('/', [HomeController::class, 'index']);
$router->get('/about', [HomeController::class, 'about']);
$router->get('/login', [HomeController::class, 'login']);
$router->get('/signup', [HomeController::class, 'signup']);
$router->post('/user/signup', [UsersController::class, 'signup']);
$router->get('/user/activate', [UsersController::class, 'activate']);
$router->post('/user/login', [UsersController::class, 'login']);
$router->get('/user/logout', [UsersController::class, 'logout']);

$auth = [AuthMiddleware::class];
$router->get('/profile', [ProfileController::class, 'show'], $auth);
$router->post('/profile', [ProfileController::class, 'update'], $auth);
$router->post('/profile/password', [ProfileController::class, 'updatePassword'], $auth);
$router->post('/profile/delete', [ProfileController::class, 'destroy'], $auth);
$router->get('/posts', [PostsController::class, 'index']);
$router->get('/posts/mine', [PostsController::class, 'mine'], $auth);
$router->get('/posts/create', [PostsController::class, 'create'], $auth);
$router->post('/posts', [PostsController::class, 'store'], $auth);
$router->get('/posts/{id}/edit', [PostsController::class, 'edit'], $auth);
$router->post('/posts/{id}/delete', [PostsController::class, 'destroy'], $auth);
$router->get('/posts/{id}', [PostsController::class, 'show']);
$router->post('/posts/{id}', [PostsController::class, 'update'], $auth);

$router->get('/playground', [PlaygroundController::class, 'index']);



$uri = rtrim((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
if ($uri === '') {
    $uri = '/';
}

$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($method, $uri);