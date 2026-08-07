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
										<div class="page-header-icon"><i data-feather="star"></i></div>
										Reviews
									</h1>
									<div class="page-header-subtitle">Moderate customer reviews across all products</div>
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
								<i data-feather="star" class="me-2"></i>
								Review Management
							</div>

						</div>

						<div class="card-body">
							<!-- Reviews Toolbar -->
							<div class="reviews-toolbar d-flex align-items-center gap-2 mb-4">

								<div class="input-group flex-grow-1">

									<span class="input-group-text">
										<i data-feather="search"></i>
									</span>

									<input type="search" id="search" class="form-control"
										placeholder="Search by reviewer username or email..." autocomplete="off">

								</div>

								<select id="minRateFilter" class="form-select" style="max-width: 160px;">
									<option value="">Any rating</option>
									<option value="9">9+ / 10</option>
									<option value="7">7+ / 10</option>
									<option value="5">5+ / 10</option>
									<option value="3">3+ / 10</option>
								</select>

								<select id="statusFilter" class="form-select" style="max-width: 160px;">
									<option value="">All statuses</option>
									<option value="active">Active</option>
									<option value="hidden">Hidden</option>
								</select>

							</div>
							<div class="table-responsive">

								<table id="reviewsTable" class="table table-hover align-middle">

									<thead>

										<tr>

											<th>Reviewer</th>

											<th>Product</th>

											<th>Rating</th>

											<th>Review</th>

											<th>Submitted</th>

											<th>Status</th>

											<th class="text-end">
												Actions
											</th>

										</tr>

									</thead>

									<tbody id="reviewsTableBody">

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
	<script type="module" src="<?= Asset::admin('js/pages/reviews/ReviewManagement.js') ?>"></script>
</body>

</html>