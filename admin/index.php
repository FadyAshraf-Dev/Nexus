<?php
declare(strict_types=1);
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

require_once '../bootstrap/bootstrap.php';
Gatekeeper::authorize([
    Role::ADMIN,

    Role::VENDOR

]);

$userRole = Gatekeeper::roleId();

require 'includes/head.php';
?>
    <body class="nav-fixed">
        <?php require_once 'includes/navbar.php'; ?>
        <div id="layoutSidenav">
            <?php require_once 'includes/sidebar.php'; ?>
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
<?php require 'includes/footer.php'; ?>
            </div>
        </div>
        <?php require 'includes/scripts.php'; ?>
    </body>
</html>
