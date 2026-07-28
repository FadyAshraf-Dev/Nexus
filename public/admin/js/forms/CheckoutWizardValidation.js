export default class CheckoutWizardValidation {

    constructor(
        dom,
        wizard,
        validation
    ) {

        this.dom = dom;

        this.wizard = wizard;

        this.validation = validation;

    }

    initialize() {

        this.bindNext();

        this.bindPrevious();

        this.bindStepper();

        this.bindSubmit();

    }

    /* ============================================
       Next
    ============================================ */

    bindNext() {

        this.dom.nextButton.addEventListener(

            "click",

            () => {

                this.validation.start();

                const panel =
                    this.currentPanel();

                if (
                    !this.validation.validate(panel)
                ) {
                    return;
                }

                if (
                    this.wizard.currentStep
                    ===
                    this.wizard.totalSteps - 1
                ) {

                    this.populateReview();

                }

                this.wizard.next();

            }

        );

    }

    /* ============================================
       Previous
    ============================================ */

    bindPrevious() {

        this.dom.previousButton.addEventListener(

            "click",

            () => {

                this.wizard.previous();

            }

        );

    }

    /* ============================================
       Stepper
    ============================================ */

    bindStepper() {

        this.dom
            .getAll("[data-step-btn]")
            .forEach(button => {

                button.addEventListener(

                    "click",

                    () => {

                        const target =
                            Number(
                                button.dataset.stepBtn
                            );

                        /*
                        Always allow backwards
                        */

                        if (
                            target
                            <=
                            this.wizard.currentStep
                        ) {

                            this.wizard.goTo(target);

                            return;

                        }

                        /*
                        Validate every step
                        between current
                        and destination.
                        */

                        this.validation.start();

                        for (

                            let step =
                                this.wizard.currentStep;

                            step < target;

                            step++

                        ) {

                            const panel =
                                this.dom.get(
                                    `[data-step-panel="${step}"]`
                                );

                            if (
                                !this.validation.validate(panel)
                            ) {
                                return;
                            }

                        }

                        if (
                            target
                            ===
                            this.wizard.totalSteps
                        ) {

                            this.populateReview();

                        }

                        this.wizard.goTo(target);

                    }

                );

            });

    }

    /* ============================================
       Submit
    ============================================ */

    bindSubmit() {

        this.dom.form.addEventListener(

            "submit",

            event => {

                this.validation.start();

                if (
                    !this.validation.validateForm()
                ) {

                    event.preventDefault();

                }

            }

        );

    }

    /* ============================================
       Helpers
    ============================================ */

    currentPanel() {

        return this.dom.get(

            `[data-step-panel="${this.wizard.currentStep}"]`

        );

    }

    populateReview() {

        const address = [

            this.dom.shippingAddress1.value,

            this.dom.shippingAddress2.value,

            this.dom.shippingCity.value,

            this.dom.shippingState.value,

            this.dom.shippingCountry.value,

            this.dom.shippingZip.value

        ]

        .filter(Boolean)

        .join(", ");

        this.dom.reviewAddress.textContent =
            address;

        this.dom.reviewPhone.textContent =
            this.dom.contactPhone.value;

        this.dom.address.value =
            address;

    }

}