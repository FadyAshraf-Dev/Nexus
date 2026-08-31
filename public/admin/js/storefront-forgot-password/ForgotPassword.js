class ForgotPassword {

    constructor() {

        this.form = document.getElementById("forgot-password-form");
        this.feedback = document.getElementById("forgot-password-feedback");

    }

    initialize() {

        this.form.addEventListener(
            "submit",
            (event) => this.handleSubmit(event)
        );

    }

    async handleSubmit(event) {

        event.preventDefault();

        this.feedback.textContent = "";

        try {

            const response = await fetch("api/auth/forgot-password.php", {
                method: "POST",
                body: new FormData(this.form),
            });

            const data = await response.json();

            this.feedback.textContent = data.message;

            if (data.success) {
                this.form.querySelector('button[type="submit"]').disabled = true;
            }

        } catch (error) {

            console.error(error);

            this.feedback.textContent = "Something went wrong. Please try again.";

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