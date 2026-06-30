const ELEMENTS = {
  form: "addProductForm",

  costPrice: "inputCostPrice",
  sellingPrice: "inputSellingPrice",

  stockQuantity: "inputStockQuantity",
  lowStock: "inputLowStockThreshold",

  discountType: "selectDiscountType",
  discountValue: "inputDiscountValue",
  imageGallery: "inputImageGallery",
imagePreviewContainer: "imagePreviewContainer",
};

document.addEventListener("DOMContentLoaded", initializePage);

function initializePage() {
  const app = {
    dom: createDom(ELEMENTS),

    state: {
      validationStarted: false,
    },
  };

  initializeFormValidation(app);

  initializeWizardNavigation(app);

  initializeScrollBehavior(app);

  initializeDiscountToggle(app);

  initializeDynamicConstraints(app);

  initializeWizardValidation(app);
  initializeImageGallery(app);
//   document.querySelectorAll("[required]").forEach(function(field) {

//     const label = document.querySelector(
//         `label[for="${field.id}"]`
//     );

//     if (label) {
//         label.classList.add("required");
//     }

// });
}

/* =====================================================
   Wizard Validation
===================================================== */
