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
    Response::redirectAdmin('login.php?error=empty_fields');
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
        Response::redirectAdmin("login.php?error=failed_login");
    }
    // 🛡️ Erases the old session ID file on the server and issues a brand new random ID token to the browser
    session_regenerate_id(true);
    // Save authorization identity properties to active session arrays (exclude the password!)
    $_SESSION["user"] = [
        'id' => $user['id'],
        'email' => $user['email'],
        // 'first_name' => $user['first_name'],
        // 'last_name'  => $user['last_name'],
        'role_id' => $user['role_id']
    ];

    // Traffic Control Routing Layer
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

    // error_log($e);

    // Response::redirectAdmin(
    //     'login.php?error=server_error'
    // );
    die($e);
}

