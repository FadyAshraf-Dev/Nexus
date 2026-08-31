import Dom from "../../core/Dom.js";
import Renderer from "../../core/Renderer.js";
import Ajax from "../../core/Ajax.js";

const ELEMENTS = {

    heading: "activate-heading",
    message: "activate-message",
    footer: "activate-footer",

};

class Activate {

    constructor() {

        this.dom = new Dom(ELEMENTS);

        this.token = this.dom.heading.dataset.token;

    }

    async initialize() {

        try {

            const data = await Ajax.get(
                "api/auth/activate.php?token=" + encodeURIComponent(this.token)
            );

            this.showResult(true, data.message);

        } catch (error) {

            // Ajax throws on non-2xx, and activate.php returns 400
            // for an invalid/expired token - that's a normal,
            // expected outcome here, not an unexpected failure, so
            // it's handled the same way as a success response
            // rather than falling through to the generic message
            // below. error.payload carries the parsed JSON body
            // (see Ajax.handleResponse), which still has .message.
            if (error.payload) {

                this.showResult(false, error.payload.message);

                return;

            }

            console.error(error);

            this.showResult(
                false,
                "Please try again, or request a new activation link.",
                "Something went wrong."
            );

        }

    }

    showResult(success, message, heading = null) {

        Renderer.replace(
            this.dom.heading,
            heading ?? (success ? "Account activated!" : "Activation failed.")
        );

        Renderer.replace(this.dom.message, message);

        this.dom.footer.style.display = "";

    }

}

document.addEventListener(
    "DOMContentLoaded",
    () => {

        const page = new Activate();

        page.initialize();

    }
);
