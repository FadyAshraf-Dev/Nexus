import Dom from "../../core/Dom.js";
import Renderer from "../../core/Renderer.js";
import Ajax from "../../core/Ajax.js";

const ELEMENTS = {

    form: "forgot-password-form",
    feedback: "forgot-password-feedback",

};

class ForgotPassword {

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

        try {

            const data = await Ajax.post(
                "api/auth/forgot-password.php",
                new FormData(this.dom.form)
            );

            Renderer.replace(this.dom.feedback, data.message);

            this.dom.form.querySelector('button[type="submit"]').disabled = true;

        } catch (error) {

            console.error(error);

            Renderer.replace(
                this.dom.feedback,
                error.message ?? "Something went wrong. Please try again."
            );

        }

    }

}

document.addEventListener(
    "DOMContentLoaded",
    () => {

        const page = new ForgotPassword();

        page.initialize();

    }
);
