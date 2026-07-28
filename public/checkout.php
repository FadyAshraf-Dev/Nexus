<?php
declare(strict_types=1);

require_once dirname(__DIR__, 1) . '/bootstrap/bootstrap.php';
Gatekeeper::authorize([1, 2, 3]);
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

  <section class="checkout checkout__wizard">
    <div class="container">
      <nav class="checkout__stepper" aria-label="Checkout steps">
        <button type="button" class="checkout__step is-active" data-step-btn="1">
          <span class="checkout__step-number">01</span>
          <span class="checkout__step-label">Address</span>
        </button>

        <button type="button" class="checkout__step" data-step-btn="2">
          <span class="checkout__step-number">02</span>
          <span class="checkout__step-label">Contact</span>
        </button>

        <button type="button" class="checkout__step" data-step-btn="3">
          <span class="checkout__step-number">03</span>
          <span class="checkout__step-label">Review</span>
        </button>
      </nav>

      <form id="checkout-form" class="checkout__form" novalidate>
        <input type="hidden" id="checkout-address" name="address">
        <input type="hidden" id="checkout-payment-method" name="payment_method" value="cod">

        <div class="checkout__layout">
          <div class="checkout__main">

            <section class="checkout__panel is-active" data-step-panel="1">
              <h6 class="checkout__title">Shipping address</h6>

              <div class="checkout__grid">
                <div class="checkout__field">
                  <label for="shipping-country">Country<span>*</span></label>
                  <input id="shipping-country" type="text" autocomplete="country-name" required>
                  <small class="checkout__error" data-error-for="shipping-country"></small>
                </div>

                <div class="checkout__field">
                  <label for="shipping-city">City<span>*</span></label>
                  <input id="shipping-city" type="text" autocomplete="address-level2" required>
                  <small class="checkout__error" data-error-for="shipping-city"></small>
                </div>

                <div class="checkout__field checkout__field--full">
                  <label for="shipping-address-1">Street address<span>*</span></label>
                  <input id="shipping-address-1" type="text" autocomplete="address-line1" required placeholder="Street Address">
                  <small class="checkout__error" data-error-for="shipping-address-1"></small>
                </div>

                <div class="checkout__field checkout__field--full">
                  <label for="shipping-address-2">Apartment, suite, unit</label>
                  <input id="shipping-address-2" type="text" autocomplete="address-line2"
                    placeholder="Apartment, suite, unit (optional)">
                  <small class="checkout__error" data-error-for="shipping-address-2"></small>
                </div>

                <div class="checkout__field">
                  <label for="shipping-state">State / Governorate<span>*</span></label>
                  <input id="shipping-state" type="text" autocomplete="address-level1" required>
                  <small class="checkout__error" data-error-for="shipping-state"></small>
                </div>

                <div class="checkout__field">
                  <label for="shipping-zip">Postcode / ZIP<span>*</span></label>
                  <input id="shipping-zip" type="text" autocomplete="postal-code" required>
                  <small class="checkout__error" data-error-for="shipping-zip"></small>
                </div>
              </div>
            </section>

            <section class="checkout__panel" data-step-panel="2">
              <h6 class="checkout__title">Contact info</h6>

              <div class="checkout__grid">
                <div class="checkout__field checkout__field--full">
                  <label for="contact-phone">Phone number<span>*</span></label>
                  <input id="contact-phone" type="tel" inputmode="tel" autocomplete="tel" required placeholder="01xxxxxxxxx">
                  <small class="checkout__error" data-error-for="contact-phone"></small>
                </div>
              </div>

              <div class="checkout__hint">
                Use a number we can actually call. Courier drama is bad for everyone.
              </div>
            </section>


            <section class="checkout__panel" data-step-panel="3">
              <h6 class="checkout__title">Review and confirmation</h6>

              <div class="checkout__review">
                <div class="checkout__review-block">
                  <h5>Shipping address</h5>
                  <p id="checkout-review-address">—</p>
                </div>

                <div class="checkout__review-block">
                  <h5>Contact number</h5>
                  <p id="checkout-review-phone">—</p>
                </div>

                <div class="checkout__review-block">
                  <h5>Payment method</h5>
                  <p>Pay at the door</p>
                </div>
              </div>
              
              <div id="checkout-submit-errors" class="checkout__submit-errors" hidden></div>
            </section>

            <div class="checkout__actions">
              <button type="button" id="checkout-prev" class="site-btn site-btn--ghost" disabled>Back</button>
              <button type="button" id="checkout-next" class="site-btn">Next</button>
              <button type="submit" id="checkout-submit" class="site-btn" hidden>Place order</button>
            </div>

          </div>

<aside class="checkout__summary">

    <h4 class="checkout__summary-title">
        Your order
    </h4>

    <div
        id="checkout-overview-list"
        class="checkout__order__products">
    </div>

    <div class="checkout__promo">

        <label
            class="checkout__promo-label"
            for="checkout-promo-input">

            Promo code

        </label>

        <div class="checkout__promo-row">

            <input
                id="checkout-promo-input"
                type="text"
                placeholder="Enter promo code">

            <button
                id="checkout-apply-promo"
                type="button"
                class="site-btn site-btn--promo">

                Apply

            </button>

        </div>

        <small
            id="checkout-promo-feedback"
            class="checkout__promo-feedback">

        </small>

    </div>

    <ul class="checkout__total__all">

        <li>
            Subtotal
            <span id="checkout-subtotal">$0.00</span>
        </li>

        <li>
            Shipping
            <span id="checkout-shipping">$50.00</span>
        </li>

        <li id="checkout-discount-row" hidden>

            Discount

            <span
                id="checkout-discount"
                class="checkout__discount">

                -$0.00

            </span>

        </li>

        <li>

            Total

            <span id="checkout-total">

                $0.00

            </span>

        </li>

    </ul>

    <p class="checkout__summary-note">

        Cash on delivery only.
        No card circus, no tax headache,
        no drama.

    </p>

</aside>        </div>
      </form>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>

  <?php include 'includes/scripts.php'; ?>
  <script type="module" src="/admin/js/pages/shop/Checkout.js"></script>
</body>

</html>