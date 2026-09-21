<?php
declare(strict_types=1);

session_start();

require_once dirname(__DIR__) . '/autoload.php';


use App\Controllers\HomeController;
use App\Controllers\PlaygroundController;
use App\Controllers\UsersController;
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

$router->get('/playground', [PlaygroundController::class, 'index']);



$uri = rtrim((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
if ($uri === '') {
    $uri = '/';
}

$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($method, $uri);