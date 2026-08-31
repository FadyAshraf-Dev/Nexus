<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/bootstrap/bootstrap.php';
Gatekeeper::authorize([Role::VENDOR, Role::ADMIN]);

$productId = filter_var(
    $_GET['id'] ?? null,
    FILTER_VALIDATE_INT
);

if (!$productId) {
    Response::redirectAdmin('index.php');
}

$pdo = Database::connection();

$productRepository = new ProductRepository($pdo);

$product = $productRepository->findById($productId);

if (!$product) {
    Response::redirectAdmin('index.php');
}

// Owner-only, no admin override - matches update.php's rule
// exactly. See update.php for why edit and delete diverge here.
if ((int) $product['vendor_id'] !== Session::id()) {
    Response::forbidden();
}

$imageService = new ImageService($pdo);

$currentImages = $imageService->findByProduct($productId);

$errors = Errors::all();
$old = Old::all();
Errors::clear();
Old::clear();

// Pre-fill from the product row, but let a just-submitted
// invalid form's Old values win - same precedence add_product.php
// uses, just with the product row as the fallback instead of
// an empty string.
function old(string $key, mixed $default = ''): mixed
{
   $value = $GLOBALS['old'][$key]
      ?? $GLOBALS['product'][$key]
      ?? $default;

   return Html::escape((string) $value);
}

function error(string $key): string
{
   $message = $GLOBALS['errors'][$key] ?? '';

   return '<div class="invalid-feedback">'
      . Html::escape($message)
      . '</div>';
}

$userId = Session::id();
$userRole = Session::roleId();
require 'includes/head.php';

$categoryRepository = new CategoryRepository($pdo);

$categories = $categoryRepository->findAll();

