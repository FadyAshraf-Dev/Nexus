class Renderer {

    static replace(element, html) {
        this.ensureElement(element);

        element.innerHTML = html;
    }

    static append(element, html) {
        this.ensureElement(element);

        element.insertAdjacentHTML(
            "beforeend",
            html
        );
    }

    static prepend(element, html) {
        this.ensureElement(element);

        element.insertAdjacentHTML(
            "afterbegin",
            html
        );
    }

    static clear(element) {
        this.ensureElement(element);

        element.innerHTML = "";
    }

    static showLoading(
        element,
        message = "Loading..."
    ) {
        this.ensureElement(element);

        element.innerHTML = `
            <tr>
                <td colspan="999" class="text-center py-5 text-muted">
                    ${message}
                </td>
            </tr>
        `;
    }

    static showEmpty(
        element,
        message = "Nothing to display."
    ) {
        this.ensureElement(element);

        element.innerHTML = `
            <tr>
                <td colspan="999" class="text-center py-5 text-muted">
                    ${message}
                </td>
            </tr>
        `;
    }

    static showError(
        element,
        message = "Something went wrong."
    ) {
        this.ensureElement(element);

        element.innerHTML = `
            <tr>
                <td colspan="999" class="text-center py-5 text-danger">
                    ${message}
                </td>
            </tr>
        `;
    }

    static showSuccess(
        element,
        message = "Operation completed successfully."
    ) {
        this.ensureElement(element);

        element.innerHTML = `
            <tr>
                <td colspan="999" class="text-center py-5 text-success">
                    ${message}
                </td>
            </tr>
        `;
    }

    static ensureElement(element) {

        if (!(element instanceof Element)) {
            throw new Error(
                "Renderer expected a valid DOM element."
            );
        }

    }

}