export default class WizardValidation {

    constructor(dom, wizard, formValidation) {

        this.dom = dom;
        this.wizard = wizard;
        this.formValidation = formValidation;

        this.steps = [];

    }

    initialize() {

        this.steps = this.dom.getAll(".tab-pane");

        this.bindTabValidation();

        this.bindNextButtons();

        this.bindSubmit();

    }

    /* =====================================================
       Next Buttons
    ===================================================== */

    bindNextButtons() {

        this.dom.getAll(".btn-wizard-next")
            .forEach(button => {

                button.addEventListener(
                    "click",
                    event => this.handleNext(event, button),
                    true
                );

            });

    }

    handleNext(event, button) {

        this.formValidation.start();

        const currentStep = button.closest(".tab-pane");

        if (!this.formValidation.validate(currentStep)) {

            event.preventDefault();

            event.stopImmediatePropagation();

            return;

        }

        this.wizard.next(button);

    }

    /* =====================================================
       Wizard Tabs
    ===================================================== */

    bindTabValidation() {

        this.dom
            .getAll("#productWizardTab .nav-link")
            .forEach(tab => {

                tab.addEventListener(
                    "show.bs.tab",
                    event => this.handleTabChange(event)
                );

            });

    }

    handleTabChange(event) {

        this.formValidation.start();

        const currentStep =
            document.querySelector(".tab-pane.active");

        const currentIndex =
            this.steps.indexOf(currentStep);

        const targetStep =
            this.dom.get(event.target.getAttribute("href"));

        const targetIndex =
            this.steps.indexOf(targetStep);

        // Going backwards?
        if (targetIndex <= currentIndex) {
            return;
        }

        // Validate every skipped step.
        for (let i = currentIndex; i < targetIndex; i++) {

            if (!this.formValidation.validate(this.steps[i])) {

                event.preventDefault();

                return;

            }

        }

    }

    /* =====================================================
       Submit
    ===================================================== */

    bindSubmit() {

        if (!this.dom.form) {
            return;
        }

        this.dom.form.addEventListener(
            "submit",
            event => this.handleSubmit(event)
        );

    }

    handleSubmit(event) {

        this.formValidation.start();


        if (!this.formValidation.validateForm()) {

            event.preventDefault();

        }

    }

}