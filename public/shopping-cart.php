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

    <!-- Shopping Cart Section Begin -->
    <section class="shopping-cart spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="shopping__cart__table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cart-items">
                            </tbody>
                        </table>
                        <div id="cart-empty" class="text-center" hidden>
                            Your cart is empty.
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="continue__btn">
                                <a href="#">Continue Shopping</a>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="continue__btn update__btn">
                                <a href="#"><i class="fa fa-spinner"></i> Update cart</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="cart__discount">
                        <h6>Discount codes</h6>
                        <form action="#">
                            <input type="text" placeholder="Coupon code">
                            <button type="submit">Apply</button>
                        </form>
                    </div>
                    <div class="cart__total">
                        <h6>Cart total</h6>
                        <ul>
                            <li>
                                Subtotal
                                <span id="cart-subtotal">$0.00</span>
                            </li>

                            <li>
                                Total
                                <span id="cart-total">$0.00</span>
                            </li>
                        </ul>
                        <a href="#" class="primary-btn">Proceed to checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Shopping Cart Section End -->

    <!-- Footer Section Begin -->
    <?php include 'includes/footer.php'; ?>
    <!-- Footer Section End -->

    <!-- Js Plugins -->
    <?php include 'includes/scripts.php'; ?>
</body>

</html>