function initializeWizardValidation(app) {
      const { dom, state } = app;

  const steps = Array.from(document.querySelectorAll(".tab-pane"));
  document.querySelectorAll(".btn-wizard-next").forEach(function (button) {
    button.addEventListener(
      "click",
      function (event) {
        state.validationStarted = true;

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
    state.validationStarted = true;
    const lastStep = document.getElementById("step4");

    if (!validateContainer(lastStep)) {
      event.preventDefault();
    }
  });
  document
    .querySelectorAll("#productWizardTab .nav-link")
    .forEach(function (tab) {
      tab.addEventListener("show.bs.tab", function (event) {
        state.validationStarted = true;

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
