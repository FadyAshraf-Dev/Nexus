import Renderer from "../core/Renderer.js";
import Feedback from "../shared/Feedback.js";
import ImageTemplate from "../templates/ImageTemplate.js";
import DynamicConstraints from "../products/DynamicConstraints.js";

export default class ProductImageGallery {

    constructor(dom) {

        this.dom = dom;

        this.template = new ImageTemplate();

        this.files = [];

    }

    initialize() {

        this.bindInput();

        this.bindRemove();

        this.render();

    }

    bindInput() {
        this.dom.imageGallery.addEventListener(

            "change",

            () => this.addFiles(this.dom.imageGallery.files)

        );

    }

    bindRemove() {

        document.addEventListener(

            "click",

            e => {

                const button =
                    e.target.closest(
                        '[data-action="remove-image"]'
                    );

                if (!button) {
                    return;
                }

                this.remove(
                    Number(button.dataset.id)
                );

            }

        );

    }

    addFiles(fileList) {

        this.files.push(...Array.from(fileList));

        if (!this.validate()) {
            return;
        }

        this.syncInput();

        this.render();

    }

    remove(index) {

        this.files.splice(index, 1);

        this.syncInput();

        this.render();

    }

    validate() {

        if (
            this.files.length >
            ProductConstraints.MAX_IMAGES
        ) {

            Feedback.show(

                this.dom.imageInput,

                `Maximum ${ProductConstraints.MAX_IMAGES} images allowed.`

            );

            return false;

        }

        Feedback.clear(this.dom.imageInput);

        return true;

    }

    syncInput() {

        const transfer =
            new DataTransfer();

        this.files.forEach(file => {

            transfer.items.add(file);

        });

        this.dom.imageInput.files =
            transfer.files;

    }

    render() {

        Renderer.replace(

            this.dom.imagePreviewContainer,

            this.template.gallery(this.files)

        );

        feather.replace();

    }

    clear() {

        this.files = [];

        this.syncInput();

        this.render();

    }

    getFiles() {

        return [...this.files];

    }

}