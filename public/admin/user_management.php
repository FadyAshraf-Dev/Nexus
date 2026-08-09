<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/bootstrap/bootstrap.php';
Gatekeeper::authorize([Role::ADMIN]);
require 'includes/head.php';
$userRole = Session::roleId();

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
										<div class="page-header-icon"><i data-feather="users"></i></div>
										Users
									</h1>
									<div class="page-header-subtitle">Manage all platform users</div>
								</div>
							</div>
						</div>
					</div>
				</header>
				<!-- Main page content-->
				<div class="container-xl px-4 mt-n10">

					<div class="card mb-4">

						<div class="card-header">
							<i data-feather="users" class="me-2"></i>
							User Management
						</div>

						<div class="card-body">
							<!-- Users Toolbar -->
							<div class="users-toolbar d-flex align-items-center gap-2 mb-4">

								<select id="roleFilter" class="form-select" style="max-width: 200px;">
									<option value="">All roles</option>
									<option value="1">Customer</option>
									<option value="2">Vendor</option>
									<option value="3">Admin</option>
								</select>

								<input
									type="text"
									id="searchFilter"
									class="form-control"
									style="max-width: 260px;"
									placeholder="Search username or email..."
								>

							</div>
							<div class="table-responsive">

								<table id="usersTable" class="table table-hover align-middle">

									<thead>

										<tr>

											<th>Username</th>

											<th>Email</th>

											<th>Role</th>

											<th class="text-end">Actions</th>

										</tr>

									</thead>

									<tbody id="usersTableBody">

										<!-- Renderer inserts rows -->

									</tbody>

								</table>

							</div>

							<div id="paginationContainer" class="mt-4">

								<!-- PaginationTemplate -->

							</div>
						</div>

					</div>

				</div>
			</main>
			<?php require 'includes/footer.php'; ?>
		</div>
	</div>
	<?php require 'includes/scripts.php'; ?>
	<script type="module" src="<?= Asset::admin('js/pages/users/UserManagement.js') ?>"></script>
</body>

</html>