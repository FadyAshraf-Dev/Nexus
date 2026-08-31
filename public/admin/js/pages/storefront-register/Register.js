import Dom from "../../core/Dom.js";
import Renderer from "../../core/Renderer.js";
import Ajax from "../../core/Ajax.js";

const ELEMENTS = {

    form: "register-form",
    feedback: "register-feedback",

};

class Register {

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
                this.dom.form.action,
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

        this.dom.form.reset();
        this.dom.form.querySelector('button[type="submit"]').disabled = true;

    }

}

document.addEventListener(
    "DOMContentLoaded",
    () => {

        const page = new Register();

        page.initialize();

    }
);
