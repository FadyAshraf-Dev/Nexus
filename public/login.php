<!doctype html>
<html lang="zxx">
<?php include 'includes/head.php'; ?>

<body>
  <!-- Page Preloder -->
  <div id="preloder">
    <div class="loader"></div>
  </div>

  <!-- Offcanvas Menu Begin -->
  <?php include_once 'includes/topbar.php' ?>
  <!-- Offcanvas Menu End -->

  <!-- Header Section Begin -->
  <?php include_once 'includes/header.php' ?>

  <!-- Header Section End -->

  <!-- Login Section Begin -->
  <section class="login spad">
    <div class="container">
      <div class="login__wrapper">
        <!-- Left Side -->
        <div class="login__content">
          <div class="login__header">
            <span class="login__subtitle"> Welcome back! </span>

            <h2>
              Glad to see you,<br />
              again.
            </h2>

            <p>
              Sign in to continue shopping across thousands of products from
              trusted vendors.
            </p>
          </div>

          <form action="#" method="post" class="login__form">
            <div class="login__group">
              <label for="email"> Email Address </label>

              <div class="login__input">
                <span class="icon_mail_alt"></span>

                <input type="email" id="email" name="email" placeholder="Enter your email" required />
              </div>
            </div>

            <div class="login__group">
              <label for="password"> Password </label>

              <div class="login__input">
                <span class="icon_lock"></span>

                <input type="password" id="password" name="password" placeholder="Enter your password" required />

                <button type="button" class="login__toggle-password">
                  <i class="fa fa-eye"></i>
                </button>
              </div>
            </div>

            <div class="login__options">
              <label class="login__remember">
                <input type="checkbox" name="remember_me" />

                <span></span>

                Remember me
              </label>

              <a href="#"> Forgot password? </a>
            </div>

            <button type="submit" class="site-btn login__button">
              Sign In
            </button>
          </form>

          <div class="login__divider">
            <span>OR</span>
          </div>

          <div class="login__footer">
            <p>Don't have an account?</p>

            <a href="register.html"> Create one </a>
          </div>
        </div>

        <!-- Right Side -->
        <div class="login__illustration">
          <img src="img/login/login-illustration.png" alt="Nexus Login Illustration" />
        </div>
      </div>
    </div>
  </section>
  <!-- Login Section End -->

  <!-- Footer Section Begin -->
  <?php include 'includes/footer.php'; ?>

  <!-- Footer Section End -->

  <!-- Search Begin -->
  <?php include 'includes/search.php'; ?>
  <!-- Search End -->

  <!-- Js Plugins -->
  <?php include 'includes/scripts.php'; ?>

</body>

</html>