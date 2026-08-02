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

    <section class="my-orders spad">
        <div class="container">

            <h3 class="my-orders__title">My Orders</h3>

            <div class="my-orders__tabs" role="tablist">
                <button
                    type="button"
                    class="my-orders__tab is-active"
                    data-status-group="shipping">
                    On Shipping
                    <span class="my-orders__tab-count" id="my-orders-count-shipping"></span>
                </button>
                <button
                    type="button"
                    class="my-orders__tab"
                    data-status-group="arrived">
                    Arrived
                    <span class="my-orders__tab-count" id="my-orders-count-arrived"></span>
                </button>
                <button
                    type="button"
                    class="my-orders__tab"
                    data-status-group="canceled">
                    Canceled
                    <span class="my-orders__tab-count" id="my-orders-count-canceled"></span>
                </button>
            </div>

            <div id="my-orders-loading" class="text-center">Loading your orders...</div>

            <div id="my-orders-error" class="text-center" hidden></div>

            <div id="my-orders-empty" class="my-orders__empty" hidden>
                <p>No orders here yet.</p>
                <a href="/index.php" class="site-btn">Continue shopping</a>
            </div>

            <div id="my-orders-list" class="my-orders__list"></div>

        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/scripts.php'; ?>
    <script type="module" src="/admin/js/pages/my-orders/MyOrders.js"></script>
</body>

</html>
