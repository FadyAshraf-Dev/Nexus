/* =====================================================
   Wizard Navigation
===================================================== */

function initializeWizardNavigation() {
    document.querySelectorAll(".btn-wizard-next")
        .forEach(function (button) {

            button.addEventListener("click", function () {

                const targetTab =
                    document.querySelector(button.dataset.next);

                if (!targetTab) {
                    return;
                }

                bootstrap.Tab
                    .getOrCreateInstance(targetTab)
                    .show();

            });

        });

    document.querySelectorAll(".btn-wizard-prev")
        .forEach(function (button) {

            button.addEventListener("click", function () {

                const targetTab =
                    document.querySelector(button.dataset.prev);

                if (!targetTab) {
                    return;
                }

                bootstrap.Tab
                    .getOrCreateInstance(targetTab)
                    .show();

            });

        });

}
/* =====================================================
   Scroll Behavior
===================================================== */

function initializeScrollBehavior(app) {

    document
        .querySelectorAll('#productWizardTab [data-bs-toggle="tab"]')
        .forEach(function (tab) {

            tab.addEventListener("shown.bs.tab", function () {

                tab.closest(".card").scrollIntoView({

                    behavior: "smooth",
                    block: "start"

                });

            });

        });

}