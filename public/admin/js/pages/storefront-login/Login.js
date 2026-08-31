import Dom from "../../core/Dom.js";
import Renderer from "../../core/Renderer.js";
import Ajax from "../../core/Ajax.js";

const ELEMENTS = {

    form: "login-form",
    feedback: "login-feedback",

};

class Login {

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
                this.dom.form.action,
                new FormData(this.dom.form)
            );

            this.redirect(data.user.role_id);

        } catch (error) {

            console.error(error);

            this.showError(
                error.message ?? "Something went wrong. Please try again."
            );

        }

    }

    showError(message) {

        Renderer.replace(this.dom.feedback, message);

        this.dom.feedback.style.color = "#c0392b";

    }

    redirect(roleId) {

        window.location.href =
            (roleId === 2 || roleId === 3) ? "admin/index.php" : "index.php";

    }

}

document.addEventListener(
    "DOMContentLoaded",
    () => {

        const page = new Login();

        page.initialize();

    }
);
