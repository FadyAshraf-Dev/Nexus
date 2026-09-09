<?php

declare(strict_types=1);

// Composer-managed packages (currently just fadyashraf-dev/nexus-support,
// which app/Helpers/Config.php and app/Core/Database.php delegate to).
// Composer itself is only needed for this - the rest of the app still
// uses the flat-directory autoloader below, unchanged.
if (is_file(ROOT_PATH . '/vendor/autoload.php')) {
    require_once ROOT_PATH . '/vendor/autoload.php';
}

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
        ROOT_PATH . '/app/Coupon/',
        ROOT_PATH . '/app/Notification/',
        ROOT_PATH . '/app/Review/',
        ROOT_PATH . '/app/Verification/',
        ROOT_PATH . '/app/VendorRequests/',
    ];
    foreach ($directories as $directory) {

        $file = $directory . $class . '.php';

        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});
