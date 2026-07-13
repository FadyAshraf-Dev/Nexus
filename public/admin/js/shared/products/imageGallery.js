/* =====================================================
   Image Gallery
===================================================== */

let selectedFiles = [];

function initializeImageGallery(app) {

    const { dom } = app;

    const form = dom.form;
    const input = dom.imageGallery;
    const previewContainer = dom.imagePreviewContainer;

    if (!input || !previewContainer) {
        return;
    }

    input.addEventListener("change", function () {

        selectedFiles = Array.from(input.files);

        renderImagePreviews(
            selectedFiles,
            previewContainer
        );

    });

    new Sortable(previewContainer, {

        animation: 150,

        handle: ".image-drag-handle",

        ghostClass: "sortable-ghost",

        chosenClass: "sortable-chosen",

        onEnd(event) {

            const moved =
                selectedFiles.splice(event.oldIndex, 1)[0];

            selectedFiles.splice(
                event.newIndex,
                0,
                moved
            );

            renderImagePreviews(
                selectedFiles,
                previewContainer
            );

        }

    });

    form.addEventListener("submit", function () {

        const dt = new DataTransfer();

        selectedFiles.forEach(function (file) {

            dt.items.add(file);

        });

        input.files = dt.files;

    });

}

function renderImagePreviews(files, container) {

    container.innerHTML = "";

    files.forEach(function (file, index) {

        const reader = new FileReader();

        reader.onload = function () {

            container.appendChild(
                createPreviewCard(
                    reader.result,
                    index
                )
            );

        };

        reader.readAsDataURL(file);

    });

}

function createPreviewCard(imageSource, index) {

    const column = document.createElement("div");

    column.className = "col-md-4";

    column.innerHTML = `
        <div class="card h-100 shadow-sm image-preview-card">

            <img
                src="${imageSource}"
                class="card-img-top image-drag-handle"
                style="height:220px; object-fit:cover;"
                draggable="false"
            >

            <div class="card-body text-center">

                ${
                    index === 0
                        ? '<span class="badge bg-primary">Primary Image</span>'
                        : '<span class="badge bg-secondary">Gallery Image</span>'
                }

            </div>

        </div>
    `;

    return column;

}