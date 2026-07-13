const MAX_PRODUCT_IMAGES = 5;

function initializeImageValidation(app) {
    let fileInput = app.imageGallery;
    if (!fileInput) {
        return;
    }

    fileInput.addEventListener("change", function () {

        fileInput.setCustomValidity("");

        if (fileInput.files.length > MAX_PRODUCT_IMAGES) {

            fileInput.setCustomValidity(
                `You can upload a maximum of ${MAX_PRODUCT_IMAGES} images.`
            );

        }

        if (fileInput.checkValidity()) {

            fileInput.classList.remove("is-invalid");

        } else {

            fileInput.classList.add("is-invalid");

        }

    });

}