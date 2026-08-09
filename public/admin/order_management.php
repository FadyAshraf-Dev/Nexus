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
										<div class="page-header-icon"><i data-feather="shopping-bag"></i></div>
										Orders
									</h1>
									<div class="page-header-subtitle">View and update customer orders</div>
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
								<i data-feather="shopping-bag" class="me-2"></i>
								Order Management
							</div>

						</div>

						<div class="card-body">
							<!-- Orders Toolbar -->
							<div class="orders-toolbar d-flex align-items-center gap-2 mb-4">

								<select id="statusFilter" class="form-select" style="max-width: 200px;">
									<option value="">All statuses</option>
									<option value="pending">Pending</option>
									<option value="confirmed">Confirmed</option>
									<option value="processing">Processing</option>
									<option value="shipped">Shipped</option>
									<option value="delivered">Delivered</option>
									<option value="cancelled">Cancelled</option>
								</select>

							</div>
							<div class="table-responsive">

								<table id="ordersTable" class="table table-hover align-middle">

									<thead>

										<tr>

											<th>Order</th>

											<th>Customer</th>

											<th>Total</th>

											<th>Placed</th>

											<th>Status</th>

											<th class="text-end">
												Update Status
											</th>

										</tr>

									</thead>

									<tbody id="ordersTableBody">

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
	<script type="module" src="<?= Asset::admin('js/pages/orders/OrderManagement.js') ?>"></script>
</body>

</html>