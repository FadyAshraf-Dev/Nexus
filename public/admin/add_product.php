<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/bootstrap/bootstrap.php';
Gatekeeper::authorize([Role::VENDOR]);
$errors = Errors::all();
$old = Old::all();
Errors::clear();
Old::clear();

function old(string $key, mixed $default = ''): mixed
{
   return Html::escape($GLOBALS['old'][$key] ?? $default);
}

function error(string $key): string
{
   if (!isset($GLOBALS['errors'][$key])) {
      return '';
   }

   return '<div class="text-danger small mt-1">'
      . Html::escape($GLOBALS['errors'][$key])
      . '</div>';
}
$userId = Gatekeeper::id();
$userRole = Gatekeeper::roleId();
require 'includes/head.php';
$pdo = Database::connection();

$categoryRepository = new CategoryRepository($pdo);

$categories = $categoryRepository->findAll();
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
                              <div class="page-header-icon"><i data-feather="arrow-right-circle"></i></div>
                              Wizard
                           </h1>
                           <div class="page-header-subtitle">Wizard examples for step-by-step form submission
                              content to use as part of an application</div>
                        </div>
                     </div>
                  </div>
               </div>
            </header>
            <!-- Main page content-->
            <div class="container-xl px-4 mt-n10">
               <!-- Rule #2: widened grid wrapper (col-xl-10) instead of the template's narrower default-->
               <div class="row justify-content-center">
                  <div class="col-xl-10 col-lg-12">
                     <!-- Wizard card example with navigation-->
                     <div class="card">
                        <div class="card-header border-bottom">
                           <!-- Wizard navigation-->
                           <div class="nav nav-pills nav-justified flex-column flex-xl-row nav-wizard"
                              id="productWizardTab" role="tablist">
                              <!-- Wizard navigation item 1-->
                              <a class="nav-item nav-link active" id="step1-tab" href="#step1" data-bs-toggle="tab"
                                 role="tab" aria-controls="step1" aria-selected="true">
                                 <div class="wizard-step-icon">1</div>
                                 <div class="wizard-step-text">
                                    <div class="wizard-step-text-name">Basic Information</div>
                                    <div class="wizard-step-text-details">Name, description &amp; category
                                    </div>
                                 </div>
                              </a>
                              <!-- Wizard navigation item 2-->
                              <a class="nav-item nav-link" id="step2-tab" href="#step2" data-bs-toggle="tab" role="tab"
                                 aria-controls="step2" aria-selected="false">
                                 <div class="wizard-step-icon">2</div>
                                 <div class="wizard-step-text">
                                    <div class="wizard-step-text-name">Pricing</div>
                                    <div class="wizard-step-text-details">Cost, price &amp; discounts</div>
                                 </div>
                              </a>
                              <!-- Wizard navigation item 3-->
                              <a class="nav-item nav-link" id="step3-tab" href="#step3" data-bs-toggle="tab" role="tab"
                                 aria-controls="step3" aria-selected="false">
                                 <div class="wizard-step-icon">3</div>
                                 <div class="wizard-step-text">
                                    <div class="wizard-step-text-name">Inventory</div>
                                    <div class="wizard-step-text-details">Stock &amp; alert thresholds</div>
                                 </div>
                              </a>
                              <!-- Wizard navigation item 4-->
                              <a class="nav-item nav-link" id="step4-tab" href="#step4" data-bs-toggle="tab" role="tab"
                                 aria-controls="step4" aria-selected="false">
                                 <div class="wizard-step-icon">4</div>
                                 <div class="wizard-step-text">
                                    <div class="wizard-step-text-name">Images</div>
                                    <div class="wizard-step-text-details">Main photo &amp; gallery</div>
                                 </div>
                              </a>
                           </div>
                        </div>
                        <div class="card-body">
                           <!-- Single form wraps the whole wizard so one submit posts every step's fields together-->
                           <form id="addProductForm" action="src/actions/products/insert.php" method="POST"
                              enctype="multipart/form-data" novalidate>
                              <input type="hidden" name="csrf_token" value="<?= CSRF::token(); ?>">
                              <div class="tab-content" id="productWizardTabContent">
                                 <!-- ============================================= -->
                                 <!-- STEP 1: BASIC INFORMATION                     -->
                                 <!-- ============================================= -->
                                 <div class="tab-pane py-5 py-xl-8 fade show active" id="step1" role="tabpanel"
                                    aria-labelledby="step1-tab">
                                    <div class="row justify-content-center">
                                       <div class="col-xxl-9 col-xl-10">
                                          <h3 class="text-primary">Step 1</h3>
                                          <h5 class="card-title mb-4">Enter the basic product details</h5>

                                          <div class="mb-3">
                                             <label class="small mb-1" for="inputProductName">Product
                                                Name</label>
                                             <input
                                                class="form-control <?= isset($errors['product_name']) ? ' is-invalid' : '' ?>"
                                                id="inputProductName" name="product_name" type="text"
                                                value="<?= old('product_name') ?>"
                                                placeholder="e.g. Wireless Mechanical Keyboard" minlength="3"
                                                maxlength="150" required />
                                             <?= error('product_name') ?>
                                          </div>

                                          <div class="mb-3">
                                             <label class="small mb-1" for="inputShortDescription">Short
                                                Description</label>
                                             <input
                                                class="form-control <?= isset($errors['short_description']) ? ' is-invalid' : '' ?>"
                                                id="inputShortDescription" name="short_description"
                                                value="<?= old('short_description') ?>" type="text" minlength="10"
                                                maxlength="170" required
                                                placeholder="A one-line summary shown in product listings" />
                                             <?= error('short_description') ?>
                                          </div>

                                          <div class="mb-3">
                                             <label class="small mb-1" for="inputFullDescription">Full
                                                Description</label>
                                             <textarea
                                                class="form-control <?= isset($errors['full_description']) ? ' is-invalid' : '' ?>"
                                                id="inputFullDescription" name="full_description" rows="5"
                                                placeholder="Detailed product description, features, specifications..."
                                                minlength="20" required><?= old('full_description') ?></textarea>
                                             <?= error('full_description') ?>
                                          </div>

                                          <div class="row gx-3">
                                             <div class="mb-3 col-md-6">
                                                <label class="small mb-1" for="selectCategory">Category</label>
                                                <select
                                                   class="form-select <?= isset($errors['category_id']) ? ' is-invalid' : '' ?>"
                                                   id="selectCategory" name="category_id" required>
                                                   <option value="" disabled <?= empty($old['category_id']) ? 'selected' : '' ?>>
                                                      Select a category...
                                                   </option>

                                                   <?php foreach ($categories as $category): ?>

                                                      <option value="<?= $category['id'] ?>" <?= (($old['category_id'] ?? '') == $category['id']) ? 'selected' : '' ?>>
                                                         <?= Html::escape($category['category_name']) ?>
                                                      </option>

                                                   <?php endforeach; ?>

                                                </select>

                                                <?= error('category_id') ?>
                                             </div>
                                             <div class="mb-3 col-md-6">
                                                <label class="small mb-1" for="inputBrand">Brand</label>
                                                <input
                                                   class="form-control <?= isset($errors['brand']) ? ' is-invalid' : '' ?>"
                                                   id="inputBrand" name="brand" type="text"
                                                   placeholder="Enter the brand name" maxlength="100" value="<?= old('brand') ?>" />
                                                <?= error('brand') ?>
                                             </div>
                                          </div>
                                          <div
                                             class="<?= Errors::has('status') ? 'border border-danger rounded p-2' : '' ?>">

                                             <div class="mb-3">
                                                <label class="small mb-1 d-block">Status</label>
                                                <?php
                                                $status = $old['status'] ?? 'active';
                                                ?>

                                                <div class="form-check form-check-inline">
                                                   <input class="form-check-input" id="statusActive" name="status"
                                                      type="radio" value="active" <?= $status === 'active' ? 'checked' : '' ?>>
                                                   <label class="form-check-label" for="statusActive">
                                                      Active
                                                   </label>
                                                </div>

                                                <div class="form-check form-check-inline">
                                                   <input class="form-check-input" id="statusInactive" name="status"
                                                      type="radio" value="inactive" <?= $status === 'inactive' ? 'checked' : '' ?>>
                                                   <label class="form-check-label" for="statusInactive">
                                                      Inactive
                                                   </label>
                                                </div>
                                             </div>
                                             <?= error('brand') ?>

                                          </div>

                                          <hr class="my-4" />
                                          <div class="d-flex justify-content-between">
                                             <button class="btn btn-light" type="button" disabled>Previous</button>
                                             <button class="btn btn-primary btn-wizard-next" type="button"
                                                data-next="#step2-tab">Next</button>
                                          </div>
                                       </div>
                                    </div>
                                 </div>

                                 <!-- ============================================= -->
                                 <!-- STEP 2: PRICING                                -->
                                 <!-- ============================================= -->
                                 <div class="tab-pane py-5 py-xl-8 fade" id="step2" role="tabpanel"
                                    aria-labelledby="step2-tab">
                                    <div class="row justify-content-center">
                                       <div class="col-xxl-9 col-xl-10">
                                          <h3 class="text-primary">Step 2</h3>
                                          <h5 class="card-title mb-4">Set up pricing and discounts</h5>

                                          <div class="row gx-3">
                                             <div class="mb-3 col-md-6">
                                                <label class="small mb-1" for="inputCostPrice">Cost
                                                   Price</label>
                                                <div class="input-group">
                                                   <span class="input-group-text">$</span>
                                                   <input
                                                      class="form-control <?= isset($errors['cost_price']) ? ' is-invalid' : '' ?>"
                                                      id="inputCostPrice" name="cost_price" type="number" min="0"
                                                      step="0.01" placeholder="0.00" required
                                                      value="<?= old('cost_price') ?>" />
                                                   </div>
                                                   <?= error('cost_price') ?>
                                             </div>
                                             <div class="mb-3 col-md-6">
                                                <label class="small mb-1" for="inputSellingPrice">Selling Price</label>
                                                <div class="input-group">
                                                   <span class="input-group-text">$</span>
                                                   <input
                                                      class="form-control <?= isset($errors['selling_price']) ? ' is-invalid' : '' ?>"
                                                      id="inputSellingPrice" name="selling_price" type="number" min="0"
                                                      step="0.01" placeholder="0.00" required
                                                      value="<?= old('selling_price') ?>" />
                                                   </div>
                                                   <?= error('selling_price') ?>
                                             </div>
                                          </div>

                                          <div class="row gx-3">
                                             <div class="mb-3 col-md-6">
                                                <label class="small mb-1" for="selectDiscountType">Discount Type</label>
                                                <select
                                                   class="form-select<?= isset($errors['discount_type']) ? ' is-invalid' : '' ?>"
                                                   id="selectDiscountType" name="discount_type">

                                                   <option value="" <?= empty($old['discount_type']) ? 'selected' : '' ?>>
                                                      No discount
                                                   </option>

                                                   <option value="relative" <?= ($old['discount_type'] ?? '') === 'relative' ? 'selected' : '' ?>>
                                                      Percentage (%)
                                                   </option>

                                                   <option value="fixed" <?= ($old['discount_type'] ?? '') === 'fixed' ? 'selected' : '' ?>>
                                                      Fixed Amount
                                                   </option>

                                                </select>

                                                <?= error('discount_type') ?>
                                             </div>
                                             <div class="mb-3 col-md-6">
                                                <label class="small mb-1" for="inputDiscountValue">Discount
                                                   Value</label>
                                                <input
                                                   class="form-control<?= isset($errors['discount_value']) ? ' is-invalid' : '' ?>"
                                                   id="inputDiscountValue" name="discount_value" type="number" min="0"
                                                   step="0.01" value="<?= Html::escape($old['discount_value'] ?? '') ?>"
                                                   placeholder="0.00" <?= empty($old['discount_type']) ? 'disabled' : '' ?>>

                                                <?= error('discount_value') ?>
                                             </div>
                                          </div>

                                          <hr class="my-4" />
                                          <div class="d-flex justify-content-between">
                                             <button class="btn btn-light btn-wizard-prev" type="button"
                                                data-prev="#step1-tab">Previous</button>
                                             <button class="btn btn-primary btn-wizard-next" type="button"
                                                data-next="#step3-tab">Next</button>
                                          </div>
                                       </div>
                                    </div>
                                 </div>

                                 <!-- ============================================= -->
                                 <!-- STEP 3: INVENTORY                              -->
                                 <!-- ============================================= -->
                                 <div class="tab-pane py-5 py-xl-8 fade" id="step3" role="tabpanel"
                                    aria-labelledby="step3-tab">
                                    <div class="row justify-content-center">
                                       <div class="col-xxl-9 col-xl-10">
                                          <h3 class="text-primary">Step 3</h3>
                                          <h5 class="card-title mb-4">Manage stock and alert thresholds
                                          </h5>

                                          <div class="row gx-3">
                                             <div class="mb-3 col-md-6">
                                                <label class="small mb-1" for="inputStockQuantity">Stock
                                                   Quantity</label>
                                                <input
                                                   class="form-control <?= isset($errors['stock_quantity']) ? ' is-invalid' : '' ?>"
                                                   id="inputStockQuantity" name="stock_quantity" type="number" min="0"
                                                   step="1" placeholder="0" required
                                                   value="<?= old('stock_quantity') ?>" />
                                                <?= error('stock_quantity') ?>
                                             </div>
                                             <div class="mb-3 col-md-6">
                                                <label class="small mb-1" for="inputLowStockThreshold">Low Stock Alert
                                                   Threshold</label>
                                                <input
                                                   class="form-control <?= isset($errors['low_stock_threshold']) ? ' is-invalid' : '' ?>"
                                                   id="inputLowStockThreshold" name="low_stock_threshold" type="number"
                                                   min="0" step="1" placeholder="e.g. 5"
                                                   value="<?= old('low_stock_threshold') ?>" />
                                                <?= error('low_stock_threshold') ?>
                                                <div class="form-text">You'll be notified when stock
                                                   falls at or below this number.</div>
                                             </div>
                                          </div>

                                          <hr class="my-4" />
                                          <div class="d-flex justify-content-between">
                                             <button class="btn btn-light btn-wizard-prev" type="button"
                                                data-prev="#step2-tab">Previous</button>
                                             <button class="btn btn-primary btn-wizard-next" type="button"
                                                data-next="#step4-tab">Next</button>
                                          </div>
                                       </div>
                                    </div>
                                 </div>

                                 <!-- ============================================= -->
                                 <!-- STEP 4: IMAGES                                 -->
                                 <!-- ============================================= -->
                                 <div class="tab-pane py-5 py-xl-8 fade" id="step4" role="tabpanel"
                                    aria-labelledby="step4-tab">
                                    <div class="row justify-content-center">
                                       <div class="col-xxl-9 col-xl-10">
                                          <h3 class="text-primary">Step 4</h3>
                                          <h5 class="card-title mb-4">Upload product images</h5>

                                          <div class="mb-3">
                                             <label class="small mb-1" for="inputImageGallery">Image
                                                Gallery</label>
                                             <input
                                                class="form-control <?= isset($errors['full_description']) ? ' is-invalid' : '' ?>"
                                                id="inputImageGallery" name="images[]" type="file" accept="image/*"
                                                multiple required />
                                             <div class="form-text">You can select multiple additional
                                                images to showcase the product.</div>
                                          </div>
                                          <?= error('images') ?>

                                          <hr class="my-4" />
                                          <div class="d-flex justify-content-between">
                                             <button class="btn btn-light btn-wizard-prev" type="button"
                                                data-prev="#step3-tab">Previous</button>
                                             <button class="btn btn-primary" type="submit">Submit
                                                Product</button>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
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

   <script src="<?= Asset::admin('js/shared/validation.js') ?>"></script>
   <script src="<?= Asset::admin('js/shared/wizard.js') ?>"></script>
   <script src="<?= Asset::admin('js/shared/discount.js') ?>"></script>
   <script src="<?= Asset::admin('js/shared/constraints.js') ?>"></script>
   <script src="<?= Asset::admin('js/shared/scroll.js') ?>"></script>
   <script src="<?= Asset::admin('js/shared/dom.js') ?>"></script>

   <script src="<?= Asset::admin('js/pages/add-product.js') ?>"></script>
   <script>

   </script>

</body>

</html>