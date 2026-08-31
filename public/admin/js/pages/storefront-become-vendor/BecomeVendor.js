import Dom from "../../core/Dom.js";
import Renderer from "../../core/Renderer.js";
import Ajax from "../../core/Ajax.js";

const ELEMENTS = {

    form: "vendor-request-form",
    feedback: "vendor-request-feedback",

};

class BecomeVendor {

    constructor() {

        this.dom = new Dom(ELEMENTS);

    }

    initialize() {

        this.dom.form.addEventListener(
            "submit",
            (event) => this.handleSubmit(event)
        );

    }

    async handleSubmit(event) {

        event.preventDefault();

        Renderer.clear(this.dom.feedback);
        this.clearFieldErrors();

        try {

            const data = await Ajax.post(
                "api/vendor-request/create.php",
                new FormData(this.dom.form)
            );

            this.showSuccess(data.message);

        } catch (error) {

            console.error(error);

            if (error.payload?.errors) {
                this.showFieldErrors(error.payload.errors);
            }

            this.showError(
                error.message ?? "Please check your details and try again."
            );

        }

    }

    clearFieldErrors() {

        this.dom
            .getAll(".login__field-error")
            .forEach((el) => Renderer.clear(el));

    }

    showFieldErrors(errors) {

        for (const [field, message] of Object.entries(errors)) {

            const el = this.dom.form.querySelector(`[data-error-for="${field}"]`);

            if (el) {
                Renderer.replace(el, message);
            }

        }

    }

    showError(message) {

        Renderer.replace(this.dom.feedback, message);

        this.dom.feedback.style.color = "#c0392b";

    }

    showSuccess(message) {

        Renderer.replace(this.dom.feedback, message);

        this.dom.feedback.style.color = "#2e7d32";

        setTimeout(() => window.location.reload(), 1500);

    }

}

document.addEventListener(
    "DOMContentLoaded",
    () => {

        const page = new BecomeVendor();

        page.initialize();

    }
);
