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

  <!-- Register Section Begin -->
  <section class="login spad">
    <div class="container">
      <div class="login__wrapper">
        <div class="login__content">
          <div class="login__header">
            <span class="login__subtitle"> Join Nexus </span>

            <h2>
              Create your<br />
              account.
            </h2>

            <p>
              Sign up to start shopping across thousands of products from
              trusted vendors.
            </p>
          </div>

          <div id="register-feedback"></div>

          <form id="register-form" action="api/auth/register.php" method="post" class="login__form">
            <div class="login__group">
              <label for="username"> Username </label>

              <div class="login__input">
                <span class="icon_profile"></span>

                <input type="text" id="username" name="username" placeholder="Choose a username" required
                  minlength="3" maxlength="50" pattern="[a-zA-Z0-9_]+" />
              </div>
              <div class="login__field-error" data-error-for="username"></div>
            </div>

            <div class="login__group">
              <label for="email"> Email Address </label>

              <div class="login__input">
                <span class="icon_mail_alt"></span>

                <input type="email" id="email" name="email" placeholder="Enter your email" required />
              </div>
              <div class="login__field-error" data-error-for="email"></div>
            </div>

            <div class="login__group">
              <label for="password"> Password </label>

              <div class="login__input">
                <span class="icon_lock"></span>

                <input type="password" id="password" name="password" placeholder="Create a password" required
                  minlength="8" />

                <button type="button" class="login__toggle-password">
                  <i class="fa fa-eye"></i>
                </button>
              </div>
              <div class="login__field-error" data-error-for="password"></div>
            </div>

            <div class="login__group">
              <label for="password_confirmation"> Confirm Password </label>

              <div class="login__input">
                <span class="icon_lock"></span>

                <input type="password" id="password_confirmation" name="password_confirmation"
                  placeholder="Re-enter your password" required minlength="8" />
              </div>
              <div class="login__field-error" data-error-for="password_confirmation"></div>
            </div>

            <button type="submit" class="site-btn login__button">
              Create Account
            </button>
          </form>

          <div class="login__divider">
            <span>OR</span>
          </div>

          <div class="login__footer">
            <p>Already have an account?</p>

            <a href="login.php"> Sign in </a>
          </div>
        </div>

        <div class="login__illustration">
          <img src="img/login/login-illustration.png" alt="Nexus Login Illustration" />
        </div>
      </div>
    </div>
  </section>
  <!-- Register Section End -->

  <?php include 'includes/footer.php'; ?>

  <?php include 'includes/search.php'; ?>

  <?php include 'includes/scripts.php'; ?>
  <script type="module" src="/admin/js/pages/storefront-register/Register.js"></script>

</body>

</html>
