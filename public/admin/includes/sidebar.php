<div id="layoutSidenav_nav">
                <nav class="sidenav shadow-right sidenav-light">
                    <div class="sidenav-menu">
                        <div class="nav accordion" id="accordionSidenav">
                            
                            <?php if (isset($userRole) && (int)$userRole === 3): ?>
                                <div class="sidenav-menu-heading">Admin Panel</div>
                                
                                <a class="nav-link<?= Navigation::activeIf('index.php') ?>" href="index.php">
                                    <div class="nav-link-icon"><i data-feather="home"></i></div>
                                    Home
                                </a>
                                <a class="nav-link<?= Navigation::activeIf('user_management.php') ?>" href="user_management.php">
                                    <div class="nav-link-icon"><i data-feather="users"></i></div>
                                    User Management
                                </a>
                                <a class="nav-link<?= Navigation::activeIf('vendor_requests.php') ?>" href="vendor_requests.php">
                                    <div class="nav-link-icon"><i data-feather="user-check"></i></div>
                                    Vendor Requests
                                </a>
                                <a class="nav-link<?= Navigation::activeIf('category_management.php') ?>" href="category_management.php">
                                    <div class="nav-link-icon"><i data-feather="layers"></i></div>
                                    Category Management
                                </a>
                                <a class="nav-link<?= Navigation::activeIf('product_management.php') ?>" href="product_management.php">
                                    <div class="nav-link-icon"><i data-feather="shopping-bag"></i></div>
                                    Product Management
                                </a>
                                <a class="nav-link<?= Navigation::activeIf('review_management.php') ?>" href="review_management.php">
                                    <div class="nav-link-icon"><i data-feather="message-square"></i></div>
                                    Review Management
                                </a>
                                <a class="nav-link<?= Navigation::activeIf('coupon_management.php') ?>" href="coupon_management.php">
                                    <div class="nav-link-icon"><i data-feather="tag"></i></div>
                                    Coupon Management
                                </a>
                                <a class="nav-link<?= Navigation::activeIf('order_management.php') ?>" href="order_management.php">
                                    <div class="nav-link-icon"><i data-feather="shopping-cart"></i></div>
                                    Order Management
                                </a>
                            <?php endif; ?>


                            <?php if (isset($userRole) && (int)$userRole === 2): ?>
                                <div class="sidenav-menu-heading">Vendor Dashboard</div>
                                
                                <a class="nav-link<?= Navigation::activeIf('index.php') ?>" href="index.php">
                                    <div class="nav-link-icon"><i data-feather="home"></i></div>
                                    Home
                                </a>
                                <a class="nav-link<?= Navigation::activeIf('my_products.php') ?>" href="my_products.php">
                                    <div class="nav-link-icon"><i data-feather="box"></i></div>
                                    My Products
                                </a>
                                <a class="nav-link<?= Navigation::activeIf('add_product.php') ?>" href="add_product.php">
                                    <div class="nav-link-icon"><i data-feather="plus-circle"></i></div>
                                    Add Products
                                </a>
                            <?php endif; ?>

                        </div>
                    </div>
                    <div class="sidenav-footer">
                        <div class="sidenav-footer-content">
                            <div class="sidenav-footer-subtitle">Logged in as:</div>
                            <div class="sidenav-footer-title">Valerie Luna</div>
                        </div>
                    </div>
                </nav>
            </div>