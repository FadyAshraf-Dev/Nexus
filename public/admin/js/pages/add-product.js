const ELEMENTS = {
  form: "addProductForm",

  costPrice: "inputCostPrice",
  sellingPrice: "inputSellingPrice",

  stockQuantity: "inputStockQuantity",
  lowStock: "inputLowStockThreshold",

  discountType: "selectDiscountType",
  discountValue: "inputDiscountValue",
};

document.addEventListener("DOMContentLoaded", initializePage);

function initializePage() {
  const dom = createDom(ELEMENTS);

  initializeFormValidation(dom.form);

  initializeWizardNavigation();

  initializeScrollBehavior();

  initializeDiscountToggle(dom);

  initializeDynamicConstraints(dom);

  initializeWizardValidation();
}

/* =====================================================
   Wizard Validation
===================================================== */

function initializeWizardValidation() {
  document.querySelectorAll(".btn-wizard-next").forEach(function (button) {
    button.addEventListener(
      "click",
      function (event) {
        startValidation();

        const currentStep = button.closest(".tab-pane");

        if (!validateContainer(currentStep)) {
          event.stopImmediatePropagation();
        }
      },
      true,
    );
  });

  const form = document.getElementById("addProductForm");

  form.addEventListener("submit", function (event) {
    startValidation();
    const lastStep = document.getElementById("step4");

    if (!validateContainer(lastStep)) {
      event.preventDefault();
    }
  });
}
