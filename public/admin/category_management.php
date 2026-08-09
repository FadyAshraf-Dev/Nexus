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
										<div class="page-header-icon"><i data-feather="folder"></i></div>
										Categories
									</h1>
									<div class="page-header-subtitle">Manage product categories</div>
								</div>
							</div>
						</div>
					</div>
				</header>
				<!-- Main page content-->
				<div class="container-xl px-4 mt-n10">

					<div class="card mb-4">

						<div class="card-header">
							<i data-feather="folder" class="me-2"></i>
							Category Management
						</div>

						<div class="card-body">
							<div class="table-responsive">

								<table id="categoriesTable" class="table table-hover align-middle">

									<thead>

										<tr>

											<th>Name</th>

											<th>Slug</th>

											<th class="text-end">Actions</th>

										</tr>

									</thead>

									<tbody id="categoriesTableBody">

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
	<script type="module" src="<?= Asset::admin('js/pages/categories/CategoryManagement.js') ?>"></script>
</body>

</html>