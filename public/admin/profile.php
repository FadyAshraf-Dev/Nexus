<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/bootstrap/bootstrap.php';
Gatekeeper::authorize([Role::ADMIN, Role::VENDOR]);
require 'includes/head.php';
$userRole = Session::roleId();

$pdo = Database::connection();
$userRepository = new UserRepository($pdo);
$user = $userRepository->findById(Session::id());

?>

<body class="nav-fixed">
	<?php require 'includes/navbar.php'; ?>
	<div id="layoutSidenav">
		<?php require 'includes/sidebar.php'; ?>
		<div id="layoutSidenav_content">
			<main>
				<header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
					<div class="container-xl px-4">
						<div class="page-header-content pt-4">
							<div class="row align-items-center justify-content-between">
								<div class="col-auto mt-4">
									<h1 class="page-header-title">
										<div class="page-header-icon"><i data-feather="user"></i></div>
										My Profile
									</h1>
									<div class="page-header-subtitle">Update your account info and password</div>
								</div>
							</div>
						</div>
					</div>
				</header>

				<div class="container-xl px-4 mt-n10">

					<div class="row">
						<div class="col-lg-6">
							<div class="card mb-4">
								<div class="card-header">
									<i data-feather="user" class="me-2"></i>
									Account Info
								</div>
								<div class="card-body">

									<?php if (!$user['email_verified_at']): ?>
										<div class="alert alert-warning">
											Your email is not verified. Changing your email will require re-verification.
										</div>
									<?php endif; ?>

									<div id="profile-info-feedback" class="mb-3"></div>

									<form id="profile-info-form">
										<input type="hidden" name="csrf_token" value="<?= CSRF::token(); ?>">

										<div class="mb-3">
											<label class="small mb-1" for="username">Username</label>
											<input type="text" class="form-control" id="username" name="username"
												value="<?= htmlspecialchars($user['username'], ENT_QUOTES) ?>" required
												minlength="3" maxlength="50" pattern="[a-zA-Z0-9_]+">
										</div>

										<div class="mb-3">
											<label class="small mb-1" for="email">Email Address</label>
											<input type="email" class="form-control" id="email" name="email"
												value="<?= htmlspecialchars($user['email'], ENT_QUOTES) ?>" required
												maxlength="100">
										</div>

										<button type="submit" class="btn btn-primary">Save Changes</button>
									</form>

								</div>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="card mb-4">
								<div class="card-header">
									<i data-feather="lock" class="me-2"></i>
									Change Password
								</div>
								<div class="card-body">

									<div id="profile-password-feedback" class="mb-3"></div>

									<form id="profile-password-form">
										<input type="hidden" name="csrf_token" value="<?= CSRF::token(); ?>">

										<div class="mb-3">
											<label class="small mb-1" for="current_password">Current Password</label>
											<input type="password" class="form-control" id="current_password"
												name="current_password" required>
										</div>

										<div class="mb-3">
											<label class="small mb-1" for="new_password">New Password</label>
											<input type="password" class="form-control" id="new_password"
												name="new_password" required minlength="8">
										</div>

										<div class="mb-3">
											<label class="small mb-1" for="new_password_confirmation">Confirm New
												Password</label>
											<input type="password" class="form-control" id="new_password_confirmation"
												name="new_password_confirmation" required minlength="8">
										</div>

										<button type="submit" class="btn btn-primary">Update Password</button>
									</form>

								</div>
							</div>
						</div>
					</div>

				</div>
			</main>
			<?php require 'includes/footer.php'; ?>
		</div>
	</div>
	<?php require 'includes/scripts.php'; ?>
	<script type="module" src="<?= Asset::admin('js/pages/profile/Profile.js') ?>"></script>

</body>

</html>