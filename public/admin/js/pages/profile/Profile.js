class Profile {

    bindForm(formId, feedbackId, endpoint, onSuccess) {

        const form = document.getElementById(formId);
        const feedback = document.getElementById(feedbackId);

        form.addEventListener(
            "submit",
            (event) => this.handleSubmit(event, form, feedback, endpoint, onSuccess)
        );

    }

    async handleSubmit(event, form, feedback, endpoint, onSuccess) {

        event.preventDefault();

        feedback.textContent = "";

        form
            .querySelectorAll(".login__field-error")
            .forEach((el) => el.textContent = "");

        try {

            const response = await fetch(endpoint, {
                method: "POST",
                body: new FormData(form),
            });

            const data = await response.json();

            if (!data.success && data.errors) {
                this.showFieldErrors(form, data.errors);
            }

            feedback.textContent = data.message;
            feedback.style.color = data.success ? "#2e7d32" : "#c0392b";

            if (data.success && onSuccess) {
                onSuccess(form);
            }

        } catch (error) {

            console.error(error);

            feedback.textContent = "Something went wrong. Please try again.";
            feedback.style.color = "#c0392b";

        }

    }

    showFieldErrors(form, errors) {

        for (const [field, message] of Object.entries(errors)) {

            const el = form.querySelector(`[data-error-for="${field}"]`);

            if (el) {
                el.textContent = message;
            }

        }

    }

    initialize() {

        this.bindForm(
            "profile-info-form",
            "profile-info-feedback",
            "api/profile/update-info.php"
        );

        this.bindForm(
            "profile-password-form",
            "profile-password-feedback",
            "api/profile/change-password.php",
            (form) => form.reset()
        );

    }

}

document.addEventListener(
    "DOMContentLoaded",
    () => {

        const page = new Profile();

        page.initialize();

    }
);