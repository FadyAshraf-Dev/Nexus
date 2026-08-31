<?php

declare(strict_types=1);

require_once dirname(__DIR__, 1) . '/bootstrap/bootstrap.php';

Gatekeeper::authorize([Role::CUSTOMER, Role::VENDOR, Role::ADMIN]);

$pdo = Database::connection();
$userRepository = new UserRepository($pdo);
$user = $userRepository->findById(Session::id());
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

            <h3 class="my-orders__title">My Profile</h3>

            <div class="row">

                <!-- Account Info Card -->
                <div class="col-lg-6 mb-4">
                    <div class="my-orders__card">
                        <div class="profile__card-title">
                            <i class="icon_profile"></i>
                            Account Info
                        </div>

                        <?php if (!$user['email_verified_at']): ?>
                            <div class="profile__notice">
                                Your email is not verified. Changing your email will require re-verification.
                            </div>
                        <?php endif; ?>

                        <div id="profile-info-feedback" class="profile__feedback"></div>

                        <form id="profile-info-form" class="login__form profile__form">
                            <div class="login__group">
                                <label for="username"> Username </label>

                                <div class="login__input">
                                    <span class="icon_profile"></span>

                                    <input type="text" id="username" name="username"
                                        value="<?= htmlspecialchars($user['username'], ENT_QUOTES) ?>" required
                                        minlength="3" maxlength="50" pattern="[a-zA-Z0-9_]+" />
                                </div>
                                <div class="login__field-error" data-error-for="username"></div>
                            </div>

                            <div class="login__group">
                                <label for="email"> Email Address </label>

                                <div class="login__input">
                                    <span class="icon_mail_alt"></span>

                                    <input type="email" id="email" name="email"
                                        value="<?= htmlspecialchars($user['email'], ENT_QUOTES) ?>" required
                                        maxlength="100" />
                                </div>
                                <div class="login__field-error" data-error-for="email"></div>
                            </div>

                            <button type="submit" class="site-btn login__button">Save Changes</button>
                        </form>

                        <div class="profile__signout">
                            <form method="post" action="/api/auth/logout.php">
                                <input type="hidden" name="csrf_token" value="<?= CSRF::token(); ?>">
                                <button type="submit" class="profile__signout-btn">Sign Out</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Change Password Card -->
                <div class="col-lg-6 mb-4">
                    <div class="my-orders__card">
                        <div class="profile__card-title">
                            <i class="icon_lock"></i>
                            Change Password
                        </div>

                        <div id="profile-password-feedback" class="profile__feedback"></div>

                        <form id="profile-password-form" class="login__form profile__form">
                            <div class="login__group">
                                <label for="current_password"> Current Password </label>

                                <div class="login__input">
                                    <span class="icon_lock"></span>

                                    <input type="password" id="current_password" name="current_password" required />
                                </div>
                                <div class="login__field-error" data-error-for="current_password"></div>
                            </div>

                            <div class="login__group">
                                <label for="new_password"> New Password </label>

                                <div class="login__input">
                                    <span class="icon_lock"></span>

                                    <input type="password" id="new_password" name="new_password" required
                                        minlength="8" />
                                </div>
                                <div class="login__field-error" data-error-for="new_password"></div>
                            </div>

                            <div class="login__group">
                                <label for="new_password_confirmation"> Confirm New Password </label>

                                <div class="login__input">
                                    <span class="icon_lock"></span>

                                    <input type="password" id="new_password_confirmation"
                                        name="new_password_confirmation" required minlength="8" />
                                </div>
                                <div class="login__field-error" data-error-for="new_password_confirmation"></div>
                            </div>

                            <button type="submit" class="site-btn login__button">Update Password</button>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <?php include 'includes/search.php'; ?>

    <?php include 'includes/scripts.php'; ?>
    <script type="module" src="/admin/js/pages/storefront-profile/Profile.js"></script>

</body>

</html>
