<?php

declare(strict_types=1);

spl_autoload_register(function (string $class): void {

    $prefix = 'App\\';

    $baseDirectory = __DIR__ . '/src/';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr(
        $class,
        strlen($prefix)
    );

    $relativePath = str_replace(
        '\\',
        DIRECTORY_SEPARATOR,
        $relativeClass
    );

    $file = $baseDirectory . $relativePath . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});