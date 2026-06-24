<?php

require_once '../classes/Gatekeeper.php';
Gatekeeper::allow([3,2]);
$userRole = $_SESSION["user"]["role_id"];

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Overview - SB Admin Pro</title>
        <link href="css/styles.css" rel="stylesheet" />
        <link rel="icon" type="image/x-icon" href="assets/img/favicon.png" />
        <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" crossorigin="anonymous"></script>
    </head>
    <body class="nav-fixed">
        <nav class="topnav navbar navbar-expand shadow justify-content-between justify-content-sm-start navbar-light bg-white" id="sidenavAccordion">
            <!-- Sidenav Toggle Button-->
            <button class="btn btn-icon btn-transparent-dark order-1 order-lg-0 me-2 ms-lg-2 me-lg-0" id="sidebarToggle"><i data-feather="menu"></i></button>
            <!-- Navbar Brand-->
            <!-- * * Tip * * You can use text or an image for your navbar brand.-->
            <!-- * * * * * * When using an image, we recommend the SVG format.-->
            <!-- * * * * * * Dimensions: Maximum height: 32px, maximum width: 240px-->
            <a class="navbar-brand pe-3 ps-4 ps-lg-2" href="index.html">SB Admin Pro</a>
            <!-- Navbar Search Input-->
            <!-- * * Note: * * Visible only on and above the lg breakpoint-->
            <form class="form-inline me-auto d-none d-lg-block me-3">
                <div class="input-group input-group-joined input-group-solid">
                    <input class="form-control pe-0" type="search" placeholder="Search" aria-label="Search" />
                    <div class="input-group-text"><i data-feather="search"></i></div>
                </div>
            </form>
            <!-- Navbar Items-->
            <ul class="navbar-nav align-items-center ms-auto">
                <!-- Documentation Dropdown-->
                <li class="nav-item dropdown no-caret d-none d-md-block me-3">
                    <a class="nav-link dropdown-toggle" id="navbarDropdownDocs" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="fw-500">Documentation</div>
                        <i class="fas fa-chevron-right dropdown-arrow"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end py-0 me-sm-n15 me-lg-0 o-hidden animated--fade-in-up" aria-labelledby="navbarDropdownDocs">
                        <a class="dropdown-item py-3" href="https://docs.startbootstrap.com/sb-admin-pro" target="_blank">
                            <div class="icon-stack bg-primary-soft text-primary me-4"><i data-feather="book"></i></div>
                            <div>
                                <div class="small text-gray-500">Documentation</div>
                                Usage instructions and reference
                            </div>
                        </a>
                        <div class="dropdown-divider m-0"></div>
                        <a class="dropdown-item py-3" href="https://docs.startbootstrap.com/sb-admin-pro/components" target="_blank">
                            <div class="icon-stack bg-primary-soft text-primary me-4"><i data-feather="code"></i></div>
                            <div>
                                <div class="small text-gray-500">Components</div>
                                Code snippets and reference
                            </div>
                        </a>
                        <div class="dropdown-divider m-0"></div>
                        <a class="dropdown-item py-3" href="https://docs.startbootstrap.com/sb-admin-pro/changelog" target="_blank">
                            <div class="icon-stack bg-primary-soft text-primary me-4"><i data-feather="file-text"></i></div>
                            <div>
                                <div class="small text-gray-500">Changelog</div>
                                Updates and changes
                            </div>
                        </a>
                    </div>
                </li>
                <!-- Navbar Search Dropdown-->
                <!-- * * Note: * * Visible only below the lg breakpoint-->
                <li class="nav-item dropdown no-caret me-3 d-lg-none">
                    <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="searchDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i data-feather="search"></i></a>
                    <!-- Dropdown - Search-->
                    <div class="dropdown-menu dropdown-menu-end p-3 shadow animated--fade-in-up" aria-labelledby="searchDropdown">
                        <form class="form-inline me-auto w-100">
                            <div class="input-group input-group-joined input-group-solid">
                                <input class="form-control pe-0" type="text" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2" />
                                <div class="input-group-text"><i data-feather="search"></i></div>
                            </div>
                        </form>
                    </div>
                </li>
                <!-- Alerts Dropdown-->
                <li class="nav-item dropdown no-caret d-none d-sm-block me-3 dropdown-notifications">
                    <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownAlerts" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i data-feather="bell"></i></a>
                    <div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownAlerts">
                        <h6 class="dropdown-header dropdown-notifications-header">
                            <i class="me-2" data-feather="bell"></i>
                            Alerts Center
                        </h6>
                        <!-- Example Alert 1-->
                        <a class="dropdown-item dropdown-notifications-item" href="#!">
                            <div class="dropdown-notifications-item-icon bg-warning"><i data-feather="activity"></i></div>
                            <div class="dropdown-notifications-item-content">
                                <div class="dropdown-notifications-item-content-details">December 29, 2021</div>
                                <div class="dropdown-notifications-item-content-text">This is an alert message. It's nothing serious, but it requires your attention.</div>
                            </div>
                        </a>
                        <!-- Example Alert 2-->
                        <a class="dropdown-item dropdown-notifications-item" href="#!">
                            <div class="dropdown-notifications-item-icon bg-info"><i data-feather="bar-chart"></i></div>
                            <div class="dropdown-notifications-item-content">
                                <div class="dropdown-notifications-item-content-details">December 22, 2021</div>
                                <div class="dropdown-notifications-item-content-text">A new monthly report is ready. Click here to view!</div>
                            </div>
                        </a>
                        <!-- Example Alert 3-->
                        <a class="dropdown-item dropdown-notifications-item" href="#!">
                            <div class="dropdown-notifications-item-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></div>
                            <div class="dropdown-notifications-item-content">
                                <div class="dropdown-notifications-item-content-details">December 8, 2021</div>
                                <div class="dropdown-notifications-item-content-text">Critical system failure, systems shutting down.</div>
                            </div>
                        </a>
                        <!-- Example Alert 4-->
                        <a class="dropdown-item dropdown-notifications-item" href="#!">
                            <div class="dropdown-notifications-item-icon bg-success"><i data-feather="user-plus"></i></div>
                            <div class="dropdown-notifications-item-content">
                                <div class="dropdown-notifications-item-content-details">December 2, 2021</div>
                                <div class="dropdown-notifications-item-content-text">New user request. Woody has requested access to the organization.</div>
                            </div>
                        </a>
                        <a class="dropdown-item dropdown-notifications-footer" href="#!">View All Alerts</a>
                    </div>
                </li>
                <!-- Messages Dropdown-->
                <li class="nav-item dropdown no-caret d-none d-sm-block me-3 dropdown-notifications">
                    <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownMessages" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i data-feather="mail"></i></a>
                    <div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownMessages">
                        <h6 class="dropdown-header dropdown-notifications-header">
                            <i class="me-2" data-feather="mail"></i>
                            Message Center
                        </h6>
                        <!-- Example Message 1  -->
                        <a class="dropdown-item dropdown-notifications-item" href="#!">
                            <img class="dropdown-notifications-item-img" src="assets/img/illustrations/profiles/profile-2.png" />
                            <div class="dropdown-notifications-item-content">
                                <div class="dropdown-notifications-item-content-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
                                <div class="dropdown-notifications-item-content-details">Thomas Wilcox · 58m</div>
                            </div>
                        </a>
                        <!-- Example Message 2-->
                        <a class="dropdown-item dropdown-notifications-item" href="#!">
                            <img class="dropdown-notifications-item-img" src="assets/img/illustrations/profiles/profile-3.png" />
                            <div class="dropdown-notifications-item-content">
                                <div class="dropdown-notifications-item-content-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
                                <div class="dropdown-notifications-item-content-details">Emily Fowler · 2d</div>
                            </div>
                        </a>
                        <!-- Example Message 3-->
                        <a class="dropdown-item dropdown-notifications-item" href="#!">
                            <img class="dropdown-notifications-item-img" src="assets/img/illustrations/profiles/profile-4.png" />
                            <div class="dropdown-notifications-item-content">
                                <div class="dropdown-notifications-item-content-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
                                <div class="dropdown-notifications-item-content-details">Marshall Rosencrantz · 3d</div>
                            </div>
                        </a>
                        <!-- Example Message 4-->
                        <a class="dropdown-item dropdown-notifications-item" href="#!">
                            <img class="dropdown-notifications-item-img" src="assets/img/illustrations/profiles/profile-5.png" />
                            <div class="dropdown-notifications-item-content">
                                <div class="dropdown-notifications-item-content-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
                                <div class="dropdown-notifications-item-content-details">Colby Newton · 3d</div>
                            </div>
                        </a>
                        <!-- Footer Link-->
                        <a class="dropdown-item dropdown-notifications-footer" href="#!">Read All Messages</a>
                    </div>
                </li>
                <!-- User Dropdown-->
                <li class="nav-item dropdown no-caret dropdown-user me-3 me-lg-4">
                    <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownUserImage" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img class="img-fluid" src="assets/img/illustrations/profiles/profile-1.png" /></a>
                    <div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownUserImage">
                        <h6 class="dropdown-header d-flex align-items-center">
                            <img class="dropdown-user-img" src="assets/img/illustrations/profiles/profile-1.png" />
                            <div class="dropdown-user-details">
                                <div class="dropdown-user-details-name">Valerie Luna</div>
                                <div class="dropdown-user-details-email">vluna@aol.com</div>
                            </div>
                        </h6>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="account-profile.html">
                            <div class="dropdown-item-icon"><i data-feather="settings"></i></div>
                            Account
                        </a>
                        <a class="dropdown-item" href="<?= "src/auth/logout.php" ?>">
                            <div class="dropdown-item-icon"><i data-feather="log-out"></i></div>
                            Logout
                        </a>
                    </div>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sidenav shadow-right sidenav-light">
                    <div class="sidenav-menu">
                        <div class="nav accordion" id="accordionSidenav">
                            
                            <?php if (isset($userRole) && (int)$userRole === 3): ?>
                                <div class="sidenav-menu-heading">Admin Panel</div>
                                
                                <a class="nav-link active" href="index.php">
                                    <div class="nav-link-icon"><i data-feather="home"></i></div>
                                    Home
                                </a>
                                <a class="nav-link" href="user_management.php">
                                    <div class="nav-link-icon"><i data-feather="users"></i></div>
                                    User Management
                                </a>
                                <a class="nav-link" href="category_management.php">
                                    <div class="nav-link-icon"><i data-feather="layers"></i></div>
                                    Category Management
                                </a>
                                <a class="nav-link" href="product_management.php">
                                    <div class="nav-link-icon"><i data-feather="shopping-bag"></i></div>
                                    Product Management
                                </a>
                                <a class="nav-link" href="review_management.php">
                                    <div class="nav-link-icon"><i data-feather="message-square"></i></div>
                                    Review Management
                                </a>
                                <a class="nav-link" href="coupon_management.php">
                                    <div class="nav-link-icon"><i data-feather="tag"></i></div>
                                    Coupon Management
                                </a>
                            <?php endif; ?>


                            <?php if (isset($userRole) && (int)$userRole === 2): ?>
                                <div class="sidenav-menu-heading">Vendor Dashboard</div>
                                
                                <a class="nav-link" href="vendor_home.php">
                                    <div class="nav-link-icon"><i data-feather="home"></i></div>
                                    Home
                                </a>
                                <a class="nav-link" href="my_products.php">
                                    <div class="nav-link-icon"><i data-feather="box"></i></div>
                                    My Products
                                </a>
                                <a class="nav-link" href="add_product.php">
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
            <div id="layoutSidenav_content">
                <main>
                    <header class="py-10 mb-4 bg-gradient-primary-to-secondary">
                        <div class="container-xl px-4">
                            <div class="text-center">
                                <h1 class="text-white">Welcome to SB Admin Pro</h1>
                                <p class="lead mb-0 text-white-50">A professionally designed admin panel template built with Bootstrap 5</p>
                            </div>
                        </div>
                    </header>
                    <!-- Main page content-->
                    <div class="container-xl px-4">
                        <h2 class="mt-5 mb-0">Dashboards</h2>
                        <p>Three dashboard examples to get you started!</p>
                        <hr class="mt-0 mb-4" />
                        <div class="row">
                            <div class="col-md-6 col-xl-4 mb-4 mb-xl-0">
                                <a class="d-block lift rounded overflow-hidden mb-2" href="dashboard-1.html"><img class="img-fluid" src="https://assets.startbootstrap.com/img/screenshots-product-pages/sb-admin-pro/dashboards/default.png" alt="..." /></a>
                                <div class="text-center small">Default Dashboard</div>
                            </div>
                            <div class="col-md-6 col-xl-4 mb-4 mb-xl-0">
                                <a class="d-block lift rounded overflow-hidden mb-2" href="dashboard-3.html"><img class="img-fluid" src="https://assets.startbootstrap.com/img/screenshots-product-pages/sb-admin-pro/dashboards/affiliate.png" alt="..." /></a>
                                <div class="text-center small">Affiliate Dashboard</div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <a class="d-block lift rounded overflow-hidden mb-2" href="dashboard-2.html"><img class="img-fluid" src="https://assets.startbootstrap.com/img/screenshots-product-pages/sb-admin-pro/dashboards/multipurpose.png" alt="..." /></a>
                                <div class="text-center small">Multipurpose Dashboard</div>
                            </div>
                        </div>
                        <h2 class="mt-5 mb-0">App Pages</h2>
                        <p>App pages to cover common use pages to help build your app!</p>
                        <hr class="mt-0 mb-4" />
                        <div class="row">
                            <div class="col-sm-6 col-md-4 col-xl-3 mb-4">
                                <a class="d-block lift rounded overflow-hidden mb-2" href="account-billing.html"><img class="img-fluid" src="https://assets.startbootstrap.com/img/screenshots-product-pages/sb-admin-pro/pages/account-billing.png" alt="..." /></a>
                                <div class="text-center small">Account - Billing</div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3 mb-4">
                                <a class="d-block lift rounded overflow-hidden mb-2" href="account-notifications.html"><img class="img-fluid" src="https://assets.startbootstrap.com/img/screenshots-product-pages/sb-admin-pro/pages/account-notifications.png" alt="..." /></a>
                                <div class="text-center small">Account - Notifications</div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3 mb-4">
                                <a class="d-block lift rounded overflow-hidden mb-2" href="account-profile.html"><img class="img-fluid" src="https://assets.startbootstrap.com/img/screenshots-product-pages/sb-admin-pro/pages/account-profile.png" alt="..." /></a>
                                <div class="text-center small">Account - Profile</div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3 mb-4">
                                <a class="d-block lift rounded overflow-hidden mb-2" href="account-security.html"><img class="img-fluid" src="https://assets.startbootstrap.com/img/screenshots-product-pages/sb-admin-pro/pages/account-security.png" alt="..." /></a>
                                <div class="text-center small">Account - Security</div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3 mb-4">
                                <a class="d-block lift rounded overflow-hidden mb-2" href="auth-login-basic.html"><img class="img-fluid" src="https://assets.startbootstrap.com/img/screenshots-product-pages/sb-admin-pro/pages/auth-login-basic.png" alt="..." /></a>
                                <div class="text-center small">Auth - Login</div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3 mb-4">
                                <a class="d-block lift rounded overflow-hidden mb-2" href="auth-login-social.html"><img class="img-fluid" src="https://assets.startbootstrap.com/img/screenshots-product-pages/sb-admin-pro/pages/auth-login-social.png" alt="..." /></a>
                                <div class="text-center small">Auth - Login (Social)</div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3 mb-4">
                                <a class="d-block lift rounded overflow-hidden mb-2" href="multi-tenant-select.html"><img class="img-fluid" src="https://assets.startbootstrap.com/img/screenshots-product-pages/sb-admin-pro/pages/auth-mutli-tenant.png" alt="..." /></a>
                                <div class="text-center small">Auth - Multi Tenant</div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3 mb-4">
                                <a class="d-block lift rounded overflow-hidden mb-2" href="auth-password-basic.html"><img class="img-fluid" src="https://assets.startbootstrap.com/img/screenshots-product-pages/sb-admin-pro/pages/auth-password-basic.png" alt="..." /></a>
                                <div class="text-center small">Auth - Password</div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3 mb-4">
                                <a class="d-block lift rounded overflow-hidden mb-2" href="auth-password-social.html"><img class="img-fluid" src="https://assets.startbootstrap.com/img/screenshots-product-pages/sb-admin-pro/pages/auth-password-social.png" alt="..." /></a>
                                <div class="text-center small">Auth - Password (Social)</div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3 mb-4">
                                <a class="d-block lift rounded overflow-hidden mb-2" href="auth-register-basic.html"><img class="img-fluid" src="https://assets.startbootstrap.com/img/screenshots-product-pages/sb-admin-pro/pages/auth-register-basic.png" alt="..." /></a>
                                <div class="text-center small">Auth - Register</div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3 mb-4">
                                <a class="d-block lift rounded overflow-hidden mb-2" href="auth-register-social.html"><img class="img-fluid" src="https://assets.startbootstrap.com/img/screenshots-product-pages/sb-admin-pro/pages/auth-register-social.png" alt="..." /></a>
                                <div class="text-center small">Auth - Register (Social)</div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3 mb-4">
                                <a class="d-block lift rounded overflow-hidden mb-2" href="invoice.html"><img class="img-fluid" src="https://assets.startbootstrap.com/img/screenshots-product-pages/sb-admin-pro/pages/invoice.png" alt="..." /></a>
                                <div class="text-center small">Invoice</div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3 mb-4">
                                <a class="d-block lift rounded overflow-hidden mb-2" href="knowledge-base-article.html"><img class="img-fluid" src="https://assets.startbootstrap.com/img/screenshots-product-pages/sb-admin-pro/pages/knowledgebase-article.png" alt="..." /></a>
                                <div class="text-center small">Knowledgebase - Article</div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3 mb-4">
                                <a class="d-block lift rounded overflow-hidden mb-2" href="knowledge-base-category.html"><img class="img-fluid" src="https://assets.startbootstrap.com/img/screenshots-product-pages/sb-admin-pro/pages/knowledgebase-category.png" alt="..." /></a>
                                <div class="text-center small">Knowledgebase - Category</div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3 mb-4">
                                <a class="d-block lift rounded overflow-hidden mb-2" href="knowledge-base-home-1.html"><img class="img-fluid" src="https://assets.startbootstrap.com/img/screenshots-product-pages/sb-admin-pro/pages/knowledgebase-home-1.png" alt="..." /></a>
                                <div class="text-center small">Knowledgebase - Home 1</div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3 mb-4">
                                <a class="d-block lift rounded overflow-hidden mb-2" href="knowledge-base-home-2.html"><img class="img-fluid" src="https://assets.startbootstrap.com/img/screenshots-product-pages/sb-admin-pro/pages/knowledgebase-home-2.png" alt="..." /></a>
                                <div class="text-center small">Knowledgebase - Home 2</div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3 mb-4">
                                <a class="d-block lift rounded overflow-hidden mb-2" href="pricing.html"><img class="img-fluid" src="https://assets.startbootstrap.com/img/screenshots-product-pages/sb-admin-pro/pages/pricing.png" alt="..." /></a>
                                <div class="text-center small">Pricing</div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3 mb-4">
                                <a class="d-block lift rounded overflow-hidden mb-2" href="wizard.html"><img class="img-fluid" src="https://assets.startbootstrap.com/img/screenshots-product-pages/sb-admin-pro/pages/wizard.png" alt="..." /></a>
                                <div class="text-center small">Wizard</div>
                            </div>
                        </div>
                        <h2 class="mt-5 mb-0">Starter Layouts</h2>
                        <p>Layouts for creating new pages within your project!</p>
                        <hr class="mt-0 mb-4" />
                        <div class="row">
                            <div class="col-sm-6 col-md-4 mb-4">
                                <div class="small mb-1">Navigation</div>
                                <div class="list-group mb-4">
                                    <a class="list-group-item list-group-item-action p-3" href="layout-static.html">
                                        <div class="d-flex align-items-center justify-content-between">
                                            Static Sidenav
                                            <i class="text-muted" data-feather="arrow-right"></i>
                                        </div>
                                    </a>
                                    <a class="list-group-item list-group-item-action p-3" href="layout-dark.html">
                                        <div class="d-flex align-items-center justify-content-between">
                                            Dark Sidenav
                                            <i class="text-muted" data-feather="arrow-right"></i>
                                        </div>
                                    </a>
                                    <a class="list-group-item list-group-item-action p-3" href="layout-rtl.html">
                                        <div class="d-flex align-items-center justify-content-between">
                                            RTL Layout
                                            <i class="text-muted" data-feather="arrow-right"></i>
                                        </div>
                                    </a>
                                </div>
                                <div class="small mb-1">Container Options</div>
                                <div class="list-group">
                                    <a class="list-group-item list-group-item-action p-3" href="layout-boxed.html">
                                        <div class="d-flex align-items-center justify-content-between">
                                            Boxed Layouts
                                            <i class="text-muted" data-feather="arrow-right"></i>
                                        </div>
                                    </a>
                                    <a class="list-group-item list-group-item-action p-3" href="layout-fluid.html">
                                        <div class="d-flex align-items-center justify-content-between">
                                            Fluid Layout
                                            <i class="text-muted" data-feather="arrow-right"></i>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 mb-4">
                                <div class="small mb-1">Page Headers</div>
                                <div class="list-group">
                                    <a class="list-group-item list-group-item-action p-3" href="header-simplified.html">
                                        <div class="d-flex align-items-center justify-content-between">
                                            Simplified
                                            <i class="text-muted" data-feather="arrow-right"></i>
                                        </div>
                                    </a>
                                    <a class="list-group-item list-group-item-action p-3" href="header-compact.html">
                                        <div class="d-flex align-items-center justify-content-between">
                                            Compact
                                            <i class="text-muted" data-feather="arrow-right"></i>
                                        </div>
                                    </a>
                                    <a class="list-group-item list-group-item-action p-3" href="header-overlap.html">
                                        <div class="d-flex align-items-center justify-content-between">
                                            Content Overlap
                                            <i class="text-muted" data-feather="arrow-right"></i>
                                        </div>
                                    </a>
                                    <a class="list-group-item list-group-item-action p-3" href="header-breadcrumbs.html">
                                        <div class="d-flex align-items-center justify-content-between">
                                            Breadcrumbs
                                            <i class="text-muted" data-feather="arrow-right"></i>
                                        </div>
                                    </a>
                                    <a class="list-group-item list-group-item-action p-3" href="header-light.html">
                                        <div class="d-flex align-items-center justify-content-between">
                                            Light
                                            <i class="text-muted" data-feather="arrow-right"></i>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 mb-4">
                                <div class="small mb-1">Starter Layouts</div>
                                <div class="list-group mb-4">
                                    <a class="list-group-item list-group-item-action p-3" href="starter-default.html">
                                        <div class="d-flex align-items-center justify-content-between">
                                            Default
                                            <i class="text-muted" data-feather="arrow-right"></i>
                                        </div>
                                    </a>
                                    <a class="list-group-item list-group-item-action p-3" href="starter-minimal.html">
                                        <div class="d-flex align-items-center justify-content-between">
                                            Minimal
                                            <i class="text-muted" data-feather="arrow-right"></i>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
                <footer class="footer-admin mt-auto footer-light">
                    <div class="container-xl px-4">
                        <div class="row">
                            <div class="col-md-6 small">Copyright &copy; Your Website 2021</div>
                            <div class="col-md-6 text-md-end small">
                                <a href="#!">Privacy Policy</a>
                                &middot;
                                <a href="#!">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>
