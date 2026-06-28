/* =====================================================
   Scroll Behavior
===================================================== */

function initializeScrollBehavior() {

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