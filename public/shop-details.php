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

    <!-- Shop Details Section Begin -->
    <!-- Shop Details Section Begin -->
    <section class="shop-details">

        <div class="product__details__pic">

            <div class="container">

                <div class="row">

                    <div class="col-lg-12">

                        <div class="product__details__breadcrumb">

                            <a href="./index.html">Home</a>
                            <a href="./shop.html">Shop</a>
                            <span>Product Details</span>

                        </div>

                    </div>

                </div>

                <div class="row">

                    <!-- Gallery thumbnails -->
                    <div class="col-lg-3 col-md-3">

                        <ul id="product-gallery-nav" class="nav nav-tabs" role="tablist">
                        </ul>

                    </div>

                    <!-- Gallery preview -->
                    <div class="col-lg-6 col-md-9">

                        <div id="product-gallery-preview" class="tab-content">
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="product__details__content">

            <div class="container">

                <div class="row d-flex justify-content-center">

                    <div class="col-lg-8">

                        <div class="product__details__text">

                            <h4 id="product-name"></h4>

                            <div class="rating">

                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star-o"></i>

                                <span> - 5 Reviews</span>

                            </div>

                            <h3 id="product-price-container">


                                <span id="product-old-price"></span>

                            </h3>

                            <p id="product-short-description"></p>

                            <div class="product__details__cart__option">

                                <div class="quantity">

                                    <div class="pro-qty">

                                        <input type="text" value="1">

                                    </div>

                                </div>

                                <a href="#" class="primary-btn">
                                    Add to cart
                                </a>

                            </div>

                            <div class="product__details__last__option">

                                <ul>

                                    <li id="product-category-row">
                                        <span>Category:</span>
                                    </li>

                                    <li id="product-brand-row">
                                        <span>Brand:</span>
                                    </li>
                                </ul>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="row">

                    <div class="col-lg-12">

                        <div class="product__details__tab">

                            <!-- These are DESCRIPTION tabs, NOT gallery tabs -->
                            <ul class="nav nav-tabs" role="tablist">

                                <li class="nav-item">

                                    <a class="nav-link active" data-toggle="tab" href="#tabs-description" role="tab">

                                        Full Description

                                    </a>

                                </li>

                                <li class="nav-item">

                                    <a class="nav-link" data-toggle="tab" href="#tabs-reviews" role="tab">

                                        Customer Reviews (0)

                                    </a>

                                </li>

                            </ul>

                            <div class="tab-content">

                                <div class="tab-pane active" id="tabs-description" role="tabpanel">

                                    <div class="product__details__tab__content">

                                        <p id="product-full-description"></p>

                                    </div>

                                </div>

                                <div class="tab-pane" id="tabs-reviews" role="tabpanel">

                                    <div class="product__details__tab__content">

                                        Reviews coming soon.

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>
    <!-- Shop Details Section End --> <!-- Shop Details Section End -->

    <!-- Related Section Begin -->
    <section class="related spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="related-title">Related Product</h3>
                </div>
            </div>
            <div id="related-products" class="row">
            </div>
        </div>
    </section>
    <!-- Footer Section Begin -->
    <?php include 'includes/footer.php'; ?>
    <!-- Footer Section End -->

    <!-- Search Begin -->
    <?php include 'includes/search.php'; ?>
    <!-- Search End -->

    <!-- Js Plugins -->
    <?php include 'includes/scripts.php'; ?>
    <script type="module" src="/admin/js/pages/shop/ShopDetails.js"></script>
</body>

</html>