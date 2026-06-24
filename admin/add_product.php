
<?php
require_once "../classes/Utils.php";
require_once '../classes/Gatekeeper.php';
Gatekeeper::allow([2]);
$vendor = $_SESSION["user"]["id"];
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
        <title>Wizard - SB Admin Pro</title>
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
                    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
                        <div class="container-xl px-4">
                            <div class="page-header-content pt-4">
                                <div class="row align-items-center justify-content-between">
                                    <div class="col-auto mt-4">
                                        <h1 class="page-header-title">
                                            <div class="page-header-icon"><i data-feather="arrow-right-circle"></i></div>
                                            Wizard
                                        </h1>
                                        <div class="page-header-subtitle">Wizard examples for step-by-step form submission content to use as part of an application</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </header>
                    <!-- Main page content-->
                    <div class="container-xl px-4 mt-n10">
                        <!-- Rule #2: widened grid wrapper (col-xl-10) instead of the template's narrower default-->
                        <div class="row justify-content-center">
                            <div class="col-xl-10 col-lg-12">
                                <!-- Wizard card example with navigation-->
                                <div class="card">
                                    <div class="card-header border-bottom">
                                        <!-- Wizard navigation-->
                                        <div class="nav nav-pills nav-justified flex-column flex-xl-row nav-wizard" id="productWizardTab" role="tablist">
                                            <!-- Wizard navigation item 1-->
                                            <a class="nav-item nav-link active" id="step1-tab" href="#step1" data-bs-toggle="tab" role="tab" aria-controls="step1" aria-selected="true">
                                                <div class="wizard-step-icon">1</div>
                                                <div class="wizard-step-text">
                                                    <div class="wizard-step-text-name">Basic Information</div>
                                                    <div class="wizard-step-text-details">Name, description &amp; category</div>
                                                </div>
                                            </a>
                                            <!-- Wizard navigation item 2-->
                                            <a class="nav-item nav-link" id="step2-tab" href="#step2" data-bs-toggle="tab" role="tab" aria-controls="step2" aria-selected="false">
                                                <div class="wizard-step-icon">2</div>
                                                <div class="wizard-step-text">
                                                    <div class="wizard-step-text-name">Pricing</div>
                                                    <div class="wizard-step-text-details">Cost, price &amp; discounts</div>
                                                </div>
                                            </a>
                                            <!-- Wizard navigation item 3-->
                                            <a class="nav-item nav-link" id="step3-tab" href="#step3" data-bs-toggle="tab" role="tab" aria-controls="step3" aria-selected="false">
                                                <div class="wizard-step-icon">3</div>
                                                <div class="wizard-step-text">
                                                    <div class="wizard-step-text-name">Inventory</div>
                                                    <div class="wizard-step-text-details">Stock &amp; alert thresholds</div>
                                                </div>
                                            </a>
                                            <!-- Wizard navigation item 4-->
                                            <a class="nav-item nav-link" id="step4-tab" href="#step4" data-bs-toggle="tab" role="tab" aria-controls="step4" aria-selected="false">
                                                <div class="wizard-step-icon">4</div>
                                                <div class="wizard-step-text">
                                                    <div class="wizard-step-text-name">Images</div>
                                                    <div class="wizard-step-text-details">Main photo &amp; gallery</div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- Single form wraps the whole wizard so one submit posts every step's fields together-->
                                        <form id="addProductForm" action="add-product-handler.php" method="POST" enctype="multipart/form-data" novalidate>
                                            <input type="hidden" name="csrf_token" value="<?= Utils::getCSRFToken(); ?>">
                                            <div class="tab-content" id="productWizardTabContent">
                                                <!-- ============================================= -->
                                                <!-- STEP 1: BASIC INFORMATION                     -->
                                                <!-- ============================================= -->
                                                <div class="tab-pane py-5 py-xl-8 fade show active" id="step1" role="tabpanel" aria-labelledby="step1-tab">
                                                    <div class="row justify-content-center">
                                                        <div class="col-xxl-9 col-xl-10">
                                                            <h3 class="text-primary">Step 1</h3>
                                                            <h5 class="card-title mb-4">Enter the basic product details</h5>

                                                            <div class="mb-3">
                                                                <label class="small mb-1" for="inputProductName">Product Name</label>
                                                                <input class="form-control" id="inputProductName" name="product_name" type="text" placeholder="e.g. Wireless Mechanical Keyboard" required />
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="small mb-1" for="inputShortDescription">Short Description</label>
                                                                <input class="form-control" id="inputShortDescription" name="short_description" type="text" maxlength="255" placeholder="A one-line summary shown in product listings" />
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="small mb-1" for="inputFullDescription">Full Description</label>
                                                                <textarea class="form-control" id="inputFullDescription" name="full_description" rows="5" placeholder="Detailed product description, features, specifications..."></textarea>
                                                            </div>

                                                            <div class="row gx-3">
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="small mb-1" for="selectCategory">Category</label>
                                                                    <select class="form-select" id="selectCategory" name="category_id" required>
                                                                        <option value="" selected disabled>Select a category...</option>
                                                                        <option value="1">Electronics</option>
                                                                        <option value="2">Apparel</option>
                                                                        <option value="3">Home &amp; Kitchen</option>
                                                                        <option value="4">Beauty &amp; Personal Care</option>
                                                                        <option value="5">Sports &amp; Outdoors</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="small mb-1" for="inputBrand">Brand</label>
                                                                    <input class="form-control" id="inputBrand" name="brand" type="text" placeholder="Enter the brand name" />
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="small mb-1 d-block">Status</label>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" id="statusActive" name="status" type="radio" value="active" checked />
                                                                    <label class="form-check-label" for="statusActive">Active</label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" id="statusInactive" name="status" type="radio" value="inactive" />
                                                                    <label class="form-check-label" for="statusInactive">Inactive</label>
                                                                </div>
                                                            </div>

                                                            <hr class="my-4" />
                                                            <div class="d-flex justify-content-between">
                                                                <button class="btn btn-light" type="button" disabled>Previous</button>
                                                                <button class="btn btn-primary btn-wizard-next" type="button" data-next="#step2-tab">Next</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- ============================================= -->
                                                <!-- STEP 2: PRICING                                -->
                                                <!-- ============================================= -->
                                                <div class="tab-pane py-5 py-xl-8 fade" id="step2" role="tabpanel" aria-labelledby="step2-tab">
                                                    <div class="row justify-content-center">
                                                        <div class="col-xxl-9 col-xl-10">
                                                            <h3 class="text-primary">Step 2</h3>
                                                            <h5 class="card-title mb-4">Set up pricing and discounts</h5>

                                                            <div class="row gx-3">
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="small mb-1" for="inputCostPrice">Cost Price</label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">$</span>
                                                                        <input class="form-control" id="inputCostPrice" name="cost_price" type="number" min="0" step="0.01" placeholder="0.00" required />
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="small mb-1" for="inputSellingPrice">Selling Price</label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">$</span>
                                                                        <input class="form-control" id="inputSellingPrice" name="selling_price" type="number" min="0" step="0.01" placeholder="0.00" required />
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row gx-3">
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="small mb-1" for="selectDiscountType">Discount Type</label>
                                                                    <select class="form-select" id="selectDiscountType" name="discount_type">
                                                                        <option value="" selected>No discount</option>
                                                                        <option value="percentage">Percentage</option>
                                                                        <option value="fixed">Fixed Amount</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="small mb-1" for="inputDiscountValue">Discount Value</label>
                                                                    <input class="form-control" id="inputDiscountValue" name="discount_value" type="number" min="0" step="0.01" placeholder="0.00" disabled />
                                                                </div>
                                                            </div>

                                                            <hr class="my-4" />
                                                            <div class="d-flex justify-content-between">
                                                                <button class="btn btn-light btn-wizard-prev" type="button" data-prev="#step1-tab">Previous</button>
                                                                <button class="btn btn-primary btn-wizard-next" type="button" data-next="#step3-tab">Next</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- ============================================= -->
                                                <!-- STEP 3: INVENTORY                              -->
                                                <!-- ============================================= -->
                                                <div class="tab-pane py-5 py-xl-8 fade" id="step3" role="tabpanel" aria-labelledby="step3-tab">
                                                    <div class="row justify-content-center">
                                                        <div class="col-xxl-9 col-xl-10">
                                                            <h3 class="text-primary">Step 3</h3>
                                                            <h5 class="card-title mb-4">Manage stock and alert thresholds</h5>

                                                            <div class="row gx-3">
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="small mb-1" for="inputStockQuantity">Stock Quantity</label>
                                                                    <input class="form-control" id="inputStockQuantity" name="stock_quantity" type="number" min="0" step="1" placeholder="0" required />
                                                                </div>
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="small mb-1" for="inputLowStockThreshold">Low Stock Alert Threshold</label>
                                                                    <input class="form-control" id="inputLowStockThreshold" name="low_stock_threshold" type="number" min="0" step="1" placeholder="e.g. 5" />
                                                                    <div class="form-text">You'll be notified when stock falls at or below this number.</div>
                                                                </div>
                                                            </div>

                                                            <hr class="my-4" />
                                                            <div class="d-flex justify-content-between">
                                                                <button class="btn btn-light btn-wizard-prev" type="button" data-prev="#step2-tab">Previous</button>
                                                                <button class="btn btn-primary btn-wizard-next" type="button" data-next="#step4-tab">Next</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- ============================================= -->
                                                <!-- STEP 4: IMAGES                                 -->
                                                <!-- ============================================= -->
                                                <div class="tab-pane py-5 py-xl-8 fade" id="step4" role="tabpanel" aria-labelledby="step4-tab">
                                                    <div class="row justify-content-center">
                                                        <div class="col-xxl-9 col-xl-10">
                                                            <h3 class="text-primary">Step 4</h3>
                                                            <h5 class="card-title mb-4">Upload product images</h5>

                                                            <div class="mb-3">
                                                                <label class="small mb-1" for="inputMainImage">Main Image</label>
                                                                <input class="form-control" id="inputMainImage" name="main_image" type="file" accept="image/*" />
                                                                <div class="form-text">This is the primary image shown in listings and search results.</div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="small mb-1" for="inputImageGallery">Image Gallery</label>
                                                                <input class="form-control" id="inputImageGallery" name="gallery_images[]" type="file" accept="image/*" multiple />
                                                                <div class="form-text">You can select multiple additional images to showcase the product.</div>
                                                            </div>

                                                            <hr class="my-4" />
                                                            <div class="d-flex justify-content-between">
                                                                <button class="btn btn-light btn-wizard-prev" type="button" data-prev="#step3-tab">Previous</button>
                                                                <button class="btn btn-primary" type="submit">Submit Product</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
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
        <script src="js/wizard.js"></script>
                <script>
                // Enable/disable the Discount Value field based on Discount Type.
                var discountType = document.getElementById('selectDiscountType');
                var discountValue = document.getElementById('inputDiscountValue');
                if (discountType && discountValue) {
                    discountType.addEventListener('change', function () {
                        var hasDiscount = discountType.value !== '';
                        discountValue.disabled = !hasDiscount;
                        if (!hasDiscount) {
                            discountValue.value = '';
                        }
                    });
                }
            
        </script>

    </body>
</html>
