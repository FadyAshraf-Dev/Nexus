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