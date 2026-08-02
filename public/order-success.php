<?php

declare(strict_types=1);

require_once dirname(__DIR__, 1) . '/bootstrap/bootstrap.php';

Gatekeeper::authorize([1, 2, 3]);

$orderId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$orderId) {
    header('Location: /shopping-cart.php');
    exit;
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

    <section class="order-success spad">
        <div class="container">

            <div class="order-success__intro" id="order-success-intro" hidden>
                <span class="order-success__icon">
                    <i class="icon_check"></i>
                </span>
                <h3>Thank you for your order!</h3>
                <p>Your order has been placed and is now being processed.</p>
                <a href="/index.php" class="site-btn">Continue shopping</a>
            </div>

            <div id="order-success-loading" class="text-center">
                Loading order details...
            </div>

            <div id="order-success-error" class="text-center" hidden></div>

            <div class="row" id="order-success-content" hidden>
                <div class="col-lg-8">

                    <div class="order-success__meta">
                        <div>
                            <h6>Order <span id="order-success-id"></span></h6>
                            <p class="order-success__date">
                                Placed on <span id="order-success-date"></span>
                            </p>
                        </div>
                        <span
                            id="order-success-status"
                            class="order-success__status">
                        </span>
                    </div>

                    <div class="checkout__order__products" id="order-success-items"></div>

                    <div class="order-success__details">
                        <div class="order-success__detail">
                            <h6>Shipping address</h6>
                            <p id="order-success-address"></p>
                        </div>
                        <div class="order-success__detail">
                            <h6>Contact number</h6>
                            <p id="order-success-phone"></p>
                        </div>
                        <div class="order-success__detail">
                            <h6>Payment method</h6>
                            <p>Cash on delivery</p>
                        </div>
                    </div>

                </div>

                <div class="col-lg-4">
                    <div class="checkout__order">
                        <h6 class="order__title">Order summary</h6>
                        <ul class="checkout__total__all">
                            <li>
                                Subtotal
                                <span id="order-success-subtotal">$0.00</span>
                            </li>
                            <li>
                                Shipping
                                <span id="order-success-shipping">$0.00</span>
                            </li>
                            <li id="order-success-discount-row" hidden>
                                Discount
                                <span id="order-success-discount">-$0.00</span>
                            </li>
                            <li>
                                Total
                                <span id="order-success-total">$0.00</span>
                            </li>
                        </ul>
                        <a href="/my-orders.php" class="site-btn">View my orders</a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/scripts.php'; ?>
    <script type="module" src="/admin/js/pages/order/OrderSuccess.js"></script>
</body>

</html>
