import Feedback from "../../shared/Feedback.js";

const ELEMENTS = {

    form: "loginForm",

    email: "inputEmailAddress",
    password: "inputPassword",

};

class Login {

    constructor() {

        this.form = document.getElementById(ELEMENTS.form);

        this.emailField = document.getElementById(ELEMENTS.email);
        this.passwordField = document.getElementById(ELEMENTS.password);

        this.submitButton = this.form.querySelector('button[type="submit"]');

    }

    initialize() {

        this.form.addEventListener(
            "submit",
            (event) => this.handleSubmit(event)
        );

    }

    async handleSubmit(event) {

        event.preventDefault();

        Feedback.clear(this.emailField);
        Feedback.clear(this.passwordField);

        this.submitButton.disabled = true;

        try {

            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.content;

            const response = await fetch(
                this.form.action,
                {
                    method: "POST",
                    body: new FormData(this.form),
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                }
            );

            const payload = await response.json();

            if (!payload.success) {

                Feedback.show(
                    this.passwordField,
                    payload.message ?? "Please check your details and try again."
                );

                return;

            }

            this.redirect(payload.user.role_id);

        } catch (error) {

            console.error(error);

            Feedback.show(
                this.passwordField,
                "Something went wrong. Please try again."
            );

        } finally {

            this.submitButton.disabled = false;

        }

    }

    /**
     * Admin/vendor roles land in the admin dashboard; anyone else
     * (a customer somehow hitting this form) is sent back to the
     * storefront, mirroring the traffic-control check already
     * done server-side at the top of login.php.
     */
    redirect(roleId) {

        window.location.href =
            (roleId === 2 || roleId === 3)
                ? "index.php"
                : "../index.php";

    }

}

document.addEventListener(
    "DOMContentLoaded",
    () => {

        const page = new Login();

        page.initialize();

    }
);