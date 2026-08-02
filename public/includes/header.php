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

        <a href="/login.php">

          <span>Hello, Sign in</span>

          <strong>Account</strong>

        </a>

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