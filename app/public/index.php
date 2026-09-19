<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/autoload.php';


use App\Controllers\HomeController;
use App\Router;

$router = new Router();

$router->get('/', [HomeController::class, 'index']);


$uri = parse_url(
    $_SERVER['REQUEST_URI'],
    PHP_URL_PATH
);

$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($method, $uri);