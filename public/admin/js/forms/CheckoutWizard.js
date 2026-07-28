export default class CheckoutWizard {

    constructor(dom) {

        this.dom = dom;

        this.currentStep = 1;

    }

    initialize() {

        this.stepButtons =
            this.dom.getAll("[data-step-btn]");

        this.stepPanels =
            this.dom.getAll("[data-step-panel]");

        this.totalSteps =
            this.stepPanels.length;

        this.previousButton =
            this.dom.previousButton;

        this.nextButton =
            this.dom.nextButton;

        this.submitButton =
            this.dom.submitButton;

        this.render();

    }

    next() {

        this.goTo(this.currentStep + 1);

    }

    previous() {

        this.goTo(this.currentStep - 1);

    }

    goTo(step) {

        if (
            step < 1 ||
            step > this.totalSteps
        ) {
            return;
        }

        this.currentStep = step;

        this.render();

    }

    render() {

        this.stepButtons.forEach(button => {

            const active =
                Number(button.dataset.stepBtn)
                === this.currentStep;

            button.classList.toggle(
                "is-active",
                active
            );

        });

        this.stepPanels.forEach(panel => {

            const active =
                Number(panel.dataset.stepPanel)
                === this.currentStep;

            panel.classList.toggle(
                "is-active",
                active
            );

        });

        this.previousButton.disabled =
            this.currentStep === 1;

        const last =
            this.currentStep === this.totalSteps;

        this.nextButton.hidden = last;

        this.submitButton.hidden = !last;

    }

}