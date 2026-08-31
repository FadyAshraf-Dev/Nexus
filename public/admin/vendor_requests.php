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
										<div class="page-header-icon"><i data-feather="user-check"></i></div>
										Vendor Requests
									</h1>
									<div class="page-header-subtitle">Review applications to become a vendor</div>
								</div>
							</div>
						</div>
					</div>
				</header>
				<!-- Main page content-->
				<div class="container-xl px-4 mt-n10">

					<div class="card mb-4">

						<div class="card-header">
							<i data-feather="user-check" class="me-2"></i>
							Applications
						</div>

						<div class="card-body">

							<div class="vendor-requests-toolbar d-flex align-items-center gap-2 mb-4">

								<select id="statusFilter" class="form-select" style="max-width: 200px;">
									<option value="pending" selected>Pending</option>
									<option value="approved">Approved</option>
									<option value="rejected">Rejected</option>
									<option value="">All</option>
								</select>

							</div>

							<div class="table-responsive">

								<table id="vendorRequestsTable" class="table table-hover align-middle">

									<thead>
										<tr>
											<th>Store Name</th>
											<th>Applicant</th>
											<th>Phone</th>
											<th>Submitted</th>
											<th>Status</th>
											<th class="text-end">Actions</th>
										</tr>
									</thead>

									<tbody id="vendorRequestsTableBody">
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

	<!-- Reject Reason Modal -->
	<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<form id="rejectForm">
					<div class="modal-header">
						<h5 class="modal-title">Reject Vendor Request</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<p class="text-muted small">
							This reason will be included in the email sent to the applicant.
						</p>
						<label for="rejectReason" class="small mb-1">Reason for rejection</label>
						<textarea id="rejectReason" name="reason" class="form-control" rows="4" required
							minlength="5" maxlength="255"></textarea>
						<div id="rejectReasonError" class="text-danger small mt-1"></div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-danger">Reject Application</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<?php require 'includes/scripts.php'; ?>
	<script type="module" src="<?= Asset::admin('js/pages/vendor-requests/VendorRequests.js') ?>"></script>
</body>

</html>
