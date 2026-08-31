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

$token = (string) ($_GET['token'] ?? '');

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

  <!-- Reset Password Section Begin -->
  <section class="login spad">
    <div class="container">
      <div class="login__wrapper">
        <div class="login__content">

          <?php if ($token === ''): ?>

            <div class="login__header">
              <h2>Invalid reset link.</h2>
              <p>This link is missing its token. Please request a new one.</p>
            </div>

            <div class="login__footer">
              <a href="forgot-password.php"> Request a new link </a>
            </div>

          <?php else: ?>

            <div class="login__header">
              <span class="login__subtitle"> Almost done </span>

              <h2>
                Choose a new<br />
                password.
              </h2>
            </div>

            <div id="reset-password-feedback"></div>

            <form id="reset-password-form" class="login__form">
              <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES) ?>">

              <div class="login__group">
                <label for="password"> New Password </label>

                <div class="login__input">
                  <span class="icon_lock"></span>

                  <input type="password" id="password" name="password" placeholder="Enter new password" required
                    minlength="8" />
                </div>
              </div>

              <div class="login__group">
                <label for="password_confirmation"> Confirm New Password </label>

                <div class="login__input">
                  <span class="icon_lock"></span>

                  <input type="password" id="password_confirmation" name="password_confirmation"
                    placeholder="Re-enter new password" required minlength="8" />
                </div>
              </div>

              <button type="submit" class="site-btn login__button">
                Reset Password
              </button>
            </form>

          <?php endif; ?>

        </div>

        <div class="login__illustration">
          <img src="img/login/login-illustration.png" alt="Nexus Login Illustration" />
        </div>
      </div>
    </div>
  </section>
  <!-- Reset Password Section End -->

  <?php include 'includes/footer.php'; ?>

  <?php include 'includes/search.php'; ?>

  <?php include 'includes/scripts.php'; ?>

  <?php if ($token !== ''): ?>
  <script type="module" src="/admin/js/pages/storefront-reset-password/ResetPassword.js"></script>
  <?php endif; ?>

</body>

</html>
