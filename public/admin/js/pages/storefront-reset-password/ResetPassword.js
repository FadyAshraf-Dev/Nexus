import Dom from "../../core/Dom.js";
import Renderer from "../../core/Renderer.js";
import Ajax from "../../core/Ajax.js";

const ELEMENTS = {

    form: "reset-password-form",
    feedback: "reset-password-feedback",

};

class ResetPassword {

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
                "api/auth/reset-password.php",
                new FormData(this.dom.form)
            );

            Renderer.replace(this.dom.feedback, data.message);

            setTimeout(() => window.location.href = "login.php", 1500);

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

        const page = new ResetPassword();

        page.initialize();

    }
);
