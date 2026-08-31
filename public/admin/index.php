<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap/bootstrap.php';

Gatekeeper::authorize([
    Role::ADMIN,
    Role::VENDOR,
]);

$userRole = Session::roleId();
$isAdmin = $userRole === Role::ADMIN;

$pdo = Database::connection();
$user = (new UserRepository($pdo))->findById(Session::id());

require 'includes/head.php';
?>
<body class="nav-fixed">
	<?php require_once 'includes/navbar.php'; ?>
	<div id="layoutSidenav">
		<?php require_once 'includes/sidebar.php'; ?>
		<div id="layoutSidenav_content">
			<main>
				<header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
					<div class="container-xl px-4">
						<div class="page-header-content pt-4">
							<div class="row align-items-center justify-content-between">
								<div class="col-auto mt-4">
									<h1 class="page-header-title">
										<div class="page-header-icon"><i data-feather="home"></i></div>
										Welcome back<?= $user ? ', ' . htmlspecialchars($user['username'], ENT_QUOTES) : '' ?>
									</h1>
									<div class="page-header-subtitle">
										<?= $isAdmin
											? "Here's everything you can manage as an admin."
											: "Here's everything you can manage as a vendor." ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</header>

				<div class="container-xl px-4 mt-n10">

					<?php if ($isAdmin): ?>

						<div class="card mb-4">
							<div class="card-body">
								<h2 class="h4 mb-1">Marketplace</h2>
								<p class="text-muted mb-0">Manage the products, categories, and orders customers see.</p>
							</div>
						</div>
						<div class="row">

							<div class="col-md-6 col-xl-4 mb-4">
								<a href="product_management.php" class="card lift h-100 text-decoration-none">
									<div class="card-body d-flex align-items-start">
										<i data-feather="shopping-bag" class="text-primary me-3"></i>
										<div>
											<div class="fw-bold">Product Management</div>
											<div class="text-muted small">Review and edit every vendor's listings.</div>
										</div>
									</div>
								</a>
							</div>

							<div class="col-md-6 col-xl-4 mb-4">
								<a href="category_management.php" class="card lift h-100 text-decoration-none">
									<div class="card-body d-flex align-items-start">
										<i data-feather="layers" class="text-primary me-3"></i>
										<div>
											<div class="fw-bold">Category Management</div>
											<div class="text-muted small">Organize how products are grouped and browsed.</div>
										</div>
									</div>
								</a>
							</div>

							<div class="col-md-6 col-xl-4 mb-4">
								<a href="coupon_management.php" class="card lift h-100 text-decoration-none">
									<div class="card-body d-flex align-items-start">
										<i data-feather="tag" class="text-primary me-3"></i>
										<div>
											<div class="fw-bold">Coupon Management</div>
											<div class="text-muted small">Create and manage storewide discounts.</div>
										</div>
									</div>
								</a>
							</div>

							<div class="col-md-6 col-xl-4 mb-4">
								<a href="order_management.php" class="card lift h-100 text-decoration-none">
									<div class="card-body d-flex align-items-start">
										<i data-feather="shopping-cart" class="text-primary me-3"></i>
										<div>
											<div class="fw-bold">Order Management</div>
											<div class="text-muted small">Track and manage every order placed storewide.</div>
										</div>
									</div>
								</a>
							</div>

							<div class="col-md-6 col-xl-4 mb-4">
								<a href="review_management.php" class="card lift h-100 text-decoration-none">
									<div class="card-body d-flex align-items-start">
										<i data-feather="message-square" class="text-primary me-3"></i>
										<div>
											<div class="fw-bold">Review Management</div>
											<div class="text-muted small">Moderate customer reviews left on products.</div>
										</div>
									</div>
								</a>
							</div>

						</div>

						<h2 class="mt-4 mb-0">People</h2>
						<p>Manage accounts and who's allowed to sell.</p>
						<hr class="mt-0 mb-4" />
						<div class="row">

							<div class="col-md-6 col-xl-4 mb-4">
								<a href="user_management.php" class="card lift h-100 text-decoration-none">
									<div class="card-body d-flex align-items-start">
										<i data-feather="users" class="text-primary me-3"></i>
										<div>
											<div class="fw-bold">User Management</div>
											<div class="text-muted small">Browse and manage every registered account.</div>
										</div>
									</div>
								</a>
							</div>

							<div class="col-md-6 col-xl-4 mb-4">
								<a href="vendor_requests.php" class="card lift h-100 text-decoration-none">
									<div class="card-body d-flex align-items-start">
										<i data-feather="user-check" class="text-primary me-3"></i>
										<div>
											<div class="fw-bold">Vendor Requests</div>
											<div class="text-muted small">Review and approve applications to become a vendor.</div>
										</div>
									</div>
								</a>
							</div>

							<div class="col-md-6 col-xl-4 mb-4">
								<a href="profile.php" class="card lift h-100 text-decoration-none">
									<div class="card-body d-flex align-items-start">
										<i data-feather="settings" class="text-primary me-3"></i>
										<div>
											<div class="fw-bold">My Profile</div>
											<div class="text-muted small">Update your own account info and password.</div>
										</div>
									</div>
								</a>
							</div>

						</div>

					<?php else: ?>

						<div class="card mb-4">
							<div class="card-body">
								<h2 class="h4 mb-1">Your Store</h2>
								<p class="text-muted mb-0">Manage the products you sell on Nexus.</p>
							</div>
						</div>
						<div class="row">

							<div class="col-md-6 col-xl-4 mb-4">
								<a href="my_products.php" class="card lift h-100 text-decoration-none">
									<div class="card-body d-flex align-items-start">
										<i data-feather="box" class="text-primary me-3"></i>
										<div>
											<div class="fw-bold">My Products</div>
											<div class="text-muted small">View, edit, and manage your product listings.</div>
										</div>
									</div>
								</a>
							</div>

							<div class="col-md-6 col-xl-4 mb-4">
								<a href="add_product.php" class="card lift h-100 text-decoration-none">
									<div class="card-body d-flex align-items-start">
										<i data-feather="plus-circle" class="text-primary me-3"></i>
										<div>
											<div class="fw-bold">Add a Product</div>
											<div class="text-muted small">List a new item for sale.</div>
										</div>
									</div>
								</a>
							</div>

							<div class="col-md-6 col-xl-4 mb-4">
								<a href="profile.php" class="card lift h-100 text-decoration-none">
									<div class="card-body d-flex align-items-start">
										<i data-feather="settings" class="text-primary me-3"></i>
										<div>
											<div class="fw-bold">My Profile</div>
											<div class="text-muted small">Update your own account info and password.</div>
										</div>
									</div>
								</a>
							</div>

						</div>

					<?php endif; ?>

				</div>
			</main>
			<?php require 'includes/footer.php'; ?>
		</div>
	</div>
	<?php require 'includes/scripts.php'; ?>
</body>

</html>
