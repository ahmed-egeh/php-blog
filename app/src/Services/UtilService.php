<?php
namespace App\Services;

class UtilService {
    public static function isCurrentRoute(string $route): bool {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === $route;
    }
}