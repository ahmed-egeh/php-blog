<?php
declare(strict_types=1);

session_start();

require_once dirname(__DIR__) . '/autoload.php';

use App\Application\User\UserService;
use App\Controllers\HomeController;
use App\Controllers\PlaygroundController;
use App\Controllers\PostsController;
use App\Controllers\ProfileController;
use App\Controllers\UsersController;
use App\Http\Request;
use App\Infrastructure\Container;
use App\Middleware\AuthMiddleware;
use App\Router;

$container = Container::boot();
$container->get(UserService::class)->resumeRememberedSession();

$router = new Router($container);

$router->get('/', HomeController::class, 'index');
$router->get('/about', HomeController::class, 'about');
$router->get('/login', HomeController::class, 'login');
$router->get('/signup', HomeController::class, 'signup');
$router->post('/user/signup', UsersController::class, 'signup');
$router->get('/user/activate', UsersController::class, 'activate');
$router->post('/user/login', UsersController::class, 'login');
$router->get('/user/logout', UsersController::class, 'logout');

$router->get('/profile', ProfileController::class, 'show', AuthMiddleware::class);
$router->post('/profile', ProfileController::class, 'update', AuthMiddleware::class);
$router->post('/profile/password', ProfileController::class, 'updatePassword', AuthMiddleware::class);
$router->post('/profile/delete', ProfileController::class, 'destroy', AuthMiddleware::class);
$router->get('/posts', PostsController::class, 'index');
$router->get('/posts/mine', PostsController::class, 'mine', AuthMiddleware::class);
$router->get('/posts/create', PostsController::class, 'create', AuthMiddleware::class);
$router->post('/posts', PostsController::class, 'store', AuthMiddleware::class);
$router->get('/posts/{id}/edit', PostsController::class, 'edit', AuthMiddleware::class);
$router->post('/posts/{id}/delete', PostsController::class, 'destroy', AuthMiddleware::class);
$router->get('/posts/{id}', PostsController::class, 'show');
$router->post('/posts/{id}', PostsController::class, 'update', AuthMiddleware::class);

$router->get('/playground', PlaygroundController::class, 'index');

$router->dispatch(Request::method(), Request::path());
