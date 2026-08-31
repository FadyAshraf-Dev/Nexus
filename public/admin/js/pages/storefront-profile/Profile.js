import Renderer from "../../core/Renderer.js";
import Ajax from "../../core/Ajax.js";

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

        Renderer.clear(feedback);

        form
            .querySelectorAll(".login__field-error")
            .forEach((el) => Renderer.clear(el));

        try {

            const data = await Ajax.post(
                endpoint,
                new FormData(form)
            );

            Renderer.replace(feedback, data.message);
            feedback.style.color = "#2e7d32";

            if (onSuccess) {
                onSuccess(form);
            }

        } catch (error) {

            console.error(error);

            if (error.payload?.errors) {
                this.showFieldErrors(form, error.payload.errors);
            }

            Renderer.replace(
                feedback,
                error.message ?? "Something went wrong. Please try again."
            );
            feedback.style.color = "#c0392b";

        }

    }

    showFieldErrors(form, errors) {

        for (const [field, message] of Object.entries(errors)) {

            const el = form.querySelector(`[data-error-for="${field}"]`);

            if (el) {
                Renderer.replace(el, message);
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
