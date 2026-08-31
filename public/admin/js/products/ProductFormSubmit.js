import Feedback from "../shared/Feedback.js";

/**
 * Submits a product wizard form (multipart, for file uploads) via
 * fetch instead of a native POST, and maps a JSON error response
 * back onto the form's fields.
 *
 * Not built on core/Ajax.js: Ajax always JSON.stringify()s its
 * body, which can't carry File objects. CSRF::verify() already
 * accepts the token as a plain $_POST field (see CSRF.php), so a
 * native FormData submission works against the same backend
 * without changes there.
 */
export default class ProductFormSubmit {

    constructor(form, redirectUrl) {

        this.form = form;

        this.redirectUrl = redirectUrl;

    }

    async submit() {

        const submitButton =
            this.form.querySelector('button[type="submit"]');

        if (submitButton) {
            submitButton.disabled = true;
        }

        try {

            const response = await fetch(
                this.form.action,
                {
                    method: "POST",
                    body: new FormData(this.form),
                    headers: {
                        Accept: "application/json",
                    },
                }
            );

            const payload = await response.json();

            if (!payload.success) {

                this.showErrors(payload.errors ?? {});

                alert(
                    payload.message ??
                    "Please check the form and try again."
                );

                return;

            }

            window.location.href = this.redirectUrl;

        } catch (error) {

            console.error(error);

            alert("Something went wrong. Please try again.");

        } finally {

            if (submitButton) {
                submitButton.disabled = false;
            }

        }

    }

    /**
     * Maps { field: message } validation errors onto their inputs
     * and jumps to the first invalid step's tab, since the wizard
     * hides steps 2-4 by default and a buried error is as good as
     * invisible.
     */
    showErrors(errors) {

        let firstInvalidField = null;

        for (const [field, message] of Object.entries(errors)) {

            const input = this.form.querySelector(
                `[name="${field}"], [name="${field}[]"]`
            );

            if (!input) {
                continue;
            }

            Feedback.show(input, message);

            firstInvalidField ??= input;

        }

        if (!firstInvalidField) {
            return;
        }

        const step = firstInvalidField.closest(".tab-pane");

        const tabId = step?.getAttribute("id");

        if (!tabId) {
            return;
        }

        const tabButton = document.querySelector(
            `#productWizardTab [href="#${tabId}"]`
        );

        if (tabButton) {
            bootstrap.Tab.getOrCreateInstance(tabButton).show();
        }

    }

}
