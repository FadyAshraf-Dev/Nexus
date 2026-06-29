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
  const steps = Array.from(document.querySelectorAll(".tab-pane"));
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
  document
    .querySelectorAll("#productWizardTab .nav-link")
    .forEach(function (tab) {
      tab.addEventListener("show.bs.tab", function (event) {
        startValidation();

        const currentStep = document.querySelector(".tab-pane.active");

        const currentIndex = steps.indexOf(currentStep);

        const targetStep = document.querySelector(
          event.target.getAttribute("href"),
        );

        const targetIndex = steps.indexOf(targetStep);

        // Going backwards? Always allow.
        if (targetIndex <= currentIndex) {
          return;
        }

        // Validate every skipped step.
        for (let i = currentIndex; i < targetIndex; i++) {
          if (!validateContainer(steps[i])) {
            event.preventDefault();
            return;
          }
        }
      });
    });
}
