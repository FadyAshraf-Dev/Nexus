<?php
declare(strict_types=1);
// admin/src/auth/check.php
require_once dirname(__DIR__, 4) . '/bootstrap/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::redirectAdmin("login.php");
}
CSRF::verify();


// Grab the inputs (PDO handles SQL security, but trimming whitespace is still good practice)
$validator = (new Validator($_POST))->validate([
    'email' => 'required|email|max_len:100',
    'password' => 'required|min_len:7|max_len:100',
]);

if ($validator->fails()) {
    Flash::error("Please Enter The Required Fields");

    Response::redirectAdmin('login.php');
}

$data = $validator->validated();

$email = $data['email'];
$password = $data['password'];
try {

    $pdo = Database::connection();
    // 2. Prepare the SQL blueprint with a safe named placeholder (:email)
    $sql = "SELECT id, role_id, password, email FROM users WHERE email = :email LIMIT 1";
    $statement = $pdo->prepare($sql);

    // 3. Execute the statement by passing the data securely bound to the placeholder
    $statement->execute([':email' => $email]);

    // 4. Fetch the resulting row as an associative array
    $user = $statement->fetch();

    // 5. Check if a user was found and verify their credentials
    // (If you are using plain text passwords for now, keep this. If using password_hash, use password_verify)
    if (!$user || $password !== $user['password']) {
        Flash::error("Invalid email or password.");

        Response::redirectAdmin("login.php");
    }
    Session::login($user);  
    if (
        in_array(
            $user['role_id'],
            [Role::ADMIN, Role::VENDOR],
            true
        )
    ) {
        // Authorized Dashboard operators (Admin / Vendor)
        Response::redirectAdmin("index.php");
    } else {
        // Normal consumer client identity -> route to general public catalog
        Response::redirectBase("index.php");
    }


} catch (Throwable $e) {

    error_log($e);
    Flash::success("Servers Are Currently Down.");
    Response::redirectAdmin('login.php');
}

