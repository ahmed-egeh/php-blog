<?php
declare(strict_types=1);

session_start();

require_once dirname(__DIR__) . '/autoload.php';


use App\Controllers\HomeController;
use App\Router;

$router = new Router();

$router->get('/', [HomeController::class, 'index']);
$router->get('/login', [HomeController::class, 'login']);


$uri = parse_url(
    $_SERVER['REQUEST_URI'],
    PHP_URL_PATH
);

$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($method, $uri);