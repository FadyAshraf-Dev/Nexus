<?php

declare(strict_types=1);

require_once dirname(__DIR__, 4) . '/bootstrap/bootstrap.php';

    $pdo = Database::connection();
    $authentication = new AuthenticationService($pdo);

$authentication->logout(
    Session::id()
);

Flash::success('Logged Out Successfully.');

Response::redirectAdmin('login.php');