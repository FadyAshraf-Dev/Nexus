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

  <!-- Activation Section Begin -->
  <section class="login spad">
    <div class="container">
      <div class="login__wrapper">
        <div class="login__content">

          <?php if ($token === ''): ?>

            <div class="login__header">
              <h2>Invalid activation link.</h2>
              <p>This link is missing its token. Please check the link in your email, or sign up again.</p>
            </div>

            <div class="login__footer">
              <a href="register.php"> Back to sign up </a>
            </div>

          <?php else: ?>

            <div class="login__header">
              <span class="login__subtitle"> Almost there </span>

              <h2 id="activate-heading" data-token="<?= htmlspecialchars($token, ENT_QUOTES) ?>">
                Activating your<br />
                account...
              </h2>

              <p id="activate-message">Please wait a moment.</p>
            </div>

            <div class="login__footer" id="activate-footer" style="display:none;">
              <a href="login.php"> Go to sign in </a>
            </div>

          <?php endif; ?>

        </div>

        <div class="login__illustration">
          <img src="img/login/login-illustration.png" alt="Nexus Login Illustration" />
        </div>
      </div>
    </div>
  </section>
  <!-- Activation Section End -->

  <?php include 'includes/footer.php'; ?>

  <?php include 'includes/search.php'; ?>

  <?php include 'includes/scripts.php'; ?>

  <?php if ($token !== ''): ?>
  <script type="module" src="/admin/js/pages/storefront-activate/Activate.js"></script>
  <?php endif; ?>

</body>

</html>
