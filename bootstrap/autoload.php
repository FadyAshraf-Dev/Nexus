<?php

declare(strict_types=1);

spl_autoload_register(function (string $class): void {

    $directories = [
        ROOT_PATH . '/app/Authentication/',
        ROOT_PATH . '/app/Cart/',
        ROOT_PATH . '/app/Categories/',
        ROOT_PATH . '/app/Core/',
        ROOT_PATH . '/app/Logging/',
        ROOT_PATH . '/app/Products/',
        ROOT_PATH . '/app/Security/',
        ROOT_PATH . '/app/Users/',
        ROOT_PATH . '/app/Helpers/',
        ROOT_PATH . '/app/Order/',
    ];
    foreach ($directories as $directory) {

        $file = $directory . $class . '.php';

        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});