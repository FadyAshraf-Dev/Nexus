<?php

declare(strict_types=1);

require_once dirname(__DIR__, 1) . '/bootstrap/bootstrap.php';

Gatekeeper::authorize([Role::CUSTOMER]);

$pdo = Database::connection();
$vendorRequestRepository = new VendorRequestRepository($pdo);
$pendingRequest = $vendorRequestRepository->findPendingForUser(Session::id());
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

            <h3 class="my-orders__title">Become a Vendor</h3>

            <div class="row">
                <div class="col-lg-8">

                    <?php if ($pendingRequest): ?>

                        <div class="my-orders__card">
                            <div class="profile__card-title">
                                <i class="icon_clock"></i>
                                Application Pending Review
                            </div>

                            <p>
                                Your application for <strong><?= htmlspecialchars($pendingRequest['store_name'], ENT_QUOTES) ?></strong>
                                was submitted on
                                <?= date('F j, Y', strtotime($pendingRequest['created_at'])) ?>
                                and is awaiting admin review. We'll email you once it's decided.
                            </p>
                        </div>

                    <?php else: ?>

                        <div class="my-orders__card">
                            <div class="profile__card-title">
                                <i class="icon_bag"></i>
                                Vendor Application
                            </div>

                            <p>
                                Tell us about your business. Applications are reviewed by our team,
                                and you'll be notified by email once a decision is made.
                            </p>

                            <div id="vendor-request-feedback" class="profile__feedback"></div>

                            <form id="vendor-request-form" class="login__form profile__form">
                                <div class="login__group">
                                    <label for="store_name"> Store Name </label>

                                    <div class="login__input">
                                        <span class="icon_bag"></span>

                                        <input type="text" id="store_name" name="store_name"
                                            placeholder="e.g. Nile Handmade Goods" required minlength="2"
                                            maxlength="100" />
                                    </div>
                                    <div class="login__field-error" data-error-for="store_name"></div>
                                </div>

                                <div class="login__group">
                                    <label for="description"> Tell us about what you'll sell </label>

                                    <div class="login__input login__input--textarea">
                                        <textarea id="description" name="description" rows="5"
                                            placeholder="What products or services will you offer? What makes your store worth approving?"
                                            required minlength="20" maxlength="2000"></textarea>
                                    </div>
                                    <div class="login__field-error" data-error-for="description"></div>
                                </div>

                                <div class="login__group">
                                    <label for="phone"> Contact Phone </label>

                                    <div class="login__input">
                                        <span class="icon_phone"></span>

                                        <input type="tel" id="phone" name="phone" placeholder="e.g. +20 100 123 4567"
                                            required minlength="6" maxlength="30" />
                                    </div>
                                    <div class="login__field-error" data-error-for="phone"></div>
                                </div>

                                <button type="submit" class="site-btn login__button">
                                    Submit Application
                                </button>
                            </form>
                        </div>

                    <?php endif; ?>

                </div>
            </div>

        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <?php include 'includes/search.php'; ?>

    <?php include 'includes/scripts.php'; ?>

    <?php if (!$pendingRequest): ?>
    <script type="module" src="/admin/js/pages/storefront-become-vendor/BecomeVendor.js"></script>
    <?php endif; ?>

</body>

</html>
