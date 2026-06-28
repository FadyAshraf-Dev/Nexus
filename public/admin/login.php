<?php
declare(strict_types=1);
// ini_set('display_errors', 1);
// error_reporting(E_ALL);
require_once dirname(__DIR__, 2) . '/bootstrap/bootstrap.php';
if (isset($_SESSION["user"])) {
    // Traffic control check: Where should an already logged-in user go?

    $userRole = $_SESSION["user"]["role_id"];
    if ($userRole == 3 || $userRole == 2) {
        // They are an admin/vendor, kick them straight into the admin dashboard
        Response::redirectAdmin("index.php");
    } else {
        // They are a customer, bounce them back out to the public storefront
        Response::redirectBase("index.php");
    }
}

// Map query parameter strings to custom readable alert messages
$errorMessage = "";
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'failed_login':
            $errorMessage = "<strong>Invalid Credentials!</strong> The email or password you entered is incorrect. Please try again.";
            break;
        case 'empty_fields':
            $errorMessage = "<strong>Missing Information!</strong> Please fill in both the email and password fields.";
            break;
        case 'logged_out':
            $errorMessage = "You have been successfully logged out.";
            break;
        default:
            $errorMessage = "An unexpected error occurred. Please try logging in again.";
            break;
    }
}
require_once 'includes/head.php';

?>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container-xl px-4">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header justify-content-center">
                                    <h3 class="fw-light my-2">Login</h3>
                                </div>
                                <div class="card-body">

                                    <?php if (!empty($errorMessage)): ?>
                                        <?php
                                        // Determine background color scheme context based on the parameter type
                                        $alertClass = ($_GET['error'] === 'logged_out') ? 'alert-success' : 'alert-danger';
                                        ?>
                                        <div class="alert <?php echo $alertClass; ?> alert-dismissible fade show"
                                            role="alert">
                                            <?php echo $errorMessage; ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>

                                    <form role="form" id="loginForm" action="src/auth/check.php" method="post">
                                        <input type="hidden" name="csrf_token" value="<?= CSRF::token(); ?>">

                                        <div class="mb-3">
                                            <label class="small mb-1" for="inputEmailAddress">Email</label>
                                            <input class="form-control" id="inputEmailAddress" name="email" type="email"
                                                placeholder="Enter email address" autocomplete="email" required
                                                maxlength="100" spellcheck="false" autocapitalize="none" />
                                            <div class="invalid-feedback" id="emailFeedback">
                                                Please enter a valid email address.
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="small mb-1" for="inputPassword">Password</label>
                                            <input class="form-control" id="inputPassword" name="password"
                                                type="password" placeholder="Enter password"
                                                autocomplete="current-password" required minlength="7"
                                                maxlength="100" />

                                            <div class="invalid-feedback" id="passwordFeedback">
                                                Password must be at least 8 characters long.
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" id="rememberPasswordCheck"
                                                    type="checkbox" value="1" />
                                                <label class="form-check-label" name="isRemembered"
                                                    for="rememberPasswordCheck">Remember password</label>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <a class="small" href="auth-password-basic.html">Forgot Password?</a>
                                            <button type="submit" class="btn btn-primary">Login</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center">
                                    <div class="small"><a href="auth-register-basic.html">Need an account? Sign up!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <?php require_once 'includes/footer.php'; ?>
        </div>
    </div>

    <?php require_once 'includes/scripts.php'; ?>
    <script>

        document.addEventListener("DOMContentLoaded", function () {

            initializeFormValidation(
                document.getElementById("loginForm")
            );

        });

    </script>
</body>

</html>