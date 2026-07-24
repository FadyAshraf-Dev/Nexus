<?php
declare(strict_types=1);
require_once dirname(__DIR__, 1) . '/bootstrap/bootstrap.php';

?>
<!DOCTYPE html>
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

    <!-- Checkout Section Begin -->
    <section class="checkout spad">
      <div class="container">
        <div class="checkout__form">
          <form action="#">
            <div class="row">
              <div class="col-lg-8 col-md-6">
                <h6 class="checkout__title">Billing Details</h6>
                <div class="row">
                  <div class="col-lg-6">
                    <div class="checkout__input">
                      <p>Fist Name<span>*</span></p>
                      <input type="text" />
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="checkout__input">
                      <p>Last Name<span>*</span></p>
                      <input type="text" />
                    </div>
                  </div>
                </div>
                <div class="checkout__input">
                  <p>Country<span>*</span></p>
                  <input type="text" />
                </div>
                <div class="checkout__input">
                  <p>Address<span>*</span></p>
                  <input
                    type="text"
                    placeholder="Street Address"
                    class="checkout__input__add"
                  />
                  <input
                    type="text"
                    placeholder="Apartment, suite, unite ect (optinal)"
                  />
                </div>
                <div class="checkout__input">
                  <p>Town/City<span>*</span></p>
                  <input type="text" />
                </div>
                <div class="checkout__input">
                  <p>Country/State<span>*</span></p>
                  <input type="text" />
                </div>
                <div class="checkout__input">
                  <p>Postcode / ZIP<span>*</span></p>
                  <input type="text" />
                </div>
              </div>
              <div class="col-lg-4 col-md-6">
                <div class="checkout__order">
                  <h4 class="order__title">Your order</h4>
                  <div class="checkout__order__products">
                    <div class="checkout__order__item">
                      <div class="checkout__order__image">
                        <img src="img/product/product-1.jpg" alt="" />
                        <span class="checkout__order__quantity">2</span>
                      </div>

                      <div class="checkout__order__content">
                        <h6 class="checkout__order__name">
                          Men Top Black Puffed Jacket
                        </h6>

                        <p class="checkout__order__category">Men's Black</p>
                      </div>

                      <div class="checkout__order__price">$999.00</div>
                    </div>

                    <div class="checkout__order__item">
                      <div class="checkout__order__image">
                        <img src="img/product/product-2.jpg" alt="" />
                        <span class="checkout__order__quantity">1</span>
                      </div>

                      <div class="checkout__order__content">
                        <h6 class="checkout__order__name">Women Jacket</h6>

                        <p class="checkout__order__category">Women's Top</p>
                      </div>

                      <div class="checkout__order__price">$1200.00</div>
                    </div>
                  </div>
                  <ul class="checkout__total__all">
                    <li>Subtotal <span>$750.99</span></li>
                    <li>Total <span>$750.99</span></li>
                  </ul>
                  <div class="checkout__input__checkbox">
                    <label for="acc-or">
                      Create an account?
                      <input type="checkbox" id="acc-or" />
                      <span class="checkmark"></span>
                    </label>
                  </div>
                  <p>
                    Lorem ipsum dolor sit amet, consectetur adip elit, sed do
                    eiusmod tempor incididunt ut labore et dolore magna aliqua.
                  </p>
                  <div class="checkout__input__checkbox">
                    <label for="payment">
                      Check Payment
                      <input type="checkbox" id="payment" />
                      <span class="checkmark"></span>
                    </label>
                  </div>
                  <div class="checkout__input__checkbox">
                    <label for="paypal">
                      Paypal
                      <input type="checkbox" id="paypal" />
                      <span class="checkmark"></span>
                    </label>
                  </div>
                  <button type="submit" class="site-btn">PLACE ORDER</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </section>
    <!-- Checkout Section End -->

    <!-- Footer Section Begin -->
    <?php include 'includes/footer.php'; ?>
    <!-- Footer Section End -->


    <!-- Js Plugins -->
    <?php include 'includes/scripts.php'; ?>
  </body>
</html>
