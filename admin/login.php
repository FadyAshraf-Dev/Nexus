<?php
require_once '../config/database.php';

if (isset($_SESSION["userId"])) {
    // Traffic control check: Where should an already logged-in user go?

    if ($_SESSION["userRole"] == 3 || $_SESSION["userRole"] == 2) {
        // They are an admin/vendor, kick them straight into the admin dashboard
        redirect("index.php"); 
    } else {
        // They are a customer, bounce them back out to the public storefront
        redirect("../index.php");
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
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Login - SB Admin Pro</title>
        <link href="css/styles.css" rel="stylesheet" />
        <link rel="icon" type="image/x-icon" href="assets/img/favicon.png" />
        <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-primary">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container-xl px-4">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header justify-content-center"><h3 class="fw-light my-2">Login</h3></div>
                                    <div class="card-body">
                                        
                                        <?php if (!empty($errorMessage)): ?>
                                            <?php 
                                                // Determine background color scheme context based on the parameter type
                                                $alertClass = ($_GET['error'] === 'logged_out') ? 'alert-success' : 'alert-danger';
                                            ?>
                                            <div class="alert <?php echo $alertClass; ?> alert-dismissible fade show" role="alert">
                                                <?php echo $errorMessage; ?>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                        <?php endif; ?>

                                        <form role="form" id="loginForm" action="src/auth/check.php" method="post" novalidate>
                                            
                                            <div class="mb-3">
                                                <label class="small mb-1" for="inputEmailAddress">Email</label>
                                                <input class="form-control" id="inputEmailAddress" name="email" type="email" placeholder="Enter email address" required />
                                                <div class="invalid-feedback" id="emailFeedback">
                                                    Please enter a valid email address.
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                            <label class="small mb-1" for="inputPassword">Password</label>
                                            <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Enter password" required />
                                            
                                            <div class="invalid-feedback" id="passwordFeedback">
                                                Password must be at least 8 characters long.
                                            </div>
                                        </div>                                           
                                            
                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" id="rememberPasswordCheck" type="checkbox" value="" />
                                                    <label class="form-check-label" name="isRemembered" for="rememberPasswordCheck">Remember password</label>
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <a class="small" href="auth-password-basic.html">Forgot Password?</a>
                                                <button type="submit" class="btn btn-primary">Login</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer text-center">
                                        <div class="small"><a href="auth-register-basic.html">Need an account? Sign up!</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            <div id="layoutAuthentication_footer">
                <footer class="footer-admin mt-auto footer-dark">
                    <div class="container-xl px-4">
                        <div class="row">
                            <div class="col-md-6 small">Copyright &copy; Your Website 2026</div>
                            <div class="col-md-6 text-md-end small">
                                <a href="#!">Privacy Policy</a>
                                &middot;
                                <a href="#!">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="js/login-validation.js"></script>
    </body
</html>