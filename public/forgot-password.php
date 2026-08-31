<?php

declare(strict_types=1);
require_once dirname(__DIR__, 1) . '/bootstrap/bootstrap.php';

if (isset($_SESSION["user"])) {

    $userRole = $_SESSION["user"]["role_id"];
    if ($userRole == 3 || $userRole == 2) {
        Response::redirectAdmin("index.php");
    } else {
        Response::redirectBase("index.php");
    }
}

?>
<!DOCTYPE html>
<html lang="zxx">
<?php include 'includes/head.php'; ?>

<body>
  <div id="preloder">
    <div class="loader"></div>
  </div>

  <?php include_once 'includes/topbar.php' ?>

  <?php include_once 'includes/header.php' ?>

  <!-- Forgot Password Section Begin -->
  <section class="login spad">
    <div class="container">
      <div class="login__wrapper">
        <div class="login__content">
          <div class="login__header">
            <span class="login__subtitle"> Trouble signing in? </span>

            <h2>
              Reset your<br />
              password.
            </h2>

            <p>
              Enter the email address on your account and we'll send you a link to reset your password.
            </p>
          </div>

          <div id="forgot-password-feedback"></div>

          <form id="forgot-password-form" class="login__form">
            <div class="login__group">
              <label for="email"> Email Address </label>

              <div class="login__input">
                <span class="icon_mail_alt"></span>

                <input type="email" id="email" name="email" placeholder="Enter your email" required />
              </div>
            </div>

            <button type="submit" class="site-btn login__button">
              Send Reset Link
            </button>
          </form>

          <div class="login__footer">
            <p>Remembered it after all?</p>

            <a href="login.php"> Back to sign in </a>
          </div>
        </div>

        <div class="login__illustration">
          <img src="img/login/login-illustration.png" alt="Nexus Login Illustration" />
        </div>
      </div>
    </div>
  </section>
  <!-- Forgot Password Section End -->

  <?php include 'includes/footer.php'; ?>

  <?php include 'includes/search.php'; ?>

  <?php include 'includes/scripts.php'; ?>
  <script type="module" src="/admin/js/pages/storefront-forgot-password/ForgotPassword.js"></script>

</body>

</html>
