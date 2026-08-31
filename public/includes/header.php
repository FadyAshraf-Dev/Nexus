<header class="header">
  <div class="container">
    <div class="header__content">

      <div class="header__logo">
        <a href="index.php">
          <span class="header__brand-title">NEXUS</span>
          <span class="header__brand-subtitle">Fashion Marketplace</span>
        </a>
      </div>

      <form class="header__search" id="header-search-form">

        <input type="text" id="global-search" placeholder="Search products...">

        <button type="submit">

          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2">

            <circle cx="11" cy="11" r="7"></circle>

            <line x1="21" y1="21" x2="16.65" y2="16.65">
            </line>

          </svg>

        </button>

      </form>

      <div class="header__actions">

        <?php if (Session::check()): ?>

          <?php if (Session::roleId() === Role::CUSTOMER): ?>
            <a href="/become-vendor.php">
              <span>Want to sell?</span>
              <strong>Become a Vendor</strong>
            </a>
          <?php endif; ?>

          <div class="header__notifications">
            <button type="button" id="notification-bell-toggle" class="header__notifications-toggle">
              <span class="icon_comment_alt"></span>
              <span id="notification-badge" class="header__notifications-badge" hidden></span>
            </button>

            <div id="notification-dropdown" class="header__notifications-dropdown">
              <div class="header__notifications-header">Notifications</div>
              <div id="notification-list">
                <div class="header__notifications-empty" id="notification-empty">
                  No notifications yet.
                </div>
              </div>
            </div>
          </div>

          <a href="/profile.php">

            <span>Hello, <?= htmlspecialchars(Session::user()['email'], ENT_QUOTES) ?></span>

            <strong>My Profile</strong>

          </a>

        <?php else: ?>

          <a href="/login.php">

            <span>Hello, Sign in</span>

            <strong>Account</strong>

          </a>

        <?php endif; ?>

        <a href="/my-orders.php">

          <span>Returns</span>

          <strong>& Orders</strong>

        </a>

        <a href="/shopping-cart.php" class="header__cart">

          <div class="header__cart-icon">

            <img src="img/icon/cart.png" alt="Cart">

            <span id="cart-count" hidden>0</span>

          </div>

          <span>Cart</span>

        </a>
      </div>

    </div>
  </div>
</header>