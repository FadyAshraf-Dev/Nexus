<?php

declare(strict_types=1);

spl_autoload_register(function (string $class): void {

    $directories = [
        ROOT_PATH . '/app/Infrastructure/',
        ROOT_PATH . '/app/Security/',
        ROOT_PATH . '/app/Session/',
        ROOT_PATH . '/app/Helpers/',
        ROOT_PATH . '/app/Validation/',
        ROOT_PATH . '/app/Repositories/',
        ROOT_PATH . '/app/Repositories/contracts/',
        ROOT_PATH . '/app/Services/',
        ROOT_PATH . '/app/Logging/',
    ];

    foreach ($directories as $directory) {

        $file = $directory . $class . '.php';

        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});