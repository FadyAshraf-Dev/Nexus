export default class Feedback {

    /**
     * Displays an error message for a form control.
     *
     * @param {HTMLElement} field
     * @param {string} message
     */
    static show(field, message) {

        if (!(field instanceof HTMLElement)) {
            throw new Error("Feedback.show() expects a valid form field.");
        }

        field.classList.add("is-invalid");

        let feedback = this.find(field);

        if (!feedback) {

            feedback = document.createElement("div");
            feedback.className = "invalid-feedback";

            field.insertAdjacentElement(
                "afterend",
                feedback
            );
        }

        feedback.textContent = message;
    }

    /**
     * Clears validation feedback from a form control.
     *
     * @param {HTMLElement} field
     */
    static clear(field) {

        if (!(field instanceof HTMLElement)) {
            return;
        }

        field.classList.remove("is-invalid");

        const feedback = this.find(field);

        if (feedback) {
            feedback.remove();
        }
    }

    /**
     * Clears validation feedback from every field inside
     * the supplied container.
     *
     * @param {HTMLElement} container
     */
    static clearContainer(container) {

        if (!(container instanceof HTMLElement)) {
            return;
        }

        container
            .querySelectorAll(".is-invalid")
            .forEach(field => field.classList.remove("is-invalid"));

        container
            .querySelectorAll(".invalid-feedback")
            .forEach(feedback => feedback.remove());
    }

    /**
     * Returns the feedback element belonging to a field.
     *
     * @param {HTMLElement} field
     * @returns {HTMLElement|null}
     */
    static find(field) {

        const next = field.nextElementSibling;

        if (
            next &&
            next.classList.contains("invalid-feedback")
        ) {
            return next;
        }

        return null;
    }

}