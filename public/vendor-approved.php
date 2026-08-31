<?php

declare(strict_types=1);

require_once dirname(__DIR__, 1) . '/bootstrap/bootstrap.php';

Gatekeeper::authorize([Role::CUSTOMER, Role::VENDOR, Role::ADMIN]);

$pdo = Database::connection();
$userRepository = new UserRepository($pdo);
$user = $userRepository->findById(Session::id());

// The user may have been a CUSTOMER when their session started and
// only just got approved as a VENDOR - refresh the session's
// cached role from the DB so Gatekeeper checks on the pages this
// links to (e.g. add_product.php) see the new role immediately,
// without needing a logout/login. Session::refreshRole() is a
// same-request no-op if the role hasn't actually changed.
if ($user !== null) {
    Session::refreshRole((int) $user['role_id']);
}

$isVendor = $user !== null && (int) $user['role_id'] === Role::VENDOR;
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

    <section class="login spad">
        <div class="container">
            <div class="login__wrapper">
                <div class="login__content">

                    <?php if ($isVendor): ?>

                        <div class="login__header">
                            <span class="login__subtitle"> Welcome aboard </span>

                            <h2>
                                Congratulations,<br />
                                you're a vendor!
                            </h2>

                            <p>
                                Your application was approved. You can now create and manage your own
                                product listings, and start selling on Nexus.
                            </p>
                        </div>

                        <a href="admin/index.php" class="site-btn login__button" style="display:inline-block; text-align:center; text-decoration:none;">
                            Go to Vendor Dashboard
                        </a>

                    <?php else: ?>

                        <div class="login__header">
                            <h2>Nothing to see here yet.</h2>
                            <p>
                                This page is for approved vendor applications. If you're expecting to see
                                this, check your application status on your account.
                            </p>
                        </div>

                        <div class="login__footer">
                            <a href="become-vendor.php"> View my application </a>
                        </div>

                    <?php endif; ?>

                </div>

                <div class="login__illustration">
                    <img src="img/login/login-illustration.png" alt="Nexus Login Illustration" />
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <?php include 'includes/search.php'; ?>

    <?php include 'includes/scripts.php'; ?>

</body>

</html>
