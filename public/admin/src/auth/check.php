<?php

declare(strict_types=1);
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once dirname(__DIR__, 4) . '/bootstrap/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::redirectAdmin('login.php');
}

CSRF::verify();

$validator = (new Validator($_POST))->validate([
    'email'    => 'required|email|max_len:100',
    'password' => 'required|min_len:7|max_len:100',
]);

if ($validator->fails()) {
    Flash::error('Please enter the required fields.');
    Response::redirectAdmin('login.php');
}

$data = $validator->validated();

try {
    $pdo = Database::connection();
    
    $authentication = new AuthenticationService($pdo);

    $user = $authentication->login(
        $data['email'],
        $data['password'],
        isset($_POST['remember_me'])
    );

    
    if (
        in_array(
            $user['role_id'],
            [Role::ADMIN, Role::VENDOR],
            true
            )
            ) {
        Flash::success('Logged In Successfully');
        Response::redirectAdmin('index.php');
    }

    Response::redirectBase('index.php');

} catch (Throwable $e) {

    error_log($e->getMessage());

    Flash::error($e->getMessage());

    Response::redirectAdmin('login.php');
}