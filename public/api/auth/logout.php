<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/bootstrap/bootstrap.php';

// Where to send the user after logout depends on who they were -
// an admin/vendor belongs back at the admin login, a customer
// belongs back at the storefront login. Read the role BEFORE
// logout() destroys the session, or it's gone.
$wasAdminOrVendor = in_array(
    Session::roleId(),
    [Role::ADMIN, Role::VENDOR],
    true
);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $wasAdminOrVendor
        ? Response::redirectAdmin('login.php')
        : Response::redirectBase('login.php');
}

CSRF::verify();

$pdo = Database::connection();
$authentication = new AuthenticationService($pdo);

$authentication->logout(
    Session::id()
);

Flash::success('Logged Out Successfully.');

$wasAdminOrVendor
    ? Response::redirectAdmin('login.php')
    : Response::redirectBase('login.php');