$selectedCategoryId = $old['category_id'] ?? $product['category_id'];
$selectedStatus = $old['status'] ?? $product['status'];
$selectedDiscountType = $old['discount_type'] ?? $product['discount_type'] ?? '';
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
                              <div class="page-header-icon"><i data-feather="edit"></i></div>
                              Edit Product
                           </h1>
                           <div class="page-header-subtitle">Update <?= Html::escape($product['product_name']) ?></div>
                        </div>
                     </div>
                  </div>
               </div>
            </header>
            <!-- Main page content-->
            <div class="container-xl px-4 mt-n10">
               <div class="row justify-content-center">
                  <div class="col-xl-10 col-lg-12">
                     <div class="card">
                        <div class="card-header border-bottom">
                           <div class="nav nav-pills nav-justified flex-column flex-xl-row nav-wizard"
                              id="productWizardTab" role="tablist">
                              <a class="nav-item nav-link active" id="step1-tab" href="#step1" data-bs-toggle="tab"
                                 role="tab" aria-controls="step1" aria-selected="true">
                                 <div class="wizard-step-icon">1</div>
                                 <div class="wizard-step-text">
                                    <div class="wizard-step-text-name">Basic Information</div>
                                    <div class="wizard-step-text-details">Name, description &amp; category
                                    </div>
                                 </div>
                              </a>
                              <a class="nav-item nav-link" id="step2-tab" href="#step2" data-bs-toggle="tab" role="tab"
                                 aria-controls="step2" aria-selected="false">
                                 <div class="wizard-step-icon">2</div>
                                 <div class="wizard-step-text">
                                    <div class="wizard-step-text-name">Pricing</div>
                                    <div class="wizard-step-text-details">Cost, price &amp; discounts</div>
                                 </div>
                              </a>
                              <a class="nav-item nav-link" id="step3-tab" href="#step3" data-bs-toggle="tab" role="tab"
                                 aria-controls="step3" aria-selected="false">
                                 <div class="wizard-step-icon">3</div>
                                 <div class="wizard-step-text">
                                    <div class="wizard-step-text-name">Inventory</div>
                                    <div class="wizard-step-text-details">Stock &amp; alert thresholds</div>
                                 </div>
                              </a>
                              <a class="nav-item nav-link" id="step4-tab" href="#step4" data-bs-toggle="tab" role="tab"
                                 aria-controls="step4" aria-selected="false">
                                 <div class="wizard-step-icon">4</div>
                                 <div class="wizard-step-text">
                                    <div class="wizard-step-text-name">Images</div>
                                    <div class="wizard-step-text-details">Replace the gallery (optional)</div>
                                 </div>
                              </a>
                           </div>
                        </div>
                        <div class="card-body">
                           <form id="editProductForm" action="api/product/update.php" method="POST"
                              enctype="multipart/form-data" novalidate>
                              <input type="hidden" name="csrf_token" value="<?= CSRF::token(); ?>">
                              <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                              <div class="tab-content" id="productWizardTabContent">
                                 <!-- STEP 1: BASIC INFORMATION -->
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
                                             <div class="form-text">
                                                Changing the name regenerates the product's URL slug automatically.
                                             </div>
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
                                                   <option value="" disabled <?= empty($selectedCategoryId) ? 'selected' : '' ?>>
                                                      Select a category...
                                                   </option>

                                                   <?php foreach ($categories as $category): ?>

                                                      <option value="<?= $category['id'] ?>" <?= ($selectedCategoryId == $category['id']) ? 'selected' : '' ?>>
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
                                                   placeholder="Enter the brand name" maxlength="100"
                                                   value="<?= old('brand') ?>" />
                                                <?= error('brand') ?>
                                             </div>
                                          </div>
                                          <div
                                             class="<?= Errors::has('status') ? 'border border-danger rounded p-2' : '' ?>">

                                             <div class="mb-3">
                                                <label class="small mb-1 d-block required">Status</label>

                                                <div class="form-check form-check-inline">
                                                   <input class="form-check-input" id="statusActive" name="status"
                                                      type="radio" value="active" <?= $selectedStatus === 'active' ? 'checked' : '' ?>>
                                                   <label class="form-check-label" for="statusActive">
                                                      Active
                                                   </label>
                                                </div>

                                                <div class="form-check form-check-inline">
                                                   <input class="form-check-input" id="statusInactive" name="status"
                                                      type="radio" value="inactive" <?= $selectedStatus === 'inactive' ? 'checked' : '' ?>>
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

                                 <!-- STEP 2: PRICING -->
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
                                                   <?= error('cost_price') ?>
                                                </div>
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
                                                   <?= error('selling_price') ?>
                                                </div>
                                             </div>
                                          </div>

                                          <div class="row gx-3">
                                             <div class="mb-3 col-md-6">
                                                <label class="small mb-1" for="selectDiscountType">Discount Type</label>
                                                <select
                                                   class="form-select<?= isset($errors['discount_type']) ? ' is-invalid' : '' ?>"
                                                   id="selectDiscountType" name="discount_type">

                                                   <option value="" <?= empty($selectedDiscountType) ? 'selected' : '' ?>>
                                                      No discount
                                                   </option>

                                                   <option value="relative" <?= $selectedDiscountType === 'relative' ? 'selected' : '' ?>>
                                                      Percentage (%)
                                                   </option>

                                                   <option value="fixed" <?= $selectedDiscountType === 'fixed' ? 'selected' : '' ?>>
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
                                                   step="0.01" value="<?= old('discount_value') ?>"
                                                   placeholder="0.00" <?= empty($selectedDiscountType) ? 'disabled' : '' ?>>

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

                                 <!-- STEP 3: INVENTORY -->
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

                                 <!-- STEP 4: IMAGES -->
                                 <div class="tab-pane py-5 py-xl-8 fade" id="step4" role="tabpanel"
                                    aria-labelledby="step4-tab">
                                    <div class="row justify-content-center">
                                       <div class="col-xxl-9 col-xl-10">
                                          <h3 class="text-primary">Step 4</h3>
                                          <h5 class="card-title mb-4">Product images</h5>

                                          <?php if ($currentImages): ?>
                                             <div class="mb-3">
                                                <label class="small mb-1 d-block">Current Images</label>
                                                <div class="row g-3">
                                                   <?php foreach ($currentImages as $image): ?>
                                                      <div class="col-auto">
                                                         <img src="<?= Html::escape($image['image_path']) ?>"
                                                            alt="<?= Html::escape($product['product_name']) ?>"
                                                            width="80" height="80" class="rounded"
                                                            style="object-fit:cover" />
                                                      </div>
                                                   <?php endforeach; ?>
                                                </div>
                                                <div class="form-text">
                                                   These are the images currently live on the product. Uploading new
                                                   images below replaces the entire gallery - leave the field empty to
                                                   keep these as they are.
                                                </div>
                                             </div>
                                             <hr class="my-4" />
                                          <?php endif; ?>

                                          <div class="mb-3">

                                             <label class="small mb-1" for="inputImageGallery">
                                                Replace Gallery (optional)
                                             </label>

                                             <input
                                                class="form-control <?= isset($errors['images']) ? 'is-invalid' : '' ?>"
                                                id="inputImageGallery" name="images[]" type="file" accept="image/*"
                                                multiple />

                                             <div class="form-text">
                                                Leave empty to keep the current images. Otherwise, upload up to 5 -
                                                the first becomes the new thumbnail.
                                             </div>

                                             <?= error('images') ?>

                                             <div id="imagePreviewContainer" class="row g-3 mt-3">
                                             </div>

                                          </div>
                                          <hr class="my-4" />
                                          <div class="d-flex justify-content-between">
                                             <button class="btn btn-light btn-wizard-prev" type="button"
                                                data-prev="#step3-tab">Previous</button>
                                             <button class="btn btn-primary" type="submit">Save Changes</button>
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

   <script type="module" src="<?= Asset::admin('js/pages/products/EditProduct.js') ?>"></script>

</body>

</html>