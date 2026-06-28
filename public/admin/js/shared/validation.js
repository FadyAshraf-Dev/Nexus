/* =====================================================
   Container Validation
===================================================== */

function validateContainer(container) {
    const fields = container.querySelectorAll(
        "input, select, textarea"
    );

    let valid = true;
    let firstInvalid = null;

    fields.forEach(function (field) {

        if (field.disabled) {
            return;
        }

        if (field.type === "file") {

            if (field.required && field.files.length === 0) {

                field.classList.add("is-invalid");

                valid = false;

                if (!firstInvalid) {
                    firstInvalid = field;
                }

            } else {

                field.classList.remove("is-invalid");

            }

            return;
        }

        if (!field.checkValidity()) {

            field.classList.add("is-invalid");

            valid = false;

            if (!firstInvalid) {
                firstInvalid = field;
            }

        } else {

            field.classList.remove("is-invalid");

        }

    });

    if (firstInvalid) {
        firstInvalid.focus();
    }

    return valid;
}

/* =====================================================
   Live Validation
===================================================== */

function initializeFormValidation(form) {

    form.querySelectorAll(
        "input, select, textarea"
    ).forEach(function (field) {

        field.addEventListener("input", function () {

            if (field.type === "file") {
                return;
            }

            field.classList.toggle(
                "is-invalid",
                !field.checkValidity()
            );

        });

        field.addEventListener("change", function () {

            if (field.type === "file") {

                field.classList.toggle(
                    "is-invalid",
                    field.required && field.files.length === 0
                );

                return;
            }

            field.classList.toggle(
                "is-invalid",
                !field.checkValidity()
            );

        });

    });

}