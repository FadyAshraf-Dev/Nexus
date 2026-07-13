<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/bootstrap/bootstrap.php';
Gatekeeper::authorize([Role::VENDOR]);
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
										<div class="page-header-icon"><i data-feather="filter"></i></div>
										Tables
									</h1>
									<div class="page-header-subtitle">An extension of the Simple DataTables library,
										customized for SB Admin Pro</div>
								</div>
							</div>
						</div>
					</div>
				</header>
				<!-- Main page content-->
				<div class="container-xl px-4 mt-n10">

					<div class="card mb-4">

						<div class="card-header d-flex justify-content-between align-items-center">

							<div>
								<i data-feather="package" class="me-2"></i>
								My Products
							</div>

							<a href="add_product.php" class="btn btn-primary">
								<i data-feather="plus"></i>
								Add Product
							</a>

						</div>

						<div class="card-body">

							<div class="table-responsive">

								<table id="productsTable" class="table table-hover align-middle">

									<thead>

										<tr>

											<th>Image</th>

											<th>Product</th>

											<th>Category</th>

											<th>Price</th>

											<th>Stock</th>

											<th>Status</th>

											<th>Discount</th>

											<th class="text-end">
												Actions
											</th>

										</tr>

									</thead>

									<tbody id="productsTableBody">

										<!-- Renderer inserts rows -->

									</tbody>

								</table>

							</div>

						</div>

					</div>

				</div>
			</main>
			<?php require 'includes/footer.php'; ?>
		</div>
	</div>
	<?php require 'includes/scripts.php'; ?>
	<script src="<?= Asset::admin('js/core/Ajax.js') ?>"></script>
	<script src="<?= Asset::admin('js/core/Renderer.js') ?>"></script>
	<script src="<?= Asset::admin('js/pages/products/my-products.js') ?>"></script>
</body>

</html>